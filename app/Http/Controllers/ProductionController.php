<?php

namespace App\Http\Controllers;

use App\Production;
use DB;
use Illuminate\Http\Request;

class ProductionController extends Controller
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
        $Production = DB::SELECT("SELECT A.id, quantity, po_date, po_no, etd, A.producthead_id, buyer, product_style, product_code, product_image, material, material_remarks, carton, carton_remarks, color_card, color_card_remarks, cutting, cutting_remarks, polish, polish_remarks, injection, injection_remarks, assembly, assembly_remarks FROM (
            SELECT id, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists WHERE deleted_by  = 0 AND DATE(etd) >= CURDATE() - INTERVAL 60 DAY
            )A LEFT JOIN (SELECT id, buyer, product_style, product_code, product_image FROM productheads
            )B ON A.producthead_id = B.id LEFT JOIN(SELECT polist_id, producthead_id, 
            SUM(material)material, GROUP_CONCAT(material_remarks)material_remarks, 
            SUM(carton)carton, GROUP_CONCAT(carton_remarks)carton_remarks, 
            SUM(color_card)color_card, GROUP_CONCAT(color_card_remarks)color_card_remarks, 
            SUM(cutting)cutting, GROUP_CONCAT(cutting_remarks)cutting_remarks,
            SUM(polish)polish, GROUP_CONCAT(polish_remarks)polish_remarks,
            SUM(injection)injection, GROUP_CONCAT(injection_remarks)injection_remarks,
            SUM(assembly)assembly, GROUP_CONCAT(assembly_remarks)assembly_remarks FROM (
                SELECT productions.id, B.prod_qty, productions.prod_date,productions.polist_id, producthead_id, productions.prodstore_id, store_name,
                (CASE WHEN productions.prodstore_id = 1 THEN B.prod_qty ELSE 0 END)material,
                (CASE WHEN productions.prodstore_id = 1 THEN remarks END)material_remarks,
                (CASE WHEN productions.prodstore_id = 2 THEN B.prod_qty ELSE 0 END)carton,
                (CASE WHEN productions.prodstore_id = 2 THEN remarks END)carton_remarks,
                (CASE WHEN productions.prodstore_id = 3 THEN B.prod_qty ELSE 0 END)color_card,
                (CASE WHEN productions.prodstore_id = 3 THEN remarks END)color_card_remarks,
                (CASE WHEN productions.prodstore_id = 4 THEN B.prod_qty ELSE 0 END)cutting,
                (CASE WHEN productions.prodstore_id = 4 THEN remarks END)cutting_remarks,
                (CASE WHEN productions.prodstore_id = 5 THEN B.prod_qty ELSE 0 END)polish,
                (CASE WHEN productions.prodstore_id = 5 THEN remarks END)polish_remarks,
                (CASE WHEN productions.prodstore_id = 6 THEN B.prod_qty ELSE 0 END)injection,
                (CASE WHEN productions.prodstore_id = 6 THEN remarks END)injection_remarks,
                (CASE WHEN productions.prodstore_id = 7 THEN B.prod_qty ELSE 0 END)assembly,
                (CASE WHEN productions.prodstore_id = 7 THEN remarks END)assembly_remarks FROM 
                    productions RIGHT JOIN (SELECT SUM(prod_qty)prod_qty, MAX(prod_date)prod_date, prodstore_id, polist_id FROM productions GROUP BY prodstore_id, polist_id
                    )B ON productions.prod_date = B.prod_date AND productions.polist_id = B.polist_id AND productions.prodstore_id = B.prodstore_id LEFT JOIN(SELECT id, name store_name FROM prodstores
                    )C ON productions.prodstore_id = C.id
                )A GROUP BY polist_id, producthead_id
            )C ON A.id = C.polist_id");

        $Prodstore = DB::SELECT("SELECT id, name FROM prodstores WHERE deleted_by = 0");

        return compact('Production', 'Prodstore');
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
        $request->user()->production()->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Production = DB::SELECT("SELECT id, prod_qty, prod_date, remarks, polist_id, producthead_id, prodstore_id FROM productions WHERE polist_id = ?", [$id]);

        return compact('Production');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function edit(Production $production)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Production $production)
    {
        $production->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function destroy(Production $production)
    {
        $production->delete();
    }
}
