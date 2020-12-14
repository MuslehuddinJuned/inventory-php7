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
        $PoList = DB::SELECT('SELECT A.id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image FROM (
            SELECT id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists WHERE DATE(etd) >= CURDATE() - INTERVAL 30 DAY
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
        // be carefull when add deleted_by functionality for uniqness of po_no
        $this->validate($request, [
            'po_no'=> 'required|unique:polists,po_no',
            'producthead_id'=> 'required',
            'quantity'=> 'required',
            'etd'=> 'required',
            'po_date'=> 'required'
        ]);

        $polist = $request->user()->polist()->create($request->all());

        $PoList = DB::SELECT('SELECT A.id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image FROM (
            SELECT id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists WHERE DATE(etd) >= CURDATE() - INTERVAL 30 DAY
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
    public function show($date)
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

        if($date == 1) {
            //for monitor ETD
            $po_no = DB::SELECT('SELECT DISTINCT(po_no)po_no, etd from polists');

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
                    )C ON B.id = C.producthead_id LEFT JOIN (SELECT SUM(po_qty)po_qty, polist_id, inventory_id FROM recdetails WHERE accept = 1 GROUP BY polist_id, inventory_id
                    )D ON D.polist_id = A.id AND D.inventory_id = C.inventory_id
                )A GROUP BY etd, inventory_id");

            return compact('etd', 'Inventory', 'etdQty');
        }        

        //for Production Plan------------------------------//
        $buyer = DB::SELECT("SELECT DISTINCT buyer FROM productheads WHERE deleted_by = 0 ORDER BY buyer");

        $Production = DB::SELECT("SELECT id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image, department, smv, manpower, total_prod, (COALESCE(total_prod, 0)-COALESCE(quantity, 0))balance, (manpower*60/smv)hourly_target, ((COALESCE(total_prod, 0)-COALESCE(quantity, 0))*smv/manpower/60)req_hour, ((COALESCE(total_prod, 0)-COALESCE(quantity, 0))*smv*manpower/manpower/60)req_manhour, ((COALESCE(total_prod, 0)-COALESCE(quantity, 0))*smv/manpower/480)req_day FROM(
            SELECT A.id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image, department, smv, manpower, SUM(total_prod)total_prod FROM (
                SELECT id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id FROM polists WHERE DATE(etd)= ?
                )A LEFT JOIN (SELECT id, buyer, product_style, product_code, product_image, smv, manpower FROM productheads WHERE deleted_by = 0
                )B ON A.producthead_id = B.id LEFT JOIN (SELECT (COALESCE(qty_1, 0) + COALESCE(qty_2, 0) + COALESCE(qty_3, 0) + COALESCE(qty_4, 0) + COALESCE(qty_5, 0) + COALESCE(qty_6, 0) + COALESCE(qty_7, 0) + COALESCE(qty_8, 0) + COALESCE(qty_9, 0) + COALESCE(qty_10, 0) + COALESCE(qty_11, 0) + COALESCE(qty_12, 0) + COALESCE(qty_13, 0) + COALESCE(qty_14, 0) + COALESCE(qty_15, 0) + COALESCE(qty_16, 0) + COALESCE(qty_17, 0) + COALESCE(qty_18, 0) + COALESCE(qty_19, 0) + COALESCE(qty_20, 0) + COALESCE(qty_21, 0) + COALESCE(qty_22, 0) + COALESCE(qty_23, 0) + COALESCE(qty_24, 0))
                total_prod, department, polist_id FROM prodhourlies
                )C ON A.id = C.polist_id GROUP BY id, lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id, buyer, product_style, product_code, product_image, department, smv, manpower
            )A", [$date]);

        return compact('buyer', 'Production');
    }

    public function monitor($department)
    {
        $stock = DB::SELECT("SELECT id, buyer, product_style, product_code, specification, product_image, parts_name, parts_description, (SUM(req_qty)-prod_qty)stock, unit FROM(
                SELECT B.id, buyer, product_style, product_code, specification, product_image, parts_name, parts_description, 
                (COALESCE(parts_qty, 0)*COALESCE(quantity, 0))req_qty, COALESCE(prod_qty, 0)prod_qty, unit FROM(
                SELECT id, buyer, product_style, product_code, specification, product_image FROM productheads WHERE deleted_by = 0
                )A LEFT JOIN (SELECT id, parts_name, parts_description, parts_qty, unit, producthead_id FROM subparts
                )B ON A.id = B.producthead_id LEFT JOIN(SELECT quantity, producthead_id FROM polists WHERE deleted_by = 0 AND DATE(etd) < CURDATE()
                )C ON A.id = C.producthead_id LEFT JOIN(SELECT SUM(quantity)prod_qty, department, subpart_id FROM prodparts WHERE department = ? GROUP by subpart_id, department
                )D ON B.id = D.subpart_id
            )A GROUP BY id, buyer, product_style, product_code, specification, product_image, parts_name, parts_description, unit", [$department]);

        $etd = DB::SELECT('SELECT DISTINCT(etd)etd FROM polists WHERE DATE(etd) >= CURDATE() ORDER BY etd');

        $etdQty = DB::SELECT("SELECT id, etd, SUM(quantity)quantity FROM(
                SELECT B.id, etd, (COALESCE(parts_qty, 0)*COALESCE(quantity, 0))quantity FROM(
                SELECT id FROM productheads WHERE deleted_by = 0
                )A LEFT JOIN (SELECT id, parts_qty, producthead_id FROM subparts
                )B ON A.id = B.producthead_id RIGHT JOIN(SELECT quantity, etd, producthead_id FROM polists WHERE deleted_by = 0 AND DATE(etd) >= CURDATE()
                )C ON A.id = C.producthead_id
            )A GROUP BY id, etd");

        $buyer = DB::SELECT("SELECT DISTINCT buyer FROM productheads WHERE deleted_by = 0 ORDER BY buyer");

        return compact('stock', 'etd', 'etdQty', 'buyer');
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
