<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Sheep
 * @package App\Models
 *
 * @property int $id
 * @property int $yard_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Sheep extends Model
{
    use SoftDeletes;

    public function yard()
    {
        return $this->hasOne(Yard::class, 'id', 'yard_id');
    }
}
