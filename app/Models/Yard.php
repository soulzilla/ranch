<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class YardResource
 * @package App\Models
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Sheep[] $sheep
 */
class Yard extends Model
{
    public function sheep()
    {
        return $this->hasMany(Sheep::class, 'yard_id', 'id')->orderBy('id');
    }
}
