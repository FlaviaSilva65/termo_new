<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SetorSupervisores Model
 *
 * @property \App\Model\Table\SetoresTable&\Cake\ORM\Association\BelongsTo $Setores
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\SetorSupervisore newEmptyEntity()
 * @method \App\Model\Entity\SetorSupervisore newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\SetorSupervisore> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SetorSupervisore get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\SetorSupervisore findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\SetorSupervisore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\SetorSupervisore> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SetorSupervisore|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\SetorSupervisore saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\SetorSupervisore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorSupervisore>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SetorSupervisore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorSupervisore> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SetorSupervisore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorSupervisore>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SetorSupervisore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SetorSupervisore> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SetorSupervisoresTable extends Table
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

        $this->setTable('setor_supervisores');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Setores', [
            'foreignKey' => 'setores_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
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
            ->integer('setores_id')
            ->notEmptyString('setores_id');

        $validator
            ->integer('usuario_id')
            ->notEmptyString('usuario_id');

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
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
