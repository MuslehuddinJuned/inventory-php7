<?php

namespace App\Http\Controllers;

use App\M_attendance;
use Illuminate\Http\Request;
use DB;

class MAttendanceController extends Controller
{
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
        switch ($request['others']) {
            case 'In':
                $request['in_time'] = date('H:i:s', strtotime(gmdate("H:i:s")) + 3600*6);
                $request['others'] = null;
                break;
            case 'Out':
                $request['out_time'] = date('H:i:s', strtotime(gmdate("H:i:s")) + 3600*6);
                $request['others'] = null;
                break;            
            default:
                # code...
                break;
        }

        $M_attendance = new M_attendance;
        $M_attendance->others = $request['others'];
        $M_attendance->in_time = $request['in_time'];
        $M_attendance->out_time = $request['out_time'];
        $M_attendance->att_date = $request['att_date'];
        $M_attendance->employee_id = $request['employee_id'];

        $M_attendance->save();

        // if(request()->expectsJson()){
        //     return response()->json([
        //         'attendance' => $attendance
        //     ]);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\M_attendance  $m_attendance
     * @return \Illuminate\Http\Response
     */
    public function show($att_date)
    {
        $attendance = DB::SELECT('SELECT A.id, B.id att_id, name, id_no, designation, att_date, in_time, out_time, others FROM(
            SELECT id, name, id_no, designation FROM m_employees
                )A LEFT JOIN (SELECT id, att_date, in_time, out_time, others, employee_id FROM m_attendances WHERE att_date = ?
                )B ON A.id = B.employee_id', [$att_date]);

        return compact ('attendance');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\M_attendance  $m_attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(M_attendance $m_attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\M_attendance  $m_attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, M_attendance $m_attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\M_attendance  $m_attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(M_attendance $m_attendance)
    {
        //
    }
}
