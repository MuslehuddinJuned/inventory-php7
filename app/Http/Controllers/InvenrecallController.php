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
        $inventoryrec_d= DB::SELECT('SELECT A.id, quantity, receive_etd, master_sheet, price, remarks, user_id, inventory_id, inventoryreceive_id, store_id, store_name, item, item_code, specification, unit, cann_per_sheet, unit_price, item_image, weight, created_at, updated_at FROM(
            SELECT id, quantity, receive_etd, master_sheet, price, remarks, user_id, inventory_id, inventoryreceive_id, created_at, updated_at FROM invenrecalls WHERE inventoryreceive_id = ? AND deleted_by = 0
            )A LEFT JOIN (SELECT id, store_id, item, item_code, specification, unit, cann_per_sheet, unit_price, item_image, weight FROM inventories
            )B ON A.inventory_id = B.id LEFT JOIN (SELECT id, name store_name FROM stores
            )C ON B.store_id = C.id', [$id]);

        // $po_no = DB::SELECT('SELECT po_no FROM(
        //     SELECT id, polist_id FROM inventoryreceives WHERE id = ?
        //     )A LEFT JOIN (SELECT po_no, id FROM polists
        //     )B ON A.polist_id = B.id ORDER BY po_no', [$id]);

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
        $invenrecall->update($request->all());
        
        if($request['price']){
            $Inventory = Inventory::find($request['inventory_id']);
            $Inventory->unit_price = $request['price'];
            $Inventory->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invenrecall $invenrecall)
    {
        // $invenrecall->delete();

        $Invenrecall = Invenrecall::find($invenrecall->id);
        $Invenrecall->deleted_by = auth()->user()->id;
        $Invenrecall->save();
    }
}
