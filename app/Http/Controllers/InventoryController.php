<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;
use DB;

class InventoryController extends Controller
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
        $Inventory = DB::SELECT("SELECT A.id, ((CASE WHEN receive_master_sheet IS NULL THEN 0 ELSE receive_master_sheet END) - (CASE WHEN issue_master_sheet IS NULL THEN 0 ELSE issue_master_sheet END))stock_master_sheet,
            ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, store_id, store_name, cann_per_sheet, grade, accounts_code, weight, item, item_code, specification, unit, unit_price, item_image FROM(            
            SELECT id, store_id, item, item_code, specification, cann_per_sheet, grade, accounts_code, COALESCE(weight, '')weight, unit, unit_price, item_image FROM inventories
            )A LEFT JOIN (
            SELECT inventory_id, SUM(master_sheet)receive_master_sheet, SUM(quantity)receive_qty from invenrecalls WHERE deleted_by = 0 GROUP BY inventory_id
            )B ON A.id = B.inventory_id LEFT JOIN(SELECT inventory_id, SUM(master_sheet)issue_master_sheet, SUM(quantity)issue_qty from recdetails WHERE accept = 1 GROUP BY inventory_id
            )C ON A.id = C.inventory_id LEFT JOIN(SELECT id, name store_name FROM stores
			)D ON A.store_id = D.id");

        return compact ('Inventory');
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
        // 'item_code'=> 'required|unique:inventories,item_code'
        $this->validate($request, [
            'item_code'=> 'required',
            'unit'=> 'required'
        ]);


        if($request->item_image){
            $exploded = explode(',', $request->item_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'png'))
                $extesion = 'png';
            else
                $extesion = 'jpg';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path().'/images/item/'.$fileName;
            // $path = '/home/sustipe/inventory.sustipe.com/images/item/'.$fileName;
    
            // store new image
            file_put_contents($path, $decoded);
            $InventoryList = $request->user()->inventories()->create($request->except('item_image') + [
                'item_image' => $fileName
            ]);
        } else {
            $InventoryList = $request->user()->inventories()->create($request->except('item_image'));
        }

        if(request()->expectsJson()){
            return response()->json([
                'InventoryList' => $InventoryList
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //        
    }

    public function inout($id,$y1,$m1,$d1,$y2,$m2,$d2)
    {
        $date_1 = $y1.'-'.$m1.'-'.$d1;
        $date_2 = $y2.'-'.$m2.'-'.$d2;

        $inOutDetails = DB::SELECT("SELECT A.id, store_id, store_name, item, item_image, item_code, specification, unit, unit_price, inout_date, etd,rec_req_no, po_no, received_qty, issued_qty FROM(
            SELECT id, store_id, item, item_image, item_code, specification, unit, unit_price FROM inventories WHERE id = ?
            )A LEFT JOIN ( SELECT id, name store_name FROM stores
			)B ON A.store_id = B.id LEFT JOIN (

                SELECT inventory_id, challan_no rec_req_no, NULL po_no, receive_date inout_date, etd, received_qty, 0 issued_qty FROM(
                SELECT inventory_id, inventoryreceive_id, receive_etd etd, (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END)received_qty FROM invenrecalls WHERE deleted_by = 0
                )A LEFT JOIN (SELECT id, challan_no, receive_date FROM inventoryreceives WHERE deleted_by = 0
                )B ON A.inventoryreceive_id = B.id WHERE inventory_id = ? and receive_date BETWEEN ? and ?
                        
                UNION ALL 
                            
                SELECT inventory_id, requisition_no rec_req_no, po_no, created_at inout_date, etd, 0 received_qty, issued_qty FROM(
                SELECT (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END) issued_qty, issue_etd etd, inventory_id, rechead_id, polist_id FROM recdetails WHERE accept = 1
                )A LEFT JOIN (SELECT rechead_id, created_at FROM inventoryissues
                )B ON A.rechead_id = B.rechead_id LEFT JOIN (SELECT id, po_no FROM polists
                )C ON A.polist_id = C.id  LEFT JOIN (SELECT id, requisition_no FROM recheads
                )D ON A.rechead_id = D.id WHERE inventory_id = ? and created_at BETWEEN ? and ?
                    
                UNION ALL
                        
                SELECT inventory_id, rec_req_no, po_no, inout_date, etd, (COALESCE(received_qty, 0)-COALESCE(issued_qty, 0))received_qty, 0 issued_qty FROM(
                SELECT inventory_id, ('Opening Balance')rec_req_no, NULL po_no, NULL inout_date, NULL etd, SUM(received_qty)received_qty,SUM(issued_qty)issued_qty FROM(
                    SELECT A.inventory_id, issued_qty, received_qty FROM(
                        SELECT inventory_id, SUM(received_qty)received_qty  FROM(
                            SELECT inventory_id, inventoryreceive_id, quantity received_qty, created_at FROM invenrecalls WHERE inventory_id = ? AND deleted_by = 0
                            )A INNER JOIN (SELECT id, receive_date , deleted_by FROM inventoryreceives WHERE receive_date < ? AND deleted_by = 0
                            )B ON A.inventoryreceive_id = B.id GROUP BY inventory_id
                        )A LEFT JOIN(
                        SELECT SUM(issued_qty)issued_qty, inventory_id FROM(
                            SELECT rechead_id FROM inventoryissues WHERE  created_at < ?
                            )A INNER JOIN (SELECT id FROM recheads
                            )B ON A.rechead_id= B.id INNER JOIN (SELECT inventory_id, SUM(quantity)issued_qty, rechead_id FROM recdetails WHERE accept = 1 AND inventory_id = ? GROUP BY inventory_id, rechead_id
                            )C ON B.id = C.rechead_id GROUP BY inventory_id
                        )B ON B.inventory_id = A.inventory_id
                    )A GROUP BY inventory_id
                )A
            )C ON A.id = C.inventory_id ORDER BY inout_date", [$id, $id, $date_1, $date_2, $id, $date_1, $date_2, $id, $date_1, $date_1, $id]);

        return compact('inOutDetails');
        
    }

    public function etd()
    {
        $Inventory = DB::SELECT('SELECT A.id, ((CASE WHEN receive_master_sheet IS NULL THEN 0 ELSE receive_master_sheet END) - (CASE WHEN issue_master_sheet IS NULL THEN 0 ELSE issue_master_sheet END))stock_master_sheet,
            ((CASE WHEN receive_qty IS NULL THEN 0 ELSE receive_qty END) - (CASE WHEN issue_qty IS NULL THEN 0 ELSE issue_qty END))stock, store_id, store_name, 
            (CASE WHEN receive_etd IS NULL THEN issue_etd ELSE receive_etd END)etd, issue_etd, cann_per_sheet, grade, accounts_code, weight, item, item_code, specification, unit, unit_price, item_image FROM(            
            SELECT id, store_id, item, item_code, specification, cann_per_sheet, grade, accounts_code, weight, unit, unit_price, item_image FROM inventories
            )A LEFT JOIN (
            SELECT inventory_id, SUM(master_sheet)receive_master_sheet, SUM(quantity)receive_qty, receive_etd FROM invenrecalls WHERE deleted_by = 0 GROUP BY inventory_id, receive_etd
            )B ON A.id = B.inventory_id LEFT JOIN(SELECT inventory_id, SUM(master_sheet)issue_master_sheet, SUM(quantity)issue_qty, issue_etd from recdetails WHERE accept = 1 GROUP BY inventory_id, issue_etd
            )C ON A.id = C.inventory_id and B.receive_etd = C.issue_etd LEFT JOIN(SELECT id, name store_name FROM stores
            )D ON A.store_id = D.id');
            
        return compact('Inventory');
    }

    public function balance($y1,$m1,$d1,$y2,$m2,$d2)
    {
        $date_1 = $y1.'-'.$m1.'-'.$d1;
        $date_2 = $y2.'-'.$m2.'-'.$d2;

        $balance = DB::SELECT("SELECT A.id, store_id, store_name, cann_per_sheet, item, item_image, item_code, specification, unit, unit_price, (
            CASE WHEN received_qty IS NULL THEN 0 ELSE received_qty END - CASE WHEN issued_qty IS NULL THEN 0 ELSE issued_qty END) opening, (CASE WHEN receiving_qty IS NULL THEN 0 ELSE receiving_qty END)receiving_qty, (CASE WHEN issueing_qty IS NULL THEN 0 ELSE issueing_qty END)issueing_qty, (
            CASE WHEN received_qty IS NULL THEN 0 ELSE received_qty END + CASE WHEN receiving_qty IS NULL THEN 0 ELSE receiving_qty END - CASE WHEN issued_qty IS NULL THEN 0 ELSE issued_qty END - CASE WHEN issueing_qty IS NULL THEN 0 ELSE issueing_qty END)closing FROM(
            SELECT id, store_id, cann_per_sheet, item, item_image, item_code, specification, unit, unit_price FROM inventories
            )A LEFT JOIN ( SELECT id, name store_name FROM stores
			)B ON A.store_id = B.id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)received_qty FROM(SELECT id FROM inventoryreceives WHERE deleted_by = 0 AND receive_date < ?
                )A LEFT JOIN(SELECT inventory_id, quantity, inventoryreceive_id FROM invenrecalls WHERE deleted_by = 0
                )B ON A.id = B.inventoryreceive_id WHERE inventory_id IS NOT null GROUP BY inventory_id
            )D ON A.id = D.inventory_id LEFT JOIN (
            SELECT inventory_id, SUM(quantity)receiving_qty FROM(SELECT id FROM inventoryreceives WHERE deleted_by = 0 AND receive_date BETWEEN ? and ?
                )A LEFT JOIN(SELECT inventory_id, quantity, inventoryreceive_id FROM invenrecalls WHERE deleted_by = 0
                )B ON A.id = B.inventoryreceive_id WHERE inventory_id IS NOT null GROUP BY inventory_id
            )E ON A.id = E.inventory_id LEFT JOIN (
            SELECT SUM(quantity)issued_qty, inventory_id FROM(SELECT rechead_id FROM inventoryissues WHERE  created_at < ?
                )A LEFT JOIN (SELECT id FROM recheads
                )B ON A.rechead_id= B.id LEFT JOIN (SELECT inventory_id, quantity, rechead_id FROM recdetails WHERE accept = 1
                )C ON B.id = C.rechead_id WHERE inventory_id IS NOT null GROUP BY inventory_id
            )F ON A.id = F.inventory_id LEFT JOIN (
            SELECT SUM(quantity)issueing_qty, inventory_id FROM(SELECT rechead_id FROM inventoryissues WHERE  created_at BETWEEN ? and ?
                )A LEFT JOIN (SELECT id FROM recheads
                )B ON A.rechead_id= B.id LEFT JOIN (SELECT inventory_id, quantity, rechead_id FROM recdetails WHERE accept = 1
                )C ON B.id = C.rechead_id WHERE inventory_id IS NOT null GROUP BY inventory_id
            )G ON A.id = G.inventory_id", [$date_1, $date_1, $date_2, $date_1, $date_1, $date_2]);

        return compact('balance');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        // 'item_code'=> 'required|unique:inventories,item_code,'.$inventory->id,
        $this->validate($request, [
            'item_code'=> 'required',
            'unit'=> 'required'
        ]); 

        if($request->item_image){
            $exploded = explode(',', $request->item_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'png'))
                $extesion = 'png';
            else
                $extesion = 'jpg';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path().'/images/item/'.$fileName;
            // $path = '/home/sustipe/inventory.sustipe.com/images/item/'.$fileName;

            // store new image
            file_put_contents($path, $decoded);
            
            // delete previous image
            $Inventory = Inventory::find($inventory->id);

            if($Inventory->item_image != 'noimage.jpg'){
                //Delete Image
                $path = public_path().'/images/item/'.$Inventory->item_image;
                // $path = '/home/sustipe/inventory.sustipe.com/images/item/'.$Inventory->item_image;
                @unlink($path);
            }

            // save Data
            $inventory->update($request->except('item_image') + [
                'item_image' => $fileName
            ]);

        } else {
            $inventory->update($request->except('item_image'));

            $image = Inventory::select('item_image')->where('id', $request->id)->get();
            $fileName = $image[0]['item_image'];
        }

        if(request()->expectsJson()){
            return response()->json([
                'fileName' => $fileName
            ]);
        } 

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        // delete previous image

        // $Inventory = Inventory::find($inventory->id);

        // if($Inventory->item_image != 'noimage.jpg'){
        //     //Delete Image
        //     $path = public_path().'/images/item/'.$Inventory->item_image;
        //     // $path = '/home/sustipe/inventory.sustipe.com/images/item/'.$Inventory->item_image;
        //     @unlink($path);
        // }

        // $inventory->delete();
    }
}
