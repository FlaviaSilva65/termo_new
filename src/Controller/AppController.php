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

        $identity = $this->Authentication->getIdentity();

        if ($identity) {

            $usuario = $this->Authentication->getIdentity();

            $usuario_unid_escolares = $this->fetchTable('UsuarioUnidEscolares');
            $tp_usuarios = $this->fetchTable('TpUsuarios');
            $unid_escolares = $this->fetchTable('UnidEscolares');
            $unid_escolares_vw = $this->fetchTable('UnidEscolaresVw');

            $funcao = $tp_usuarios->get($usuario->tp_usuarios_id);

            // $setor = $usuario_unid_escolares->find()->where(['usuario_id' => $usuario->id ?: 0])->all()->last();
            // $usuario_lists = $this->fetchTable('Usuarios');

            $escola_usuarios = [];

            // if ($usuario->tp_usuarios_id == 2) {
            //     // $setor_supervisor = TableRegistry::getTableLocator()->get('SetorSupervisores');
            //     $setor_supervisor = $this->fetchTable('SetorSupervisores');
            //     // $unid_escolares_vw = TableRegistry::getTableLocator()->get('UnidEscolaresVw');
            //     // $unid_escolares = TableRegistry::getTableLocator()->get('UnidEscolares');
            //     $setores = $setor_supervisor->find()->where(['usuario_id' => $usuario->id])->all();

            //     if (count($setores) === 0) {
            //         // $this->Authentication->logout();

            //         // $this->Flash->error('Supervisor não cadastrado.');
            //         $this->redirect($this->referer());
            //     } else {


            //         $setor_id = $setores->last();

            //         $escolas = $unid_escolares->find('list', keyField: 'id', valueField: 'nm_unid_escolar')
            //             ->where(['setores_id' => $setor_id->setores_id, 'ativo is' => null])
            //             ->orderBy(['nm_unid_escolar' => 'ASC'])
            //             // ->orderBy(function ($exp, $query){
            //             //     return $exp->add("REPLACE(nm_unid_escolar, 'E.M. ', '') ASC");
            //             // })
            //             // (['nm_unid_escolar' => 'ASC'])
            //             ->toArray();

            //         $this->set(compact('escolas'));
            //     }
            if ($usuario->tp_usuarios_id == 2 || $usuario->tp_usuarios_id == 1 || $usuario->tp_usuarios_id == 5) {

                $usuario_unidades = $usuario_unid_escolares->find()->where(['usuario_id' => $usuario->id])->all();

                if (count($usuario_unidades) > 0) {

                    foreach ($usuario_unidades as $escola_user):
                        $escola_usuarios[] = $escola_user->unid_escolares_id;
                    endforeach;
                }

                $escolas = $unid_escolares_vw->find('list', keyField: 'id', valueField: 'nm_unid_escolar')
                    ->where(['id IN' => $escola_usuarios])->orderBy(function ($exp, $query) {
                        return $exp->add("REPLACE(nm_unid_escolar, 'E.M. ', '') ASC");
                    })->orderBy(['nm_unid_escolar' => 'ASC'])
                    ->toArray();
                $this->set(compact('escolas'));
                // } elseif ($usuario->tp_usuarios_id == 9) {

                //     $supervisores = $usuario_lists->find('list', keyField: 'id', valueField: 'nm_usuario')
                //         ->where(['tp_usuarios_id' => 2, 'ic_ativo' => 1])
                //         ->toArray();

                //     $this->set(compact('supervisores'));
            }

            // $this->set(compact('escolas'));
            $this->set(compact('funcao'));
        };

        $this->set('identity', $identity);

        if ($this->request->is('post')) {
            // debug($this->request->getData());
            // die;

            $ano_search = $this->request->getData('ano_search');
            $escola_id = $this->request->getData('escola_id');

            if ($ano_search != '' && $escola_id != '') {
                $this->redirect(['action' => 'dashRelatoriosAnteriores', $ano_search, $escola_id]);
            }
        }

        // if ($this->Authentication->getResult()->isValid()) {
        //     $usuario = true;
        //     $this->set(compact('usuario'));
        // }

        $anoAtual = date('Y');

        if ($identity && ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5 || $identity->tp_usuarios_id == 2)) {
            // $years = 2026;
            if ($anoAtual > 2026) {
                $years = [];
                $years = array_combine(range($anoAtual, 2016, -1), range($anoAtual, 2016, -1));
            } else {
                $years = '----';
            }
        } else {
            $years = [];
            $years = array_combine(range($anoAtual, 2016, -1), range($anoAtual, 2016, -1));
        }

        // $ano_search = [2016,2017,2018];
        $this->set(compact('years'));

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $identity = $this->Authentication->getIdentity();

        if (!$identity) {
            return;
        }

        $service = new IdentityService();

        $usuarioAtual = $service->get(
            $this->request->getSession(),
            $identity
        );

        $this->request = $this->request->withAttribute(
            'identity',
            $usuarioAtual
        );

    }

    public function isAuthorized($identity)
    {
        if (isset($identity['tp_usuarios_id']) && $identity['tp_usuarios_id'] == 2) {
            return true;
        }

        return false;
    }
}
