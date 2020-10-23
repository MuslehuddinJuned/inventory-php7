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
        //return 'hi';
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
        // $path = '/home/sustipe/inventory.sustipe.com/file/attendance/'.$fileName;
        file_put_contents($path, $decoded);

        // $data = Excel::import(new AttendanceImport, $path);
        $data = Excel::toArray(new AttendanceImport, $path);
        if(count($data) > 0) {
            foreach($data as $key => $value)
            {
                foreach($value as $row)
                {
                    $insert_data[] = array(
                        'ac_no'     => $row['AC-No'],
                        'name'      => $row['Name'],
                        'department'=> $row['Department'],
                        'date'      => date_create($row['Date']),
                        'time'      => $row['Time'],
                        'in_time_1' => substr($row['Time'],0,5),
                        'out_time_1'=> substr($row['Time'],6,5),
                        'in_time_2' => substr($row['Time'],12,5),
                        'out_time_2'=> substr($row['Time'],strrpos($row['Time']," ") + 1,5)
                    );
                }
            }
        }

        if(!empty($insert_data)) {
            DB::table('attendances')->insert($insert_data);
        }
        
        @unlink($path);

        // if(request()->expectsJson()){
        //     return response()->json([
        //         'attendance' => $myArray[0]
        //     ]);
        // }
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
        $Attendance = Attendance::where('date', $date)->orderBy('ac_no', 'asc')->get();

        return compact('Attendance');
    }

    public function daily($attendance)
    {
        $date = date_create($attendance);
        $Attendance = DB::SELECT("SELECT A.id, employee_id, first_name, last_name, designation, department, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM(
            SELECT id, employee_id, first_name, last_name, designation, department FROM employees WHERE deleted_by = 0 and status = 'active'
            )A LEFT JOIN (SELECT id, ac_no, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date = ?
            )B ON A.employee_id = B.ac_no ORDER BY employee_id", [$date]);

        return compact('Attendance');
    }

    public function personnel($id, $start, $end)
    {
        $start = date_create($start);
        $end = date_create($end);
        $Attendance = DB::SELECT("SELECT A.id, employee_id, first_name, last_name, designation, department, employee_image, weekly_holiday, null status,
            date, DAYNAME(date)day, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM(
            SELECT id, employee_id, first_name, last_name, designation, department, employee_image, weekly_holiday FROM employees WHERE id = ?
            )A LEFT JOIN (SELECT id, ac_no, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date BETWEEN ? AND ?
            )B ON A.employee_id = B.ac_no", [$id, $start, $end]);
            
        $Leave = DB::SELECT("SELECT leave_type, leave_start, leave_end, day_count, employee_id 
            FROM usedleaves WHERE employee_id = ? AND ((leave_start BETWEEN ? AND ?) OR (leave_end BETWEEN ? AND ?))", [$id, $start, $end, $start, $end]);

        $Holiday = DB::SELECT("SELECT  event, yearly_holiday FROM holidays WHERE yearly_holiday BETWEEN ? AND ?", [$start, $end]);

        return compact('Attendance', 'Leave', 'Holiday');
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
        $attendance->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy($date)
    {
        DB::SELECT('DELETE FROM attendances WHERE date = ?', [$date]);
    }
}
