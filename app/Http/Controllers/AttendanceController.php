<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Imports\AttendanceImport;
use DB;
use Excel;
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
        
        $exploded = explode(',', $request->uploadFile);
        $decoded = base64_decode($exploded[1]);

        if(str_contains($exploded[0], 'spreadsheetml'))
            $extesion = 'xlsx';
        else
            $extesion = 'xls';

        $fileName = str_random().'.'.$extesion;
        $path = public_path().'/file/attendance/'.$fileName;
        file_put_contents($path, $decoded);
        // $path = '/home/sustipe/inventory.sustipe.com/file/attendance/'.$fileName;

        $data = Excel::import(new AttendanceImport, $path);
        $myArray = Excel::toArray(new AttendanceImport, $path);
        
        // @unlink('/storage/framework/laravel-excel/laravel-excel-'.$fileName);
        @unlink($path);

        if(request()->expectsJson()){
            return response()->json([
                'attendance' => $myArray[0]
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show($attendance)
    {
        $date = date_create($attendance);
        $Attendance = Attendance::where('date', $date)->get();

        return compact('Attendance');
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
