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
        $Inventory = DB::SELECT('SELECT A.id, ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, store_id, store_name, cann_per_sheet, item, item_code, specification, unit, unit_price, item_image FROM(            
            SELECT id, store_id, item, item_code, specification, cann_per_sheet,  unit, unit_price, item_image FROM inventories
            )A LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id
            )B ON A.id = B.inventory_id LEFT JOIN(SELECT inventory_id, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id
            )C ON A.id = C.inventory_id LEFT JOIN(SELECT id, name store_name FROM stores
			)D ON A.store_id = D.id');

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
        // 'item_code'=> 'required|unique:inventories,item_code'
        $this->validate($request, [
            'item_code'=> 'required',
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
    public function show($id)
    {
        $inOutDetails = DB::SELECT('SELECT A.id, store_id, store_name, item, item_image, item_code, specification, unit, unit_price, inout_date, received_qty, issued_qty FROM(
            SELECT id, store_id, item, item_image, item_code, specification, unit, unit_price FROM inventories WHERE id = ?
            )A LEFT JOIN ( SELECT id, name store_name FROM stores
			)B ON A.store_id = B.id LEFT JOIN (

            SELECT inventory_id, created_at inout_date, received_qty, 0 issued_qty FROM(
            SELECT inventory_id, inventoryreceive_id, (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END)received_qty FROM invenrecalls
            )A LEFT JOIN (SELECT id, created_at FROM inventoryreceives WHERE deleted_by = 0
            )B ON A.inventoryreceive_id = B.id WHERE inventory_id = ?
                    
            UNION ALL 
                        
            SELECT inventory_id, created_at inout_date, 0 received_qty, issued_qty FROM(
            SELECT (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END) issued_qty, inventory_id, rechead_id FROM recdetails WHERE accept = 1
            )A LEFT JOIN (
            SELECT rechead_id, created_at FROM `inventoryissues`
            )B ON A.rechead_id = B.rechead_id WHERE inventory_id = ?
            )B ON A.id = B.inventory_id ORDER BY inout_date DESC', [$id, $id, $id]);

        return compact('inOutDetails');
        
    }

    public function balance($y1,$m1,$d1,$y2,$m2,$d2)
    {
        $date_1 = $y1.'-'.$m1.'-'.$d1;
        $date_2 = $y2.'-'.$m2.'-'.$d2;

        $balance = DB::SELECT('SELECT id, store_name, item, item_image, item_code, specification, unit, unit_price, (
            CASE WHEN received_qty IS NULL THEN 0 ELSE received_qty END - CASE WHEN issued_qty IS NULL THEN 0 ELSE issued_qty END) opening, (CASE WHEN receiving_qty IS NULL THEN 0 ELSE receiving_qty END)receiving_qty, (CASE WHEN issueing_qty IS NULL THEN 0 ELSE issueing_qty END)issueing_qty, (
            CASE WHEN received_qty IS NULL THEN 0 ELSE received_qty END + CASE WHEN receiving_qty IS NULL THEN 0 ELSE receiving_qty END - CASE WHEN issued_qty IS NULL THEN 0 ELSE issued_qty END - CASE WHEN issueing_qty IS NULL THEN 0 ELSE issueing_qty END)closing FROM(
            SELECT id, store_name, item, item_image, item_code, specification, unit, unit_price FROM inventories
            )A LEFT JOIN (
            SELECT inventory_id, SUM(quantity)received_qty FROM invenrecalls WHERE created_at < ? GROUP BY inventory_id
            )B ON A.id = B.inventory_id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receiving_qty FROM invenrecalls WHERE created_at BETWEEN ? AND ? GROUP BY inventory_id
            )C ON A.id = C.inventory_id LEFT JOIN (
            SELECT SUM(quantity)issued_qty, inventory_id FROM recdetails WHERE accept = 1 AND created_at < ? GROUP BY inventory_id
            )D ON A.id = D.inventory_id LEFT JOIN (
            SELECT SUM(quantity)issueing_qty, inventory_id FROM recdetails WHERE accept = 1 AND created_at BETWEEN ? and ? GROUP BY inventory_id
            )E ON A.id = E.inventory_id', [$date_1, $date_1, $date_2, $date_1, $date_1, $date_2]);

        return compact('balance');
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
        // 'item_code'=> 'required|unique:inventories,item_code,'.$inventory->id,
        $this->validate($request, [
            'item_code'=> 'required',
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
