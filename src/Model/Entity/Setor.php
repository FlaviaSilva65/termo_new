<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setor Entity
 * 
 * @property int $id
 * @property string nm_setor
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 */

 class Setor extends Entity
 {
    protected array $_accessible = [
        'nm_setor' => true,
        'created' => true,
        'modified' => true
    ];
    
 }