<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page');
        $limit = $request->input('limit') ? $request->input('limit') : 10;

        $cacheKey = 'users_' . md5(json_encode($request->all()));
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            $list = User::select(
                'authors.id', 
                'users.*',
            )
                ->rightJoin('users', 'authors.email', '=', 'users.email')
                ->where('users.publications', '>', 0)
                ->orderBy('users.publications', 'DESC');
            $result = UserResource::collection($list->paginate($limit));
            Cache::put($cacheKey, $result, 3600);
        }
        return $result;
    }
}
