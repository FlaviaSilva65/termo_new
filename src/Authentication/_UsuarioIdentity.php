<?php

namespace App\Authentication;

use ArrayAccess;
use Authentication\IdentityInterface;
use App\Model\Entity\Usuario;

class UsuarioIdentity implements IdentityInterface
{
    private Usuario $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
        
    }

    public function getIdentifier()
    {
        return $this->usuario->id;
    }

    public function getOriginalData()
    {
        return $this->usuario;
    }

    public function __get($name)
    {
        return $this->usuario->{$name};
    }

    public function __isset($name)
    {
        return isset($this->usuario->{$name});
    }

    public function offsetExists($offset): bool
    {
        return isset($this->usuario->{$offset});
    }

    public function offsetGet($offset): mixed
    {
        return $this->usuario->{$offset};
    }

    public function offsetSet($offset, $value): void
    {
        $this->usuario->{$offset} = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->usuario->{$offset});
    }
}