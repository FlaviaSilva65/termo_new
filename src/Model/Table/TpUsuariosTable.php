<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TpUsuarios Model
 *
 * @method \App\Model\Entity\TpUsuario newEmptyEntity()
 * @method \App\Model\Entity\TpUsuario newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\TpUsuario> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TpUsuario get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\TpUsuario findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\TpUsuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\TpUsuario> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TpUsuario|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\TpUsuario saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\TpUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TpUsuario>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TpUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TpUsuario> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TpUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TpUsuario>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TpUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TpUsuario> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TpUsuariosTable extends Table
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

        $this->setTable('tp_usuarios');
        $this->setDisplayField('nm_tp_usuarios');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('nm_tp_usuarios')
            ->maxLength('nm_tp_usuarios', 50)
            ->requirePresence('nm_tp_usuarios', 'create')
            ->notEmptyString('nm_tp_usuarios');

        return $validator;
    }
}
