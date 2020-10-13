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
        // $productDetails = DB::SELECT('SELECT A.id, quantity, material_number, material_name, description, material_name_ch, description_ch, unit_weight, unit, remarks, producthead_id, inventory_id, store_id, store_name FROM(        
        //     SELECT id, quantity, material_number, material_name, description, material_name_ch, description_ch, unit_weight, unit, remarks, producthead_id, inventory_id, store_id FROM productdetails WHERE producthead_id = ?
        //     )A LEFT JOIN (SELECT id, name store_name FROM stores
        // 	)B ON A.store_id = B.id ORDER BY store_name', [$id]);
        
        $productDetails = DB::SELECT('SELECT A.id, sn, quantity, remarks, A.inventory_id, producthead_id, store_id, store_name, item, item_code, weight, 
            (CASE WHEN description IS NULL THEN specification ELSE description END)specification, description, unit, unit_price, item_image FROM(        
            SELECT id, sn, quantity, description,remarks, producthead_id, inventory_id FROM productdetails WHERE producthead_id = ?)A LEFT JOIN (
            SELECT id, store_id, item, item_code, weight, specification, unit, unit_price, item_image FROM inventories
            )B ON A.inventory_id = B.id LEFT JOIN (
            SELECT id, name store_name from stores
            )C ON B.store_id = C.id  ORDER BY sn', [$id]);


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
        $Productdetails->sn = $request['sn'];
        $Productdetails->material_number = $request['material_number'];
        $Productdetails->material_name = $request['material_name'];
        $Productdetails->description = $request['description'];
        $Productdetails->material_name_ch = $request['material_name_ch'];
        $Productdetails->description_ch = $request['description_ch'];
        $Productdetails->unit_weight = $request['unit_weight'];
        $Productdetails->unit = $request['unit'];
        $Productdetails->store_id = $request['store_id'];
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
