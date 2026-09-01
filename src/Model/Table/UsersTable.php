<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;

class UsersTable extends Table
{
public function initialize(array $config): void
{
    parent::initialize($config);
    $this->setConnection(ConnectionManager::get('bdescola'));

    $this->belongsTo('Escolas', [
        'foreignKey' => 'escola_id',
        'joinType' => 'INNER',
    ]);
}
}