<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SetorUnidEscolares Model
 *
 * @property \App\Model\Table\UnidEscolaresTable&\Cake\ORM\Association\BelongsTo $UnidEscolares
 *
 * @method \App\Model\Entity\SetorUnidEscolare newEmptyEntity()
 * @method \App\Model\Entity\SetorUnidEscolare newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\SetorUnidEscolare> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SetorUnidEscolare get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\SetorUnidEscolare findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\SetorUnidEscolare patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\SetorUnidEscolare> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SetorUnidEscolare|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\SetorUnidEscolare saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\SetorUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorUnidEscolare>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SetorUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorUnidEscolare> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SetorUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorUnidEscolare>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SetorUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorUnidEscolare> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SetorUnidEscolaresTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('setor_unid_escolares');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('UnidEscolares', [
            'foreignKey' => 'unid_escolares_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Setores', [
            'foreignKey' => 'setores_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('setores_id')
            ->notEmptyString('setores_id');

        $validator
            ->integer('unid_escolares_id')
            ->notEmptyString('unid_escolares_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['setores_id'], 'Setores'), ['errorField' => 'setores_id']);
        $rules->add($rules->existsIn(['unid_escolares_id'], 'UnidEscolares'), ['errorField' => 'unid_escolares_id']);

        return $rules;
    }
}
