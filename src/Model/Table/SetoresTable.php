<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use CakePHP\Sniffs\WhiteSpace\TabAndSpaceSniff;

/**
 * Setor Model
 * 
 */
class SetoresTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('setores');
        $this->setDisplayField('nm_setor');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SetorUnidEscolares', [
            'foreignKey' => 'setores_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('UnidEscolares',[
            'foreignKey' => 'setores_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     * 
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake|Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('nm_setor')
            ->maxLength('nm_setor', 50)
            ->requirePresence('nm_setor', 'create')
            ->notEmptyString('nm_setor');

        return $validator;
    }
}
