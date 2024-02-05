<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckApiGroupAccess
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->route()->uri();
        $method = $request->route()->methods()[0];

        foreach (auth()->apps() as $myApp) {
            $permissions = Cache::rememberForever('app_permission_' . $myApp, function () use ($myApp) {
                $permissions = collect();
                $app = App::where(['uuid' => $myApp])->first();
                if ($app) {
                    $apiGroups = $app->groupApis;
                    foreach ($apiGroups as $apiGroup) {
                        $apiLists = $apiGroup->api_lists;
                        $pluckedData = $apiLists->map(function ($item) {
                            return [
                                'path' => $item->path,
                                'method' => $item->method,
                            ];
                        });
                        $permissions = $permissions->merge($pluckedData);
                    }
                }

                return $permissions;
            });
            if ($permissions->contains(['path' => $uri, 'method' => $method])) {

                return $next($request);
            }
        }
//
//        //get app need to buy
//        $apps = Cache::rememberForever('all_app', function () use ($uri, $method) {
//
//            return App::all();
//        });
//        $message = ['plan' => 'Does not have app/add-on for this feature. Comeback Later!!'];
//        foreach ($apps as $app) {
//            $permissions = collect();
//            $apiGroups = $app->groupApis;
//            foreach ($apiGroups as $apiGroup) {
//                $apiLists = $apiGroup->api_lists;
//                $pluckedData = $apiLists->map(function ($item) {
//                    return [
//                        'path' => $item->path,
//                        'method' => $item->method,
//                    ];
//                });
//                $permissions = $permissions->merge($pluckedData);
//            }
//            if ($permissions->contains(['path' => $uri, 'method' => $method])) {
//                $message = ['plan' => 'platform_package_' . $app->uuid];
//            }
//        }

        return response()->json([
            'status' => false,
            'locale' => app()->getLocale(),
            'message' => __('messages.unauthorized'),
        ], 403);
    }
}
