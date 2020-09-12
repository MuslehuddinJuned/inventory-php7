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
        $PoList = DB::SELECT('SELECT A.id, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image FROM (
            SELECT id, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists
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
        //po_no should be unique, don't change it
        $this->validate($request, [
            'po_no'=> 'required|unique:polists,po_no',
            'producthead_id'=> 'required',
            'quantity'=> 'required',
            'etd'=> 'required',
            'po_date'=> 'required'
        ]);

        $polist = $request->user()->polist()->create($request->all());

        $PoList = DB::SELECT('SELECT A.id, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image FROM (
            SELECT id, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists
            )A LEFT JOIN (SELECT id, buyer, product_style, product_code, product_image FROM productheads WHERE deleted_by = 0
            )B ON A.producthead_id = B.id');

        if(request()->expectsJson()){
            return response()->json([
                'polist' => $PoList
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Polist  $polist
     * @return \Illuminate\Http\Response
     */
    public function show($po_id)
    {
        // $polist = DB::SELECT('SELECT product_code, SUM(quantity*bom_qty) total_qty, (CASE WHEN inventory_qty IS NULL THEN 0 ELSE inventory_qty END) inventory_qty, po_date, po_no, store_id, store_name, item, item_code, specification, 
        //     unit, cann_per_sheet, grade, accounts_code, weight, unit_price, item_image FROM(
        //     SELECT id, (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END) quantity, remarks, po_date, po_no, user_id, producthead_id, created_at, updated_at FROM polists WHERE id = ?
        //     )A LEFT JOIN (SELECT id, buyer, product_code FROM productheads WHERE deleted_by = 0
        //     )B ON A.producthead_id = B.id LEFT JOIN (SELECT id, producthead_id, inventory_id, quantity bom_qty FROM productdetails
        //     )C ON B.id = C.producthead_id LEFT JOIN (SELECT id, store_id, item, item_code, specification, unit, cann_per_sheet, grade, accounts_code, weight, unit_price, item_image FROM inventories
        //     )D ON C.inventory_id = D.id LEFT JOIN (SELECT id, name store_name FROM stores
        //     )E ON D.store_id = E.id LEFT JOIN(SELECT challan_no, challan_date, SUM(inventory_qty)inventory_qty, master_sheet, price, inventory_id FROM(
        //         SELECT id, challan_no, polist_id,challan_date FROM inventoryreceives WHERE deleted_by = 0 and polist_id = ?
        //         )A LEFT JOIN (SELECT id, (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END) inventory_qty, master_sheet, price, receive_etd, inventory_id, inventoryreceive_id FROM invenrecalls
        //         )B ON A.id = B.inventoryreceive_id GROUP BY challan_no, challan_date, master_sheet, price, inventory_id
        //     )F ON F.inventory_id = D.id GROUP BY product_code, inventory_qty, po_date, po_no, store_id, store_name, item, item_code, specification, unit, 
        //     cann_per_sheet, grade, accounts_code, weight, unit_price, item_image', [$po_id, $po_id]);

        // $po_no = DB::SELECT('SELECT DISTINCT(po_no)po_no, etd from polists');

        $etd = DB::SELECT('SELECT DISTINCT(etd)etd FROM polists WHERE DATE(etd) >= CURDATE() ORDER BY etd');

        $Inventory = DB::SELECT('SELECT A.id, ((CASE WHEN receive_master_sheet IS NULL THEN 0 ELSE receive_master_sheet END) - (CASE WHEN issue_master_sheet IS NULL THEN 0 ELSE issue_master_sheet END))stock_master_sheet,
            ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, store_id, store_name, cann_per_sheet, grade, accounts_code, weight, item, item_code, specification, unit, unit_price, item_image FROM(            
            SELECT id, store_id, item, item_code, specification, cann_per_sheet, grade, accounts_code, weight, unit, unit_price, item_image FROM inventories
            )A LEFT JOIN (
            SELECT inventory_id, SUM(master_sheet)receive_master_sheet, SUM(quantity)receive_qty from invenrecalls GROUP BY inventory_id
            )B ON A.id = B.inventory_id LEFT JOIN(SELECT inventory_id, SUM(master_sheet)issue_master_sheet, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id
            )C ON A.id = C.inventory_id LEFT JOIN(SELECT id, name store_name FROM stores
            )D ON A.store_id = D.id');
            
        $etdQty = DB::SELECT("SELECT SUM(CASE WHEN quantity IS NULL THEN 0 ELSE quantity END)quantity, GROUP_CONCAT(product_code SEPARATOR ' | ')product_code, etd, inventory_id FROM(
            SELECT ((pcs-CASE WHEN po_qty IS NULL THEN 0 ELSE po_qty END)*quantity) quantity, product_code, etd, C.inventory_id FROM(
            SELECT id, quantity pcs, etd, producthead_id FROM polists WHERE DATE(etd) >= CURDATE()
                )A LEFT JOIN (SELECT id, product_code FROM productheads WHERE deleted_by = 0
                )B ON A.producthead_id = B.id LEFT JOIN (SELECT id, producthead_id, inventory_id, quantity FROM productdetails
                )C ON B.id = C.producthead_id LEFT JOIN (SELECT SUM(po_qty)po_qty, `polist_id`, `inventory_id` FROM recdetails WHERE accept = 1 GROUP BY `polist_id`, `inventory_id`
                )D ON D.polist_id = A.id AND D.inventory_id = C.inventory_id
            )A GROUP BY etd, inventory_id");


        return compact('etd', 'Inventory', 'etdQty');
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
        //po_no should be unique, don't change it
        $this->validate($request, [
            'po_no'=> 'required|unique:polists,po_no,'.$polist->id,
            'producthead_id'=> 'required',
            'quantity'=> 'required',
            'etd'=> 'required',
            'po_date'=> 'required'
        ]);

        $polist->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Polist  $polist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Polist $polist)
    {
        $polist->delete();
    }
}
