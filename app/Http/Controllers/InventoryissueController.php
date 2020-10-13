<?php

namespace App\Http\Controllers;

use App\Inventoryissue;
use App\Rechead;
use App\Recdetails;
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
        $Inventoryissue = DB::SELECT("SELECT A.id, requisition_no, requisition_by, GROUP_CONCAT(po_no SEPARATOR ' | ')po_no, remarks, accept, updated_at, created_at, store_name, store_id FROM (
            SELECT id, requisition_no, requisition_by, remarks, accept, updated_at, created_at FROM recheads WHERE accept IS NULL
            )A LEFT JOIN (
            SELECT inventory_id,polist_id, rechead_id FROM recdetails
            )B ON A.id = B.rechead_id LEFT JOIN(
            SELECT id, store_id FROM inventories
            )C ON B.inventory_id = C.id LEFT JOIN ( SELECT id, name store_name FROM stores
			)D ON C.store_id = D.id LEFT JOIN ( SELECT id, po_no FROM polists
			)E ON B.polist_id = E.id GROUP BY A.id, requisition_no, requisition_by, remarks, accept, updated_at, created_at, store_name, store_id ORDER BY updated_at DESC");
            
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
        $Inventoryissue = new Inventoryissue;
        $Inventoryissue->rechead_id = $request['rechead_id'];
        $Inventoryissue->user_id = auth()->user()->id;
        
        $Rechead = Rechead::find($request->rechead_id);
        $Rechead->accept = $request['accept'];

        $Recdetails = Recdetails::where('rechead_id', $request->rechead_id)->get();
        for ($i=0; $i < count($Recdetails) ; $i++) { 
            $Recdetails[$i]->accept = $request['accept'];
            $Recdetails[$i]->save();
        }
        
        $Inventoryissue->save();
        $Rechead->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Inventoryissue = DB::SELECT("SELECT A.id, requisition_no, requisition_by, GROUP_CONCAT(po_no SEPARATOR ' | ')po_no, remarks, issue_etd, (CASE WHEN accept=-1 THEN 'Rejected' ELSE 'Accepted' END)decision, updated_at, created_at, store_name, store_id FROM (
            SELECT id, requisition_no, requisition_by, remarks, accept, updated_at, created_at FROM recheads WHERE accept IS  NOT NULL
            )A LEFT JOIN (
            SELECT inventory_id, polist_id, rechead_id, issue_etd FROM recdetails
            )B ON A.id = B.rechead_id LEFT JOIN(
            SELECT id, store_id FROM inventories
            )C ON B.inventory_id = C.id LEFT JOIN ( SELECT id, name store_name FROM stores
			)D ON C.store_id = D.id LEFT JOIN ( SELECT id, po_no FROM polists
			)E ON B.polist_id = E.id GROUP BY A.id, requisition_no, requisition_by, remarks, issue_etd, decision, updated_at, created_at, store_name, store_id ORDER BY updated_at DESC");
            
        return compact ('Inventoryissue');
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
