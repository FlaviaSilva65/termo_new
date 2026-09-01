<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;

class FuncionariosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setConnection(ConnectionManager::get('dashescola'));
        // $this->setPrimaryKey('id_escola');


        //** VER a associação  */

        $this->hasMany('Dashboards', [
            'foreignKey' => 'funcionario_id'
        ]);

        // $this->belongsTo('Escolas', [
        //     'foreignKey' => 'escola_id',
        //     'joinType' => 'INNER',
        // ]);

    }
}