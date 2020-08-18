<?php

namespace App\Http\Controllers;

use App\Producthead;
use Illuminate\Http\Request;
use DB;

class ProductheadController extends Controller
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
        $productHead = Producthead::where('deleted_by', '0')->get();

        return compact('productHead');
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
        $this->validate($request, [
            'product_category'=> 'required',
            'product_code'=> 'required|unique:productheads,product_code'
        ]);

        if($request->product_image){
            $exploded = explode(',', $request->product_image);
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
            $productHead = $request->user()->producthead()->create($request->except('product_image') + [
                'product_image' => $fileName
            ]);
        } else {
            $productHead = $request->user()->producthead()->create($request->except('product_image'));
        }

        if(request()->expectsJson()){
            return response()->json([
                'productHead' => $productHead
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function show(Producthead $producthead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function edit(Producthead $producthead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producthead $producthead)
    {
        $this->validate($request, [
            'product_category'=> 'required',
            'product_code'=> 'required|unique:productheads,product_code,'.$producthead->id,
        ]);

        if($request->product_image){
            $exploded = explode(',', $request->product_image);
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
            $Producthead = Producthead::find($producthead->id);

            if($Producthead->product_image != 'noimage.jpg'){
                //Delete Image
                $path = public_path().'/images/product/'.$producthead->product_image;
                // $path = '/home/sustipe/inventory.sustipe.com/images/product/'.$producthead->product_image;
                @unlink($path);
            }

            //save data
            $producthead->update($request->except('product_image') + [
                'product_image' => $fileName
            ]);
        } else {
            $producthead->update($request->except('product_image'));

            $image = Producthead::select('product_image')->where('id', $request->id)->get();
            $fileName = $image[0]['product_image'];
        }

        if(request()->expectsJson()){
            return response()->json([
                'fileName' => $fileName
            ]);
        }        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producthead $producthead)
    {
        // delete previous image
        $Producthead = Producthead::find($producthead->id);

        if($Producthead->product_image != 'noimage.jpg'){
            //Delete Image
            $path = public_path().'/images/product/'.$producthead->product_image;
            // $path = '/home/sustipe/inventory.sustipe.com/images/product/'.$producthead->product_image;
            @unlink($path);
        }

        $Producthead->delete();
    }
}
