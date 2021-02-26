<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComponentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fullName' => $this->fullName(),
            'metal_id' => $this->metal_id,
            'type_id' => $this->type_id,
            'param' => $this->param,
            'tier' => $this->tier,
            'elements' => $this->elements
        ];
    }
}
