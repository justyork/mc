<?php

use App\Http\Controllers\Api\ComponentController;
use App\Http\Resources\ComponentResource;
use App\Models\Metal;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'components' => ComponentResource::collection(\App\Models\Component::orderBy('name')->get()),
        'executes' => \App\Models\Execute::orderBy('name')->get(),
        'metals' => Metal::orderBy('name')->get(),
        'types' => \App\Models\ResourceType::orderBy('name')->get(),
    ]);
});

Route::put('/', [ComponentController::class, 'store']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
