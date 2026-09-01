<?php

namespace App\Policy;

use App\Model\Entity\Relatorio;
use Authentication\Identity;
use Authentication\IdentityInterface as AuthenticationIdentityInterface;
use Authorization\IdentityInterface;

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
        // mesma regra usada em manterPerguntas, ou uma regra específica
        return $this->canManterPerguntas($user, $relatorio);
    }
}
