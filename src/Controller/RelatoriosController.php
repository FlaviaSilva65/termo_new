<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Relatorio;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Authorization\Exception\ForbiddenException;
use Cake\I18n\Date;
use DateTime;

/**
 * Relatorios Controller
 *
 * @property \App\Model\Table\RelatoriosTable $Relatorios
 */
class RelatoriosController extends AppController
{
    // public function initialize(): void
    // {
    //     parent::initialize();
    //     // $this->loadComponent('CrachaDigital');
    //     $this->Authentication->allowUnauthenticated(['manterPerguntas']);
    // }

    public function manterPerguntas($dimensao, $escola_id, $id = null)
    {
        $this->Authorization->skipAuthorization();
        $identity = $this->Authentication->getIdentity();

        // Mapeia perfis que só navegam/assinam (não editam nem salvam rascunho)
        $camposAssinaturaPorPerfil = [
            1 => 'id_ass_dir',   // Diretor
            5 => 'id_ass_assis', // Assistente
            7 => 'id_ass_sub',   // Subsecretaria
            8 => 'id_ass_sub',   // Subsecretaria-adjunto
        ];

        $modoSomenteLeitura = array_key_exists($identity->tp_usuarios_id, $camposAssinaturaPorPerfil);
        $campoAssinaturaUsuario = $camposAssinaturaPorPerfil[$identity->tp_usuarios_id] ?? null;

        $queryPerguntas = $this->fetchTable('Perguntas');
        $queryOcorrencias = $this->fetchTable('Ocorrencias');
        $UnidEscolares = $this->fetchTable('UnidEscolares');
        $Usuarios = $this->fetchTable('Usuarios');

        $Escolas = $this->fetchTable('Escolas');
        $dashboards = $this->fetchTable('Dashboards'); // Acessando a tabela Dashboards com a DashboardsTable
        $funcionarios = $this->fetchTable('Funcionarios'); // Acessando a tabela Funcionarios com FuncionariosTable

        $Respostas = $this->fetchTable('Respostas');
        $OcorrenciaRelatorios = $this->fetchTable('OcorrenciaRelatorios');
        $Providencia = $this->fetchTable('Providencias');

        // ------ MODO: pendências? ------
        $somentePendencias = (bool)$this->request->getQuery('pendencias');

        // \Cake\Log\Log::debug('X-Requested-With: ' . $this->request->getHeaderLine('X-Requested-With'));
        // \Cake\Log\Log::debug('is ajax: ' . var_export($this->request->is('ajax'), true));

        $escolaName = $UnidEscolares->find()
            ->select(['sigla', 'nm_unid_escolar'])
            ->where(['id' => $escola_id])
            ->first();

        // Aqui vem os dados do Banco ESCOLAS  
        $dados_escolas = $Escolas->find()->where(['nome LIKE' => '%' .  $escolaName->nm_unid_escolar . '%'])->first();
        $usuario_dashboards = $dashboards->find()->where(['escola_id' => $dados_escolas->id_escola])->all();

        $funcionario_ids = [];
        foreach ($usuario_dashboards as $usuario_dashboard):
            $funcionario_ids[] = $usuario_dashboard->funcionario_id;
        endforeach;

        $func_dashboard = $funcionarios->find()->where(['id_funcionario IN' => $funcionario_ids, 'funcao IN' => [2, 3]])->all();

        $rf_list = [];
        foreach ($func_dashboard as $funcionario):
            $rf_list[] = $funcionario->rf;
        endforeach;

        $gestores = $Usuarios->find()
            ->where(['cd_rf IN' => $rf_list])
            ->all();

        $relatorio = $this->Relatorios->newEmptyEntity();

        $forcarNovo = (bool)$this->request->getQuery('novo');

        if ($id) {
            $relatorio = $this->Relatorios->get($id);
        } else {

            $relatorioExistente = $this->Relatorios->find()
                ->where([
                    'usuario_id' => $identity->id,
                    'unid_escolar_id' => $escola_id,
                    'YEAR(created)' => date('Y'),
                    'MONTH(created)' => date('m'),
                    'ic_rascunho' => 1, // ainda em rascunho
                ])
                ->orderByDesc('id')
                ->first();

            if ($relatorioExistente) {
                $this->Authorization->authorize($relatorioExistente, 'manterPerguntas');
                return $this->redirect(['action' => 'manterPerguntas', $dimensao, $escola_id, $relatorioExistente->id]);
            }

            $relatorio_ano = $this->Relatorios->find()->where(['unid_escolar_id' => $escola_id, 'YEAR(created)' => date('Y')])->all();
            $ultimo_relatorio = $relatorio_ano->last();

            $termo_id = $ultimo_relatorio ? $ultimo_relatorio->termo_id + 1 : 1;

            $relatorio = $this->Relatorios->newEntity([
                'termo_id' => $termo_id,
                'usuario_id' => $identity->id,
                'unid_escolar_id' => $escola_id,
                // 'data' => date('Y-m-d'),
                'ic_rascunho' => 1, // 1 = rascunho, 0 = finalizado
            ]);

            $this->Relatorios->save($relatorio);
            // dd($relatorio);
            return $this->redirect(['action' => 'manterPerguntas', $dimensao, $escola_id, $relatorio->id]);
        }
        // dd($relatorio);
        if ($relatorio->responsavel_id) {

            $usuarios_lista = collection($Usuarios->find()->where(['id' => $relatorio->responsavel_id])->all())
                ->combine('id', 'nm_usuario')
                ->toArray();
        } else {
            $usuarios_lista = collection($gestores)
                ->combine('id', 'nm_usuario') // ajuste os nomes dos campos conforme sua tabela
                ->toArray();
        }

        $this->Authorization->authorize($relatorio, 'manterPerguntas');

        //Monta o set da seção do termo e garante o nº de dimensões de
        // if (!$somentePendencias) {
        $dimensao = $this->secoesTermo($dimensao, $queryPerguntas, $relatorio->id, $identity->tp_usuarios_id);
        // }

        if (
            in_array($identity->tp_usuarios_id, [1, 5]) &&
            $dimensao == 7
        ) {
            return $this->redirect([
                'action' => 'manterPerguntas',
                6,
                $escola_id,
                $id
            ]);
        }


        // ----- Se estiver em modo pendências, calcula quais dimensões ainda têm pendência -----
        $dimensoesComPendencia = [];

        if ($somentePendencias) {
            $perguntaIdsPendentesGeral = $Providencia->find()
                ->select(['pergunta_id'])
                ->where(['relatorio_id' => $id, 'status' => 0])
                ->all()
                ->extract('pergunta_id')
                ->toArray();

            if (!empty($perguntaIdsPendentesGeral)) {
                $dimensoesComPendencia = $queryPerguntas->find()
                    ->select(['dimensao'])
                    ->where(['id IN' => $perguntaIdsPendentesGeral])
                    ->distinct(['dimensao'])
                    ->orderByAsc('dimensao')
                    ->all()
                    ->extract('dimensao')
                    ->toArray();
            }

            // Se a dimensão atual não tem mais pendência (ex: acabou de resolver a última),
            // pula pra próxima dimensão da lista, ou volta ao painel se não sobrar nenhuma.
            if (!$this->request->is(['post', 'put'])) {
                if (!empty($dimensoesComPendencia) && !in_array($dimensao, $dimensoesComPendencia)) {
                    $proxima = null;
                    foreach ($dimensoesComPendencia as $d) {
                        if ($d > $dimensao) {
                            $proxima = $d;
                            break;
                        }
                    }
                    if ($proxima) {
                        return $this->redirect(['action' => 'manterPerguntas', $proxima, $escola_id, $id, '?' => ['pendencias' => 1]]);
                    }
                    $this->Flash->success('Todas as pendências deste relatório foram resolvidas.');
                    return $this->redirect(['action' => 'dash-supervisor']);
                }
            }
        }

        if ($this->request->is(['post', 'put'])) {
            if ($modoSomenteLeitura) {
                $this->response = $this->response
                    ->withStatus(403)
                    ->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Sem permissão para editar.']));
                $this->autoRender = false;
                return $this->response;
            }
            $data = $this->request->getData();

            \Cake\Log\Log::debug('DADOS CoMPLETOS: ' . json_encode($data));

            if (!empty($data['relatorio'])) {
                $relatorio = $this->Relatorios->patchEntity($relatorio, $data['relatorio']);
                if (!$this->Relatorios->save($relatorio)) {
                    \Cake\Log\Log::debug('Erro ao salvar relatorio: ' . json_encode($relatorio->getErrors()));
                }
            }

            // Se veio a data da visita, registra também em Respostas
            // para contar como pergunta respondida na dimensão.
            if (!empty($data['relatorio']['data'])) {
                $perguntaData = $queryPerguntas->find()
                    ->select(['id'])
                    ->where(['dimensao' => $dimensao, 'tipo' => 'data'])
                    ->first();

                if ($perguntaData) {
                    $existenteData = $Respostas->find()
                        ->where(['relatorio_id' => $id, 'pergunta_id' => $perguntaData->id])
                        ->first();

                    $entityData = $existenteData ?: $Respostas->newEntity([
                        'relatorio_id' => $id,
                        'pergunta_id' => $perguntaData->id,
                    ]);

                    $entityData->resposta = $data['relatorio']['data'];
                    $entityData->status = 1; // sempre considerada "sem pendência"
                    $Respostas->save($entityData);
                }
            }


            // Salva o usuário selecionado na pergunta 35 (id_usuario)
            if ($this->request->getData('responsavel_id')) {
                $relatorio = $this->Relatorios->patchEntity($relatorio, [
                    'responsavel_id' => $this->request->getData('responsavel_id'),
                ]);

                if (!$this->Relatorios->save($relatorio)) {
                    \Cake\Log\Log::debug('Erro ao salvar responsavel_id: ' . json_encode($relatorio->getErrors()));
                }
            }

            // Ids de perguntas válidas para este conjunto (evita erro de FK com ids "fantasmas")
            $perguntaIdsValidasPost = $queryPerguntas->find()
                ->select(['id'])
                ->all()
                ->extract('id')
                ->toArray();

            foreach ($data['respostas'] ?? [] as $perguntaId => $resposta) {

                $existente = $Respostas->find()
                    ->where(['relatorio_id' => $id, 'pergunta_id' => $perguntaId])
                    ->first();

                $entity = $existente ?: $Respostas->newEntity([
                    'relatorio_id' => $id,
                    'pergunta_id' => $perguntaId,
                ]);

                $entity->resposta = $resposta['resposta'] ?? null;
                $entity->observacao = $resposta['observacao'] ?? null;
                $entity->status = isset($resposta['status']) ? (int)$resposta['status'] : 1;

                $Respostas->save($entity);

                if ($entity->status == 0) {
                    $verExistencia = $Providencia->find()
                        ->where(['relatorio_id' => $id, 'pergunta_id' => $perguntaId])
                        ->first();

                    $providenciaEntity = $verExistencia ?: $Providencia->newEntity([
                        'relatorio_id' => $id,
                        'unid_escolar_id' => $escola_id,
                        'pergunta_id' => $perguntaId,
                        'usuario_id' => $identity->id

                    ]);
                    $providenciaEntity->status = 0;
                    $providenciaEntity->resposta_id = $entity->id;
                    $providenciaEntity->descricao = $resposta['observacao'] ?? null;

                    $Providencia->save($providenciaEntity);
                } else {
                    // Se existir uma providência aberta para essa pergunta, marca como resolvida
                    $Providencia->updateAll(
                        ['status' => 1],
                        ['relatorio_id' => $id, 'pergunta_id' => $perguntaId, 'status' => 0]
                    );
                }

                if (isset($resposta['ocorrencias'])) {
                    $idsPossiveis = $queryOcorrencias->find()
                        ->select(['id'])
                        ->where(['pergunta_id' => $perguntaId])
                        ->all()
                        ->extract('id')
                        ->toArray();

                    $OcorrenciaRelatorios->deleteAll([
                        'relatorio_id' => $id,
                        'ocorrencia_id IN' => $idsPossiveis,
                    ]);

                    // Filtra somente as ocorrências que foram de fato marcadas (valor "1")
                    $ocorrenciasMarcadas = array_filter($resposta['ocorrencias'], function ($valor) {
                        return (string)$valor === '1';
                    });

                    foreach (array_keys($ocorrenciasMarcadas) as $ocorrenciaId) {
                        $OcorrenciaRelatorios->save($OcorrenciaRelatorios->newEntity([
                            'relatorio_id' => $id,
                            'ocorrencia_id' => $ocorrenciaId,
                        ]));
                    }
                }
            }

            $isAjax = $this->request->is('ajax')
                || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest'
                || (bool)$this->request->getData('_ajax');

            if ($isAjax) {
                $contagem = $this->contarRespondidasPorDimensao($id, $queryPerguntas);

                $this->autoRender = false;
                $this->response = $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'relatorio_id' => $id,
                        'contagem' => $contagem,
                    ]));
                return $this->response;
            }

            $this->Flash->success('Respostas salvas.');

            $proximaDimensao = (int)$this->request->getData('_proxima_dimensao', $dimensao);
            $queryString = $somentePendencias ? ['?' => ['pendencias' => 1]] : [];

            if ($somentePendencias) {
                // Recalcula pendências restantes após o save
                $pendentesRestantes = $Providencia->find()
                    ->select(['pergunta_id'])
                    ->where(['relatorio_id' => $id, 'status' => 0])
                    ->all()
                    ->extract('pergunta_id')
                    ->toArray();

                if (empty($pendentesRestantes)) {
                    $this->Flash->success('Todas as pendências deste relatório foram resolvidas.');
                    return $this->redirect(['action' => 'dash-supervisor']);
                }

                $dimensoesRestantes = $queryPerguntas->find()
                    ->select(['dimensao'])
                    ->where(['id IN' => $pendentesRestantes])
                    ->distinct(['dimensao'])
                    ->orderByAsc('dimensao')
                    ->all()
                    ->extract('dimensao')
                    ->toArray();

                // Se a dimensão atual ainda tem pendência, fica nela; senão, pula pra próxima disponível
                $proximaDimensao = in_array($dimensao, $dimensoesRestantes)
                    ? $dimensao
                    : (collection($dimensoesRestantes)->filter(fn($d) => $d > $dimensao)->first() ?? min($dimensoesRestantes));
            }
            return $this->redirect(array_merge(
                ['action' => 'manterPerguntas', $proximaDimensao, $escola_id, $id],
                $queryString
            ));
            // return $this->redirect(['action' => 'manterPerguntas', $proximaDimensao, $escola_id, $id]);
        }

        //Perguntas de acordo com a dimensão
        $condicoes = ['dimensao' => $dimensao];

        if ($id != null) {
            $condicoes['created <'] = $relatorio->created;
        }

        // if (in_array($identity->tp_usuarios_id, [1, 5])) {
        //     $condicoes['Perguntas.id !='] = 34;
        // }

        $perguntas = $queryPerguntas->find()
            ->select(['id', 'ordem', 'descricao', 'tipo', 'opcoes', 'importancia', 'dimensao', 'created'])
            ->where($condicoes)
            ->orderByAsc('ordem')->all();

        //Monta array com ocorrencis existentes em relação a pergunta
        $ocorrencias = [];
        foreach ($perguntas as $p)
            if ($p->tipo == 'checkbox')
                $ocorrencias[$p->id] = $queryOcorrencias->find()
                    ->select(['id', 'nm_tp_ocorrencia'])
                    ->where(['pergunta_id' => $p->id])
                    ->orderBy(['CASE WHEN nm_tp_ocorrencia = "Outros" THEN 1 ELSE 0 END' => 'ASC', 'nm_tp_ocorrencia' => 'ASC'])
                    ->all();
        $perguntaIds = $perguntas->extract('id')->toArray();

        // $this->Flash->error('Não foi possível salvar as respostas.');

        if ($id) {
            $respostasSalvas = $Respostas->find()
                ->where(['relatorio_id' => $id, 'pergunta_id IN' => $perguntaIds])
                ->all()
                ->indexBy('pergunta_id')
                ->toArray();

            $ocorrenciaIdsSalvas = $OcorrenciaRelatorios->find()
                ->where(['relatorio_id' => $id])
                ->all()
                ->extract('ocorrencia_id')
                ->toArray();
        }

        // Contagem de pendências por dimensão (para exibir badge na coluna de seções)
        $pendenciasPorDimensaoCount = [];
        if ($id) {
            $pendenciaIdsPergunta = $Providencia->find()
                ->select(['pergunta_id'])
                ->where(['relatorio_id' => $id, 'status' => 0])
                ->all()
                ->extract('pergunta_id')
                ->toArray();

            if (!empty($pendenciaIdsPergunta)) {
                $perguntasPendentesTodasDim = $queryPerguntas->find()
                    ->select(['id', 'dimensao'])
                    ->where(['id IN' => $pendenciaIdsPergunta])
                    ->all();

                foreach ($perguntasPendentesTodasDim as $pp) {
                    $pendenciasPorDimensaoCount[$pp->dimensao] = ($pendenciasPorDimensaoCount[$pp->dimensao] ?? 0) + 1;
                }
            }
        }

        if (in_array($identity->tp_usuarios_id, [1, 5])) {
            $perguntas = $perguntas->filter(function ($p) {
                return $p->id != 34;
            });
        }

        // ----- Aplica o filtro de pendências por último, já com $respostasSalvas montado -----
        if ($somentePendencias) {
            $perguntaIdsPendentes = $Providencia->find()
                ->select(['pergunta_id'])
                ->where(['relatorio_id' => $id, 'status' => 0])
                ->all()
                ->extract('pergunta_id')
                ->toArray();

            $perguntas = $perguntas->filter(function ($p) use ($perguntaIdsPendentes) {

                return in_array($p->id, $perguntaIdsPendentes);
            });
        }

        $this->set([
            'escolaName' => $escolaName,
            'relatorio' => $relatorio,
            'dimensao' => $dimensao,
            'usuarios_lista' => $usuarios_lista,
            'perguntas' => $perguntas,
            'ocorrencias' => $ocorrencias,
            'escola_id' => $escola_id,
            'respostasSalvas' => $respostasSalvas ?? [],
            'ocorrenciaIdsSalvas' => $ocorrenciaIdsSalvas ?? [],
            'somentePendencias' => $somentePendencias,
            'dimensoesComPendencia' => $dimensoesComPendencia,
            'pendenciasPorDimensao' => $pendenciasPorDimensaoCount,
            'modoSomenteLeitura' => $modoSomenteLeitura,
            'campoAssinaturaUsuario' => $campoAssinaturaUsuario
        ]);
    }

    private function secoesTermo($dimensao, $queryPerguntas, $relatorioId = null, $tpUsuarioId = null)
    {
        // Qtd de ocorrências por dimensão
        $Dimensoes = $this->fetchTable('Dimensao');
        $Respostas = $this->fetchTable('Respostas');
        $OcorrenciaRelatorios = $this->fetchTable('OcorrenciaRelatorios');
        $Ocorrencias = $this->fetchTable('Ocorrencias');

        if ($relatorioId) {
            $relatorio = $this->Relatorios->get($relatorioId);
        }

        // Total de perguntas por dimensão
        $countQuery = $queryPerguntas->find();
        $totalPorDimensao = $countQuery
            ->select(['dimensao', 'total' => $countQuery->func()->count('*'), 'created'])
            ->where(
                !empty($relatorio->created)
                    ? ['created <' => $relatorio->created]
                    : []
            )
            ->groupBy('dimensao')
            ->orderByAsc('dimensao')
            ->all()
            ->indexBy('dimensao')
            ->toArray();

        // $dt = $query->select(['dimensao', 'total' => $query->func()->count('*')])->groupBy('dimensao')->orderByAsc('dimensao')->all()->indexBy('dimensao')->toArray();
        if ($relatorioId) {
            // $relatorio = $this->Relatorios->get($relatorioId);
            // Perguntas (id + dimensao + tipo), para cruzar com o que foi respondido
            $perguntas = $queryPerguntas->find()
                ->select(['id', 'dimensao', 'tipo', 'created'])
                ->where(['created <' => $relatorio->created])
                ->all();
        } else {
            $perguntas = $queryPerguntas->find()
                ->select(['id', 'dimensao', 'tipo'])
                ->all();
        }
        $perguntaIdsRespondidas = [];

        if ($relatorioId) {
            // Todas as respostas salvas para este relatório (radio e checkbox geram linha aqui)
            $respostasSalvas = $Respostas->find()
                ->select(['pergunta_id', 'resposta', 'observacao'])
                ->where(['relatorio_id' => $relatorioId])
                ->all()
                ->indexBy('pergunta_id')
                ->toArray();

            // Perguntas do tipo checkbox que têm ao menos 1 ocorrência marcada
            $ocorrenciaIdsMarcadas = $OcorrenciaRelatorios->find()
                ->select(['ocorrencia_id'])
                ->where(['relatorio_id' => $relatorioId])
                ->all()
                ->extract('ocorrencia_id')
                ->toArray();

            $perguntaIdsComOcorrenciaMarcada = [];
            if ($ocorrenciaIdsMarcadas) {
                $perguntaIdsComOcorrenciaMarcada = $Ocorrencias->find()
                    ->select(['pergunta_id'])
                    ->where(['id IN' => $ocorrenciaIdsMarcadas])
                    ->all()
                    ->extract('pergunta_id')
                    ->toArray();
            }

            foreach ($perguntas as $p) {
                $resposta = $respostasSalvas[$p->id] ?? null;
                $observacaoPreenchida = $resposta && trim((string)$resposta['observacao']) !== '';

                if ($p->tipo === 'checkbox') {
                    // Respondida se: observação preenchida OU alguma ocorrência marcada
                    $temOcorrenciaMarcada = in_array($p->id, $perguntaIdsComOcorrenciaMarcada);

                    if ($observacaoPreenchida || $temOcorrenciaMarcada) {
                        $perguntaIdsRespondidas[] = $p->id;
                    }
                } elseif ($p->tipo === 'texto') {
                    // Observação é a própria resposta
                    if ($observacaoPreenchida) {
                        $perguntaIdsRespondidas[] = $p->id;
                    }
                } else {
                    // radio: respondida se "resposta" está preenchido
                    if ($resposta && trim((string)$resposta['resposta']) !== '') {
                        $perguntaIdsRespondidas[] = $p->id;
                    }
                }
            }
        }

        // Contagem de respondidas por dimensão
        $respondidasPorDimensao = [];
        foreach ($perguntas as $p) {
            if (in_array($p->id, $perguntaIdsRespondidas)) {
                $respondidasPorDimensao[$p->dimensao] = ($respondidasPorDimensao[$p->dimensao] ?? 0) + 1;
            }
        }

        // Seções do Termo, vindas da tabela Dimensoes
        $dimensoesDb = $Dimensoes->find()
            ->select(['id', 'titCompleto', 'titParcial'])
            ->orderByAsc('id')
            ->all();

        $dimensoes = [];
        foreach ($dimensoesDb as $d) {

               // Tipos 1 e 5 não visualizam a dimensão 7
    if (in_array($tpUsuarioId, [1, 5]) && $d->id == 7) {
        continue;
    }
            $dimensoes[] = (object)[
                'dimensao' => $d->id,
                'titCompleto' => $d->titCompleto,
                'titParcial' => $d->titParcial,
                'pergTotal' => $totalPorDimensao[$d->id]['total'] ?? 0,
                'pergRespondidas' => $respondidasPorDimensao[$d->id] ?? 0,
            ];
        }

        $idsDimensoes = array_map(
            fn($d) => $d->dimensao,
            $dimensoes
        );
        
        if (!in_array($dimensao, $idsDimensoes)) {
            $dimensao = $idsDimensoes[0] ?? 1;
        }
        
        $indiceAtual = array_search($dimensao, $idsDimensoes, true);
        
        $desabilitaAnt = ($indiceAtual === 0) ? 'disabled' : '';
        $desabilitaProx = ($indiceAtual === count($idsDimensoes) - 1) ? 'disabled' : '';

        // Mantém Botões Etapa anterior e posterior em seus limites
        // if ($dimensao < 1) $dimensao = 1;
        // else if ($dimensao > count($dimensoes)) $dimensao = count($dimensoes);

        // if ($dimensao <= 1) {
        //     $desabilitaAnt = 'disabled';
        //     $dimensao = 1;
        // } else if ($dimensao >= count($dimensoes)) {
        //     $desabilitaProx = 'disabled';
        //     $dimensao = count($dimensoes);
        // }

        $this->set([
            'romanos' => [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII',
                13 => 'XIII',
                14 => 'XIV',
                15 => 'XV',
                16 => 'XVI',
                17 => 'XVII',
                18 => 'XVIII',
                19 => 'XIX',
                20 => 'XX'
            ],
            'dimensoes' => $dimensoes,
            'desabilitaAnt' => $desabilitaAnt ?? '',
            'desabilitaProx' => $desabilitaProx ?? '',
        ]);
        return $dimensao;
    }

    private function contarRespondidasPorDimensao($id, $queryPerguntas)
    {
        if (!$id) {
            return [];
        }

        $Respostas = $this->fetchTable('Respostas');
        $OcorrenciaRelatorios = $this->fetchTable('OcorrenciaRelatorios');
        $Ocorrencias = $this->fetchTable('Ocorrencias');

        $perguntas = $queryPerguntas->find()
            ->select(['id', 'dimensao', 'tipo'])
            ->all();

        $respostas = $Respostas->find()
            ->select(['pergunta_id', 'resposta', 'observacao']) // <- adicionado 'observacao'
            ->where(['relatorio_id' => $id])
            ->all()
            ->indexBy('pergunta_id')
            ->toArray();

        // Mapa ocorrencia_id -> pergunta_id, pra saber a quem pertence cada ocorrência marcada
        $ocorrenciaParaPergunta = $Ocorrencias->find()
            ->select(['id', 'pergunta_id'])
            ->all()
            ->indexBy('id')
            ->toArray();

        $ocorrenciaIdsSalvas = $OcorrenciaRelatorios->find()
            ->select(['ocorrencia_id'])
            ->where(['relatorio_id' => $id])
            ->all()
            ->extract('ocorrencia_id')
            ->toArray();

        // Perguntas (checkbox) que têm ao menos 1 ocorrência marcada
        $perguntasComOcorrenciaMarcada = [];
        foreach ($ocorrenciaIdsSalvas as $ocorrenciaId) {
            $perguntaId = $ocorrenciaParaPergunta[$ocorrenciaId]->pergunta_id ?? null;
            if ($perguntaId) {
                $perguntasComOcorrenciaMarcada[$perguntaId] = true;
            }
        }

        $contagem = [];
        foreach ($perguntas as $p) {
            $r = $respostas[$p->id] ?? null;
            $temObservacao = $r && $r->observacao !== null && trim($r->observacao) !== '';

            if ($p->tipo === 'radio') {
                // Respondida somente se selecionou uma opção
                $respondida = ($r && $r->resposta !== null && $r->resposta !== '');
            } elseif ($p->tipo === 'checkbox') {
                // Ao menos 1 ocorrência marcada OU observação preenchida
                $temOcorrenciaMarcada = isset($perguntasComOcorrenciaMarcada[$p->id]);
                $respondida = $temOcorrenciaMarcada || $temObservacao;
            } elseif ($p->tipo === 'texto') {
                // Observação é a própria resposta
                $respondida = $temObservacao;
            } else {
                // Tipo desconhecido/futuro: fallback seguro, considera resposta OU observação
                $respondida = ($r && $r->resposta !== null && $r->resposta !== '') || $temObservacao;
            }

            if ($respondida) {
                $contagem[$p->dimensao] = ($contagem[$p->dimensao] ?? 0) + 1;
            }
        }

        return $contagem; // ex: [1 => 4, 2 => 0, 3 => 2, ...]
    }

    public function concluirAssinatura($relatorio_id)
    {
        $identity = $this->Authentication->getIdentity();

        $relatorio = $this->Relatorios->get($relatorio_id);

        $this->Authorization->authorize($relatorio, 'concluirAssinatura');

        if ($identity->tp_usuarios_id == 2) {
            $relatorio->ic_rascunho = 0;
            $relatorio->id_ass_super = $identity->id;
        } elseif ($identity->tp_usuarios_id == 1) {
            $relatorio->id_ass_dir = $identity->id;
            $relatorio->data_ass_dir = date('Y-m-d');
        } elseif ($identity->tp_usuarios_id == 5) {
            $relatorio->id_ass_assis = $identity->id;
            $relatorio->data_ass_assis = date('Y-m-d');
        } elseif ($identity->tp_usuarios_id == 4 || $identity->tp_usuarios_id == 8) {
            $relatorio->id_ass_sub = $identity->id;
            $relatorio->data_ass_sub = date('Y-m-d');
        }

        if ($this->Relatorios->save($relatorio)) {
            $this->Flash->success('Termo concluído e assinado com sucesso.');
        } else {
            $this->Flash->error('Não foi possível concluir e assinar o termo.');
        }

        if ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) {
            return $this->redirect(['action' => 'dashEscolas', $relatorio->unid_escolar_id]);
        } elseif ($identity->tp_usuarios_id == 2) {
            return $this->redirect(['action' => 'dashSupervisor', $identity->id]);
        } elseif ($identity->tp_usuarios_id == 4 || $identity->tp_usuarios_id == 8) {
            return $this->redirect(['action' => 'dashSupervisorEscolas']);
        }
    }

    public function providencia($escola_id)
    {
        $this->Authorization->skipAuthorization();
        $usuario = $this->Authentication->getIdentity();

        $unid_escolares = TableRegistry::getTableLocator()->get('UnidEscolares');
        $escola = $unid_escolares->get($escola_id);

        if ($usuario->tp_usuarios_id == 2) {

            $providencias = $this->Relatorios->Respostas->find()
                ->where([
                    'Respostas.status' => 0,
                    'Relatorios.unid_escolar_id' => $escola_id,
                    'Relatorios.usuario_id' => $usuario->id
                ])
                ->orderBy(['Relatorios.data' => 'ASC', 'Respostas.pergunta_id' => 'ASC'])
                ->contain(['Relatorios', 'Perguntas'])
                ->all();
        } else {
            $providencias = $this->Relatorios->Respostas->find()
                ->where([
                    'Respostas.status' => 0,
                    'Relatorios.unid_escolar_id' => $escola_id,
                    // 'Relatorios.usuario_id' => $usuario->id
                ])
                ->orderBy(['Relatorios.data' => 'ASC', 'Respostas.pergunta_id' => 'ASC'])
                ->contain(['Relatorios', 'Perguntas'])
                ->all();
        }

        $pendencias = $providencias;

        $this->set(compact('providencias', 'escola', 'pendencias'));
    }

    public function salvarProvidencias()
    {
        // $this->authorize('update', $providencia);

        $this->Authorization->skipAuthorization();
        $identity = $this->Authentication->getIdentity();

        if ($this->request->is(['post', 'patch', 'put'])) {

            $ids = array_keys($this->request->getData('status', []));

            if (!empty($ids)) {

                $providencia = $this->Relatorios->Respostas->find()
                    ->select(['Respostas.id', 'Relatorios.unid_escolar_id'])
                    ->where([
                        'Respostas.id IN' => $ids,
                        // 'Relatorios.unid_escolar_id' => $escola_id,
                        // 'Relatorios.usuario_id' => $usuario->id
                    ])
                    ->contain(['Relatorios', 'Perguntas'])
                    ->first();

                $escolaId = $providencia->relatorio->unid_escolar_id;

                $this->Relatorios->Respostas
                    ->updateQuery()
                    ->set([
                        'status' => true,
                        'modified' => new DateTime() // Atualiza manualmente a data da alteração
                        // 'usuario_id' => $identity->id
                    ])
                    ->where([
                        'id IN' => $ids
                    ])
                    ->execute();

                $this->Flash->success('Providencias atualizadas com sucesso.');

                return $this->redirect(['action' => 'providencia', $escolaId]);
            } else {
                $this->Flash->error('Selecione pelo menos uma providência.');
                return $this->redirect($this->referer());
            }
        }
    }

    public function dashSupervisor($id)
    {
        // $id = 162;s
        $this->Authorization->skipAuthorization();
        $identity = $this->request->getAttribute('identity');
        $providencia = $this->fetchTable('Providencias');
        // $providencia = TableRegistry::getTableLocator()->get('Providencias');

        $unid_escolares = TableRegistry::getTableLocator()->get('UnidEscolares');

        $unidades = $this->fetchTable('UsuarioUnidEscolares')
            ->find()
            ->select([
                'UnidEscolares.id',
                'UnidEscolares.sigla',
                'UnidEscolares.nm_unid_escolar',
            ])
            ->where([
                'UsuarioUnidEscolares.usuario_id' => $id
            ])
            ->contain([
                'UnidEscolares' => function ($q) {
                    return $q->where(['UnidEscolares.ativo' => 1]);
                }
            ])
            ->all();

        $ano = date('Y');
        $escolasIds = $unidades
            ->map(fn($unidade) => $unidade->unid_escolare->id)
            ->toList();

        $itens = [];

        $pendenciasPorEscola = [];

        if (!empty($escolasIds)) {

            $pendenciasPorEscola = $providencia->find()
                ->select([
                    'unid_escolar_id' => 'Relatorios.unid_escolar_id',
                    'total' => $providencia->find()->func()->count('Providencias.id'),
                ])
                ->innerJoinWith('Relatorios')
                ->where([
                    'Providencias.status' => 0,
                    'Relatorios.unid_escolar_id IN' => $escolasIds,
                    'Relatorios.ic_rascunho' => 0
                ])
                ->groupBy('Relatorios.unid_escolar_id')
                ->enableAutoFields(false)
                ->all()
                ->indexBy('unid_escolar_id')
                ->toArray();
        }

        if (!empty($escolasIds)) {
            $itens = $this->Relatorios->find()
                ->select([
                    'Relatorios.id',
                    'Relatorios.termo_id',
                    'Relatorios.data',
                    'Relatorios.unid_escolar_id',
                    'Relatorios.ic_rascunho',
                    'Relatorios.id_ass_dir',
                    'Relatorios.id_ass_assis',
                    'Relatorios.id_ass_sub',
                ])
                ->where([
                    'Relatorios.unid_escolar_id IN' => $escolasIds,
                    'YEAR(Relatorios.data)' => $ano,
                    'Relatorios.usuario_id' => $identity->id
                ])
                ->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC'])
                ->all();
        }

        $titulos = [];

        foreach ($unidades as $unidade) {

            $escola = $unidade->unid_escolare;

            $titulo = new \stdClass();

            $titulo->texto =
                $escola->sigla . ' ' .
                $escola->nm_unid_escolar;

            $titulo->escola_id = $escola->id;

            $titulo->pendencias = $pendenciasPorEscola[$escola->id]->total ?? 0;

            $titulo->itens = [];

            $titulos[$escola->id] = $titulo;
        }

        $totalPendencias = array_sum(array_map(fn($p) => $p->total, $pendenciasPorEscola));

        foreach ($itens as $item) {

            $escolaId = $item->unid_escolar_id;

            // Se ainda não existe o título dessa escola
            if (!isset($titulos[$escolaId])) {
                continue;
            }

            $relatorio = new \stdClass();

            $relatorio->id = $item->id;
            $relatorio->termo_id = $item->termo_id;
            $relatorio->data = $item->data;
            $relatorio->pendencias = $item->pendencias;
            $relatorio->id_ass_dir = $item->id_ass_dir;
            $relatorio->id_ass_assis = $item->id_ass_assis;
            $relatorio->id_ass_sub = $item->id_ass_sub;

            // debug($relatorio);
            // die;

            $relatorio->subtitulo = "Termo ";

            if (
                $item->ic_rascunho == 1
            ) {
                $relatorio->situacao = 3;
            } elseif (
                !empty($item->id_ass_dir) &&
                !empty($item->id_ass_assis) &&
                !empty($item->id_ass_sub)
            ) {
                $relatorio->situacao = 2;
            } elseif (
                empty($item->id_ass_dir) ||
                empty($item->id_ass_assis) ||
                empty($item->id_ass_sub)
            ) {
                $relatorio->situacao = 1;
            }

            // pr($relatorio->subtitulo);

            $titulos[$escolaId]->itens[] = $relatorio;
        }

        // Reindexa o array para começar em 0
        $titulos = array_values($titulos);


        $this->set(compact('titulos', 'totalPendencias'));

        // pr($relatorios->toArray());
        // die;
    }

    public function dashSupervisorOld($setor_id)
    {
        $this->Authorization->skipAuthorization();
        // $identity = $this->Authentication->getIdentity();
        $identity = $this->request->getAttribute('identity');
        $unid_escolares = TableRegistry::getTableLocator()->get('UnidEscolares');
        $unid_escolares_vw = TableRegistry::getTableLocator()->get('UnidEscolaresVw');

        $query = $unid_escolares->find()->where(['setores_id' => $setor_id])->toArray();

        $escolas = $unid_escolares->find('list', keyField: 'id', valueField: 'nm_unid_escolar')->where(['setores_id' => $setor_id, 'ativo is' => null])
            ->orderBy(['nm_unid_escolar' => 'ASC'])->toArray();

        // debug($escolas);
        // die;

        $ano = date('Y');

        $escolasIds = array_keys($escolas);

        if ($this->request->is('post')) {

            $escola = $this->request->getData('escola_id');
            $view = $this->request->getData('view');

            if ($view == 1) {
                return $this->redirect(['action' => 'add', $escola]);
            } elseif ($view == 2) {
                return $this->redirect(['action' => 'dash_escolas', $escola]);
            }
        }

        // $relatorios = $this->Relatorios->find()->where(['usuario_id' => $identity->id, 'YEAR(data)' => $ano])->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);
        $relatorios = $this->Relatorios->find()->where(['unid_escolar_id IN' => $escolasIds, 'YEAR(data)' => $ano, 'usuario_id' => $identity->id])->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);

        // debug($relatorios->toArray());
        // die;

        $this->set(compact('escolas', 'relatorios', 'ano', 'identity'));
    }

    public function dashSubsecretaria()
    {
        $this->Authorization->skipAuthorization();

        $unid_escolares_vw = TableRegistry::getTableLocator()->get('UnidEscolaresVw');

        $usuarios = $this->fetchTable('Usuarios');
        $supervisoras = $usuarios->find()->where(['tp_usuarios_id' => 2])->contain(['UsuarioUnidEscolares' => ['Setores' => ['UnidEscolares']]])->toArray();
        // $escolas = $unid_escolares_vw->find('list', keyField: 'id', valueField: 'nm_unid_escolar')->where(['setores_id' => $setor_id])->toArray();

        // debug($supervisoras);
        // die;

        $this->set(compact('supervisoras'));
    }

    public function dashSubEscolas($escola_id)
    {

        $ano = date('Y');
        $unid_escolares_vw = $this->fetchTable('UnidEscolaresVw');

        $escola = $unid_escolares_vw->find()->where(['id' => $escola_id])->first();
        if ($this->identity->tp_usuarios_id == 4) {
            $this->Authorization->skipAuthorization();

            $relatorios = $this->Relatorios->find()->where([
                'unid_escolar_id' => $escola_id,
                'YEAR(data)' => $ano,
                'ic_rascunho' => 0,

            ])->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);

            $this->set(compact('relatorios'));
        }

        $this->set(compact('escola'));
    }
    public function dashDiretorEscolas($usuario)
    {
        $identity = $this->Authentication->getIdentity();
        $ano = date('Y');

        $unid_escolares_vw = $this->fetchTable('UnidEscolaresVw');
        $usuario_unid_escolares = $this->fetchTable('UsuarioUnidEscolares');
        $unid_escolares = $this->fetchTable('UnidEscolares');

        $connection = TableRegistry::getTableLocator()->get('Users', [
            'connectionName' => 'bdescola'
        ]);

        $funcionariosTable = TableRegistry::getTableLocator()->get('Funcionarios', [
            'connectionName' => 'dashescola'
        ]);

        $dashboardsTable = TableRegistry::getTableLocator()->get('Dashboards', [
            'connectionName' => 'dashescola'
        ]);

        $escolasTable = TableRegistry::getTableLocator()->get('DashEscolas');

        $funcionario = $funcionariosTable->find()
            ->select(['id_funcionario'])
            ->where(['rf' => $identity->cd_rf])
            ->first();

        $providencias = $this->fetchTable('Providencias');

        if (!$funcionario) {
            // return [];
            $this->Authorization->skipAuthorization();

            $this->Flash->error('Usuário não localizado!');
            return $this->redirect($this->referer());
        } else {
            $dashboard = $dashboardsTable->find()->where(['funcionario_id' => $funcionario->id_funcionario])->all();
            $escolaIds = [];
            foreach ($dashboard as $dash) {
                $escolaIds[] = $dash->escola_id;
            }

            $escolas = $escolasTable->find()
                ->select([
                    'id_escola',
                    'nome'
                ])
                ->where(['id_escola IN' => $escolaIds])->all();

            $nomeEscolas = $escolas->extract('nome')->toList();

            $UnidEscolares = $this->fetchTable('UnidEscolares');

            $query = $UnidEscolares->find();

            $query->where(function ($exp) use ($nomeEscolas) {

                $orConditions = [];

                foreach ($nomeEscolas as $nome) {

                    $nomeTratado = preg_replace('/^E\.M\.\s*/', '', $nome);

                    $orConditions[] = [
                        "MATCH (nm_unid_escolar) AGAINST ('$nomeTratado') "
                    ];
                }

                return $exp->or($orConditions);
            });

            $query->limit(1); // Correção por Anderson
            $escolas_diretor = $query->all();

            $idsEscolas = [];

            foreach ($escolas_diretor as $escola) {
                $idsEscolas[] = $escola->id;
            }

            if ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) {

                $escolas = $usuario_unid_escolares->find('list', keyField: 'unid_escolare.id', valueField: 'unid_escolare.nm_unid_escolar')
                    ->where(['usuario_id' => $usuario])
                    ->contain(['UnidEscolares'])->orderBy(['prioridade = 1' => 'DESC'])
                    ->toArray();
            } elseif ($identity->tp_usuarios_id == 4) {
                $escolas = $usuario_unid_escolares->find('list', keyField: 'unid_escolare.id', valueField: 'unid_escolare.nm_unid_escolar')
                    ->where(['unid_escolares_id' => $usuario])
                    ->contain(['UnidEscolares'])->orderBy(['prioridade = 1' => 'DESC'])
                    ->toArray();
            }
        }

        if (count($escolas) == 1) {
            if (count($escolas) === 1) {

                $id = array_keys($escolas);
                // $escola = $unid_escolares->get($escola->id);
                if (isset($escola)) {
                    $escola = $unid_escolares->get($escola->id);
                } else {
                    $escola = $unid_escolares->get($escolaIds);
                }
            }

            $this->set('escola', $escola);

            $id = array_keys($escolas);

            if ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) {

                $relatorios = $this->Relatorios->find()->where(['unid_escolar_id IN' => $idsEscolas, 'YEAR(data)' => $ano])
                    ->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);
            } elseif ($identity->tp_usuarios_id == 4) {
                $relatorios = $this->Relatorios->find()->where(['unid_escolar_id IN' => $idsEscolas, 'YEAR(data)' => $ano])
                    ->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);
            }
        } else {
            $relatorios = $this->Relatorios->find()->where(['unid_escolar_id IN' => $idsEscolas, 'YEAR(data)' => $ano])
                ->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);
        }

        $this->set(compact('relatorios'));

        if ($this->request->is('post')) {

            $escola = $this->request->getData('escola_id');

            $relatorios = $this->Relatorios->find()
                ->where(['unid_escolar_id IN' => $escola, 'YEAR(data)' => $ano, 'ic_rascunho' => 0])
                ->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);

            $this->set(compact('relatorios'));
        }

        $this->Authorization->skipAuthorization();

        if ($identity->tp_usuarios_id == 2) {
            $pendencias = $this->Relatorios->Respostas->find()
                ->where([
                    'Respostas.status' => 0,
                    'Relatorios.unid_escolar_id IN' => $idsEscolas,
                    'Relatorios.usuario_id' => $identity->id
                ])
                ->contain(['Relatorios', 'Perguntas'])
                ->all();
        } else {
            $pendencias = $this->Relatorios->Respostas->find()
                ->where([
                    'Respostas.status' => 0,
                    'Relatorios.unid_escolar_id IN' => $idsEscolas,
                    // 'Relatorios.usuario_id' => $identity->id
                ])
                ->contain(['Relatorios', 'Perguntas'])
                ->all();
        }

        $this->set(compact(
            'escolas',
            'ano',
            'pendencias'
            /** 'cont' ,'rel_final' */
        ));
    }

    public function dashRelatoriosAnteriores($ano, $escola_id)
    {
        $this->Authorization->skipAuthorization();
        $relatorio_anteriores = $this->fetchTable('RelatoriosAnt');
        $unid_escolares = $this->fetchTable('UnidEscolares');

        $relatorio_identificados = $relatorio_anteriores->find()->where(['dt_relatorio like' => '%' . $ano . '%', 'cd_unidade' => $escola_id])->orderByDesc('cd_termo');
        $escola = $unid_escolares->get($escola_id);

        $this->set(compact('relatorio_identificados', 'escola'));
    }

    public function dashEscolas($escola_id)
    {
        $this->Authorization->skipAuthorization();

        // debug($this->request->getData());
        // die;
        $escola_id = $escola_id + 0;
        $user = $this->getRequest()->getAttribute('identity');

        $unid_escolares = $this->fetchTable('UnidEscolares');
        $providencias = $this->fetchTable('Providencias');

        $escola = $unid_escolares->get($escola_id);

        $ano = date('Y');

        // Verifica se foi solicitado mostrar somente os relatórios
        // que estão sem assinatura
        $somentePendentes = $this->request->getQuery('pendentes') == 1;

        if ($user->tp_usuarios_id == 2) {

            // $relatorios = $this->Relatorios->find()
            //     ->where([
            //         'Relatorios.unid_escolar_id' => $escola_id,
            //         'YEAR(Relatorios.data)' => $ano
            //     ])
            //     ->contain(['Usuarios'])
            //     ->orderBy([
            //         'Relatorios.id' => 'DESC'
            //     ])
            //     ->all();
            $query = $this->Relatorios->find()
                ->where([
                    'Relatorios.unid_escolar_id' => $escola_id,
                    'YEAR(Relatorios.data)' => $ano
                ])
                ->contain(['Usuarios'])
                ->orderBy([
                    'Relatorios.id' => 'DESC'
                ]);

            // Se clicou em "Sem assinatura", filtra somente
            // os relatórios que ainda possuem assinatura faltando
            if ($somentePendentes) {
                $query->where([
                    'Relatorios.ic_rascunho !=' => 1,
                    'OR' => [
                        'Relatorios.id_ass_super IS' => null,
                        'Relatorios.id_ass_dir IS' => null,
                        'Relatorios.id_ass_sub IS' => null,
                        'Relatorios.id_ass_assis IS' => null,
                    ]
                ]);
            }

            $relatorios = $query->all();
        } elseif ($user->tp_usuarios_id == 1 || $user->tp_usuarios_id == 5) {
            $query = $this->Relatorios->find()
                ->where([
                    'Relatorios.unid_escolar_id' => $escola_id,
                    'YEAR(Relatorios.data)' => $ano,
                    'Relatorios.ic_rascunho' => 0
                ])
                ->contain(['Usuarios'])
                ->orderBy([
                    'Relatorios.id' => 'DESC'
                ]);
            // if ($somentePendentes) {
            //     $query->where([
            //         'Relatorios.ic_rascunho !=' => 1,
            //         'OR' => [
            //             'Relatorios.id_ass_dir IS' => null,
            //             'Relatorios.id_ass_assis IS' => null,
            //         ]
            //     ]);
            // }

            $relatorios = $query->all();
        }
        if ($user->tp_usuarios_id == 2) {

            $providencias = $this->Relatorios->Respostas->find()
                ->where([
                    'Respostas.status' => 0,
                    'Relatorios.unid_escolar_id' => $escola_id,
                    'Relatorios.usuario_id' => $user->id
                ])
                ->contain(['Relatorios', 'Perguntas'])
                ->all();
        } else {
            $providencias = $this->Relatorios->Respostas->find()
                ->where([
                    'Respostas.status' => 0,
                    'Relatorios.unid_escolar_id' => $escola_id,
                    'Relatorios.ic_rascunho' => 0 // Não é rascunho

                ])
                ->contain(['Relatorios', 'Perguntas'])
                ->all();
        }

        $relatoriosPendentes = $this->fetchTable('Relatorios')
            ->find()
            ->where([
                'Relatorios.unid_escolar_id' => $escola->id,
                'Relatorios.ic_rascunho !=' => 1,
                'OR' => [
                    'Relatorios.id_ass_super IS' => null,
                    'Relatorios.id_ass_dir IS' => null,
                    'Relatorios.id_ass_sub IS' => null,
                    'Relatorios.id_ass_assis IS' => null,
                ]
            ])
            ->count();

        $this->set('relatoriosPendentes', $relatoriosPendentes);
        $this->set('pendencias', $providencias);
        $this->set(compact('escola', 'ano', 'relatorios', 'somentePendentes'));
    }

    public function dashDiretor($id = null)
    {
        $identity = $this->Authentication->getIdentity();

        $this->Authorization->skipAuthorization();

        $connection = TableRegistry::getTableLocator()->get('Users', [
            'connectionName' => 'bdescola'
        ]);


        $funcionariosTable = TableRegistry::getTableLocator()->get('Funcionarios', [
            'connectionName' => 'dashescola'
        ]);

        $dashboardsTable = TableRegistry::getTableLocator()->get('Dashboards', [
            'connectionName' => 'dashescola'
        ]);

        $escolasTable = TableRegistry::getTableLocator()->get('DashEscolas');

        $funcionario = $funcionariosTable->find()
            ->select(['id_funcionario'])
            ->where(['rf' => $identity->cd_rf])
            ->first();

        if (!$funcionario) {
            return [];
        }

        $escolaIds = $dashboardsTable->find()
            ->select(['escola_id'])
            ->where([
                'funcionario_id' => $funcionario->id_funcionario
            ])
            ->all()->extract('escola_id')->toList();
        // ->toArray();

        if (empty($escolaIds)) {
            return [];
        }

        $escolas = $escolasTable->find()->select([
            'id_escola',
            'nome'
        ])
            ->where([
                'id_escola IN' => $escolaIds
            ])
            ->all();

        $nomeEscolas = $escolas->extract('nome')->toList();

        $UnidEscolares = $this->fetchTable('UnidEscolares');

        $query = $UnidEscolares->find();

        $query->where(function ($exp) use ($nomeEscolas) {

            $orConditions = [];

            foreach ($nomeEscolas as $nome) {

                $nomeTratado = preg_replace('/^E\.M\.\s*/', '', $nome);

                $orConditions[] = [
                    "MATCH (nm_unid_escolar) AGAINST ('$nomeTratado') "
                ];
            }

            return $exp->or($orConditions);
        });

        $escolas_diretor = $query->all();

        $idsEscolas = [];

        foreach ($escolas_diretor as $escola) {
            $idsEscolas[] = $escola->id;
        }

        $ano = date('Y');

        $relatorios = $this->Relatorios->find()->where(['unid_escolar_id IN' => $idsEscolas, 'YEAR(data)' => $ano])
            ->contain(['UnidEscolares'])->orderBy(['Relatorios.id' => 'DESC']);

        $usuario = $this->fetchTable('Usuarios')
            ->get($id, contain: 'Escolas.UnidEscolares');

        $this->set(compact('usuario', 'ano', 'relatorios'));
    }

    public function edit($id)
    {
        $relatorio = $this->Relatorios->get($id, contain: ['UnidEscolares', 'OcorrenciaRelatorios']);

        $unid_escolares = TableRegistry::getTableLocator()->get('UnidEscolares');

        $query = $unid_escolares->find();
        $query->select(['id', 'id_escola', 'sigla', 'nm_unid_escolar', 'nome_completo' => $query->func()->concat(['sigla' => 'identifier', ' ', 'nm_unid_escolar' => 'identifier'])]);
        $escola = $query->where(['id' => $relatorio->unid_escolar_id])->first();

        $this->Authorization->authorize($relatorio, 'edit');
        $usuario = $this->Authentication->getIdentity();

        $funcoes = TableRegistry::getTableLocator()->get('Funcoes');
        $ocorrencia = $this->fetchTable('Ocorrencias');

        $list_funcao = $funcoes->find('list', keyField: 'id', valueField: 'nm_funcao')->toArray();

        $usuarios = $this->fetchTable('Usuarios');
        $usuario_unid_escolares = $this->fetchTable('UsuarioUnidEscolares');

        $ocorrenciasRelacionadasIds = [];

        if (!empty($relatorio->ocorrencia_relatorios)) {
            $ocorrenciasRelacionadasIds = collection($relatorio->ocorrencia_relatorios)
                ->extract('ocorrencia_id')
                ->toArray();
        }

        if ($this->request->is(['post', 'put'])) {

            $relatorio = $this->Relatorios->patchEntity($relatorio, $this->request->getData(), [
                'associated' => ['OcorrenciaRelatorios']
            ]);

            $ocorrenciasManutIds = $this->request->getData('list_manutencao');
            $ocorrenciasLegIds = $this->request->getData('list_legislacao');

            $this->Relatorios->OcorrenciaRelatorios->deleteAll(['relatorio_id' => $id]);

            if (!empty($ocorrenciasManutIds)) {

                foreach ($ocorrenciasManutIds as $i => $ocorrenciasManutId) {

                    if ($ocorrenciasManutId != 0) {
                        $entity = $this->Relatorios->OcorrenciaRelatorios->newEmptyEntity();

                        $entity->relatorio_id = $id;
                        $entity->ocorrencia_id = $ocorrenciasManutId;

                        $this->Relatorios->OcorrenciaRelatorios->save($entity);
                    }
                }
            }

            if (!empty($ocorrenciasLegIds)) {

                foreach ($ocorrenciasLegIds as $i => $ocorrenciasLegId) {
                    if ($ocorrenciasLegId != 0) {
                        $entity = $this->Relatorios->OcorrenciaRelatorios->newEmptyEntity();
                        $entity->relatorio_id = $id;
                        $entity->ocorrencia_id = $ocorrenciasLegId;

                        $this->Relatorios->OcorrenciaRelatorios->save($entity);
                    }
                }
            }

            foreach ($relatorio->ocorrencia_relatorios as $i => $ocorrencia_relatorio) :

                if ($ocorrencia_relatorio->ocorrencia_id === null) {
                    unset($relatorio->ocorrencia_relatorios[$i]);
                }

            endforeach;

            $rascunho = $this->request->getData('ic_rascunho');

            if ($rascunho == '0') {
                $relatorio->id_ass_super = $relatorio->usuario_id;
            }

            if ($this->Relatorios->save($relatorio)) {

                $this->Flash->success('Relatório salvo');

                $setor_usuario = $usuario_unid_escolares->find()->where(['usuario_id' => $usuario->id])->first();

                return $this->redirect(['action' => 'dashEscolas', $relatorio->unid_escolar_id]);
            }
        }

        $list_manutencao = $ocorrencia->find()->where(['categorias_id' => 3])->toArray();
        $list_legislacao = $ocorrencia->find()->where(['categorias_id' => 4])->toArray();

        $query = $usuarios->find('list', keyField: 'id', valueField: 'nm_usuario')->contain('UsuarioUnidEscolares')->where(['UsuarioUnidEscolares.unid_escolares_id' => $relatorio->unid_escolar_id]);


        $escola_user = $this->fetchTable('Users');

        $busca_escola_users = $escola_user->find()->where(['escola_id' => $escola->id_escola])->toArray();

        if (count($busca_escola_users) > 0) {

            foreach ($busca_escola_users as $escola_user):
                $escola_usuarios[] = str_replace(array(".", ""), "", $escola_user->rf);
            endforeach;

            if (count($escola_usuarios) > 0) {
                $escola_users = $usuarios->find('list', keyField: 'id', valueField: 'nm_usuario')->where(['cd_rf IN' => $escola_usuarios])->toArray();

                if (count($escola_users) == 0) {
                    /** Aqui Salva os usuários encontrados no Banco Escola na Tabela Usuarios do bd_termo 
                     * 
                     * E Carrega a variável $escola_users com esse cadastro recém efetuado.
                     * 
                     */
                    $this->Flash->error("Não tem Equipe de Direção cadastrada no Sistema Termos. Aguardando para ver como será feito.");
                }

                $this->set(compact('escola_users'));
            }
        } else {
            $this->Flash->error("Não tem Equipe de Direção cadastrada no Sistema Escolas. ");
        }


        $this->set(compact('relatorio', 'list_funcao', 'escola_users', 'escola', 'list_manutencao', 'list_legislacao', 'ocorrenciasRelacionadasIds'));
    }

    public function visualizarPdf($id)
    {
        $this->Authorization->skipAuthorization();

        $Dimensoes = $this->fetchTable('Dimensao');
        $queryPerguntas = $this->fetchTable('Perguntas');
        $Respostas = $this->fetchTable('Respostas');
        $Ocorrencias = $this->fetchTable('Ocorrencias');
        $OcorrenciaRelatorios = $this->fetchTable('OcorrenciaRelatorios');
        $UnidEscolares = $this->fetchTable('UnidEscolares');
        $Usuarios = $this->fetchTable('Usuarios');

        $relatorio = $this->Relatorios->get($id, contain: ['Usuarios']);

        $escolaName = $UnidEscolares->find()
            ->select(['id', 'sigla', 'nm_unid_escolar'])
            ->where(['id' => $relatorio->unid_escolar_id])
            ->first();

        // Todas as perguntas criadas até a data do relatório, ordenadas por dimensão e ordem
        $perguntas = $queryPerguntas->find()
            ->select(['id', 'dimensao', 'ordem', 'descricao', 'tipo', 'opcoes', 'created'])
            ->where(['created <' => $relatorio->created])
            ->orderByAsc('dimensao')
            ->orderByAsc('ordem')
            ->all();

        $perguntaIds = $perguntas->extract('id')->toArray();

        // Respostas salvas, indexadas por pergunta_id
        $respostasSalvas = $Respostas->find()
            ->where(['relatorio_id' => $id, 'pergunta_id IN' => $perguntaIds])
            ->all()
            ->indexBy('pergunta_id')
            ->toArray();

        // Ocorrências marcadas (para perguntas do tipo checkbox)
        $ocorrenciaIdsSalvas = $OcorrenciaRelatorios->find()
            ->where(['relatorio_id' => $id])
            ->all()
            ->extract('ocorrencia_id')
            ->toArray();

        // Monta ocorrências disponíveis por pergunta (checkbox), com flag "marcado"
        $ocorrenciasPorPergunta = [];
        foreach ($perguntas as $p) {
            if ($p->tipo === 'checkbox') {
                $ocorrenciasPorPergunta[$p->id] = $Ocorrencias->find()
                    ->select(['id', 'nm_tp_ocorrencia'])
                    ->where(['pergunta_id' => $p->id])
                    ->orderBy(['CASE WHEN nm_tp_ocorrencia = "Outros" THEN 1 ELSE 0 END' => 'ASC', 'nm_tp_ocorrencia' => 'ASC'])
                    ->all();
            }
        }

        // Nomes das dimensões
        $dimensoesDb = $Dimensoes->find()
            ->select(['id', 'titCompleto'])
            ->orderByAsc('id')
            ->all()
            ->indexBy('id')
            ->toArray();

        // Agrupa as perguntas por dimensão, já na ordem correta (dimensao asc, ordem asc)
        $perguntasPorDimensao = [];
        foreach ($perguntas as $p) {
            $perguntasPorDimensao[$p->dimensao][] = $p;
        }
        ksort($perguntasPorDimensao); // garante que as dimensões fiquem em ordem crescente

        // Nome do usuário responsável (pergunta 35)
        $responsavelNome = null;
        if ($relatorio->responsavel_id) {
            $responsavel = $Usuarios->find()
                ->select(['nm_usuario'])
                ->where(['id' => $relatorio->responsavel_id])
                ->first();
            $responsavelNome = $responsavel->nm_usuario ?? null;
        }

        $this->set([
            'relatorio' => $relatorio,
            'escolaName' => $escolaName,
            'dimensoesDb' => $dimensoesDb,
            'perguntasPorDimensao' => $perguntasPorDimensao,
            'respostasSalvas' => $respostasSalvas,
            'ocorrenciasPorPergunta' => $ocorrenciasPorPergunta,
            'ocorrenciaIdsSalvas' => $ocorrenciaIdsSalvas,
            'responsavelNome' => $responsavelNome,
        ]);

        // Layout mais enxuto, sem menu/navegação, próprio para impressão
        $this->viewBuilder()->setLayout('print');
    }



    public function sign($id)
    {
        $this->viewBuilder()->setLayout('print');

        $identity = $this->Authentication->getIdentity();

        $relatorio = $this->Relatorios->get($id, contain: [
            'UnidEscolares',
            'Respostas',
            // 'Funcoes',
            'OcorrenciaRelatorios' => ['Ocorrencias'],
            'Usuarios' => ['UsuarioUnidEscolares']
        ]);

        $respostas = $this->fetchTable('Respostas')->find()
            ->where(['relatorio_id' => $id])
            ->all();

        /** Aqui colocar vários if verificando se no relatório tem o
         *  ID do Supervisor,
         *  do Diretor e
         *  do SubSecretario  */


        if ($relatorio->id_ass_super) {
            $supervisor = $this->Relatorios->Usuarios->find()->where(['id' => $relatorio->id_ass_super])->first();
            $this->set(compact('supervisor'));
        }

        if ($relatorio->id_ass_dir) {
            $diretor = $this->Relatorios->Usuarios->find()->where(['id' => $relatorio->id_ass_dir])->first();
            $this->set(compact('diretor'));
        }

        if ($relatorio->id_ass_assis) {
            $assistente = $this->Relatorios->Usuarios->find()->where(['id' => $relatorio->id_ass_assis])->first();
            $this->set(compact('assistente'));
        }

        if ($relatorio->id_ass_sub) {
            $subsecretario = $this->Relatorios->Usuarios->find()->where(['id' => $relatorio->id_ass_sub])->first();
            $this->set(compact('subsecretario'));
        }

        try {
            $this->Authorization->authorize($relatorio, 'visualizarPdf');
        } catch (ForbiddenException $e) {
            $this->Flash->error('Você não possui permissão para acessar este documento.');

            return $this->redirect(['action' => 'dash_diretor_escolas', $identity->id]);
        }

        $ocorrenciasRelacionadasIds = [];

        if (!empty($relatorio->ocorrencia_relatorios)) {
            $ocorrenciasRelacionadasIds = collection($relatorio->ocorrencia_relatorios)
                ->extract('ocorrencia_id')
                ->toArray();
        }

        $funcoes = $this->fetchTable('Funcoes');
        $perguntas = $this->fetchTable('Perguntas')->find()->orderBy(['ordem' => 'ASC'])->all();
        // ->find()->orderBy(['codigo' => 'ASC'])
        // ->all();

        $respostasMap = [];
        foreach ($relatorio->respostas as $resposta) {
            $respostasMap[$resposta->pergunta_id] = $resposta;
        }

        $list_funcao = $funcoes->find('list', keyField: 'id', valueField: 'nm_funcao')->toArray();
        $list_manutencao = $this->Relatorios->OcorrenciaRelatorios->Ocorrencias->find()->where(['pergunta_id' => 16])->toArray();
        $list_legislacao = $this->Relatorios->OcorrenciaRelatorios->Ocorrencias->find()->where(['pergunta_id' => 18])->toArray();

        $this->set(compact('relatorio', 'perguntas', 'respostasMap', 'list_funcao', 'list_manutencao', 'list_legislacao', 'ocorrenciasRelacionadasIds'));
    }

    public function cancelar()
    {
        $this->request->allowMethod(['post']);

        $id = $this->request->getData('id_relatorio');

        $sucesso = false;
        $mensagem = 'Relatório não encontrado.';

        if ($id) {
            $relatorio = $this->Relatorios->get($id);

            $this->Authorization->authorize($relatorio, 'cancelar');

            $relatorio->ic_cancelado = 1;

            if ($this->Relatorios->save($relatorio)) {
                $sucesso = true;
                $mensagem = 'O relatório foi tornado sem efeito!';
            } else {
                $mensagem = 'Não foi possível atualizar o status no banco de dados.';
            }

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'sucesso' => $sucesso,
                    'mensagem' => $mensagem
                ]));
        }
    }

    public function undersign($relatorio, $usuario)
    {
        $relatorio = $this->Relatorios->get($relatorio);

        $this->Authorization->authorize($relatorio, 'undersign');
        $identity = $this->Authentication->getIdentity();

        if ($this->request->is(['get', 'post', 'put'])) {

            $usuarios = $this->fetchTable('Usuarios');

            $resultado = $usuarios->find('all')->where(['id' => $usuario])->all();

            $arquivo = $resultado->last();

            if (!file_exists(WWW_ROOT . 'files' . DS . $arquivo->cd_assinatura)) {
                $this->Flash->error("Não Tem Arquivo de Assinatura em Nossos Arquivos. Deve solicitar atualização do seu cadastro. ");
                return $this->redirect($this->referer());
            } else {

                if ($identity->tp_usuarios_id == 4) {
                    $relatorio->id_ass_sub = $usuario;
                } elseif ($identity->tp_usuarios_id == 5) {
                    $relatorio->id_ass_assis = $usuario;
                } else {
                    $relatorio->id_ass_dir = $usuario;
                }

                if ($this->Relatorios->save($relatorio)) {

                    if ($identity->tp_usuarios_id == 4) {
                        return $this->redirect(['action' => 'dash_sub_escolas', $relatorio->unid_escolar_id]);
                    }
                    return $this->redirect(['action' => 'dash_diretor_escolas', $usuario]);
                }
                $this->Flash->error("Não foi possivel Assinar.");
            }
        }

        $this->disableAutoRender();

        $this->set(compact('relatorio'));
    }

    public function relatorioPdf($id)
    {
        $this->Authorization->skipAuthorization();

        $this->viewBuilder()->setLayout('print');

        $relatorio_anteriores = $this->fetchTable('RelatoriosAnt');
        $unid_escolares = $this->fetchTable('UnidEscolares');

        $relatorio = $relatorio_anteriores->find()->where(['RelatoriosAnt.id' => $id])->contain('UnidEscolares')->first();

        $usuarios = $this->fetchTable('Usuarios');

        if ($relatorio->cd_assinatura_supervisor != '') {

            $supervisor = explode(".", $relatorio->cd_assinatura_supervisor);

            $nm_supervisor = $usuarios->find()->select(['nm_usuario'])->where(['cd_rf' => $supervisor[0]])->first();

            $this->set(compact('nm_supervisor'));
        }
        if ($relatorio->cd_assinatura_diretor != '') {

            $diretor = explode(".", $relatorio->cd_assinatura_diretor);
            $nm_diretor = $usuarios->find()->select(['nm_usuario'])->where(['cd_rf' => $diretor[0]])->first();

            $this->set(compact('nm_diretor'));
        }

        if ($relatorio->cd_assinatura_admin != '') {

            $admin = explode(".", $relatorio->cd_assinatura_admin);
            $nm_admin = $usuarios->find()->select(['nm_usuario'])->where(['cd_rf' => $admin[0]])->first();

            $this->set(compact('nm_admin'));
        }

        $this->set(compact('relatorio'));
    }

    public function pendencias($escola_id)
    {
        $this->Authorization->skipAuthorization();

        $Providencias = $this->fetchTable('Providencias');
        $Perguntas = $this->fetchTable('Perguntas');
        $Relatorios = $this->fetchTable('Relatorios');
        $UnidEscolares = $this->fetchTable('UnidEscolares');

        $escolaName = $UnidEscolares->find()
            ->select(['id', 'sigla', 'nm_unid_escolar'])
            ->where(['id' => $escola_id])
            ->first();

        $pendenciasRaw = $Providencias->find()
            ->where([
                'Providencias.unid_escolar_id' => $escola_id,
                'Providencias.status' => 0
            ])
            ->matching('Relatorios', function ($q) {
                return $q->where([
                    'Relatorios.ic_rascunho' => 0
                ]);
            })
            ->contain(['Relatorios'])
            ->orderByDesc('Providencias.created')
            ->all();

        if ($pendenciasRaw->isEmpty()) {
            $this->Flash->success('Não há pendências em aberto para esta escola.');
            return $this->redirect($this->referer());
        }

        $relatorioIds = array_unique($pendenciasRaw->extract('relatorio_id')->toArray());

        // if (count($relatorioIds) === 1) {
        // $relatorioId = $relatorioIds[0];

        // $perguntaIdsPendentes = $pendenciasRaw
        //     ->filter(fn($item) => $item->relatorio_id == $relatorioId)
        //     ->extract('pergunta_id')
        //     ->toArray();

        // $primeiraDimensao = $Perguntas->find()
        //     ->select(['dimensao'])
        //     ->where(['id IN' => $perguntaIdsPendentes])
        //     ->orderByAsc('dimensao')
        //     ->first();

        //     return $this->redirect([
        //         'action' => 'manterPerguntas',
        //         $primeiraDimensao->dimensao ?? 1,
        //         $escola_id,
        //         $relatorioId,
        //         '?' => ['pendencias' => 1],
        //     ]);
        // }

        $perguntaIds = $pendenciasRaw->extract('pergunta_id')->toArray();

        $perguntas = $Perguntas->find()
            ->where(['id IN' => $perguntaIds])
            ->all()
            ->indexBy('id')
            ->toArray();

        $relatorios = $Relatorios->find()
            ->where(['id IN' => $relatorioIds, 'ic_rascunho' => 0])
            ->all()
            ->indexBy('id')
            ->toArray();

        $pendencias = [];
        foreach ($pendenciasRaw as $item) {
            $pergunta = $perguntas[$item->pergunta_id] ?? null;
            $relatorio = $relatorios[$item->relatorio_id] ?? null;

            $pendencias[] = [
                'id' => $item->id,
                'descricao' => $item->descricao,
                'created' => $item->created,
                'relatorio_id' => $item->relatorio_id,
                'pergunta_id' => $item->pergunta_id,
                'pergunta_descricao' => $pergunta->descricao ?? '(pergunta removida)',
                'dimensao' => $pergunta->dimensao ?? null,
                'data_relatorio' => $relatorio->data ?? null,
            ];
        }

        $pendenciasPorRelatorio = [];
        $menorDimensaoPorRelatorio = [];

        foreach ($pendencias as $p) {
            $pendenciasPorRelatorio[$p['relatorio_id']][] = $p;
        }

        if (
            $p['dimensao'] !== null &&
            (!isset($menorDimensaoPorRelatorio[$p['relatorio_id']]) || $p['dimensao'] < $menorDimensaoPorRelatorio[$p['relatorio_id']])
        ) {
            $menorDimensaoPorRelatorio[$p['relatorio_id']] = $p['dimensao'];
        }

        $this->set([
            'escolaName' => $escolaName,
            'escola_id' => $escola_id,
            'pendenciasPorRelatorio' => $pendenciasPorRelatorio,
            'menorDimensaoPorRelatorio' => $menorDimensaoPorRelatorio,
        ]);
    }

    public function atualizarStatusResposta()
    {
        $this->Authorization->skipAuthorization();

        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        $relatorioId = $this->request->getData('relatorio_id');
        $perguntaId = $this->request->getData('pergunta_id');
        $status = (int)$this->request->getData('status'); // 1 = resolvida, 0 = pendente

        $Respostas = $this->fetchTable('Respostas');
        $Providencia = $this->fetchTable('Providencias');

        $entity = $Respostas->find()
            ->where(['Respostas.relatorio_id' => $relatorioId, 'Respostas.pergunta_id' => $perguntaId])
            ->first();

        if (!$entity) {
            return $this->response->withStatus(404)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Resposta não encontrada.'
                ]));
        }

        $entity->status = $status;

        if (!$Respostas->save($entity)) {
            return $this->response
                ->withStatus(500)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Não foi possível salvar o status da resposta.'
                ]));
        }

        if ($status == 0) {
            // Reaberta como pendência
            $existente = $Providencia->find()
                ->where([
                    'Providencia.relatorio_id' => $relatorioId,
                    'Providencia.pergunta_id' => $perguntaId
                ])
                ->first();

            $identity = $this->Authentication->getIdentity();

            $relatorio = $this->Relatorios->find()
                ->where(['id' => $relatorioId])
                ->first();

            if (!$relatorio) {
                return $this->response
                    ->withStatus(404)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Relatório não encontrado.'
                    ]));
            }

            $providenciaEntity = $existente ?: $Providencia->newEntity([
                'relatorio_id' => $relatorioId,
                'pergunta_id' => $perguntaId,
                'usuario_id' => $identity->id,
            ]);
            $providenciaEntity->unid_escolar_id = $relatorio->unid_escolar_id;
            $providenciaEntity->status = 0;
            $providenciaEntity->resposta_id = $entity->id;

            if (!$Providencia->save($providenciaEntity)) {
                return $this->response
                    ->withStatus(500)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Não foi possível salvar a providência.',
                        'errors' => $providenciaEntity->getErrors()
                    ]));
            }
        } else {
            // Marca como resolvida
            $Providencia->updateAll(
                ['status' => 1],
                [
                    'relatorio_id' => $relatorioId,
                    'pergunta_id' => $perguntaId,
                    'status' => 0
                ]
            );
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'status' => $status
            ]));
    }
}
