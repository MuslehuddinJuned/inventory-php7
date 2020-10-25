<?php

namespace App\Http\Controllers;

use App\Store;
use DB;
use Illuminate\Http\Request;

class StoreController extends Controller
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
        $Store = DB::SELECT('SELECT id value, (CASE WHEN account_code IS NULL THEN name ELSE CONCAT(account_code, ": ", name) END)text, remarks  FROM stores ORDER BY account_code');

        return compact('Store');
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
            'account_code'=> 'required|unique:stores,account_code',
            'name'=> 'required|unique:stores,name'
        ]);

        $request->user()->store()->create($request->all());
        
        $Store = DB::SELECT('SELECT id value, (CASE WHEN account_code IS NULL THEN name ELSE CONCAT(account_code, ": ", name) END)text, remarks  FROM stores ORDER BY account_code');

        if(request()->expectsJson()){
            return response()->json([
                'Store' => $Store
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $this->validate($request, [
            'account_code'=> 'required|unique:stores,account_code,'.$store->id,
            'name'=> 'required|unique:stores,name,'.$store->id,
        ]);

        $store->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }
}
