<?php

namespace App\Http\Controllers;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $all = $request->input('all');
        if ($all) {
            return response()->json(Tag::select('id as value', 'name as text')->where('type', 0)->get());
        }
        return 0;
    }

    public function tagsMenu() {
        $result = null;
        $cacheKey = md5('tagsMenu');
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            $result = TagResource::collection(Tag::where('TYPE','=',0)->take(25)->orderBy('COUNT', 'DESC')->get());
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($url)
    {
        $tag = Tag::where('url', '=', $url)->firstOrFail();
        $tag->locations = array_merge(
            $tag->getParents(), 
            [['id' => $tag->id, 'name' => $tag->name, 'url' => '']] // adding last breadcrumb without link
        );

        $tag->children = $tag->getChildren();
 
        // dump($tag->toArray());
        // return;

        return new TagResource($tag);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
