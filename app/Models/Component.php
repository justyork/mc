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
 */
class Component extends Model
{
    use HasFactory;

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
}
