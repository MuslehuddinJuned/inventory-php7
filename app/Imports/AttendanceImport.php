<?php

namespace App\Imports;

use App\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class AttendanceImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Attendance([
            'ac_no'     => $row['AC-No'],
            'name'      => $row['Name'],
            'department'=> $row['Department'],
            'date'      => date_create($row['Date']),
            'time'      => $row['Time'],
            'in_time_1' => substr($row['Time'],0,5),
            'out_time_1' => substr($row['Time'],6,5),
            'in_time_2'=> substr($row['Time'],12,5),
            'out_time_2'=> substr($row['Time'],strrpos($row['Time']," "),6),
        ]);
    }
}
