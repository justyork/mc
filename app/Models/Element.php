<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Element
 * @package App\Models
 * @property float $count
 * @property int $component_id
 * @property int $id
 * @property int $base_component_id
 *
 */
class Element extends Model
{
    use HasFactory;

    protected $table = 'component_elements';

    public $timestamps = false;
}
