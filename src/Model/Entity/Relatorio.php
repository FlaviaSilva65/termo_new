<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Relatorio Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $nm_atendido
 * @property int $funcoes_id
 * @property int $unid_escolar_id
 * @property \Cake\I18n\Date $data
 * @property int $ic_acompanhado
 * @property string|null $ds_acompanhado
 * @property string|null $ds_organizacao
 * @property string|null $ds_higiene
 * @property string|null $ds_manutencao
 * @property string|null $ds_legislacao_documentos
 * @property string|null $ds_obs_geral
 * @property int|null $ic_prioridade
 * @property int|null $ic_responsavel
 * @property int|null $responsavel_id
 * @property int|null $ic_rascunho
 * @property int|null $id_ass_super
 * @property int|null $id_ass_dir
 * @property int|null $id_ass_sub
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\Funco $funco
 * @property \App\Model\Entity\OcorrenciaRelatorio[] $ocorrencia_relatorios
 */
class Relatorio extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'termo_id' => true,
        'usuario_id' => true,
        'nm_atendido' => true,
        'funcoes_id' => true,
        'unid_escolar_id' => true,
        'data' => true,
        'ic_acompanhado' => true,
        // 'ds_acompanhado' => true,
        // 'tipo_organizacao' => true,
        // 'tipo_higiene' => true,
        // 'ds_organizacao' => true,
        // 'ds_higiene' => true,
        // 'ds_manutencao' => true,
        // 'ds_legislacao_documentos' => true,
        'ds_obs_geral' => true,
        'ic_prioridade' => true,
        'ic_responsavel' => true,
        'responsavel_id' => true,
        'ic_rascunho' => true,
        'ic_cancelado' => true,
        'id_ass_super' => true,
        'data_ass_super' => true,
        'id_ass_dir' => true,
        'data_ass_dir' => true,
        'id_ass_sub' => true,
        'data_ass_sub' => true,
        'id_ass_assis' => true,
        'data_ass_assis' => true,
        'created' => true,
        'modified' => true,
        'usuario' => true,
        'funco' => true,
        'ocorrencia_relatorios' => true,
        'respostas' => true
    ];

    protected function _getSituacao()
    {
        if ($this->ic_rascunho == 1) {
            return 1 ;
        }

        if (
            !empty($this->id_ass_super) &&
            !empty($this->id_ass_dir) &&
            !empty($this->id_ass_sub) &&
            !empty($this->id_ass_assis)
        ) {
            return 2;
        }

        return 3;
    }
}
