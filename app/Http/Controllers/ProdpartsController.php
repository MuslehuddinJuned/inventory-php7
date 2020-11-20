<?php

namespace App\Http\Controllers;

use App\Prodparts;
use DB;
use Illuminate\Http\Request;

class ProdpartsController extends Controller
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
        $PoList = DB::SELECT("SELECT id value, po_no text FROM polists WHERE deleted_by = 0");

        return compact('PoList');
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
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function show(Prodparts $prodparts)
    {
        $Production = DB::SELECT("SELECT E.id, A.id polist_id, C.productdetails_id, `quantity`, `po_no`,  A.producthead_id, `buyer`, `product_style`, `product_code`, sn, parts_qty, unit, prod_qty , `prod_date`, `department`, `remarks` FROM (SELECT `id`, `quantity`, `po_no`,  `producthead_id` FROM `polists` WHERE id = 10)A LEFT JOIN (SELECT id producthead_id, `buyer`, `product_style`, `product_code` FROM `productheads`)B ON A.producthead_id = B.producthead_id LEFT JOIN(SELECT id productdetails_id, `sn`,  `unit_weight`, quantity parts_qty, `producthead_id`, `inventory_id` FROM `productdetails`)C ON B.producthead_id = C.producthead_id LEFT JOIN (SELECT id inventory_id, `store_id`, `item`, `item_code`, `specification`, `cann_per_sheet`, `grade`,`weight`, `unit` FROM `inventories`)D ON C.inventory_id = D.inventory_id LEFT JOIN (SELECT `id`, quantity prod_qty , `prod_date`, `department`, `remarks`, `producthead_id`, `productdetails_id`, `polist_id` FROM `prodparts` WHERE DATE(prod_date) = '2020-11-19'
        )E ON C.productdetails_id = E.productdetails_id AND A.id = E.polist_id ");

        return compact('Production');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function edit(Prodparts $prodparts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prodparts $prodparts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prodparts $prodparts)
    {
        //
    }
}
