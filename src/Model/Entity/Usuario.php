<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;

/**
 * Usuario Entity
 *
 * @property int $id
 * @property string $nm_usuario
 * @property string|null $email
 * @property string $username
 * @property string $password
 * @property int $tp_usuarios_id
 * @property int $ic_ativo
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\TpUsuario $tp_usuario
 */
class Usuario extends Entity
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
        'cd_rf' => true,
        'nm_usuario' => true,
        'email' => true,
        // 'username' => true,
        'cd_cpf' => true,
        'password' => true,
        'tp_usuarios_id' => true,
        'cd_assinatura' => true,
        'ic_ativo' => true,
        'ic_master' => true,
        'created' => true,
        'modified' => true,
        'tp_usuarios' => true,
        'usuario_unid_escolare' => true,
        'setor_supervisore' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
    ];

    protected function _setPassword(string $password) : ?string
    {
        // $hasher = new DefaultPasswordHasher();
        // return $hasher->hash($password);
        if (strlen($password) > 0){
            return (new DefaultPasswordHasher())->hash($password);
        }
        return null;
    }
}
