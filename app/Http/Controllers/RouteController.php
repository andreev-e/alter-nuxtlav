<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use App\Http\Resources\RouteResource;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $my = (bool) $request->input('my');
        $bounds = $request->input('bounds');
        if ($bounds) {
            $bounds = explode(',', $bounds);
            $nelng = (float) $bounds[1];
            $swlng = (float) $bounds[3];
            $nelat = (float) $bounds[0];
            $swlat = (float) $bounds[2];
            if ($nelng < 0) $nelng = 180;
        }

        $perPage = $request->input('perPage');
        
        $pois = Route::select('routes.*')->where('show', '=', 1)->orderBy('views', 'DESC');
        
        if ($bounds) {
            $pois->where('routes.lng', '<', max($swlng, $nelng))
                ->where('routes.lng', '>', min($swlng, $nelng))
                ->where('routes.lat', '<', max($nelat, $swlat))
                ->where('routes.lat', '>', min($nelat, $swlat))
                ->limit(30);
            $pois->with('tags');
            // dump($pois->limit(5)->get()->toArray());
            // return;
        }

        $tag = $request->input('tag');
        if ($tag) {     
            $pois->join('relationship', 'poi.id', '=', 'relationship.POSTID');
            $pois->join('tags', 'relationship.TAGID', '=', 'tags.id');
            $pois->where('tags.url', '=', $tag);
            $pois->with('tags');
            // $pois->select('tags.lat AS taglat', 'tags.lng AS taglng');
        }

        if ($my) {
            $pois->where('author', '=', auth()->user()->olduser->username);
        }

        if ($request->input('page')) {
            return RouteResource::collection($pois->paginate($perPage));
        } elseif ($bounds) {
            return RouteResource::collection($pois->get());
        } else {
            return RouteResource::collection($pois->limit(4)->get()); 
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function show(Route $route)
    {
        return new PoiResource($route);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function edit(Route $route)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Route $route)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Route $route)
    {
        //
    }
}
