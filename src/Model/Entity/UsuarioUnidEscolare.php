<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsuarioUnidEscolare Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $unid_escolares_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\UnidEscolare $unid_escolare
 */
class UsuarioUnidEscolare extends Entity
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
        'unid_escolares_id' => true,
        'setores_id' => true,
        'created' => true,
        'modified' => true,
        'usuario' => true,
        'unid_escolare' => true,
        'setor' => true
    ];
}
