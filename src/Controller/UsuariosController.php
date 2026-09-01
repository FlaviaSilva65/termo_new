<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\WhenThenExpression;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Schema\Collection;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Http\Client;

use function React\Promise\all;

/**
 * Usuarios Controller
 *
 * @property \App\Model\Table\UsuariosTable $Usuarios
 */
class UsuariosController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'add']);
    }

    // public function isAuthorized($usuario)
    // {
    //     if ((isset($usuario['id']) && $usuario['id'] > 0)) {

    //         return true;
    //     }
    //     return false;
    // }

    public function index()
    {

        $usuarios = $this->Usuarios->find('all', contain: ['TpUsuarios'])->where(['ic_ativo' => 1]);

        // $this->Authorization->skipAuthorization();
        $this->Authorization->applyScope($usuarios, 'index');
        $this->set('usuarios', $this->paginate($usuarios, ['limit' => 15]));
    }

    public function view($id)
    {
        $usuario = $this->Usuarios->get($id, contain: ['TpUsuarios']);

        $this->set(compact('usuario'));
    }

    public function add()
    {
        $this->Authorization->skipAuthorization();

        $usuario = $this->Usuarios->newEmptyEntity();

        // $usuario = $this->Usuarios->newEntity(['associated' => ['UsuarioUnidEscolares']]);

        if ($this->request->is('post')) {

            // debug($this->request->getData());


            // $arquivo = $this->request->getData('arquivo');

            // Essa variável foi criada para teste o croppie
            // $imagem = $this->request->getData('imagem');

            // debug($imagem);



            // $permitido = (1024) * 100;


            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData());

            $unid_escolar = $this->request->getData('nm_unid_escolar');

            $nomeTratado = preg_replace('/^E\.M\.\s*/', '', $unid_escolar);

            $UnidEscolares = $this->fetchTable('UnidEscolares');

            $escola = $UnidEscolares->find()->where(["MATCH (nm_unid_escolar) AGAINST ('$nomeTratado') "])->first();

            $usuario_unid_escolares = $this->fetchTable('UsuarioUnidEscolares');

            $tp_usuario_id = $this->request->getData('tp_usuarios_id');

            $usuario->usuario_unid_escolares = [$usuario_unid_escolares->newEntity([
                'setores_id' => $tp_usuario_id == 2 ? $escola->setores_id : false,
                'unid_escolares_id' => $tp_usuario_id !== 2 ? $escola->id : false
            ])];



            if (!empty($_POST['imagem'])) {

                $img = explode(";", $_POST['imagem']);

                $usuario->imagem = $this->request->getData('cd_rf') . '.png';

                $usuario->cd_assinatura = $usuario->imagem;

                if (isset($_POST['imagem']) && mb_stripos($_POST['imagem'], 'a:i')) {
                    $img = explode(";", $_POST['imagem']);
                    $imagem = explode(",", $img[1]);
                    $caminho = './files/';

                    // $caminho = WWW_ROOT . 'files' . DS;

                    // Cria a pasta caso não exista
                    if (!is_dir($caminho)) {
                        mkdir($caminho, 0777, true);
                        chmod($caminho, 0777);
                    }

                    // $img_recebida = base64_decode($imagem[1]);
                    $img_recebida = $_POST['imagem'];

                    $this->Flash->success('Informações adicionadas com sucesso');
                    // Transfere a imagem
                    file_put_contents($caminho . $usuario->imagem, base64_decode($imagem[1]));
                }
            }

            $nome_completo = $this->request->getData('nm_usuario');
            $primeiro = explode(' ', $nome_completo);
            $rf = $this->request->getData('cd_rf');

            $usuario->username = $rf . "." . lcfirst($primeiro[0]);

            if ($this->Usuarios->save($usuario)) {

                $this->Flash->success('O usuário foi salvo.');

                return $this->redirect(['action' => 'add']);
            }

            $this->Flash->error('Não foi possivel adicionar o usuário.');
        }
        $tp_usuarios = $this->Usuarios->TpUsuarios->find('list');
        $unid_escolares = $this->Usuarios->UsuarioUnidEscolares->UnidEscolares->find('list');
        $setor_supervisor = $this->Usuarios->SetorSupervisores->Setores->find('list');
        $this->set('usuario', $usuario);
        $this->set('tp_usuarios', $tp_usuarios);
        $this->set('unid_escolares', $unid_escolares);
        $this->set('setor_supervisor', $setor_supervisor);
    }

    public function carregarRf()
    {
        $this->Authorization->skipAuthorization();

        $rf = $this->request->getQuery('rf');

        if ($rf) {
            $http = new Client();

            $response = $http->get("https://seduc.cidadaopg.sp.gov.br/sie/api/staff/employee/$rf");

            if ($response->isOk()) {
                $dadosFuncionario = $response->getJson();
            }
        }



        $resposta = [
            'status' => 'sucesso',
            'mensagem' => 'Funcionário localizada.'
        ];

        $this->viewBuilder()->disableAutoLayout();
        $this->response = $this->response->withType('application/json');
        return $this->response->withStringBody(json_encode($dadosFuncionario));
    }

    public function listadirass()
    {
        $diretores = $this->Usuarios->find(
            'all',
            conditions: ['tp_usuarios_id' => 5, 'ic_ativo' => 1],
            contain: ['UsuarioUnidEscolares']
        );



        foreach ($diretores as $diretor):

            debug($diretor);

        // if ($diretor->usuario_unid_escolare->unid_escolares_id == '') {
        //     $len = strlen($diretor->cd_rf);

        //     if ($len == 5) {
        //         $fisrt = substr($diretor->cd_rf, 0, 2);
        //         $last = substr($diretor->cd_rf, -3);

        //         $rf = $fisrt . '.' . $last;
        //     } elseif ($len == 4) {

        //         $fisrt = substr($diretor->cd_rf, 0, 1);
        //         $last = substr($diretor->cd_rf, -3);

        //         $rf = $fisrt . '.' . $last;
        //     }

        //     $dir_bd_escolas = $this->fetchTable('Users')->find()->contain('Escolas')->where(['rf' => $rf])->all();

        //     // debug($dir_bd_escolas);

        //     foreach ($dir_bd_escolas as $escola_dir):

        //         $escola = $this->fetchTable('UnidEscolares')->find()
        //             ->where(['Upper(nm_unid_escolar) LIKE' => '%' . mb_strtoupper($escola_dir->escola->nome) . '%'])->first();

        //             // debug($escola);

        //         $usuario_unid_escolar = $this->Usuarios->UsuarioUnidEscolares->get($diretor->usuario_unid_escolare->id);
        //         $usuario_unid_escolar->unid_escolares_id = $escola->id;
        //         // $this->Usuarios->UsuarioUnidEscolares->save($usuario_unid_escolar);

        //         // debug($usuario_unid_escolar);
        //     endforeach;
        // }

        endforeach;


        die;
    }

    public function buscaunids()
    {
        $this->viewBuilder()->disableAutoLayout(false);

        $identity = $this->Authentication->getIdentity();


        $this->Authorization->authorize($identity->getOriginalData(), 'buscaunids');


        $keyword = $this->request->getQuery('keyword');

        if ($keyword != '') {
            $query = $this->Usuarios->SetorSupervisores->Setores->UnidEscolares->find()
                // ->where(['setores_id' => $keyword]); // essa busca estava considerando o setor
                // ->contain(['UnidEscolares']);
                ->where(
                    // ['Upper(nm_unid_escolar) LIKE' => '%' . mb_strtoupper($keyword) . '%']);
                    ["MATCH (nm_unid_escolar) AGAINST ('$keyword') "]
                );
        }

        $this->set('unids', $query);
    }

    public function imagem() {}

    public function imagemCelke()
    {
        $usuario = $this->Usuarios->newEmptyEntity();

        if ($this->request->is('post')) {

            //    $imagem = filter_input(INPUT_POST, 'imagem', FILTER_DEFAULT);

            // $imagem = $_FILES['imagem'];

            // // debug($imagem);
            // // die;
            // // $arquivo = $this->request->getData('imagem');
            // $permitido = (1024) * 100;

            // // list($type, $imagem) = explode(';', $imagem);
            // // list(, $imagem) = explode(',', $imagem);

            // $nome_arquivo = explode('.', $imagem['name']);

            // // $imagem = base64_decode($imagem['full_path']);

            // $imagem_nome = $nome_arquivo[0] . '.jpeg';

            // $caminho = WWW_ROOT . 'files' . DS . $imagem_nome ;

            // // debug ($nome_arquivo);
            // // debug ($imagem_nome);
            // // debug ($caminho);
            // // die;

            // move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho);

        }

        $this->set(compact('usuario'));
    }

    public function upload()
    {
        // Receber a imagem
        // $imagem = filter_input(INPUT_POST, 'imagem', FILTER_DEFAULT);
        $imagem = $_FILES['imagem'];

        // Separa as informações da imagem base64 pelo ";"
        // list($type, $imagem) = explode(';', $imagem);
        // $nome_arquivo = explode('.', $_FILES['arquivo']['name']);
        $nome_arquivo = explode('.', $imagem['name']);

        // Desconverter a imagem base64
        // $imagem = base64_decode($imagem);

        // Atribuir a extensão da imagem PNG
        // $imagem_nome = time() . '.png';
        // $imagem_nome = $nome_arquivo . '.png';
        $imagem_nome = $nome_arquivo[0] . '.jpeg';

        // Realizar o upload da imagem
        // file_put_contents('imagens/upload/' . $imagem_nome, $imagem);

        $caminho = WWW_ROOT . 'files' . DS . $imagem_nome;

        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho);

        // return $this->redirect($this->referer());

        echo "Imagem enviada com sucesso!";
    }

    public function edit($id)
    {

        $query = $this->Usuarios->get($id);

        if ($query->tp_usuarios_id == 1 || $query->tp_usuarios_id == 5) {

            $pessoa = $this->Usuarios->get($id, contain: ['TpUsuarios', 'UsuarioUnidEscolares' => ['UnidEscolares']]);
        } elseif ($query->tp_usuarios_id == 2) {
            $pessoa = $this->Usuarios->get($id, contain: ['TpUsuarios', 'UsuarioUnidEscolares' => ['Setores' => ['UnidEscolares']]]);
        } else {
            $pessoa = $this->Usuarios->get($id, contain: ['TpUsuarios']);
        }

        $usuario = $this->Authentication->getIdentity();

        $this->Authorization->authorize($pessoa, 'edit');

        $usuario_unid_escolar = $this->Usuarios->UsuarioUnidEscolares->find()->where(['usuario_id' => $id])->first();

        if (!$usuario_unid_escolar) {

            $usuario_unid_escolar = $this->Usuarios->UsuarioUnidEscolares->newEmptyEntity();

            $connection = TableRegistry::getTableLocator()->get('Users', [
                'connectionName' => 'bdescola'
            ]);

            $usuario_unid_escolar->usuario_id = $id;
        }
        $unid_escolares = TableRegistry::getTableLocator()->get('UnidEscolares');
        $usuario_unid_escolares = TableRegistry::getTableLocator()->get('UsuarioUnidEscolares');


        if ($pessoa->tp_usuarios_id == 2) {

            $escolas = $unid_escolares->find('all')->where(['setores_id' => $usuario_unid_escolar->setores_id]);

            $this->set(compact('escolas'));
        } elseif ($pessoa->tp_usuarios_id == 1 || $pessoa->tp_usuarios_id == 5) {

            if ($pessoa) {
                // $usuario = $this->Usuarios->get($id, contain: ['TpUsuarios', 'UsuarioUnidEscolares' => ['UnidEscolares']]);
                $usuario_unid_escolar = $usuario_unid_escolares->find()->where(['usuario_id' => $id]);
            }
        }


        if ($this->request->is(['post', 'put'])) {

            if ($pessoa->tp_usuarios_id == 3 || $pessoa->tp_usuarios_id == 4 || $pessoa->tp_usuarios_id == 9) {

                $pessoa = $this->Usuarios->patchEntity($pessoa, $this->request->getData());
            } elseif ($pessoa->tp_usuarios_id == 2 || $pessoa->tp_usuarios_id == 1 || $pessoa->tp_usuarios_id == 5) {
                $pessoa = $this->Usuarios->patchEntity($pessoa, $this->request->getData(), ['associated' => ['UsuarioUnidEscolares']]);
            }

            if ($pessoa->tp_usuarios_id == 1 || $pessoa->tp_usuarios_id == 5) {
                $usuario_unid_escolar = $this->Usuarios->UsuarioUnidEscolares->findOrCreate(['usuario_id' => $id, 'unid_escolares_id' => $this->request->getData('unid_escolares_id')]);
            } else {
                $usuario_unid_escolar = $this->Usuarios->findOrCreate(['id' => $id]);
            }


            if ($this->Usuarios->save($pessoa)) {
                $this->Usuarios->UsuarioUnidEscolares->save($usuario_unid_escolar);

                $this->Flash->success('Usuário salvo com sucesso.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Houve algum problema');
        }


        $tp_usuarios = $this->Usuarios->TpUsuarios->find('list');
        $unid_escolares = $this->Usuarios->UsuarioUnidEscolares->UnidEscolares->find('list');

        $this->set(compact('pessoa', 'tp_usuarios', 'unid_escolares'));
    }

    // public function login()
    // {

    //     $this->Authorization->skipAuthorization();

    //     $result = $this->Authentication->getResult();


    //     $username = $this->request->getData('username');

    //     if ($username != '') {
    //         $login = trim($username);

    //         $cpf = preg_replace('/\D/', '', $login);
    //     }


    //     if ($result && $result->isValid()) {

    //         debug($result);
    //         die;

    //         $usuario = $this->request->getAttributes();

    //         $tp_usuario = $usuario['identity']->get('tp_usuarios_id');
    //         $id_usuario = $usuario['identity']->getIdentifier();

    //         $usuario = $this->Usuarios->get($id_usuario, contain: 'Escolas.UnidEscolares');

    //         $this->request->getSession()->write('Auth', $usuario);

    //         if ($tp_usuario == 1 || $tp_usuario == 5) {


    //             $connection = $this->fetchTable('Users');

    //             // $dir_assistentes = $connection->find()->where(['cpf' => $cpf, 'Users.ativo' => 1])->toArray();

    //             $conditions = ['Users.ativo' => 1];

    //             if (is_numeric($cpf) && strlen($cpf) == 11) {

    //                 $conditions['cpf'] = $cpf;
    //             } else {

    //                 $conditions['login'] = $login;
    //             }

    //             $dir_assistentes = $connection->find()
    //                 ->where($conditions)
    //                 ->toArray();

    //             if (count($dir_assistentes) > 0) {

    //                 return $this->redirect(['controller' => 'Relatorios', 'action'  => 'dash_diretor_escolas', $usuario->id]);
    //             }
    //         } else {
    //             $this->Authorization->skipAuthorization();
    //             $setores = $this->Usuarios->UsuarioUnidEscolares->find('all')->where(['usuario_id' => $id_usuario])->all();
    //             $setor = $setores->last();

    //             if ($usuario && ($usuario->tp_usuarios_id == 9 || $usuario->tp_usuarios_id == 6)) {
    //                 return $this->redirect(['action' => 'index']);
    //             } elseif ($usuario && $usuario->tp_usuarios_id == 4) {
    //                 return $this->redirect(['controller' => 'Relatorios', 'action'  => 'dash_subsecretaria']);
    //             } else {
    //                 return $this->redirect(['controller' => 'Relatorios', 'action'  => 'dash_supervisor', $setor->setores_id]);
    //             }
    //         }
    //     }
    //     if ($this->request->is('post') && !$result->isValid()) {
    //         $this->Flash->error('Usuário ou senha inválido.');
    //     }
    // }

    public function login()
    {
        if ($this->getRequest()->getSession()->read('Impersonate')) {
            unset($_SESSION['Impersonate']);
        }

        $this->Authorization->skipAuthorization();

        if ($this->request->is('post')) {

            $result = $this->Authentication->getResult();

            if ($result && $result->isValid()) {

                // $redirect = $this->request->getQuery('redirect', '/');
                // return $this->redirect($redirect);

                $identity = $this->Authentication->getIdentity();

                $tpUsuarioId = $identity->tp_usuarios_id;

                if ($tpUsuarioId == 2) {
                    $setor_supervisor = $this->fetchTable('SetorSupervisores');
                    $setores = $setor_supervisor->find()->where(['usuario_id' => $identity->id])->all();

                    if (count($setores) === 0) {
                        $this->Authentication->logout();
                        $this->Flash->error('Supervisor não cadastrado.');
                        return $this->redirect($this->referer());
                    } else {
                        $usuarioUnidEscolares = $this->fetchTable('UsuarioUnidEscolares');
                        $setor = $usuarioUnidEscolares->find()
                            ->where(['usuario_id' => $identity->id])
                            ->all();
                            // ->last();

                            // debug($setor);
                            // die;

                        if ($setor) {
                            return $this->redirect(['controller' => 'Relatorios', 'action' => 'dash_supervisor', $identity->id]);
                        } else {
                            $this->Authentication->logout();
                            $this->Flash->error('Usuário sem permissão.');
                            return $this->redirect('/');
                        }
                    }
                } else {
                    debug('Deu ruim');
                    die;
                }

                // Busca o setor do usuário (necessário para tp 2)




                // debug($setor);
                // die;


                // Redireciona conforme o tipo de usuário
                if ($tpUsuarioId == 9 || $tpUsuarioId == 6) {
                    return $this->redirect(['controller' => 'Usuarios', 'action' => 'index']);
                } elseif ($tpUsuarioId == 4) {
                    return $this->redirect(['controller' => 'Relatorios', 'action' => 'dash_subsecretaria']);
                } elseif ($tpUsuarioId == 2) {
                    $setor_supervisor = $this->fetchTable('SetorSupervisores');
                    $setores = $setor_supervisor->find()->where(['usuario_id' => $identity->id])->all();
                    debug($identity);
                    // if (count($setores) === 0) {
                    //     $this->Flash->error('Supervisor não cadastrado.');
                    //     $this->redirect($this->referer());
                    // } else {
                    //     if ($setor->setores_id != 0) {
                    //         return $this->redirect(['controller' => 'Relatorios', 'action' => 'dash_supervisor', $setor->setores_id]);
                    //     } else {
                    //         $this->Authentication->logout();
                    //         $this->Flash->error('Usuário sem permissão.');
                    //         return $this->redirect('/');
                    //     }
                    // }
                } elseif ($tpUsuarioId == 1 || $tpUsuarioId == 5) {
                    // debug($identity);
                    // die;
                    return $this->redirect(['controller' => 'Relatorios', 'action' => 'dash_diretor_escolas', $identity->id]);
                } else {
                    // Fallback para outros tipos
                    return $this->redirect('/');
                }
            }


            $this->Flash->error('Usuário ou senha inválido.');
        }
    }

    public function logout()
    {

        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {

            $this->Authorization->skipAuthorization();

            $this->Authentication->logout();

            return $this->redirect(['controller' => 'Usuarios', 'action' => 'login']);
        }
    }

    public function redefinir($id)
    {
        if ($this->request->is(['post', 'put'])) {
            $this->request->allowMethod(['post', 'put']);

            if (!(is_null($id))) {
                $usuario = $this->Usuarios->get($id);
                $usuario->password = '123456';

                if ($this->Usuarios->save($usuario)) {
                    $this->Flash->success('Senha alterada.');

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error('Não foi possivel alterar senha.');
            }
        }
    }

    public function search()
    {
        // if ($usuario && $identity->tp_usuarios_id == 9) {

        // Criar política para essa function

        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->disableAutoLayout(false);
        $keyword = $this->request->getQuery('keyword');

        if ($keyword != '') {
            $query = $this->Usuarios->find()
                ->where(['Upper(nm_usuario) LIKE' => '%' . mb_strtoupper($keyword) . '%'])
                ->contain(['TpUsuarios']);
        }

        $this->set('usuarios', $query);
        // }
    }

    public function reciclar()
    {
        $this->Authorization->skipAuthorization();
        $usuarios = $this->Usuarios->find('all', contain: ['TpUsuarios'])->where(['ic_ativo' => 1]);

        $connection = $this->fetchTable('Users');
        $unid_escolares = $this->fetchTable('UnidEscolares');

        // $testes = $connection->find('all')->where(['escola_id' < 41])
        // ->select(['id','escola_id','rf', 'nome', 'email', 'cpf', 'funcao']);

        // foreach($testes as $teste):
        //     $rf = str_replace(array(".", ""), "", $teste->rf);
        //     $cadastro = $this->Usuarios->find()->where(['cd_rf IN' => $rf]);
        //     debug($teste);
        //     debug($cadastro->toArray());
        // endforeach;

        // die;



        $escola_users = $connection->find('all')
            ->where(['escola_id' => 66, 'ativo' => 1])
            ->select(['id', 'escola_id', 'rf', 'nome', 'email', 'cpf', 'funcao']);
        // ->toArray();

        debug(count($escola_users->toArray()));

        if (count($escola_users->toArray()) > 0) {
            foreach ($escola_users as $escola_user):
                $escola_usuarios[] = str_replace(array(".", ""), "", $escola_user->rf);
            endforeach;

            $cadastro = $this->Usuarios->find()->where(['cd_rf IN' => $escola_usuarios])->contain(['UsuarioUnidEscolares']);

            debug($escola_users->toArray());
            debug($this->Usuarios->TpUsuarios->find('list', keyField: 'id', valueField: 'nm_tp_usuarios')->toArray());
            debug($escola_usuarios);
            debug($unid_escolares->find()->where(['id_escola in' => 66])->toArray());
            debug($cadastro->toArray());
        }

        die;

        $this->set('usuarios', $usuarios);
    }
}
