<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ConfigController extends Controller
{
    public function index($key)
    {
        if (!config('app.key')) {
            return response()->json([
                'errors' => "The application key is not set!",
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (config('app.key') !== $key) {
            return response()->json([
                'errors' => "The application key does not match!",
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->getConfig([
            'app' => ['name', 'env', 'debug', 'url', 'key'],
            'database' => [
                'connections' => ['mysql']
            ],
            'filesystems' => [
                'default',
                'cloud',
                'disks' => ['local', 'public', 's3']
            ],
            'logging' => [
                'default',
                'channels' => ['single']
            ],
            'cache' => ['default', 'stores.file'],
            'session',
            'view',
        ]);
    }

    private function getConfig($configs, $parent='')
    {
        if ($parent) {
            $parent = $parent.'.';
        }

        $arr = [];
        foreach($configs as $key=>$config) {
            if (is_array($config)) {
                $arr[$key] = $this->getConfig($config,$parent.$key);
            } else {
                $arr[$config] = config($parent.$config);
            }
        }

        return $arr;
    }
}
