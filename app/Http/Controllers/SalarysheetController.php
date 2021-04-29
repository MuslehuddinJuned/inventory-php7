<?php

namespace App\Http\Controllers;

use App\Salarysheet;
use DB;
use Illuminate\Http\Request;

class SalarysheetController extends Controller
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
        $Department = DB::SELECT("SELECT DISTINCT department FROM employees WHERE deleted_by = 0 ORDER BY department");

        return compact('Department');
    }

    // public function ot($time, $in_time_1, $out_time_2, $ot, $ot_extra) {
    //     if ($ot) return $ot + $ot_extra;
    //     if(!$time || strlen($out_time_2) < 5 || strlen($in_time_1) < 5) return 0;
        
    //     $diff = floor((strtotime($out_time_2) - strtotime($in_time_1))/3600);

    //     if(($diff - 9) > 0) return $diff - 9;
    //     return 0;
    // }

    public function ot($time, $in_time_1, $out_time_2, $ot, $ot_extra) {
        if ($ot) return $ot;
        //if ($ot) return $ot + $ot_extra;   //extra OT will not be calculated
        if(!$time || strlen($out_time_2) < 5 || strlen($in_time_1) < 5) return 0;
        
        $diff = floor((strtotime($out_time_2) - strtotime($in_time_1))/3600);

        if(($diff - 9) > 0) {
            if(($diff - 9) > 1) return 2; // extra OT will not be calculated
            else return 1;
        }
        return 0;
    }

    public function weeks($weeklyHoliday, $HolidayArray, $mnth, $start_date) {
        $count = 0;
        preg_match_all('!\d+!', $weeklyHoliday, $matches);
        $weeks = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        for ($i=0; $i < count($matches[0]); $i++) { 
            $offDays = [];

            $offDays[0] = date('d',strtotime("first {$weeks[$matches[0][$i]]} of ".$mnth));
            $offDays[1] = $offDays[0] + 7;
            $offDays[2] =  $offDays[0] + 14;
            $offDays[3] =  $offDays[0] + 21;
            $offDays[4] = date('d',strtotime("last {$weeks[$matches[0][$i]]} of ".$mnth));

            if($offDays[3] == $offDays[4]){
                unset($offDays[4]);
            }

            foreach($offDays as $off){
                if(strtotime(date('Y-m-d',strtotime(date($mnth."-".$off)))) > strtotime($start_date)
                    && !in_array(date('Y-m-d',strtotime(date($mnth."-".$off))), $HolidayArray)) {
                        $count++;
                }
            }
        }

        return $count;
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
        $duplicate = Salarysheet::where('year_mnth', substr($request->start, 0, 7))->count();
        if ($duplicate == 0) {
            $start = date_create($request->start);
            $end = date_create($request->end);
            $date = $request->date;
            $month_array = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            if(substr($date, 0, 4)%4 == 0) $month_array[2] = 29;
            $days = $month_array[substr($date, 5, 2)];

            $Salary = DB::SELECT("SELECT A.id employee_id, null year_mnth, ? no_fo_days, (basic_pay/?) basic_daily, basic_pay basic_monthly, medic_alw medic_allowance, house_rent, 
                ta, da, fixed_allowance, providant_fund pf, tax, total_salary salary, 82 covert_rate, COALESCE(current_pay_doller,(total_salary-ta-da-fixed_allowance+providant_fund+tax)/82) salary_usd, 0 attendance_bonus, 0 production_bonus, 0 worked_friday_hour, 
                0 worked_friday_amount, 0 worked_holiday_hour, 0 worked_holiday_amount, (basic_pay*2/208)ot_rate, 0 ot_hour, 0 ot_amount, 
                0 attendance_allowance, 0 present_days, 0 holidays, 0 absent_days, 0 absent_amount, 0 leave_days, 0 not_for_join_days, 0 not_for_join_amount, 0 gross_pay, 0 total_deduction, 0 net_pay FROM(SELECT id FROM employees WHERE deleted_by = 0 and status = 'active'
                )A LEFT JOIN (SELECT id, current_pay_doller, COALESCE(basic_pay, 0)basic_pay, COALESCE(medic_alw, 0)medic_alw, COALESCE(house_rent, 0)house_rent, COALESCE(ta, 0)ta, COALESCE(da, 0)da, COALESCE(fixed_allowance, 0)fixed_allowance, COALESCE(other_field, 0)other_field, COALESCE(other_pay, 0)other_pay, COALESCE(providant_fund, 0)providant_fund, COALESCE(tax, 0)tax, COALESCE(total_salary, 0)total_salary, bank_name, acc_no, employee_id FROM salaries
                )B ON A.id = B.employee_id", [$days, $days]);

            $Holiday = DB::SELECT("SELECT  event, yearly_holiday FROM holidays WHERE yearly_holiday BETWEEN ? AND ?", [$start, $end]);        

            $employee = DB::SELECT("SELECT employee_id, start_date, weekly_holiday FROM employees WHERE deleted_by = 0 and status = 'active'");

            $Attendance_1 = DB::SELECT("SELECT ac_no, date, time, in_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date BETWEEN ? AND ? ORDER BY ac_no", [$start, $end]);

            for ($i=0; $i < count($Salary); $i++) {

                $Attendance = []; $m=0;
                for ($l=0; $l < count($Attendance_1); $l++) { 
                    if($Attendance_1[$l]->ac_no == $employee[$i]->employee_id){
                        $Attendance[] = $Attendance_1[$l];
                        $m++;
                    } elseif ($m>0) {
                        break;
                    }
                }
                
                $Leave = DB::SELECT("SELECT leave_type, leave_start, leave_end, day_count, employee_id 
                FROM usedleaves WHERE employee_id = ? AND ((leave_start BETWEEN ? AND ?) OR (leave_end BETWEEN ? AND ?))", [$Salary[$i]->employee_id, $start, $end, $start, $end]);
                
                //for yearly holiday
                $HolidayArray = [];
                for ($j=0; $j < count($Holiday); $j++) { 
                    if(strtotime($Holiday[$j]->yearly_holiday) > strtotime($employee[$i]->start_date))
                    $HolidayArray[] = $Holiday[$j]->yearly_holiday;
                }

                //for leave
                $earned_leave_count = 0;
                for ($j = 0; $j < count($Leave); $j++) {
                    for ($k = 0; $k < $Leave[$j]->day_count; $k++) {
                        $dates = date('Y-m-d', strtotime($Leave[$j]->leave_start .'+'.$k.' day'));
                        if(substr($request->start, 0, 7) == substr($dates, 0, 7) && $Leave[$j]->leave_type != 'unpaid_leave'){
                            $Salary[$i]->leave_days++;
                            if($Leave[$j]->leave_type == 'earned_leave' || $Leave[$j]->leave_type == 'compensatory_leave') $earned_leave_count++;
                        }           
                    }
                }

                // for present_day, absent_day, Over Time            
                for ($j = 0; $j < count($Attendance); $j++) {
                    if (strlen($Attendance[$j]->in_time_1) > 0 && $Attendance[$j]->in_time_1 != '00:00') {
                        $Salary[$i]->present_days++;
                        $Salary[$i]->ot_hour += SalarysheetController::ot($Attendance[$j]->time, $Attendance[$j]->in_time_1, $Attendance[$j]->out_time_2, $Attendance[$j]->ot, $Attendance[$j]->ot_extra);
                    }
                }

                // to calculate  weekly_holiday
                $weeklyHolidays = SalarysheetController::weeks($employee[$i]->weekly_holiday, $HolidayArray, substr($request->start, 0, 7), $employee[$i]->start_date);
                
                $Salary[$i]->holidays += (count($HolidayArray) + $weeklyHolidays);
                $Salary[$i]->not_for_join_days = floor((strtotime($employee[$i]->start_date) - strtotime($request->start))/(24*3600));
                if($Salary[$i]->not_for_join_days < 0) $Salary[$i]->not_for_join_days = 0;
                $Salary[$i]->absent_days = $days - $Salary[$i]->present_days - $Salary[$i]->holidays - $Salary[$i]->not_for_join_days;

                if($days - $Salary[$i]->present_days - $Salary[$i]->holidays - $earned_leave_count == 0) {
                    $Salary[$i]->attendance_bonus = 400;
                    $Salary[$i]->attendance_allowance = 2000;
                }
                $Salary[$i]->year_mnth = substr($request->start, 0, 7);
                $Salary[$i]->ot_amount = $Salary[$i]->ot_hour * $Salary[$i]->ot_rate;
                $Salary[$i]->absent_amount = $Salary[$i]->absent_days * (($Salary[$i]->basic_monthly/$days) + ($Salary[$i]->ta/$days) + ($Salary[$i]->da/$days));
                $Salary[$i]->salary = $Salary[$i]->salary - $Salary[$i]->ta - $Salary[$i]->da - $Salary[$i]->fixed_allowance + $Salary[$i]->pf + $Salary[$i]->tax;
                $Salary[$i]->gross_pay = $Salary[$i]->salary + $Salary[$i]->ta + $Salary[$i]->da + $Salary[$i]->ot_amount + $Salary[$i]->attendance_bonus + $Salary[$i]->attendance_allowance;
                $Salary[$i]->not_for_join_amount = $Salary[$i]->not_for_join_days * ($Salary[$i]->gross_pay/$days);
                $Salary[$i]->total_deduction = $Salary[$i]->pf + $Salary[$i]->tax + $Salary[$i]->absent_amount + $Salary[$i]->not_for_join_amount;
                $Salary[$i]->net_pay = $Salary[$i]->gross_pay - $Salary[$i]->total_deduction;
            }

            $data= json_decode( json_encode($Salary), true);
            DB::table('salarysheets')->insert($data);
        }

        // $Salarysheet = DB::SELECT("SELECT A.id, checked, employees.employee_id, first_name, last_name, designation, department, section, work_location, start_date, employee_image, year_mnth, no_fo_days, 
        // ROUND(basic_daily, 2)basic_daily, ROUND(basic_monthly, 2)basic_monthly, ROUND(house_rent, 2)house_rent, ROUND(medic_allowance, 2)medic_allowance, ROUND(A.salary, 2)salary, ROUND(salary_usd, 2)salary_usd, 
        // covert_rate, ROUND(ta, 2)ta, ROUND(da, 2)da, ROUND(attendance_bonus, 2)attendance_bonus, ROUND(production_bonus, 2)production_bonus, ROUND(worked_friday_hour, 2)worked_friday_hour, 
        // ROUND(worked_friday_amount, 2)worked_friday_amount, ROUND(worked_holiday_hour, 2)worked_holiday_hour, ROUND(worked_holiday_amount, 2)worked_holiday_amount, ROUND(ot_rate, 2)ot_rate, ot_hour, ot_amount, 
        // ROUND(fixed_allowance, 2)fixed_allowance, ROUND(attendance_allowance, 2)attendance_allowance, ROUND(fixed_allowance+attendance_allowance, 2)total_allowance, present_days, holidays, absent_days, absent_amount, leave_days, advance, pf, 
        // ROUND(tax, 2)tax, ROUND(deducted, 2)deducted, not_for_join_days, ROUND(not_for_join_amount, 2)not_for_join_amount, lay_off_days, ROUND(lay_off_amount, 2)lay_off_amount, suspense_days, 
        // ROUND(suspense_amount, 2)suspense_amount, ROUND(gross_pay, 2)gross_pay, ROUND(total_deduction, 2)total_deduction, ROUND(net_pay, 2)net_pay FROM (
        //     SELECT *  FROM salarysheets WHERE year_mnth = ?
        // )A LEFT JOIN employees ON A.employee_id = employees.id", [substr($request->start, 0, 7)]);

        $Salarysheet = DB::SELECT("SELECT A.id, checked, employees.employee_id, first_name, last_name, designation, department, section, work_location, start_date, employee_image, year_mnth, no_fo_days, basic_daily, basic_monthly, house_rent, medic_allowance, A.salary, salary_usd, covert_rate, ta, da, attendance_bonus, production_bonus, worked_friday_hour, worked_friday_amount, worked_holiday_hour, worked_holiday_amount, ot_rate, ot_hour, ot_amount, fixed_allowance, attendance_allowance, (fixed_allowance+attendance_allowance)total_allowance, present_days, holidays, absent_days, absent_amount, leave_days, advance, pf, tax, deducted, not_for_join_days, not_for_join_amount, lay_off_days, lay_off_amount, suspense_days, suspense_amount, gross_pay, total_deduction, net_pay FROM (
            SELECT *  FROM salarysheets WHERE year_mnth = ?
            )A LEFT JOIN employees ON A.employee_id = employees.id", [substr($request->start, 0, 7)]);

        if(request()->expectsJson()){
            return response()->json([
                'Salarysheet' => $Salarysheet
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function show($val)
    {
        if ($val == 'pf') {
            $pfSummary = DB::SELECT("SELECT id, A.employee_id, first_name, last_name, designation, department, pf FROM (
                SELECT id, employee_id, first_name, last_name, designation, department FROM employees WHERE deleted_by = 0 and status = 'active'
                )A LEFT JOIN(SELECT employee_id, SUM(pf)pf FROM salarysheets GROUP BY employee_id
                )B ON A.id = B.employee_id");
    
            $pfDetails = DB::SELECT("SELECT id, A.employee_id, first_name, last_name, designation, department, section, work_location, 
                start_date, employee_image, year_mnth, pf FROM (SELECT id, employee_id, first_name, last_name, designation, department, 
                section, work_location, start_date, employee_image FROM employees WHERE deleted_by = 0 and status = 'active'
                )A LEFT JOIN(SELECT employee_id, year_mnth, pf FROM salarysheets
                )B ON A.id = B.employee_id");
    
            return compact('pfSummary', 'pfDetails');
        } else {
            // For Pay Slip
            $Salarysheet = DB::SELECT("SELECT A.id, checked, employees.employee_id, first_name, last_name, designation, department, section, work_location, start_date, employee_image, year_mnth, no_fo_days, basic_daily, basic_monthly, house_rent, medic_allowance, A.salary, salary_usd, covert_rate, ta, da, attendance_bonus, production_bonus, worked_friday_hour, worked_friday_amount, worked_holiday_hour, worked_holiday_amount, ot_rate, ot_hour, ot_amount, fixed_allowance, attendance_allowance, (fixed_allowance+attendance_allowance)total_allowance, present_days, holidays, absent_days, absent_amount, leave_days, advance, pf, tax, deducted, not_for_join_days, not_for_join_amount, lay_off_days, lay_off_amount, suspense_days, suspense_amount, gross_pay, total_deduction, net_pay FROM (SELECT *  FROM salarysheets WHERE year_mnth = ?
            )A LEFT JOIN employees ON A.employee_id = employees.id", [$val]);

            return compact('Salarysheet');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function edit($date)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Salarysheet $salarysheet)
    {
        $salarysheet->update($request->except('id', 'employee_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function destroy($date)
    {
        DB::SELECT('DELETE FROM salarysheets WHERE year_mnth = ?', [$date]);
    }
}
