<?php

namespace App\Http\Controllers;

use App\Models\Poi;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class PoiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = '';
        $my = (bool) $request->input('my');
        $bounds = $request->input('bounds');
        $alreadyLoaded =  $request->input('alreadyLoaded');
        $types = $request->input('types');
        $perPage = $request->input('perPage');
        $page = $request->input('page');
        $tag = $request->input('tag');

        $cacheKey = md5('pois'. json_encode($bounds) . json_encode($types) . $page . $tag);
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            if ($bounds) {
                $bounds = explode(',', $bounds);
                $presicion = 2;
                $nelat = (float) round($bounds[0], $presicion);
                $nelng = (float) round($bounds[1], $presicion);
                $swlat = (float) round($bounds[2], $presicion);
                $swlng = (float) round($bounds[3], $presicion);
                if ($nelng < 0) $nelng = 180;
            }
    
            $pois = Poi::select('poi.*')->where('show', '=', 1);
            
            if ($bounds) {
                $centerLng = ($swlng + $nelng) / 2;
                $centerLat = ($swlat + $nelat) / 2;
                $pois->where('poi.lng', '<', max($swlng, $nelng))
                    ->where('poi.lng', '>', min($swlng, $nelng))
                    ->where('poi.lat', '<', max($nelat, $swlat))
                    ->where('poi.lat', '>', min($nelat, $swlat));
                if ($alreadyLoaded) {
                    $pois->whereNotIn('id', explode(',', $alreadyLoaded));
                }
                $pois->select(
                    '*', 
                    DB::raw("ABS(poi.lat - $centerLat) + ABS(poi.lng - $centerLng) AS fromcenter")
                );
                $pois->orderBy('fromcenter', 'desc')->limit(100);
                
            } else {
                $pois->orderBy('views', 'DESC');
            }
    
            if ($tag) {     
                $pois->join('relationship', 'poi.id', '=', 'relationship.POSTID');
                $pois->join('tags', 'relationship.TAGID', '=', 'tags.id');
                $pois->where('tags.url', '=', $tag);
                $pois->with('tags');
                // $pois->select('tags.lat AS taglat', 'tags.lng AS taglng');
            }
    
            if (Auth::check() && $my) {
                $pois->where('author', '=', auth()->user()->olduser->username);
            }
    
            if ($types) {
                $pois->whereIn('type', $types);
            }

            
            
            if ($page) {
                $result =  PoiResource::collection($pois->paginate($perPage));
            } elseif ($bounds) {
                $result =  PoiResource::collection($pois->get());
            } else {
                $result =  PoiResource::collection($pois->limit(4)->get()); 
            }

            Cache::put($cacheKey, $result, 3600);
        }
        return $result;
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
    public function show(Poi $poi)
    {
        return new PoiResource($poi);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function edit(Poi $poi)
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
    public function update(Request $request, Poi $poi)
    {
        if (auth()->user()->olduser->username === $poi->author) {
            $poi->update($request->only($poi->getFillable()));
        }
        $tags = $request->input('tags');
        if ($tags) {
            $poi->tags()->sync($tags);
        }
        return new PoiResource($poi);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poi $poi)
    {
        //
    }
}
