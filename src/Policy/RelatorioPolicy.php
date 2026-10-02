<?php

namespace App\Policy;

use App\Model\Entity\Relatorio;
use Authentication\Identity;
use Authentication\IdentityInterface as AuthenticationIdentityInterface;
use Authorization\IdentityInterface;
use Cake\ORM\TableRegistry;

class RelatorioPolicy
{
    public function canAdd(IdentityInterface $usuario, Relatorio $relatorio)
    {
        return true;
    }

    // public function canManterPerguntas(IdentityInterface $usuario, Relatorio $relatorio)
    // {
    //     return true;
    // }

    public function canSecoesTermo(IdentityInterface $usuario, Relatorio $relatorio)
    {
        return true;
    }

    public function canAddNew(IdentityInterface $usuario, Relatorio $relatorio)
    {
        return true;
    }


    public function canDashSupervisor(IdentityInterface $identity, Relatorio $relatorios)
    {
        return $identity->id == $relatorios->usuario_id;
        // $this->isAuthor($identity, $relatorios);
    }

    public function canDashSubEscolas(IdentityInterface $identity)
    {
        if ($identity->tp_usuarios_id == 4) {
            return true;
        }
    }

    public function canDashDiretorEscolas(IdentityInterface $identity, Relatorio $relatorios)
    {
        // debug($identity);
        // die;
        foreach ($identity->escolas as $escola)
            if ($escola->unid_escolare->id == $relatorios->unid_escolar_id) {
                return true;
            }
        return false;
    }


    public function isAuthor(IdentityInterface $identity, Relatorio $relatorio)
    {
        return $relatorio->usuario_id === $identity->id;
    }


    public function canEdit(IdentityInterface $identity, Relatorio $relatorio)
    {
        return $identity->id == $relatorio->usuario_id;
    }


    public function canSign(IdentityInterface $identity, Relatorio $relatorio)
    {
        $supervisor = $identity->id == $relatorio->usuario_id;

        if ($supervisor || $identity->tp_usuarios_id == 4 || $identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) return true;

        foreach ($identity->escolas as $escola)
            if ($escola->unid_escolare->id == $relatorio->unid_escolar_id) {
                return true;
            }

        // if ($identity->id == $relatorio->usuario_id) {
        //     return true;
        // }

        // if(!empty($identity->usuario_unid_escolares->unid_escolares) && $relatorio->unid_escolar_id){
        //     $userUnidadeIds = collection($identity->usuario_unid_escolares->unid_escolares)->extract('id')->toList();
        //     return in_array($relatorio->unid_escolar_id, $userUnidadeIds);
        // }

        return false;
    }

    public function canCancelar(IdentityInterface $identity, Relatorio $relatorio)
    {
        if ($identity->tp_usuarios_id == 2) {
            return true;
        }
        return false;
    }

    public function canVisualizarPdf(IdentityInterface $identity, Relatorio $relatorio)
    {
        if ($identity) {
            return true;
        }
        return false;
    }

    public function canUndersign(IdentityInterface $identity, Relatorio $relatorio)
    {
        // $diretor = $identity->id == $relatorio->usuario_id;

        if ($identity->tp_usuarios_id == 4 || $identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) {
            return true;
        }

        foreach ($identity->escolas as $escola)
            if ($escola->unid_escolare->id == $relatorio->unid_escolar_id) {
                return true;
            }
        return false;
    }

    public function canManterPerguntas(IdentityInterface $identity, Relatorio $relatorio)
    {
        // return $identity->id == $relatorio->usuario_id;
        return true;
    }

    public function canConcluirAssinatura(IdentityInterface $user, Relatorio $relatorio): bool
    {

        $tipoUsuario = (int)$user->tp_usuarios_id;

        // Supervisor: assina só durante o rascunho
        if ($tipoUsuario === 2) {
            return $relatorio->ic_rascunho == 1 && empty($relatorio->id_ass_super);
        }

        // Enquanto está em rascunho, mais ninguém assina
        if ($relatorio->ic_rascunho == 1) {
            return false;
        }

        // Diretor
        if ($tipoUsuario === 1) {
            return empty($relatorio->id_ass_dir);
        }

        // Assistente
        if ($tipoUsuario === 5) {
            return empty($relatorio->id_ass_assis);
        }

        // Subsecretário / Adjunto
        if (in_array($tipoUsuario, [4, 8], true)) {
            if (!empty($relatorio->id_ass_sub)) {
                return false;
            }

            [$temDiretor, $temAssistente] = $this->gestoresExistentes($relatorio);

            $dirOk    = !$temDiretor    || !empty($relatorio->id_ass_dir);
            $assisOk  = !$temAssistente || !empty($relatorio->id_ass_assis);

            return ($temDiretor || $temAssistente) && $dirOk && $assisOk;
        }

        return false;
    }

    private function obterRfListDaUnidade($unidade)
    {
        $unidEscolares = TableRegistry::getTableLocator()
            ->get('UnidEscolares')
            ->find()
            ->select(['id', 'id_escola'])
            ->where(['id' => $unidade])
            ->first();

        if (!$unidEscolares) {
            return [];
        }

        // 2. Busca no banco externo, na tabela de relacionamento Dashboards,
        //    os funcionario_id vinculados a essa escola
        $funcionarioIds = TableRegistry::getTableLocator()
            ->get('Dashboards')
            ->find()
            ->select(['funcionario_id'])
            ->where(['escola_id' => $unidEscolares->id_escola])
            ->all()
            ->extract('funcionario_id')
            ->toArray();

        if (empty($funcionarioIds)) {
            return [];
        }

        // 3. Busca, na tabela externa Funcionarios, quem é Diretor (2) ou Assistente (3)
        $funcionarios = TableRegistry::getTableLocator()
            ->get('Funcionarios')
            ->find()
            ->select(['rf'])
            ->where([
                'id_funcionario IN' => $funcionarioIds,
                'funcao IN' => [2, 3],
            ])
            ->all();

        // 4. Retorna só a lista de cd_rf
        return $funcionarios->extract('rf')->toArray();

    }

    /**
     * Retorna [temDiretor, temAssistente] para a unidade do relatório,
     * consultando o banco externo de gestores.
     */
    private function gestoresExistentes(Relatorio $relatorio): array
    {
        $rfList = $this->obterRfListDaUnidade($relatorio->unid_escolar_id); // sua regra atual de montar $rf_list

        if (empty($rfList)) {
            return [false, false];
        }

        $tipoGestor = TableRegistry::getTableLocator()
            ->get('Usuarios')
            ->find()
            ->select(['tp_usuarios_id'])
            ->where([
                'cd_rf IN' => $rfList,
                'tp_usuarios_id IN' => [1, 5],
            ])
            ->all()
            ->extract('tp_usuarios_id')
            ->toArray();

        $temDiretor    = in_array(1, $tipoGestor);
        $temAssistente = in_array(5, $tipoGestor);

        return [$temDiretor, $temAssistente];
    }
}
