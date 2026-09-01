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
 * @property int|null $ic_acompanhado
 * @property string|null $ds_acompanhado
 * @property int|null $ic_organizacao
 * @property string|null $ds_organizacao
 * @property int|null $ic_higiene
 * @property string|null $ds_higiene
 * @property int|null $ic_hidraulica
 * @property int|null $ic_eletrica
 * @property int|null $ic_pintura
 * @property int|null $ic_marcenaria
 * @property int|null $ic_serralheria
 * @property int|null $ic_alvenaria
 * @property int|null $ic_vidracaria
 * @property int|null $ic_lousa_digital
 * @property int|null $ic_chrome
 * @property int|null $ic_informatica
 * @property int|null $ic_equipamentos
 * @property int|null $ic_mobiliario
 * @property int|null $ic_outros
 * @property string|null $ds_manutencao
 * @property int|null $ic_sed
 * @property int|null $ic_pront_prof
 * @property int|null $ic_pront_func
 * @property int|null $ic_pl_gestao
 * @property int|null $ic_reg_vid_escolar
 * @property int|null $ic_exerc_domiciliar
 * @property int|null $ic_hist_escolar
 * @property int|null $ic_balancete_apm
 * @property int|null $ic_atend_domiciliar
 * @property int|null $ic_ouvidoria
 * @property int|null $ic_quadro_pah
 * @property int|null $ic_leg_doc
 * @property int|null $ic_reclassificacao
 * @property int|null $ic_classificacao
 * @property int|null $ic_livro_atas
 * @property int|null $ic_equivalencia_estudos
 * @property int|null $ic_diario_on_line
 * @property int|null $ic_consolidado
 * @property int|null $ic_conselho_classe
 * @property int|null $ic_del_cme_001_18
 * @property int|null $ic_ata_result_finais
 * @property int|null $ic_ficha_ind
 * @property string|null $ds_legislacao_documentos
 * @property string|null $ds_obs_geral
 * @property int|null $ic_prioridade
 * @property int|null $ic_responsavel
 * @property int|null $responsavel_id
 * @property int|null $ic_rascunho
 * @property int|null $id_ass_super
 * @property int|null $id_ass_dir
 * @property int|null $id_ass_sub
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\Funco $funco
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
        'usuario_id' => true,
        'nm_atendido' => true,
        'funcoes_id' => true,
        'unid_escolar_id' => true,
        'data' => true,
        'ic_acompanhado' => true,
        'ds_acompanhado' => true,
        'ic_organizacao' => true,
        'ds_organizacao' => true,
        'ic_higiene' => true,
        'ds_higiene' => true,
        'ic_hidraulica' => true,
        'ic_eletrica' => true,
        'ic_pintura' => true,
        'ic_marcenaria' => true,
        'ic_serralheria' => true,
        'ic_alvenaria' => true,
        'ic_vidracaria' => true,
        'ic_lousa_digital' => true,
        'ic_chrome' => true,
        'ic_informatica' => true,
        'ic_equipamentos' => true,
        'ic_mobiliario' => true,
        'ic_outros' => true,
        'ds_manutencao' => true,
        'ic_sed' => true,
        'ic_pront_prof' => true,
        'ic_pront_func' => true,
        'ic_pl_gestao' => true,
        'ic_reg_vid_escolar' => true,
        'ic_exerc_domiciliar' => true,
        'ic_hist_escolar' => true,
        'ic_balancete_apm' => true,
        'ic_atend_domiciliar' => true,
        'ic_ouvidoria' => true,
        'ic_quadro_pah' => true,
        'ic_leg_doc' => true,
        'ic_reclassificacao' => true,
        'ic_classificacao' => true,
        'ic_livro_atas' => true,
        'ic_equivalencia_estudos' => true,
        'ic_diario_on_line' => true,
        'ic_consolidado' => true,
        'ic_conselho_classe' => true,
        'ic_del_cme_001_18' => true,
        'ic_ata_result_finais' => true,
        'ic_ficha_ind' => true,
        'ds_legislacao_documentos' => true,
        'ds_obs_geral' => true,
        'ic_prioridade' => true,
        'ic_responsavel' => true,
        'responsavel_id' => true,
        'ic_rascunho' => true,
        'created' => true,
        'modified' => true,
        'usuario' => true,
        'funco' => true,
    ];
}
