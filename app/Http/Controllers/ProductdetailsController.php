<?php

namespace App\Http\Controllers;

use App\Productdetails;
use Illuminate\Http\Request;
use DB;

class ProductdetailsController extends Controller
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
        $Productdetails = $request->user()->productdetails()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'ProductdetailsID' => $Productdetails->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Productdetails  $productdetails
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productDetails = DB::SELECT('SELECT A.id, quantity,((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, remarks, A.inventory_id, producthead_id, store_name, item, item_code, specification, unit, unit_price, item_image FROM(        
            SELECT id, quantity, remarks, producthead_id, inventory_id FROM productdetails WHERE producthead_id = ?)A LEFT JOIN (
            SELECT id, store_name, item, item_code, specification, unit, unit_price, item_image FROM inventories
            )B ON A.inventory_id = B.id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id
            )C ON A.inventory_id = C.inventory_id LEFT JOIN(SELECT inventory_id, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id
            )D ON A.inventory_id = D.inventory_id', [$id]);

        return compact('productDetails');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Productdetails  $productdetails
     * @return \Illuminate\Http\Response
     */
    public function edit(Productdetails $productdetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Productdetails  $productdetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        // $productdetails->update($request->all());

        $Productdetails = Productdetails::find($id);
        
        $Productdetails->quantity = $request['quantity'];
        $Productdetails->producthead_id = $request['producthead_id'];
        $Productdetails->remarks = $request['remarks'];
        $Productdetails->inventory_id = $request['inventory_id'];
        $Productdetails->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Productdetails  $productdetails
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Productdetails = Productdetails::find($id);
        $Productdetails->delete();
    }
}
