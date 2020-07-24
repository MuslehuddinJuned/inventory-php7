<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;
use DB;

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
        $Inventory = DB::SELECT('SELECT A.id, ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, store_name, item, item_code, specification, unit, unit_price, item_image FROM(            
            SELECT id, store_name, item, item_code, specification, unit, unit_price, item_image FROM inventories
            )A LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id
            )C ON A.id = C.inventory_id LEFT JOIN(SELECT inventory_id, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id
            )D ON A.id = D.inventory_id');

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
