<?php

declare(strict_types=1);

namespace App\Service;

use Cake\Http\Exception\RedirectException;
use App\Model\Entity\Usuario;
use Cake\ORM\Locator\LocatorAwareTrait;

class UsuarioRedirectService
{

    use LocatorAwareTrait;

    public function getRedirect(Usuario $usuario): array
    {
        $tpUsuarioId = $usuario->tp_usuarios_id;

        // debug($tpUsuarioId);
        // debug($usuario);
        // die;

        $usuarioUnidEscolares = $this->fetchTable('UsuarioUnidEscolares');
        $setor = $usuarioUnidEscolares->find()
            ->where(['usuario_id' => $usuario->id])
            ->all()
            ->last();

        if ($tpUsuarioId == 9 || $tpUsuarioId == 6) {
            return [
                'controller' => 'Usuarios',
                'action' => 'index'
            ];
        }

        if ($tpUsuarioId == 4) {
            return [
                'controller' => 'Relatorios',
                'action' => 'dash_subsecretaria'
            ];
        }

        if ($tpUsuarioId == 2) {
            if ($setor->setores_id != 0) {
                return [
                    'controller' => 'Relatorios',
                    'action' => 'dash_supervisor',
                    $setor->setores_id
                ];
            }

            return [
                'controller' => 'Usuarios',
                'action' => 'index'
            ];
        }

        if ($tpUsuarioId == 1 || $tpUsuarioId == 5) {
            return [
                'controller' => 'Relatorios',
                'action' => 'dash_diretor_escolas',
                $usuario->id
            ];
        }

        return [
            'controller' => 'Usuarios',
            'action' => 'index'
        ];
    }
}
