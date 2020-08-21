<?php

namespace App\Http\Controllers;

use App\Inventoryreceive;
use Illuminate\Http\Request;
use DB;

class InventoryreceiveController extends Controller
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
        $Inventoryreceive = DB::SELECT('SELECT A.id, remarks, supplier_name, challan_no, challan_date, stock_type, storeReceive_id, store_name, store_id, cann_per_sheet FROM (
            SELECT id, remarks, supplier_name, challan_no, challan_date, stock_type, storeReceive_id FROM inventoryreceives
            )A LEFT JOIN (
            SELECT inventory_id, inventoryreceive_id FROM invenrecalls
            )B ON A.id = B.inventoryreceive_id LEFT JOIN(
            SELECT id, store_id, cann_per_sheet FROM inventories
            )C ON B.inventory_id = C.id LEFT JOIN(SELECT id, name store_name FROM stores
			)D ON C.store_id = D.id GROUP BY A.id ORDER BY challan_date DESC');
        return compact ('Inventoryreceive');
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
        $InventoryReceive = $request->user()->inventoryreceives()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryReceiveId' => $InventoryReceive->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function show( $dummy)
    {
        // For Inventory ETD
        $etd = DB::SELECT('SELECT store_id, item, item_code, specification, unit, cann_per_sheet, grade, accounts_code, 
            weight, unit_price, item_image, B.id, quantity, master_sheet, price, etd, remarks FROM(
            SELECT id, store_id, item, item_code, specification, unit, cann_per_sheet, grade, accounts_code, weight, unit_price, item_image FROM inventories
            )A INNER JOIN ( SELECT id, quantity, master_sheet, price, etd, remarks, inventory_id FROM invenrecalls
            )B ON A.id = B.inventory_id');

        return compact('etd');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventoryreceive $inventoryreceive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventoryreceive $inventoryreceive)
    {
        $inventoryreceive->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventoryreceive $inventoryreceive)
    {
        $inventoryreceive->delete();
    }
}
