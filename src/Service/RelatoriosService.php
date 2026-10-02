<?php

namespace App\Service;

use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Http\Session;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;


class RelatoriosService
{

    public function verificarCargosObrigatorios(int $escolaId): array
    {
        $Dashboards = TableRegistry::getTableLocator()
        ->get('Dashboards');

        $usuarios = $Dashboards->find()
            ->where(['escola_id' => $escolaId])
            ->all();

        $temDiretor = false;
        $temAssistente = false;

        foreach ($usuarios as $usuario) {
            if ((int)$usuario->tp_usuarios_id === 1) {
                $temDiretor = true;
            }

            if ((int)$usuario->tp_usuarios_id === 5) {
                $temAssistente = true;
            }
        }

        return [
            'diretor' => [
                'existe_externamente' => true,
                'cadastrado' => $temDiretor,
                'assinatura_obrigatoria' => true
            ],
            'assistente' => [
                'existe_externamente' => false,
                'cadastrado' => $temAssistente,
                'assinatura_obrigatoria' => false
            ],
        ];
    }
}
