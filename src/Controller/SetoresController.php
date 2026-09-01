<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;

/**
 * Setores Controller
 *
 * @property \App\Model\Table\SetoresTable $Setores
 */
class SetoresController extends AppController
{
    public function add()
    {
        $this->Authorization->skipAuthorization();
        $setores = $this->Setores->newEmptyEntity();
        if ($this->request->is('post')){
            $setores = $this->Setores->patchEntity($setores, 
            $this->request->getData());
            if ($this->Setores->save($setores)){
                $this->Flash->success('Setor salvo');

                return $this->redirect($this->referer());

            }
            $this->Flash->error('Não foi possível salvar o setor.');
        }
        $this->set(compact('setores'));
    }
}