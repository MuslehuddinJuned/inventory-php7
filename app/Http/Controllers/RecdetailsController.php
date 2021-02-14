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
        $polist = DB::SELECT('SELECT id value, po_no text FROM `polists` ORDER BY created_at DESC');
        return compact('polist');
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
        $requisition = DB::SELECT('SELECT A.id, po_no, product_code, polist_id, po_qty, store_id, quantity, weight, ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, 
        master_sheet, cann_per_sheet, grade, remarks, accept, A.inventory_id, A.issue_etd, A.rechead_id, store_name, item, item_code, specification, unit, unit_price, item_image FROM(
            SELECT id, polist_id, po_qty, quantity, master_sheet, remarks, accept, inventory_id, rechead_id, issue_etd FROM recdetails WHERE rechead_id = ?
            )A LEFT JOIN (
            SELECT id, store_id, cann_per_sheet, grade, item, item_code, specification, unit, unit_price, item_image, weight FROM inventories
            )B ON A.inventory_id = B.id LEFT JOIN ( SELECT id, name store_name FROM stores
            )C ON B.store_id = C.id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id
            )D ON A.inventory_id = D.inventory_id LEFT JOIN(SELECT inventory_id, rechead_id, SUM(quantity)issue_qty, issue_etd from recdetails WHERE accept = 1 GROUP BY inventory_id, issue_etd, rechead_id
            )E ON A.inventory_id = E.inventory_id AND E.rechead_id = A.rechead_id LEFT JOIN (SELECT id, po_no, producthead_id FROM polists
            )F ON A.polist_id = F.id LEFT JOIN (SELECT id, product_code FROM productheads
            )G ON F.producthead_id = G.id', [$id]);
        
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
        $Recdetails->polist_id = $request['polist_id'];
        $Recdetails->po_qty = $request['po_qty'];
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
