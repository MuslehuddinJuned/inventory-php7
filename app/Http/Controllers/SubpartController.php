<?php

namespace App\Http\Controllers;

use App\Subpart;
use DB;
use Illuminate\Http\Request;

class SubpartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $Productdetails = $request->user()->subpart()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'ProductdetailsID' => $Productdetails->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productDetails = Subpart::where('producthead_id', $id)->get();

        return compact('productDetails');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function edit(Subpart $subpart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subpart $subpart)
    {
        $pic = $request->parts_image;
        if( strlen($pic) > 25){
            $exploded = explode(',', $request->parts_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'png'))
                $extesion = 'png';
            else
                $extesion = 'jpg';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path().'/images/product/'.$fileName;
            // $path = '/home/sustipe/inventory.sustipe.com/images/product/'.$fileName;
    
            // store new image
            file_put_contents($path, $decoded);

            // delete previous image
            $Subpart = Subpart::find($subpart->id);

            if($Subpart->parts_image != 'noimage.jpg'){
                //Delete Image
                $path = public_path().'/images/product/'.$subpart->parts_image;
                // $path = '/home/sustipe/inventory.sustipe.com/images/product/'.$subpart->parts_image;
                @unlink($path);
            }

            //save data
            $subpart->update($request->except('parts_image') + [
                'parts_image' => $fileName
            ]);

            if(request()->expectsJson()){
                return response()->json([
                    'fileName' => $fileName
                ]);
            }
        }

        $subpart->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subpart $subpart)
    {
        // delete previous image
        $Subpart = Subpart::find($subpart->id);

        if($Subpart->product_image != 'noimage.jpg'){
            //Delete Image
            $path = public_path().'/images/product/'.$subpart->parts_image;
            // $path = '/home/sustipe/inventory.sustipe.com/images/product/'.$subpart->parts_image;
            @unlink($path);
        }

        $subpart->delete();
    }
}
