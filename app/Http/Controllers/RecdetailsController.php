<?php

namespace App\Http\Controllers;

use App\Recdetails;
use Illuminate\Http\Request;
use DB;

class RecdetailsController extends Controller
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
        $Recdetails = $request->user()->recdetails()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'RecdetailsID' => $Recdetails->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Recdetails  $recdetails
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $requisition = DB::SELECT('SELECT A.id, store_id, quantity,((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, 
        master_sheet, cann_per_sheet, grade, remarks, accept, A.inventory_id, receive_etd, A.issue_etd, rechead_id, store_name, item, item_code, specification, unit, unit_price, item_image FROM(
            SELECT id, quantity, master_sheet, remarks, accept, inventory_id, rechead_id, issue_etd FROM recdetails WHERE rechead_id= ?
            )A LEFT JOIN (
            SELECT id, store_id, cann_per_sheet, grade, item, item_code, specification, unit, unit_price, item_image FROM inventories
            )B ON A.inventory_id = B.id LEFT JOIN ( SELECT id, name store_name FROM stores
			)C ON B.store_id = C.id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receive_qty, receive_etd from invenrecalls GROUP BY inventory_id, receive_etd
            )D ON A.inventory_id = D.inventory_id and A.issue_etd = D.receive_etd LEFT JOIN(SELECT inventory_id, SUM(quantity)issue_qty, issue_etd from recdetails WHERE accept = 1 GROUP BY inventory_id, issue_etd
            )E ON A.inventory_id = E.inventory_id and E.issue_etd = D.receive_etd', [$id]);
        
        return compact('requisition');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Recdetails  $recdetails
     * @return \Illuminate\Http\Response
     */
    public function edit(Recdetails $recdetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Recdetails  $recdetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $recdetails->update($request->all());

        $Recdetails = Recdetails::find($id);
        
        $Recdetails->quantity = $request['quantity'];
        $Recdetails->rechead_id = $request['rechead_id'];
        $Recdetails->inventory_id = $request['inventory_id'];
        $Recdetails->issue_etd = $request['issue_etd'];
        $Recdetails->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Recdetails  $recdetails
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Recdetails = Recdetails::find($id);
        $Recdetails->delete();
    }
}
