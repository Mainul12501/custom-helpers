<?php

use Illuminate\Support\Facades\Route;
use Mainul\CustomHelperFunctions\Http\Controllers\ExampleController;

/*
|--------------------------------------------------------------------------
| Helper Functions Web Routes
|--------------------------------------------------------------------------
|
| Here you can register web routes for your helper functions package.
| These routes are loaded by the HelperServiceProvider and will be
| assigned to the "web" middleware group automatically.
|
| You can customize the prefix and middleware in the config file:
| config/helper-functions.php
|
*/

Route::middleware(config('helper-functions.routes.web.middleware'))
    ->prefix(config('helper-functions.routes.web.prefix'))
    ->group(function () {
        Route::get('/helper-functions/sample', [ExampleController::class, 'sample']);
        Route::get('/helper-functions/erase-all-cache', [ExampleController::class, 'eraseAll']);
    });

// Add your custom routes here
