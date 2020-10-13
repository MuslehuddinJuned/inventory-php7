<?php

namespace App\Http\Controllers;

use App\Rechead;
use Illuminate\Http\Request;
use DB;

class RecheadController extends Controller
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
        $Rechead = DB::SELECT("SELECT A.id, requisition_no, requisition_by, GROUP_CONCAT(po_no SEPARATOR ' | ')po_no, remarks, accept, created_at, store_id, store_name FROM (
            SELECT id, requisition_no, requisition_by, remarks, accept, created_at FROM recheads
            )A LEFT JOIN (
            SELECT inventory_id, rechead_id, polist_id FROM recdetails
            )B ON A.id = B.rechead_id LEFT JOIN(
            SELECT id, store_id FROM inventories
            )C ON B.inventory_id = C.id LEFT JOIN ( SELECT id, name store_name FROM stores
			)D ON C.store_id = D.id LEFT JOIN ( SELECT id, po_no FROM polists
			)E ON B.polist_id = E.id GROUP BY A.id, requisition_no, requisition_by, remarks, accept, created_at, store_id, store_name ORDER BY created_at DESC");

        return compact('Rechead');
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
        if(strlen($request->store)<2) $store = '0' . $request->store;
        else $store = $request->store;

        $requisition_no = date("ym") . $store;
        $check = date("ym") . $store . '%';

        $max_id = DB::SELECT('SELECT MAX(requisition_no) requisition_no FROM recheads WHERE requisition_no LIKE ?', [$check]);
                        
        if($max_id[0]->requisition_no){
            $requisition_no = $max_id[0]->requisition_no;
            $requisition_no++;
        } else $requisition_no = $requisition_no . '0001';

        $Rechead = $request->user()->rechead()->create($request->except('requisition_no') + [
            'requisition_no' => $requisition_no
        ]);

        if(request()->expectsJson()){
            return response()->json([
                'RecheadID' => $Rechead->id,
                'requisition_no' => $requisition_no
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function show(Rechead $rechead)
    {
        // return 'er';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function edit(Rechead $rechead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rechead $rechead)
    {
        $check = Rechead::select('accept')->where('id', $rechead->id)->get();

        if(!$check[0]['accept'])
        $rechead->update($request->except('requisition_no'));
        else errors;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rechead $rechead)
    {
        $rechead->delete();
    }
}
