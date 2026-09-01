<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Usuario;
use Cake\Controller\Controller;
// use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\UnauthorizedException;
use App\Service\UsuarioRedirectService;

class ImpersonacaoController extends AppController
{
    public function assumir(int $id)
    {
        $session = $this->request->getSession();

        // Identidade ORIGINAL, ou seja, quem fez login
        $identity = $this->Authentication->getIdentity();

        if (!$identity) {
            throw new UnauthorizedException();
        }

        // Busca o usuário que será assumido
        $usuario = $this->fetchTable('Usuarios')
            ->find()
            ->contain(['TpUsuarios'])
            ->where([
                'Usuarios.id' => $id
            ])
            ->firstOrFail();

        // debug([
        //     'authentication_identity' => $this->Authentication->getIdentity(),
        //     'request_identity' => $this->request->getAttribute('identity'),
        //     'usuario_assumido' => $usuario,
        // ]);
        // die;

        // Verifica se o usuário original pode assumir esse usuário
        $this->Authorization->authorize($usuario, 'assumir');

        // Guarda as duas identidades
        $session->write('Impersonate', [
            'usuario_original_id' => $identity->getIdentifier(),
            'usuario_id' => $usuario->id,
        ]);

        $this->Flash->success(
            "Agora você está utilizando o sistema como {$usuario->nm_usuario}."
        );

        $redirectService = new UsuarioRedirectService();

        $redirect = $redirectService->getRedirect($usuario);

        return $this->redirect($redirect);
    }

    public function pararImpersonacao()
    {
        $session = $this->request->getSession();

        if (!$session->check('Impersonate.usuario_original_id')) {
            return $this->redirect('/');
        }

        $usuarioOriginalId = $session->read(
            'Impersonate.usuario_original_id'
        );

        $usuarioOriginal = $this->fetchTable('Usuarios')
            ->find()
            ->contain(['TpUsuarios'])
            ->where([
                'Usuarios.id' => $usuarioOriginalId
            ])
            ->firstOrFail();

        $this->Authorization->authorize(
            $usuarioOriginal,
            'parar'
        );

        $session->delete('Impersonate');

        $this->Flash->success(
            'Você voltou para sua conta.'
        );

        return $this->redirect('/');
    }
}
