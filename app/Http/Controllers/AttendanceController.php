<?php

namespace App\Http\Controllers;

use App\Attendance;
use DB;
use Illuminate\Http\Request;

class AttendanceController extends Controller
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
        return 'hi';
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
        $exploded = explode(',', $request->name);
        $decoded = base64_decode($exploded[1]);

        // if(str_contains($exploded[0], 'xls'))
        //     $extesion = 'xls';
        // else
        //     $extesion = 'xlsx';

        $fileName = str_random().'.xlsx';
        // $fileName = str_random().'.xlsx'.$extesion;
        $path = public_path().'/file/attendance/'.$fileName;
        // $path = '/home/sustipe/inventory.sustipe.com/file/attendance/'.$fileName;

        // store new image
        // file_put_contents($path, $decoded);
        // $productHead = $request->user()->producthead()->create($request->except('product_image') + [
        //     'product_image' => $fileName
        // ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
