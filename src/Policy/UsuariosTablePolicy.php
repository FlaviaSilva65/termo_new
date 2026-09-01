<?php

namespace App\Policy;

use App\Model\Entity\Usuario;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforeScopeInterface;
use Authorization\Policy\ResultInterface;
// use App\Model\Entity\Usuario;
// use Authentication\IdentityInterface as AuthenticationIdentityInterface;


class UsuariosTablePolicy implements BeforeScopeInterface
{
    public function beforeScope(?IdentityInterface $identity, mixed $resource, string $action): ResultInterface|bool|null
    {
        if ($identity && ($identity->tp_usuarios_id == 9 || $identity->tp_usuarios_id == 6)) {
            return  true;
        } else {
            $resource->where('0 = 1');
        }
        return null;
    }

    public function scopeIndex($identity, $resource)
    {
        return $resource;
    }
}
