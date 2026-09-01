<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * SetorUnidEscolares Controller
 *
 * @property \App\Model\Table\SetorUnidEscolaresTable $SetorUnidEscolares
 */
class SetorUnidEscolaresController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->SetorUnidEscolares->find('all', contain: ['UnidEscolares']);
        $setorUnidEscolares = $this->paginate($query, ['limit' => 10]);

        $this->set(compact('setorUnidEscolares'));
    }

    public function add()
    {
        $setores_unids = $this->SetorUnidEscolares->Setores->find(
            'all',
            contain: ['SetorUnidEscolares' => ['UnidEscolares']]
        );
        // $setores = $query;

        $setor = $this->SetorUnidEscolares->newEmptyEntity();

        if ($this->request->is('post')) {

            $setor = $this->SetorUnidEscolares->patchEntity($setor, $this->request->getData());

            if ($this->SetorUnidEscolares->save($setor)) {
                $this->Flash->success('Setor Salvo.');

                return $this->redirect($this->referer());
            }
        }

        $setores = $this->SetorUnidEscolares->Setores->find('list');
        $unid_escolares = $this->SetorUnidEscolares->UnidEscolares->find('list');

        $this->set(compact('setor', 'setores_unids', 'setores', 'unid_escolares'));
    }
}
