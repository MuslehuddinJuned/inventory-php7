<?php

namespace App\Http\Controllers;

use App\Recdetails;
use Illuminate\Http\Request;
use DB;

class RecdetailsController extends Controller
{
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
        $requisition = DB::SELECT('SELECT A.id, quantity,((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, remarks, accept, A.inventory_id, rechead_id, store_name, item, item_code, specification, unit, unit_price, item_image FROM(
            SELECT id, quantity, remarks, accept, inventory_id, rechead_id FROM recdetails WHERE rechead_id= ?
            )A LEFT JOIN (
            SELECT id, store_name, item, item_code, specification, unit, unit_price, item_image FROM inventories
            )B ON A.inventory_id = B.id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id
            )C ON A.inventory_id = C.inventory_id LEFT JOIN(SELECT inventory_id, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id
            )D ON A.inventory_id = D.inventory_id', [$id]);
        
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
