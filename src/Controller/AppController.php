<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use DateInterval;
use DatePeriod;
use DateTime;
use PhpParser\Node\Identifier;
use App\Service\IdentityService;
use Cake\Event\EventInterface;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    // public $identity;

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent(
            'Authorization.Authorization',
            ['skipAuthorization' => ['display']]
        );

        // $identity = $this->Authentication->getIdentity();

        // if ($identity) {

        //     $usuario = $this->Authentication->getIdentity();

        //     $usuario_unid_escolares = $this->fetchTable('UsuarioUnidEscolares');
        //     $tp_usuarios = $this->fetchTable('TpUsuarios');
        //     $unid_escolares = $this->fetchTable('UnidEscolares');
        //     $unid_escolares_vw = $this->fetchTable('UnidEscolaresVw');

        //     $funcao = $tp_usuarios->get($usuario->tp_usuarios_id);

        //     $escola_usuarios = [];

        //     if ($usuario->tp_usuarios_id == 2 || $usuario->tp_usuarios_id == 1 || $usuario->tp_usuarios_id == 5) {

        //         $usuario_unidades = $usuario_unid_escolares->find()->where(['usuario_id' => $usuario->id])->all();

        //         if (count($usuario_unidades) > 0) {

        //             foreach ($usuario_unidades as $escola_user):
        //                 $escola_usuarios[] = $escola_user->unid_escolares_id;
        //             endforeach;
        //         }

        //         $escolas = $unid_escolares_vw->find('list', keyField: 'id', valueField: 'nm_unid_escolar')
        //             ->where(['id IN' => $escola_usuarios])->orderBy(function ($exp, $query) {
        //                 return $exp->add("REPLACE(nm_unid_escolar, 'E.M. ', '') ASC");
        //             })->orderBy(['nm_unid_escolar' => 'ASC'])
        //             ->toArray();
        //         $this->set(compact('escolas'));
        //     }

        //     $this->set(compact('funcao'));
        // };

        // $this->set('identity', $identity);

        // if ($this->request->is('post')) {

        //     $ano_search = $this->request->getData('ano_search');
        //     $escola_id = $this->request->getData('escola_id');

        //     if ($ano_search != '' && $escola_id != '') {
        //         $this->redirect(['action' => 'dashRelatoriosAnteriores', $ano_search, $escola_id]);
        //     }
        // }

        // $anoAtual = date('Y');

        // if ($identity && ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5 || $identity->tp_usuarios_id == 2)) {
        //     // $years = 2026;
        //     if ($anoAtual > 2026) {
        //         $years = [];
        //         $years = array_combine(range($anoAtual, 2016, -1), range($anoAtual, 2016, -1));
        //     } else {
        //         $years = '----';
        //     }
        // } else {
        //     $years = [];
        //     $years = array_combine(range($anoAtual, 2016, -1), range($anoAtual, 2016, -1));
        // }

        // // $ano_search = [2016,2017,2018];
        // $this->set(compact('years'));

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated([
            'login'
        ]);

        $request = $this->getRequest();

        $controller = $request->getParam('controller');
        $action = $request->getParam('action');

        /*
         * Página de login não precisa de autenticação.
         */
        if ($controller === 'Usuarios' && $action === 'login') {
            return;
        }

        /*
         * Verifica o resultado da autenticação.
         */
        // $result = $this->Authentication->getResult();

        // if (!$result->isValid()) {

        //     return $this->redirect([
        //         'prefix' => false,
        //         'controller' => 'Usuarios',
        //         'action' => 'login',
        //         '?' => [
        //             'redirect' => $request->getRequestTarget()
        //         ]
        //     ]);
        // }

        /*
         * Obtém o usuário autenticado.
         */
        $identity = $this->Authentication->getIdentity();

        // if ($identity === null) {

        //     return $this->redirect([
        //         'prefix' => false,
        //         'controller' => 'Usuarios',
        //         'action' => 'login',
        //         '?' => [
        //             'redirect' => $request->getRequestTarget()
        //         ]
        //     ]);
        // }

        /*
         * Trata a impersonação.
         */
        // $service = new IdentityService();

        // $usuarioAtual = $service->get(
        //     $request->getSession(),
        //     $identity
        // );

        /*
         * Disponibiliza a identidade efetiva para a requisição.
         */
        // $this->set('identity', $usuarioAtual);
        $this->set('identity', $identity);

        // $this->request = $request->withAttribute(
        //     'identity',
        //     $usuarioAtual
        // );

        /*
         * Dados utilizados pelas views.
         */
        $usuario_unid_escolares = $this->fetchTable('UsuarioUnidEscolares');
        $tp_usuarios = $this->fetchTable('TpUsuarios');
        $unid_escolares_vw = $this->fetchTable('UnidEscolaresVw');

        if (isset($identity)){
            $funcao = $tp_usuarios->get($identity->tp_usuarios_id);
            $this->set(compact('funcao'));
        }

        

        $escola_usuarios = [];

        // if (
        //     $usuarioAtual->tp_usuarios_id == 2 ||
        //     $usuarioAtual->tp_usuarios_id == 1 ||
        //     $usuarioAtual->tp_usuarios_id == 5
        // ) {

        //     $usuario_unidades = $usuario_unid_escolares
        //         ->find()
        //         ->where([
        //             'usuario_id' => $usuarioAtual->id
        //         ])
        //         ->all();

        //     foreach ($usuario_unidades as $escola_user) {
        //         $escola_usuarios[] = $escola_user->unid_escolares_id;
        //     }

        //     $escolas = $unid_escolares_vw
        //         ->find(
        //             'list',
        //             keyField: 'id',
        //             valueField: 'nm_unid_escolar'
        //         )
        //         ->where([
        //             'id IN' => $escola_usuarios
        //         ])
        //         ->orderBy([
        //             'nm_unid_escolar' => 'ASC'
        //         ])
        //         ->toArray();

        //     $this->set(compact('escolas'));
        // }

        

        /*
         * Anos disponíveis.
         */
        $anoAtual = date('Y');

    //     if (
    //         $usuarioAtual->tp_usuarios_id == 1 ||
    //         $usuarioAtual->tp_usuarios_id == 5 ||
    //         $usuarioAtual->tp_usuarios_id == 2
    //     ) {

    //         if ($anoAtual > 2026) {
    //             $years = array_combine(
    //                 range($anoAtual, 2016, -1),
    //                 range($anoAtual, 2016, -1)
    //             );
    //         } else {
    //             $years = '----';
    //         }
    //     } else {

    //         $years = array_combine(
    //             range($anoAtual, 2016, -1),
    //             range($anoAtual, 2016, -1)
    //         );
    //     }

    //     $this->set(compact('years'));
    }

    public function isAuthorized($identity)
    {
        if (isset($identity['tp_usuarios_id']) && $identity['tp_usuarios_id'] == 2) {
            return true;
        }

        return false;
    }
}
