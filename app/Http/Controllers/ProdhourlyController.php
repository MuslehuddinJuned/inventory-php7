<?php

namespace App\Http\Controllers;

use App\Prodhourly;
use DB;
use Illuminate\Http\Request;

class ProdhourlyController extends Controller
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
        $Prodhourly = $request->user()->prodhourly()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'id' => $Prodhourly->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prodhourly  $prodhourly
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {
        $PoList = DB::SELECT('SELECT A.id value, quantity, remarks, po_date, po_no text, etd, producthead_id, buyer, product_style, product_code, product_image, department, SUM(total_prod)total_prod FROM (
            SELECT id, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists WHERE DATE(etd) >= CURDATE() - INTERVAL 30 DAY
            )A LEFT JOIN (SELECT id, buyer, product_style, product_code, product_image FROM productheads WHERE deleted_by = 0
            )B ON A.producthead_id = B.id LEFT JOIN (SELECT (COALESCE(qty_1, 0) + COALESCE(qty_2, 0) + COALESCE(qty_3, 0) + COALESCE(qty_4, 0) + COALESCE(qty_5, 0) + COALESCE(qty_6, 0) + COALESCE(qty_7, 0) + COALESCE(qty_8, 0) + COALESCE(qty_9, 0) + COALESCE(qty_10, 0) + COALESCE(qty_11, 0) + COALESCE(qty_12, 0) + COALESCE(qty_13, 0) + COALESCE(qty_14, 0) + COALESCE(qty_15, 0) + COALESCE(qty_16, 0) + COALESCE(qty_17, 0) + COALESCE(qty_18, 0) + COALESCE(qty_19, 0) + COALESCE(qty_20, 0) + COALESCE(qty_21, 0) + COALESCE(qty_22, 0) + COALESCE(qty_23, 0) + COALESCE(qty_24, 0))
            total_prod, department, polist_id FROM prodhourlies WHERE DATE(prod_date) < DATE(?)
            )C ON A.id = C.polist_id GROUP BY value, quantity, remarks, po_date, text, etd, producthead_id, buyer, product_style, product_code, product_image, department', [$date]);

        $production = DB::SELECT("SELECT A.id, line, section, department, leader, remarks, prod_date, hourly_target,
            (CASE WHEN polist_id IS null THEN A.quantity ELSE B.quantity END)quantity,
            (CASE WHEN polist_id IS null THEN A.item ELSE C.product_code END)item,
            qty_1, ng_1, qty_2, ng_2, qty_3, ng_3, qty_4, ng_4, qty_5, ng_5, qty_6, ng_6, qty_7, ng_7, qty_8, ng_8, qty_9, ng_9, qty_10, ng_10, qty_11, ng_11, qty_12, ng_12, qty_13, ng_13, qty_14, ng_14, qty_15, ng_15, qty_16, ng_16, qty_17, ng_17, qty_18, ng_18, qty_19, ng_19, qty_20, ng_20, qty_21, ng_21, qty_22, ng_22, qty_23, ng_23, qty_24, ng_24, polist_id FROM(
            SELECT * FROM prodhourlies WHERE DATE(prod_date) = DATE(?)
            )A LEFT JOIN (SELECT id, quantity, producthead_id FROM polists
            )B ON A.polist_id = B.id LEFT JOIN (SELECT id, product_code FROM productheads
            )C ON B.producthead_id = C.id", [$date]);

        return compact('PoList', 'production');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prodhourly  $prodhourly
     * @return \Illuminate\Http\Response
     */
    public function edit(Prodhourly $prodhourly)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prodhourly  $prodhourly
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prodhourly $prodhourly)
    {
        $prodhourly->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prodhourly  $prodhourly
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prodhourly $prodhourly)
    {
        $prodhourly->delete();
    }
}
