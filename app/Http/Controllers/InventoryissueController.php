<?php

namespace App\Http\Controllers;

use App\Inventoryissue;
use Illuminate\Http\Request;

class InventoryissueController extends Controller
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
        $Inventoryissue = Inventoryissue::get();
        return compact ('Inventoryissue');
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
            'item_code'=> 'required|unique:inventories,item_code'
        ]);

        $InventoryIssue = $request->user()->inventories()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryIssue' => $InventoryIssue
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function show(Inventoryissue $inventoryissue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventoryissue $inventoryissue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventoryissue $inventoryissue)
    {
        $this->validate($request, [
            'item_code'=> 'required|unique:inventories,item_code,'.$inventory->id
        ]); 

        $inventoryissue->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventoryissue  $inventoryissue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventoryissue $inventoryissue)
    {
        $inventoryissue->delete();
    }
}
