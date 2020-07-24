<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
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
        $Inventory = Inventory::get();
        return compact ('Inventory');
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
            'item_code'=> 'required|unique:inventories,item_code',
            'store_name'=> 'required',
            'unit'=> 'required'
        ]);


        if($request->item_image){
            $exploded = explode(',', $request->item_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'png'))
                $extesion = 'png';
            else
                $extesion = 'jpg';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path().'/images/item/'.$fileName;
    
            // store new image
            file_put_contents($path, $decoded);
            $InventoryList = $request->user()->inventories()->create($request->except('item_image') + [
                'item_image' => $fileName
            ]);
        } else {
            $InventoryList = $request->user()->inventories()->create($request->except('item_image'));
        }

        if(request()->expectsJson()){
            return response()->json([
                'InventoryList' => $InventoryList
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        $this->validate($request, [
            'item_code'=> 'required|unique:inventories,item_code,'.$inventory->id,
            'store_name'=> 'required',
            'unit'=> 'required'
        ]); 

        if($request->item_image){
            $exploded = explode(',', $request->item_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'png'))
                $extesion = 'png';
            else
                $extesion = 'jpg';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path().'/images/item/'.$fileName;

            // store new image
            file_put_contents($path, $decoded);
            
            // delete previous image
            $Inventory = Inventory::find($inventory->id);

            if($Inventory->item_image != 'noimage.jpg'){
                //Delete Image
                $path = public_path().'/images/item/'.$Inventory->item_image;
                @unlink($path);
            }

            // save Data
            $inventory->update($request->except('item_image') + [
                'item_image' => $fileName
            ]);

        } else {
            $inventory->update($request->except('item_image'));

            $image = Inventory::select('item_image')->where('id', $request->id)->get();
            $fileName = $image[0]['item_image'];
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
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        // delete previous image
        $Inventory = Inventory::find($inventory->id);

        if($Inventory->item_image != 'noimage.jpg'){
            //Delete Image
            $path = public_path().'/images/item/'.$Inventory->item_image;
            @unlink($path);
        }

        $inventory->delete();
    }
}
