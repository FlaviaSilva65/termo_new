<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UsuarioUnidEscolares Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\UnidEscolaresTable&\Cake\ORM\Association\BelongsTo $UnidEscolares
 *
 * @method \App\Model\Entity\UsuarioUnidEscolare newEmptyEntity()
 * @method \App\Model\Entity\UsuarioUnidEscolare newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\UsuarioUnidEscolare> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuarioUnidEscolare get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\UsuarioUnidEscolare findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\UsuarioUnidEscolare patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\UsuarioUnidEscolare> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuarioUnidEscolare|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\UsuarioUnidEscolare saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\UsuarioUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuarioUnidEscolare>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuarioUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuarioUnidEscolare> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuarioUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuarioUnidEscolare>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuarioUnidEscolare>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuarioUnidEscolare> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsuarioUnidEscolaresTable extends Table
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

        $this->setTable('usuario_unid_escolares');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('UnidEscolares', [
            'foreignKey' => 'unid_escolares_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Setores', [
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
            ->integer('usuario_id')
            ->notEmptyString('usuario_id');

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
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);
        $rules->add($rules->existsIn(['unid_escolares_id'], 'UnidEscolares'), ['errorField' => 'unid_escolares_id']);

        return $rules;
    }
}
