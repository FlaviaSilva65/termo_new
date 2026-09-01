<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class EscolasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setConnection(ConnectionManager::get('bdescola'));
        $this->setPrimaryKey('id_escola');

        $this->hasOne('UnidEscolares', [
            'foreignKey' => 'email',
            'bindingKey' => 'email',
            'strategy' => 'select'
        ]);


        // $this->belongsTo('Escolas', [
        //     'foreignKey' => 'escola_id',
        //     'joinType' => 'INNER',
        // ]);
    }
}
