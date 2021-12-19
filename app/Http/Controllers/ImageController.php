<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Poi;
use Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $poi = Poi::findOrFail($request->input('id'));
        if (auth()->user()->olduser->username === $poi->author) {
            $path = '';
            // $path = $request->file('file')->store('poi_images', ['disk' => 'public']);
            $path = $request->file('file')->store('poi_images', 's3');
            // Storage::disk('s3')->setVisibility($path, 'public');
            $img = Image::create([
                'path' => $path, 
                'parent' => $poi->id, 
                'author' => auth()->user()->id,
            ]);
            return response()->json($poi->images);
        }
        return;
    }

    public function show(Image $image) 
    {
        return Storage::disk('s3')->response('poi_images' . $image->path);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        if (auth()->user()->olduser->username === $image->poi()->first()->author) {
            $image->delete();
            return response()->json($image->poi()->first()->images);
        }
    }

}
