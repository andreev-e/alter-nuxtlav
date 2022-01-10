<?php

namespace App\Http\Controllers;

use App\Models\Poi;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResourceLight;
use App\Http\Resources\PoiResourceFull;
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
        $page = $request->input('page');
        $tag = $request->input('tag');
        $only = $request->input('only');
        $additional = $request->input('additional');
        $otdalenie = $request->input('otdalenie');
        $author = $request->input('author');
        $sort = $request->input('sort');
        $direction = $request->input('direction');
        $limit = $request->input('limit') ? $request->input('limit') : 10;

        if ($only || $otdalenie) {
            $bounds = null;
        }

        $cacheKey = 'pois_' . md5(json_encode($request->all()));
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            if ($bounds) {
                $bounds = explode(',', $bounds);
                $presicion = 4;
                $nelat = (float) round($bounds[0], $presicion);
                $nelng = (float) round($bounds[1], $presicion);
                $swlat = (float) round($bounds[2], $presicion);
                $swlng = (float) round($bounds[3], $presicion);
            }
    
            $pois = Poi::select(
                'poi.id',
                'poi.name',
                'poi.type',
                'poi.lat',
                'poi.lng',
                'poi.author',
                'poi.views',
                'poi.comments',
                )
                ->where('show', '=', 1)->where('poi.lat', '<>', 0);

            if ($additional && $otdalenie) {
                $additional = explode('!', $additional);
                $pois->where(function ($where) use ($additional, $otdalenie) {
                    foreach ($additional as $point) {
                        $point = explode(";", $point);
                        $where->orWhereRaw('(6371 *
                        acos(
                            cos(radians(' . $point[0] . '))
                         * cos(radians(lat))
                         *cos(radians(lng) - radians(' . $point[1] . '))
                         + sin(radians( ' .$point[0] . '))
                         * sin(radians(lat)))
                               ) < ' . $otdalenie . '');
                        }
                    });
            }
            
            if ($sort) {
                if ($sort === 'date') {
                    $pois->orderBy($sort, 'desc');
                } elseif ($sort === 'views') {
                    $pois->orderBy($sort, 'desc');
                } elseif ($sort === 'comments') {
                    $pois->orderBy($sort, 'desc');
                }
                
            }

            if ($bounds) {
                $centerLng = ($swlng + $nelng) / 2;
                $centerLat = ($swlat + $nelat) / 2;
                if ($centerLng === 0 || $centerLat === 0) {
                    return ;
                }
                $pois->where('poi.lng', '<', max($swlng, $nelng))
                    ->where('poi.lng', '>', min($swlng, $nelng))
                    ->where('poi.lat', '<', max($nelat, $swlat))
                    ->where('poi.lat', '>', min($nelat, $swlat));
                if ($alreadyLoaded) {
                    $pois->whereNotIn('poi.id', explode(',', $alreadyLoaded));
                }

                $pois->select(
                    '*', 
                    DB::raw("SQRT((poi.lat - $centerLat) * (poi.lat - $centerLat) + (poi.lng - $centerLng) * (poi.lng - $centerLng)) AS fromcenter")
                );
                $pois->orderBy('fromcenter', 'asc');
                
            } else {
                $pois->orderBy('views', 'DESC');
            }

            if ($only) {
                $only = explode(',', $only);
                $pois->whereIn('id', $only);
            }
    
            if ($tag) {     
                $pois->join('relationship', 'poi.id', '=', 'relationship.POSTID');
                $pois->join('tags', 'relationship.TAGID', '=', 'tags.id');
                $pois->where('tags.url', '=', $tag);
                $pois->with('tags');
            }
    
            if (Auth::check() && $my) {
                $pois->where('author', '=', auth()->user()->olduser->username);
            }

            if ($author) {
                $pois->where('author', '=', $author);
            }
    
            if ($types) {
                $pois->whereIn('type', $types);
            }
                        
            if ($page) {
                $result =  PoiResourceLight::collection($pois->paginate($limit));
            } else {
                $result =  PoiResourceLight::collection($pois->limit($limit)->get()); 
            }

            Cache::put($cacheKey, $result, 3600);
        }

        // echo '1';
        // return '';
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
        return new PoiResourceFull($poi);
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
        return new PoiResourceFull($poi);
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
