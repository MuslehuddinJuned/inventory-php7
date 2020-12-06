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
        $prodparts = $request->user()->prodparts()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'id' => $prodparts->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {
        $Production = DB::SELECT("SELECT A.id, po_no, etd, product_code, product_image, parts_name, parts_description, po_qty, parts_qty, unit, total_prod_qty, quantity, prod_date, A.department, remarks FROM(
            SELECT id, quantity, prod_date, department, remarks, producthead_id, subpart_id, polist_id FROM prodparts WHERE DATE(prod_date) = ?
            )A LEFT JOIN (SELECT id, quantity po_qty, po_no, etd, producthead_id FROM polists
            )B ON A.polist_id = B.id LEFT JOIN (SELECT id producthead_id, buyer, product_style, product_code, product_image FROM productheads
            )C ON B.producthead_id = C.producthead_id LEFT JOIN(SELECT id subpart_id, parts_name, parts_description, parts_qty, unit, producthead_id FROM subparts
            )D ON C.producthead_id = D.producthead_id AND A.subpart_id = D.subpart_id LEFT JOIN (SELECT SUM(quantity) total_prod_qty , department, subpart_id, polist_id FROM prodparts WHERE DATE(prod_date) < ? GROUP BY department, subpart_id, polist_id
            )E ON D.subpart_id = E.subpart_id AND B.id = E.polist_id ORDER BY etd, po_no", [$date, $date]);
        return compact('Production');
    }

    public function production($department, $po_no, $date){
        $Production = DB::SELECT("SELECT E.id, CONCAT(parts_name, ' || ', unit)text, A.id polist_id, C.subpart_id, C.subpart_id value, po_qty, quantity, po_no,  A.producthead_id, buyer, product_style, product_code, C.subpart_id, parts_name, parts_description, parts_qty, unit, total_prod_qty, prod_date, E.department, remarks FROM (
            SELECT id, quantity po_qty, po_no,  producthead_id FROM polists WHERE id = ?
            )A LEFT JOIN (SELECT id producthead_id, buyer, product_style, product_code FROM productheads
            )B ON A.producthead_id = B.producthead_id LEFT JOIN(SELECT id subpart_id, parts_name, parts_description, parts_qty, unit, producthead_id FROM subparts
            )C ON B.producthead_id = C.producthead_id LEFT JOIN (SELECT id, quantity, prod_date, department, remarks, producthead_id, subpart_id, polist_id FROM prodparts WHERE DATE(prod_date) = ? AND department = ?
            )E ON C.subpart_id = E.subpart_id AND A.id = E.polist_id LEFT JOIN (SELECT SUM(quantity) total_prod_qty , department, subpart_id, polist_id FROM prodparts WHERE DATE(prod_date) < ? AND department = ? GROUP BY department, subpart_id, polist_id
            )F ON C.subpart_id = F.subpart_id AND A.id = F.polist_id", [$po_no, $date, $department, $date, $department]);
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
        $Prodparts = Prodparts::find($request['id']);
        $Prodparts->quantity = $request['quantity'];
        $Prodparts->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::SELECT("DELETE FROM prodparts WHERE id = ?", [$id]);
    }
}
