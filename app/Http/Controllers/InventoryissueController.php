<?php

namespace App\Http\Controllers;

use App\Inventoryissue;
use Illuminate\Http\Request;
use DB;

class InventoryissueController extends Controller
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
        $Inventoryissue = DB::SELECT('SELECT A.id, remarks, supplier_name, challan_no, challan_date, stock_type, storeReceive_id, store_name FROM (
            SELECT id, remarks, supplier_name, challan_no, challan_date, stock_type, storeReceive_id FROM inventoryreceives
            )A LEFT JOIN (
            SELECT inventory_id, inventoryreceive_id FROM inventoryreceivesdetails
            )B ON A.id = B.inventoryreceive_id LEFT JOIN(
            SELECT id, store_name FROM inventories
            )C ON B.inventory_id = C.id GROUP BY A.id');
        return compact ('Inventoryissue');
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
            'item_code'=> 'required|unique:inventories,item_code'
        ]);

        $InventoryIssue = $request->user()->inventoryissues()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryIssue' => $InventoryIssue
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function show(Inventoryissue $inventoryissue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventoryissue $inventoryissue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventoryissue $inventoryissue)
    {
        $this->validate($request, [
            'item_code'=> 'required|unique:inventories,item_code,'.$inventory->id
        ]); 

        $inventoryissue->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventoryissue $inventoryissue)
    {
        $inventoryissue->delete();
    }
}
