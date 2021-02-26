<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Component
 * @package App\Models
 * @property string $name
 * @property int $tier
 * @property int $id
 * @property int $metal_id
 * @property int $type_id
 * @property string $params
 */
class Component extends Model
{
    use HasFactory;

    public $tiers = [
        'None',
        '8 ULV',
        '32 LV',
        '128 MV',
        '512 HV',
        '2048 EV',
        '8192 IV',
        '32768 LuV',
        '131072 ZPM',
        '524288 UV'
    ];

    public $timestamps = false;
    public $fillable = ['name', 'tier'];

    public function elements()
    {
        return $this->hasMany(Element::class, 'base_component_id', 'id');
    }

    public function addElement($id, $count)
    {
        $element = new Element();
        $element->component_id = $id;
        $element->base_component_id = $this->id;
        $element->count = $count;
        $element->save();
    }

    public function metal()
    {
        return $this->hasOne(Metal::class, 'id', 'metal_id');
    }

    public function type()
    {
        return $this->hasOne(ResourceType::class, 'id', 'type_id');
    }

    public function fullName()
    {
        $tier = ($this->tier ? " ({$this->tiers[$this->tier]})" : '');
        if ($this->name) {
            return $this->name . $tier;
        }

        return $this->metal->name. ' ' . $this->type->name . $tier;
    }
}
