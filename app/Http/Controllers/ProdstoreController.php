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
        $Production = DB::SELECT("SELECT buyer, product_style, product_code, product_image, quantity, po_date, po_no, etd, prod_qty, (total_qty-quantity)remain_qty, prod_date, A.remarks, prodstore_id FROM(
                SELECT id, prod_qty, SUM(total_qty)total_qty, prod_date, remarks, A.polist_id, A.producthead_id, A.prodstore_id FROM(
                SELECT id, prod_qty, prod_date, remarks, polist_id, producthead_id, prodstore_id FROM productions WHERE prod_date = ?
                )A LEFT JOIN(SELECT prod_qty total_qty, polist_id, prodstore_id, producthead_id FROM productions
                )B ON A.polist_id = B.polist_id AND A.prodstore_id = B.prodstore_id AND A.producthead_id = B.producthead_id GROUP BY id, prod_qty, prod_date, remarks, polist_id, producthead_id, prodstore_id
            )A LEFT JOIN (SELECT id, quantity, remarks, po_date, po_no, etd FROM polists
            )B ON A.polist_id = B.id LEFT JOIN (SELECT id, buyer, product_style, product_code, product_image FROM productheads
            )C ON A.producthead_id = C.id",[$date]);

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
