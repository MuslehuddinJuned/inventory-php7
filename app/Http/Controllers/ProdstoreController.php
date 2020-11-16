<?php

namespace App\Http\Controllers;

use App\Prodstore;
use DB;
use Illuminate\Http\Request;

class ProdstoreController extends Controller
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
        $Prodstore = DB::SELECT("SELECT id, name FROM prodstores WHERE deleted_by = 0");

        return compact('Prodstore');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prodstore  $prodstore
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {        
        $Production = DB::SELECT("SELECT id, line, section, department, leader, etd, remarks, prod_date, hourly_target, (COALESCE(hourly_target, 0)*COALESCE(work_hour, 0))total_target,manpower, quantity, total_prod, (total_prod - COALESCE(quantity, 0))balance, item, daily_prod, (COALESCE(daily_prod, 0)/COALESCE(hourly_target, 0)/COALESCE(work_hour, 0)*100)achievement,polist_id, work_hour FROM(
            SELECT A.id, line, section, department, leader, etd, remarks, prod_date, hourly_target, manpower, (CASE WHEN A.polist_id IS null THEN A.quantity ELSE B.quantity END)quantity, (CASE WHEN A.polist_id IS null THEN A.item ELSE C.product_code END)item, SUM(total_prod)total_prod, daily_prod, (24 - (CHAR_LENGTH(work_hour) - CHAR_LENGTH( REPLACE ( work_hour, 'j', '')))) work_hour, A.polist_id FROM(
                SELECT id, line, section, department, leader, remarks, prod_date, hourly_target, manpower, quantity, item,  
                (COALESCE(qty_1, 0) + COALESCE(qty_2, 0) + COALESCE(qty_3, 0) + COALESCE(qty_4, 0) + COALESCE(qty_5, 0) + COALESCE(qty_6, 0) + COALESCE(qty_7, 0) + COALESCE(qty_8, 0) + COALESCE(qty_9, 0) + COALESCE(qty_10, 0) + COALESCE(qty_11, 0) + COALESCE(qty_12, 0) + COALESCE(qty_13, 0) + COALESCE(qty_14, 0) + COALESCE(qty_15, 0) + COALESCE(qty_16, 0) + COALESCE(qty_17, 0) + COALESCE(qty_18, 0) + COALESCE(qty_19, 0) + COALESCE(qty_20, 0) + COALESCE(qty_21, 0) + COALESCE(qty_22, 0) + COALESCE(qty_23, 0) + COALESCE(qty_24, 0))daily_prod, CONCAT(COALESCE(qty_1, 'j') , COALESCE(qty_2, 'j') , COALESCE(qty_3, 'j') , COALESCE(qty_4, 'j') , COALESCE(qty_5, 'j') , COALESCE(qty_6, 'j') , COALESCE(qty_7, 'j') , COALESCE(qty_8, 'j') , COALESCE(qty_9, 'j') , COALESCE(qty_10, 'j') , COALESCE(qty_11, 'j') , COALESCE(qty_12, 'j') , COALESCE(qty_13, 'j') , COALESCE(qty_14, 'j') , COALESCE(qty_15, 'j') , COALESCE(qty_16, 'j') , COALESCE(qty_17, 'j') , COALESCE(qty_18, 'j') , COALESCE(qty_19, 'j') , COALESCE(qty_20, 'j') , COALESCE(qty_21, 'j') , COALESCE(qty_22, 'j') , COALESCE(qty_23, 'j') , COALESCE(qty_24, 'j'))work_hour,
                polist_id FROM prodhourlies WHERE DATE(prod_date) = DATE(?)
                )A LEFT JOIN (SELECT id, quantity, producthead_id, etd FROM polists
                )B ON A.polist_id = B.id LEFT JOIN (SELECT id, product_code FROM productheads
                )C ON B.producthead_id = C.id LEFT JOIN (SELECT (COALESCE(qty_1, 0) + COALESCE(qty_2, 0) + COALESCE(qty_3, 0) + COALESCE(qty_4, 0) + COALESCE(qty_5, 0) + COALESCE(qty_6, 0) + COALESCE(qty_7, 0) + COALESCE(qty_8, 0) + COALESCE(qty_9, 0) + COALESCE(qty_10, 0) + COALESCE(qty_11, 0) + COALESCE(qty_12, 0) + COALESCE(qty_13, 0) + COALESCE(qty_14, 0) + COALESCE(qty_15, 0) + COALESCE(qty_16, 0) + COALESCE(qty_17, 0) + COALESCE(qty_18, 0) + COALESCE(qty_19, 0) + COALESCE(qty_20, 0) + COALESCE(qty_21, 0) + COALESCE(qty_22, 0) + COALESCE(qty_23, 0) + COALESCE(qty_24, 0))
                total_prod, polist_id FROM prodhourlies WHERE DATE(prod_date) <= DATE(?)
                )D ON A.polist_id = D.polist_id GROUP BY id, line, section, department, leader, etd, remarks, prod_date, hourly_target, manpower, quantity, item, daily_prod, polist_id, work_hour
            )A ORDER BY line",[$date, $date]);

        return compact('Production');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prodstore  $prodstore
     * @return \Illuminate\Http\Response
     */
    public function edit(Prodstore $prodstore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prodstore  $prodstore
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prodstore $prodstore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prodstore  $prodstore
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prodstore $prodstore)
    {
        //
    }
}
