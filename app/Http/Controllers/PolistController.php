<?php

namespace App\Http\Controllers;

use App\Polist;
use Illuminate\Http\Request;
use DB;

class PolistController extends Controller
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
        $PoList = DB::SELECT('SELECT A.id, quantity, remarks, po_date, po_no, producthead_id, buyer, product_style, product_code, product_image FROM (
            SELECT id, quantity, remarks, po_date, po_no, producthead_id FROM polists
            )A LEFT JOIN (SELECT id, buyer, product_style, product_code, product_image FROM productheads WHERE deleted_by = 0
            )B ON A.producthead_id = B.id');

        $productList = DB::select('SELECT id value, product_code text, buyer FROM productheads WHERE deleted_by = 0');

        return compact('PoList', 'productList');
    
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
     * @param  \App\Polist  $polist
     * @return \Illuminate\Http\Response
     */
    public function show(Polist $polist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Polist  $polist
     * @return \Illuminate\Http\Response
     */
    public function edit(Polist $polist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Polist  $polist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Polist $polist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Polist  $polist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Polist $polist)
    {
        //
    }
}
