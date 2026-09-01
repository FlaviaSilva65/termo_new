<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * UnidEscolares Controller
 *
 * @property \App\Model\Table\UnidEscolaresTable $UnidEscolares
 */
class UnidEscolaresController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();

        $usuario = $this->request->getAttribute('identity')->getIdentifier();

        // debug($usuario);




        // SE O TP_USUARIO_ID FOR = 2 É SUPERVISOR , E AÍ FAZ UMA BUSCA DE TODAS AS ESCOLAS DO SETOR DESSE USUÁRIO 



        

        $setores = $this->UnidEscolares->UsuarioUnidEscolares->find('all')
            ->where(['usuario_id' => $usuario]);
            
        // ->toArray();
        // debug($setores);

        // foreach ($setores as $setor):
        //     debug($setor);
        // endforeach;
        // // debug($this->request->getAttribute('identity')->get('tp_usuarios_id'));
        // die;

        $query = $this->UnidEscolares->find();
        $unidEscolares = $this->paginate($query, ['limit' => 30]);

        $this->set(compact('unidEscolares', 'setores'));
    }

    /**
     * View method
     *
     * @param string|null $id Unid Escolare id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $unidEscolare = $this->UnidEscolares->get($id, contain: []);
        $this->set(compact('unidEscolare'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $unidEscolare = $this->UnidEscolares->newEmptyEntity();
        if ($this->request->is('post')) {
            $unidEscolare = $this->UnidEscolares->patchEntity($unidEscolare, $this->request->getData());
            if ($this->UnidEscolares->save($unidEscolare)) {
                $this->Flash->success(__('The unid escolare has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unid escolare could not be saved. Please, try again.'));
        }
        $this->set(compact('unidEscolare'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Unid Escolare id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->Authorization->skipAuthorization();
        $unidEscolares = $this->UnidEscolares->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $unidEscolares = $this->UnidEscolares->patchEntity($unidEscolares, $this->request->getData());

            // debug($this->request->getData());
            // die;

            if ($this->UnidEscolares->save($unidEscolares)) {
                $this->Flash->success(__('Unid Escolar alterada.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('A Unid escolar, não pode ser alterada. Tente novamente.'));
        }
        $setores = $this->UnidEscolares->Setores->find('list');
        $this->set(compact('unidEscolares', 'setores'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Unid Escolare id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $unidEscolare = $this->UnidEscolares->get($id);

        if ($this->UnidEscolares->delete($unidEscolare)) {
            $this->Flash->success(__('The unid escolare has been deleted.'));
        } else {
            $this->Flash->error(__('A Unid escolar, não pode ser excluída. Tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->disableAutoLayout(false);
        $keyword = $this->request->getQuery('keyword');

        if ($keyword != '') {
            $query = $this->UnidEscolares->find()
                ->where(['Upper(nm_unid_escolar) LIKE' => '%' . mb_strtoupper($keyword) . '%']);
            // ->contain(['TpUsuarios']);
        }

        $this->set('unidEscolares', $this->paginate($query, ['limit' => 20]));
    }
}
