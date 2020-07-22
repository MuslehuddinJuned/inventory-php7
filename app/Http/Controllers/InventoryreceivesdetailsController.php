<?php

namespace App\Http\Controllers;

use App\Inventoryreceivesdetails;
use Illuminate\Http\Request;
use DB;

class InventoryreceivesdetailsController extends Controller
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
        $this->validate($request, [
            'inventory_id'=> 'required'
        ]); 

        $Inventoryreceivesdetails = $request->user()->inventoryreceivesdetails()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryreceivesdetailsID' => $Inventoryreceivesdetails->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventoryreceivesdetails  $inventoryreceivesdetails
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventoryrec_h= DB::SELECT('SELECT id, remarks, supplier_name, challan_no, challan_date, stock_type, storeReceive_id FROM inventoryreceives WHERE id = ?', [$id]);
        $inventoryrec_d= DB::SELECT('SELECT id, quantity, price, remarks, user_id, inventory_id, inventoryreceive_id, created_at, updated_at FROM inventoryreceivesdetails WHERE inventoryreceive_id = ?', [$id]);

        return compact('inventoryrec_h', 'inventoryrec_d');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventoryreceivesdetails  $inventoryreceivesdetails
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventoryreceivesdetails $inventoryreceivesdetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventoryreceivesdetails  $inventoryreceivesdetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventoryreceivesdetails $inventoryreceivesdetails)
    {        
        $inventoryreceivesdetails->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventoryreceivesdetails  $inventoryreceivesdetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventoryreceivesdetails $inventoryreceivesdetails)
    {
        //
    }
}
