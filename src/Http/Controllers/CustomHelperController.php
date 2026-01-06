<?php

namespace Mainul\CustomHelperFunctions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller;

class CustomHelperController extends Controller
{
    public function symlink()
    {
        Artisan::call('storage:link');
        echo Artisan::output();
    }
    public function optimizeReset()
    {
        Artisan::call('optimize:clear');
        return Artisan::output();
    }
    public function phpinfo()
    {
        phpinfo();
    }
}
