<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class DashEscolasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setConnection(ConnectionManager::get('bdescola'));
        $this->setTable('escolas');
        $this->setPrimaryKey('id_escola');

    }
}
