<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;

class DashboardsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setConnection(ConnectionManager::get('dashescola'));
        $this->setPrimaryKey('funcionario_id');


        //** VER a associação  */

        // $this->belongsTo('Escolas', [
        //     'foreignKey' => 'escola_id',
        //     'joinType' => 'INNER',
        // ]);

    }
}