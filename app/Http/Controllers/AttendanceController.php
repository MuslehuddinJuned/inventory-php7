<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Imports\AttendanceImport;
use DB;
use Excel;
use DateTime;
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

        /**************************start mass entry****************************/
        // $begin = new DateTime( "2015-01-01" );
        // $end   = new DateTime( "2015-01-31" );
        // for ($i = $begin; $i <= $end; $i->modify('+1 day')) { 
        //     if(count($data) > 0 && date('D', strtotime($i->format("Y-m-d"))) != 'Fri') {
        //         foreach($data as $key => $value)
        //         {
        //             foreach($value as $row)
        //             {
        //                 $insert_data[] = array(
        //                     'ac_no'     => $row['AC-No'],
        //                     'name'      => $row['Name'],
        //                     'department'=> $row['Department'],
        //                     'date'      => $i->format("Y-m-d"),
        //                     'time'      => $row['Time'],
        //                     'in_time_1' => substr($row['Time'],0,5),
        //                     'out_time_1'=> substr($row['Time'],6,5),
        //                     'in_time_2' => substr($row['Time'],12,5),
        //                     'out_time_2'=> substr($row['Time'],strrpos($row['Time']," ") + 1,5)
        //                 );
        //             }
        //         }
        //         if(!empty($insert_data)) {
        //             DB::table('attendances')->insert($insert_data);
        //             $insert_data = [];
        //         }
        //     }
        // }

        /****************end mass entry*******************/



        /****************start regular entry*******************/

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

        /****************end regular entry*******************/
        
        @unlink($path);
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
        // $Attendance = DB::SELECT("SELECT A.id, employee_id, first_name, last_name, designation, department, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM(
        //     SELECT id, employee_id, first_name, last_name, designation, department FROM employees WHERE deleted_by = 0 and status = 'active'
        //     )A LEFT JOIN (SELECT id, ac_no, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date = ?
        //     )B ON A.employee_id = B.ac_no ORDER BY employee_id", [$date]);

        $Attendance = DB::SELECT("SELECT id, ac_no employee_id, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date = ? ORDER BY employee_id", [$date]);
        $Employee = DB::SELECT("SELECT id, employee_id, first_name, designation, department FROM employees WHERE deleted_by = 0 and status = 'active' ORDER BY employee_id");

        // $array1 = json_decode(json_encode($Attendance), true);
        // $array2 = json_decode(json_encode($Employee), true);
        // $someVariable = 'someValue';
        // $results = array_reduce(array_merge($array1, $array2), function ($carry, $item) use ($someVariable) {
        //     if (isset($carry[$item['employee_id']])) {
        //         $carry[$item['employee_id']] = array_merge($carry[$item['employee_id']], $item);
        //     } else {
        //         $carry[$item['employee_id']] = $item;
        //     }
        //     return $carry;
        // }, array());

        // $result = $results;


        return compact('Attendance', 'Employee');
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

    public function absent($id, $year)
    {
        $absentList = DB::SELECT("SELECT ac_no, 
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'January') THEN 1 ELSE null END)absent_jan,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'February') THEN 1 ELSE null END)absent_feb,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'March') THEN 1 ELSE null END)absent_mar,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'April') THEN 1 ELSE null END)absent_apr,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'May') THEN 1 ELSE null END)absent_may,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'June') THEN 1 ELSE null END)absent_jun,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'July') THEN 1 ELSE null END)absent_jul,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'August') THEN 1 ELSE null END)absent_aug,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'September') THEN 1 ELSE null END)absent_sep,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'October') THEN 1 ELSE null END)absent_oct,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'November') THEN 1 ELSE null END)absent_nov,
        COUNT(CASE WHEN ((CHAR_LENGTH(time)<4 || time = '00:00' || time IS null) && DAYNAME(date) != 'Friday' && MONTHNAME(date) = 'December') THEN 1 ELSE null END)absent_dec 
        FROM attendances WHERE ac_no = ? AND YEAR(date) = ? GROUP BY ac_no ORDER BY date", [$id, $year]);

        $holiday = DB::SELECT("SELECT 
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'January' THEN 1 ELSE null END)holiday_jan,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'February' THEN 1 ELSE null END)holiday_feb,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'March' THEN 1 ELSE null END)holiday_mar,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'April' THEN 1 ELSE null END)holiday_apr,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'May' THEN 1 ELSE null END)holiday_may,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'June' THEN 1 ELSE null END)holiday_jun,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'July' THEN 1 ELSE null END)holiday_jul,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'August' THEN 1 ELSE null END)holiday_aug,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'September' THEN 1 ELSE null END)holiday_sep,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'October' THEN 1 ELSE null END)holiday_oct,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'November' THEN 1 ELSE null END)holiday_nov,
        COUNT(CASE WHEN MONTHNAME(yearly_holiday) = 'December' THEN 1 ELSE null END)holiday_dec
        FROM holidays WHERE YEAR(yearly_holiday) = ?", [$year]);

        return compact('absentList', 'holiday');
    }

    public function jobcard($department, $start, $end)
    {
        $start = date_create($start);
        $end = date_create($end);
        $Attendance = DB::SELECT("SELECT A.id, employee_id, first_name, last_name, designation, department, employee_image, weekly_holiday, null status,
            date, DAYNAME(date)day, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM(
            SELECT id, employee_id, first_name, last_name, designation, department, employee_image, weekly_holiday FROM employees WHERE department = ? and deleted_by = 0 and status = 'active'
            )A LEFT JOIN (SELECT id, ac_no, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date BETWEEN ? AND ?
            )B ON A.employee_id = B.ac_no ORDER BY employee_id, date", [$department, $start, $end]);
            
        $Leave = DB::SELECT("SELECT id, department, leave_type, leave_start, leave_end, day_count, A.employee_id FROM (
            SELECT id, department, employee_id FROM employees WHERE department = ? AND deleted_by = 0 AND status = 'active'
            )A LEFT JOIN (SELECT leave_type, leave_start, leave_end, day_count, employee_id FROM usedleaves WHERE (leave_start BETWEEN ? AND ?) OR (leave_end BETWEEN ? AND ?)
            )B ON A.id = B.employee_id ORDER BY employee_id", [$department, $start, $end, $start, $end]);

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
