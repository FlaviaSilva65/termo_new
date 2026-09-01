<?php

namespace App\Service;

use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;


class IdentityService
{
    public function get(Session $session, IdentityInterface $identity):IdentityInterface
    {
        if(!$session->check('Impersonate.usuario_id')){
            return $identity;
        }

        $usuarioId = $session->read('Impersonate.usuario_id');

        $usuario = TableRegistry::getTableLocator()
        ->get('Usuarios')
        ->find()
        ->contain(['TpUsuarios'])
        ->where([
            'Usuarios.id' => $usuarioId
        ])
        ->first();

        if (!$usuario){
            return $identity;
        }

        return new Identity($usuario);
    }
}
