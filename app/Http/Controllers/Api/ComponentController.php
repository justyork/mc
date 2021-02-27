<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComponentResource;
use App\Models\Component;
use App\Models\Metal;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ComponentController extends Controller
{

    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $name = $request->post('name');
        $typeId = $request->post('typeId');
        $metalId = $request->post('metalId');
        $tier = $request->post('tier');

        if ($name) {
            $component = Component::firstOrNew(['name' => $request->post('name'), 'tier' => $tier]);
        } else {
            $component = Component::where('type_id', $typeId)->where('metal_id', $metalId)->firstOrNew();
        }

        $component->name = $request->post('name');
        $component->type_id = $typeId;
        $component->metal_id = $metalId;
        $component->tier = $tier;

        if ($component->exists()) {
            $component->elements()->delete();
        }

        try {
            $component->saveOrFail();

            if ($request->post('components')) {
                foreach ($request->post('components') as $el) {
                    $component->addElement($el['component_id'], $el['count']);
                }
            }
        } catch (\Exception $e){

        }
        return Inertia::render('Welcome', [
            'components' => ComponentResource::collection(\App\Models\Component::all()),
            'metals' => Metal::orderBy('name')->get(),
            'types' => \App\Models\ResourceType::orderBy('name')->get(),
        ]);
    }
}
