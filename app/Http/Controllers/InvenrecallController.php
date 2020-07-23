<?php

namespace App\Http\Controllers;

use App\Invenrecall;
use App\Inventory;
use Illuminate\Http\Request;
use DB;

class InvenrecallController extends Controller
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
        // $pre = DB::SELECT('SELECT stock, stock*unit_price total_price FROM(
        //     SELECT ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, unit_price FROM(
        //     SELECT id, unit_price FROM inventories WHERE id = ?)A LEFT JOIN (        
        //     SELECT inventory_id, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id)B ON A.id = B.inventory_id LEFT JOIN(
        //     SELECT inventory_id, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id)C ON A.id = C.inventory_id) A', [$request['inventory_id']]);
        
        // $total_qty = $pre['stock'] + $request['quantity'];
        // $total_value = $pre['total_price'] + ($request['quantity'] * $request['price']);
        // $new_price = $total_value/$total_qty;
        
        $Inventory = Inventory::find($request['inventory_id']);
        $Inventory->unit_price = $request['price'];
        $Inventory->save();
        
        $Invenrecall = $request->user()->invenrecall()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryreceivesdetailsID' => $Invenrecall->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventoryrec_d= DB::SELECT('SELECT id, quantity, price, remarks, user_id, inventory_id, inventoryreceive_id, created_at, updated_at FROM invenrecalls WHERE inventoryreceive_id = ?', [$id]);

        return compact('inventoryrec_d');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function edit(Invenrecall $invenrecall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invenrecall $invenrecall)
    {
        $Inventory = Inventory::find($request['inventory_id']);
        $Inventory->unit_price = $request['price'];
        $Inventory->save();

        $invenrecall->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invenrecall $invenrecall)
    {
        $invenrecall->delete();
    }
}
