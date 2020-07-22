<?php

namespace App\Http\Controllers;

use App\Invenrecall;
use Illuminate\Http\Request;
use DB;

class InvenrecallController extends Controller
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
        //
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
        $this->validate($request, [
            'inventory_id'=> 'required'
        ]); 

        $Invenrecall = $request->user()->invenrecall()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryreceivesdetailsID' => $Invenrecall->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventoryrec_d= DB::SELECT('SELECT id, quantity, price, remarks, user_id, inventory_id, inventoryreceive_id, created_at, updated_at FROM invenrecalls WHERE inventoryreceive_id = ?', [$id]);

        return compact('inventoryrec_d');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function edit(Invenrecall $invenrecall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invenrecall $invenrecall)
    {
        $invenrecall->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invenrecall  $invenrecall
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invenrecall $invenrecall)
    {
        $invenrecall->delete();
    }
}
