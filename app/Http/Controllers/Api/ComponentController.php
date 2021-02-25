<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComponentResource;
use App\Models\Component;
use App\Providers\RouteServiceProvider;
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

        $component = Component::firstOrNew(['name' => $request->post('name')]);
        $component->name = $request->post('name');
        $component->tier = $request->post('tier');

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
            'components' => ComponentResource::collection(\App\Models\Component::all())
        ]);
    }
}
