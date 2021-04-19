<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class History
 * @package App\Models
 *
 * @property int $id
 * @property int $total_sheep_count
 * @property int $killed_sheep_count
 * @property int $alive_sheep_count
 * @property int $most_populated_yard_id
 * @property int $less_populated_yard_id
 *
 * @property string $created_at
 * @property string $updated_at
 */
class History extends Model
{

}
