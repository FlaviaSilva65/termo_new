<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Relatorios Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\FuncoesTable&\Cake\ORM\Association\BelongsTo $Funcoes
 *
 * @method \App\Model\Entity\Relatorio newEmptyEntity()
 * @method \App\Model\Entity\Relatorio newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Relatorio> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Relatorio get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Relatorio findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Relatorio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Relatorio> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Relatorio|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Relatorio saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Relatorio>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Relatorio>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Relatorio>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Relatorio> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Relatorio>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Relatorio>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Relatorio>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Relatorio> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RelatoriosTable extends Table
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

        $this->setTable('relatorios');
        $this->setDisplayField('nm_atendido');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Funcoes', [
            'foreignKey' => 'funcoes_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('UnidEscolares', [
            'foreignKey' => 'unid_escolar_id',
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
            ->scalar('nm_atendido')
            ->maxLength('nm_atendido', 100)
            ->notEmptyString('nm_atendido');

        $validator
            ->integer('funcoes_id')
            ->notEmptyString('funcoes_id');

        $validator
            ->integer('unid_escolar_id')
            ->requirePresence('unid_escolar_id', 'create')
            ->notEmptyString('unid_escolar_id');

        $validator
            ->date('data')
            ->requirePresence('data', 'create')
            ->notEmptyDate('data');

        $validator
            ->allowEmptyString('ic_acompanhado');

        $validator
            ->scalar('ds_acompanhado')
            ->allowEmptyString('ds_acompanhado');

        $validator
            ->allowEmptyString('ic_organizacao');

        $validator
            ->scalar('ds_organizacao')
            ->allowEmptyString('ds_organizacao');

        $validator
            ->allowEmptyString('ic_higiene');

        $validator
            ->scalar('ds_higiene')
            ->allowEmptyString('ds_higiene');

        $validator
            ->allowEmptyString('ic_hidraulica');

        $validator
            ->allowEmptyString('ic_eletrica');

        $validator
            ->allowEmptyString('ic_pintura');

        $validator
            ->allowEmptyString('ic_marcenaria');

        $validator
            ->allowEmptyString('ic_serralheria');

        $validator
            ->allowEmptyString('ic_alvenaria');

        $validator
            ->allowEmptyString('ic_vidracaria');

        $validator
            ->allowEmptyString('ic_lousa_digital');

        $validator
            ->allowEmptyString('ic_chrome');

        $validator
            ->allowEmptyString('ic_informatica');

        $validator
            ->allowEmptyString('ic_equipamentos');

        $validator
            ->allowEmptyString('ic_mobiliario');

        $validator
            ->allowEmptyString('ic_outros');

        $validator
            ->scalar('ds_manutencao')
            ->allowEmptyString('ds_manutencao');

        $validator
            ->allowEmptyString('ic_sed');

        $validator
            ->allowEmptyString('ic_pront_prof');

        $validator
            ->allowEmptyString('ic_pront_func');

        $validator
            ->allowEmptyString('ic_pl_gestao');

        $validator
            ->allowEmptyString('ic_reg_vid_escolar');

        $validator
            ->allowEmptyString('ic_exerc_domiciliar');

        $validator
            ->allowEmptyString('ic_hist_escolar');

        $validator
            ->allowEmptyString('ic_balancete_apm');

        $validator
            ->allowEmptyString('ic_atend_domiciliar');

        $validator
            ->allowEmptyString('ic_ouvidoria');

        $validator
            ->allowEmptyString('ic_quadro_pah');

        $validator
            ->allowEmptyString('ic_leg_doc');

        $validator
            ->allowEmptyString('ic_reclassificacao');

        $validator
            ->allowEmptyString('ic_classificacao');

        $validator
            ->allowEmptyString('ic_livro_atas');

        $validator
            ->allowEmptyString('ic_equivalencia_estudos');

        $validator
            ->allowEmptyString('ic_diario_on_line');

        $validator
            ->allowEmptyString('ic_consolidado');

        $validator
            ->allowEmptyString('ic_conselho_classe');

        $validator
            ->allowEmptyString('ic_del_cme_001_18');

        $validator
            ->allowEmptyString('ic_ata_result_finais');

        $validator
            ->allowEmptyString('ic_ficha_ind');

        $validator
            ->scalar('ds_legislacao_documentos')
            ->allowEmptyString('ds_legislacao_documentos');

        $validator
            ->scalar('ds_obs_geral')
            ->allowEmptyString('ds_obs_geral');

        $validator
            ->allowEmptyString('ic_prioridade');

        $validator
            ->allowEmptyString('ic_responsavel');

        $validator
            ->integer('responsavel_id')
            ->allowEmptyString('responsavel_id');

        $validator
            ->allowEmptyString('ic_rascunho');

        $validator
            ->allowEmptyString('id_ass_super');

        $validator
            ->allowEmptyString('id_ass_dir');

        $validator
            ->allowEmptyString('id_ass_sub');

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
        $rules->add($rules->existsIn(['funcoes_id'], 'Funcoes'), ['errorField' => 'funcoes_id']);

        return $rules;
    }
}
