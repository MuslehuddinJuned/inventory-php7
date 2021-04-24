<template>
    <div class="container-fluid">
        <div class="card filterable" :class="noprint">
            <div class="card-header row m-0">
                <div class="col-md-6">
                    <h3 class="panel-title float-left">{{ $t('daily_attendance') }} ({{attendance_date | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY') }})</h3>
                </div>                     
                <div class="col-md-6 noprint">
                    <div class="m-sm-0 input-group col-md-12 float-md-right float-sm-left">
                        <input type="date" v-model="attendance_date" class="form-control">
                        <div class="input-group-append">
                            <div @click="fetchData(false)" class="input-group-text pointer"><b-icon icon="search"></b-icon></div>
                        </div>
                        <div class="input-group-append">
                            <div @click="fetchData(true)" class="input-group-text pointer"><b-icon icon="zoom-in"></b-icon></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 card-body noprint"><b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select></div>
            <div class="card-body m-0 p-0">
                <div class="card-header d-flex align-items-center noprint">
                    <download-excel
                        id="tooltip-target-1"
                        class="btn btn-outline-default btn-sm mr-3"
                        :title="DepartmentName + ' Daily Attendance ' + attendance_date"
                        :data="attendanceByDepartment"
                        :fields="json_fields"
                        worksheet="Daily Attendance"
                        name="Daily Attendance.xls">
                        <b-icon icon="file-earmark-spreadsheet-fill"></b-icon>
                    </download-excel>
                    <b-tooltip target="tooltip-target-1" triggers="hover">
                        {{$t('save_this_table_to_excel')}}
                    </b-tooltip>
                    <b-form-group class="mb-0 mr-auto">
                        <b-input-group size="sm">
                            <b-form-input
                            v-model="filter"
                            type="search"
                            id="filterInput"
                            :placeholder= "TypetoSearch"
                            ></b-form-input>
                            <b-input-group-append>
                            <b-button :disabled="!filter" @click="filter = ''">{{ $t('Clear')}}</b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                    <b-form-group size="sm" class="mb-0 ml-auto">
                        <b-form-select
                            v-model="perPage"
                            id="perPageSelect"
                            size="sm"
                            :options="pageOptions"
                        ></b-form-select>
                    </b-form-group>                        
                </div>
                <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover stacked="md"
                :items="attendanceByDepartment"
                :fields="fields"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :filterIncludedFields="filterOn"
                :tbody-transition-props="transProps"
                @filtered="onFiltered"
                class="table-transition"
                @row-clicked="(item) => viewDetails(item.id)"
                style="cursor : pointer"
                >
                <template v-slot:table-busy>
                    <div class="text-center text-success my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>{{$t('loading')}}</strong>
                    </div>
                </template>
                <template v-slot:cell(index)="row">
                    {{ row.index+1 }}
                </template>
                </b-table>
                
                <div class="col-12 mx-auto p-0 noprint">
                    <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"                            
                    first-text="First"
                    prev-text="Prev"
                    next-text="Next"
                    last-text="Last"
                    align="center"
                    size="sm"
                    class="mdb bg-light m-0 rounded-0"
                    aria-controls="table-transition-example"
                    last-number
                    ></b-pagination>
                </div>
            </div>
        </div>
        <!-- Start view Details Modal -->
        <b-modal ref="dataView" id="dataView" size="xxl" :title="$t('personnel_attendance')" no-close-on-backdrop>
            <div v-if="perAttendanceList.length > 0" class="modal-body m-0 p-0 mb-2">
                <div class="col-md-12 m-0 p-0">
                    <span class="font-weight-bold mr-4">{{ $t('name')}}: {{perAttendanceList[0]['first_name']}}</span>
                    <span class="font-weight-bold mr-4">ID: {{perAttendanceList[0]['employee_id']}}</span>
                    <span class="font-weight-bold mr-4">{{ $t('designation')}}: {{perAttendanceList[0]['designation']}}</span>
                    <span class="font-weight-bold mr-4 mb-3">{{ $t('department')}}: {{perAttendanceList[0]['department']}}</span>
                    <download-excel
                        id="tooltip-target-1"
                        class="btn btn-outline-default btn-sm mr-3"
                        :title="'Name: ' + perAttendanceList[0]['first_name'] + ', ID: ' + perAttendanceList[0]['employee_id'] + ', Designation: ' + perAttendanceList[0]['designation'] + ', Department: ' + perAttendanceList[0]['department'] + ', Month: ' + attendance_date "
                        :data="perAttendanceList"
                        :fields="indivisual_json_fields"
                        worksheet="Daily Attendance"
                        name="Daily Attendance.xls">
                        <b-icon icon="file-earmark-spreadsheet-fill"></b-icon>
                    </download-excel>
                    <b-tooltip target="tooltip-target-1" triggers="hover">
                        {{$t('save_this_table_to_excel')}}
                    </b-tooltip>
                </div>
                <!-- <div class="col-md-12 ">
                    <span class="font-weight-bold mr-4">{{ $t('total_days')}}: {{perAttendanceList.length}}</span>
                    <span class="font-weight-bold mr-4">{{ $t('present')}}: {{n_present}}</span>
                    <span class="font-weight-bold mr-4 mb-3">{{ $t('absent')}}: {{n_absent}}</span>
                    <span class="font-weight-bold mr-4">{{ $t('yearly_holidays')}}: {{n_holiday}}</span>
                    <span class="font-weight-bold mr-4">{{ $t('weekly_holidays')}}: {{n_weeklyholiday}}</span>
                    <span class="font-weight-bold mr-4 mb-3">{{ $t('worked_on_holiday')}}: {{worked_on_holiday}}</span>
                    <span class="font-weight-bold mr-4">{{ $t('leave')}}: {{n_leave}}</span>
                    <span class="font-weight-bold mr-4">OT: {{n_ot}}</span>
                </div> -->
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover responsive :items="perAttendanceList" :fields="perAttendanceFields" class="table-bordered border-dark">                                   
                        <template slot="bottom-row">
                            <td class="text-white bg-info font-weight-bold text-center" colspan="6">Total</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_present}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_regular_days}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_absent}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_sick_leave}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_casual_leave}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_earned_leave}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_other_leave}}</td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_ot}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">0</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_worked_on_weekly_holiday}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{n_worked_on_yearly_holiday}}</td>
                            <td class="text-white bg-info font-weight-bold text-center">0</td>
                            <td class="text-white bg-info font-weight-bold text-center">0</td>
                            <td class="text-white bg-info font-weight-bold text-center">0</td>
                        </template>
                    </b-table>
                </div>                              
            </div>
            <template v-slot:modal-header="">
                <h3>Individual Job Card for the Month of {{attendance_date | dateParse('YYYY-MM-DD') | dateFormat('MMMM-YYYY')}}</h3>
            </template>
            <template v-slot:modal-footer="">
                <div class="onlyprint fixed-bottom">
                    <div class="mt-3 float-left ml-3 col-2 border-top border-dark text-center">{{$t('prepared_by')}}</div>
                    <div class="mt-3 float-left col-1"></div>
                    <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('checked_by')}}</div>
                    <div class="mt-3 float-left col-1"></div>
                    <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('dept_head')}}</div>
                    <div class="mt-3 float-left col-1"></div>
                    <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('approved_by')}}</div>
                </div>
                <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End view Details Modal -->
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('daily_attendance') }
    },

    data() {
        return{
            attendanceList : [],
            perAttendanceList: [],
            personnelLeave: [],
            holiday: [],
            weeklyHoliday: [],
            n_present: 0,
            n_absent: 0,
            n_holiday: 0,
            n_weeklyholiday: 0,
            n_leave: 0,
            n_ot: 0,
            n_worked_on_weekly_holiday: 0,
            n_worked_on_yearly_holiday: 0,
            n_casual_leave: 0,
            n_sick_leave: 0,
            n_earned_leave: 0,
            n_other_leave: 0,
            n_regular_days: 0,
            uploadFile: null,
            fileName: this.$t('choose_file'),
            uploadReady: true,
            dataEdit: false,
            audit: false,
            roles: [],
            DepartmentList: [],
            DepartmentName: 'Management',
            attendance_date: this.convertDate(new Date()),
            savingDate: null,
            weekArray: [this.$t('sunday'), this.$t('monday'), this.$t('tuesday'), this.$t('wednesday'), this.$t('thursday'), this.$t('friday'), this.$t('saturday')],
            buttonTitle : this.$t('save'),
            disable: false,

            json_fields: {
                'ID': 'employee_id',
                'Name': 'first_name',
                'Designation': 'designation',
                'Department' : 'department',
                'date' : 'Date',
                'In' : 'in_time_1',
                'Out (L)' : 'out_time_1',
                'In (L)' : 'in_time_2',
                'Out' : 'out_time_2',
                'Total Hours' : 'total_hours',
                'OT' : 'ot',
                'Extra OT' : 'ot_extra'
            },

            indivisual_json_fields: {
                'Date': 'date',
                'Day': 'day',
                'In' : 'in_time_1',
                'Out (L)' : 'out_time_1',
                'In (L)' : 'in_time_2',
                'Out' : 'out_time_2',
                'Present': 'present',
                'Regular Days': 'regular_days',
                'Absent': 'absent',
                'Sick Leave': 'sick_leave',
                'Casual Leave': 'casual_leave',
                'Earned Leave': 'earned_leave',
                'Other Leave': 'other_leave',
                'Total Hours' : 'total_hours',
                'OT': 'ot',
                'Late': 'late',
                'Worked on Friday': 'worked_on_weekly_holiday',
                'Worked on Holiday': 'worked_on_yearly_holiday',
                'Lay-off Days': 'lay_off_days',
                'Not for Join': 'not_for_join',
                'Suspense': 'suspense'
            },

            noprint: '',

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            totalRows: 1,
            currentPage: 1,
            perPage: 25,
            pageOptions: [25, 50, 100],
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        fetch(`api/salarysheet`)
        .then(res => res.json())
        .then(res => {
            this.DepartmentList = res['Department'];
            this.DepartmentList.unshift('All');
        })

        this.fetchData()
        fetch(`api/settings/roles`)
        .then(res => res.json())
        .then(res => {
            this.roles = res['allRoles'];
        })
    },

    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },

        checkRoles(role) {
            for (let i = 0; i < this.roles.length; i++) {
                if (this.roles[i]['name'] == role) {
                    return true
                }                
            } return false
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        viewDetails(id) {
            this.noprint = 'noprint'
            var date = new Date(this.attendance_date),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2)
            let start = year + '-' + mnth + '-01'
            let end = this.convertDate(new Date(year, mnth, 0))
            let first = new Date(start)
            let last = new Date(end)
            
            this.isBusy = true
            fetch(`api/persattend/${id}/${start}/${end}`)
            .then(res => res.json())
            .then(res => {
                // For Holiday
                this.holiday = res['Holiday']
                // for leave
                let leave = res['Leave'], k=0, date = null
                this.personnelLeave = []
                for (let i = 0; i < leave.length; i++) {
                    for (let j = 0; j < leave[i]['day_count']; j++) {
                        this.personnelLeave[k] = JSON.parse( JSON.stringify( leave[i] ) );
                        date =  new Date(this.personnelLeave[k]['leave_start'])
                        date.setDate(date.getDate() + j)
                        this.personnelLeave[k]['leave_start'] = this.convertDate(date)
                        k++                  
                    }
                }
                console.log(this.personnelLeave)
                // for attendance
                let perAttendanceList = res['Attendance']
                for (let i = 0; i < perAttendanceList.length; i++) {
                    perAttendanceList[i]['in_time_1'] = this.in_time_1(perAttendanceList[i]['time'], perAttendanceList[i]['in_time_1'])
                    perAttendanceList[i]['out_time_1'] = this.out_time_1(perAttendanceList[i]['time'], perAttendanceList[i]['out_time_1'])
                    perAttendanceList[i]['in_time_2'] = this.in_time_2(perAttendanceList[i]['time'], perAttendanceList[i]['in_time_2'])
                    perAttendanceList[i]['out_time_2'] = this.out_time_2(perAttendanceList[i]['time'], perAttendanceList[i]['in_time_1'], perAttendanceList[i]['out_time_2'])
                    perAttendanceList[i]['total_hours'] = this.total_hours(perAttendanceList[i]['time'], perAttendanceList[i]['in_time_1'], perAttendanceList[i]['out_time_2'])
                    perAttendanceList[i]['ot'] = this.ot(perAttendanceList[i]['time'], perAttendanceList[i]['in_time_1'], perAttendanceList[i]['out_time_2'], perAttendanceList[i]['ot'])
                    perAttendanceList[i]['ot_extra'] = this.ot_extra(perAttendanceList[i]['time'], perAttendanceList[i]['in_time_1'], perAttendanceList[i]['out_time_2'], perAttendanceList[i]['ot_extra'])  
                }

                // Fro weekly holiday
                this.weeklyHoliday = perAttendanceList[0]['weekly_holiday']

                let sn = 0, check = null, date_i = null
                this.n_present = 0
                this.n_absent = 0
                this.n_holiday = 0
                this.n_weeklyholiday = 0
                this.n_leave = 0
                this.n_ot = 0
                this.n_worked_on_weekly_holiday = 0
                this.n_worked_on_yearly_holiday = 0
                this.n_casual_leave = 0
                this.n_sick_leave = 0
                this.n_earned_leave = 0
                this.n_other_leave = 0
                this.n_regular_days = 0
                for (let i = first; i <= last; i.setDate(i.getDate() + 1)) {
                    
                    check = false
                    for (let j = 0; j < perAttendanceList.length; j++) {
                        date_i = i
                        if(perAttendanceList[j]['date'] == this.convertDate(i)) {
                            this.perAttendanceList[sn] = perAttendanceList[j]
                            this.perAttendanceList[sn]['regular_days'] = 1
                            this.n_regular_days++
                            if (this.perAttendanceList[sn]['in_time_1'].length > 0 && this.perAttendanceList[sn]['in_time_1'] != '00:00') {
                                this.perAttendanceList[sn]['status'] = 'present'
                                this.perAttendanceList[sn]['present'] = 1
                                this.n_present++
                                if(this.perAttendanceList[sn]['ot']) this.n_ot += parseInt(this.perAttendanceList[sn]['ot'])
                            } else {
                                this.perAttendanceList[sn]['status'] = 'absent'
                                this.perAttendanceList[sn]['absent'] = 1
                                this.n_absent++
                            }
                                
                            check = true
                        }                        
                    } if(!check) {
                        this.perAttendanceList[sn] = {id: perAttendanceList[0]['id'], employee_id: perAttendanceList[0]['employee_id'], first_name: perAttendanceList[0]['first_name'], last_name: perAttendanceList[0]['last_name'], designation: perAttendanceList[0]['designation'], department: perAttendanceList[0]['department'], 
                            employee_image: perAttendanceList[0]['employee_image'], weekly_holiday: perAttendanceList[0]['weekly_holiday'], status: null, date: this.convertDate(i), day: this.weekArray[i.getDay()],
                            time: '00:00', in_time_1: '00:00', in_time_2: '00:00', out_time_1: '00:00', out_time_2: '00:00', ot: null, ot_extra: null} 
                        this.perAttendanceList[sn]['regular_days'] = 1
                        this.n_regular_days++
                    }
                    
                    for (let k = 0; k < this.weeklyHoliday.length; k++) {
                        if(this.perAttendanceList[sn]['day'] == this.weekArray[this.weeklyHoliday[k]]) {
                            if (this.perAttendanceList[sn]['status'] == 'absent') {
                                this.perAttendanceList[sn]['absent'] = null
                                this.n_absent--
                            }

                            if (this.perAttendanceList[sn]['status'] == 'present') {
                                this.perAttendanceList[sn]['worked_on_weekly_holiday'] = 1
                                this.n_worked_on_weekly_holiday++
                            }

                            this.perAttendanceList[sn]['status'] = 'holiday'
                            this.perAttendanceList[sn]['weekly_holiday'] = 1
                            this.n_weeklyholiday++
                        }                        
                    }

                    for (let l = 0; l < this.holiday.length; l++) {
                        if(this.perAttendanceList[sn]['date'] == this.holiday[l]['yearly_holiday']) {
                            if (this.perAttendanceList[sn]['status'] == 'absent') {
                                this.perAttendanceList[sn]['absent'] = null
                                this.n_absent--
                            }
                            if (this.perAttendanceList[sn]['status'] == 'holiday') {
                                this.n_weeklyholiday--
                            }
                            if (this.perAttendanceList[sn]['status'] == 'present') {
                                this.perAttendanceList[sn]['worked_on_yearly_holiday'] = 1
                                this.n_worked_on_yearly_holiday++
                            }
                            this.perAttendanceList[sn]['status'] = this.holiday[l]['event']
                            this.perAttendanceList[sn]['yearly_holiday'] = 1
                            this.n_holiday++
                        }                        
                    }

                    for (let m = 0; m < this.personnelLeave.length; m++) {
                        if(this.perAttendanceList[sn]['date'] == this.personnelLeave[m]['leave_start']) {
                            this.perAttendanceList[sn]['status'] = this.personnelLeave[m]['leave_type']
                            //switch statment
                            switch (this.perAttendanceList[sn]['status']) {
                                case 'casual_leave':
                                    this.perAttendanceList[sn]['casual_leave'] = 1
                                    this.n_casual_leave++
                                    break;
                                case 'sick_leave':
                                    this.perAttendanceList[sn]['sick_leave'] = 1
                                    this.n_sick_leave++
                                    break;
                                case 'earned_leave':
                                    this.perAttendanceList[sn]['earned_leave'] = 1
                                    this.n_earned_leave++
                                    break;                            
                                default:
                                    this.perAttendanceList[sn]['other_leave'] = 1
                                    this.n_other_leave++
                                    break;
                            }
                            this.n_leave++
                        }                        
                    }

                    sn++
                }
                // this.worked_on_holiday = this.n_present+this.n_absent+this.n_holiday+this.n_weeklyholiday+this.n_leave-this.perAttendanceList.length
                // if(this.worked_on_holiday < 0) this.worked_on_holiday = 0
                this.isBusy = false
                this.$refs['dataView'].show()
            })
            .catch(err => {
                alert(err.response.data.message);
            })            
        },
        
        fetchData(check) {
            this.isBusy = true
            this.audit = check
            fetch(`api/dailyattend/${this.attendance_date}`)
            .then(res => res.json())
            .then(res => {
                let atten = [], empl = []
                atten = res['Attendance']
                empl = res['Employee']

                const mergeById = (a1, a2) =>
                a1.map(itm => ({
                    ...a2.find((item) => (item.employee_id === itm.employee_id) && item),
                    ...itm
                }));

                this.attendanceList = mergeById(empl, atten)

                for (let i = 0; i < this.attendanceList.length; i++) {
                    this.attendanceList[i]['in_time_1'] = this.in_time_1(this.attendanceList[i]['time'], this.attendanceList[i]['in_time_1'])
                    this.attendanceList[i]['out_time_1'] = this.out_time_1(this.attendanceList[i]['time'], this.attendanceList[i]['out_time_1'])
                    this.attendanceList[i]['in_time_2'] = this.in_time_2(this.attendanceList[i]['time'], this.attendanceList[i]['in_time_2'])
                    this.attendanceList[i]['out_time_2'] = this.out_time_2(this.attendanceList[i]['time'], this.attendanceList[i]['in_time_1'], this.attendanceList[i]['out_time_2'])
                    this.attendanceList[i]['total_hours'] = this.total_hours(this.attendanceList[i]['time'], this.attendanceList[i]['in_time_1'], this.attendanceList[i]['out_time_2'])
                    this.attendanceList[i]['ot'] = this.ot(this.attendanceList[i]['time'], this.attendanceList[i]['in_time_1'], this.attendanceList[i]['out_time_2'], this.attendanceList[i]['ot'])
                    this.attendanceList[i]['ot_extra'] = this.ot_extra(this.attendanceList[i]['time'], this.attendanceList[i]['in_time_1'], this.attendanceList[i]['out_time_2'], this.attendanceList[i]['ot_extra'])  
                }
                this.totalRows = this.attendanceList.length
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        in_time_1(time, time1) {
            if(!time) return '00:00'
            if (time.length > 0) {
                let times = []
                times = time1.split(":");
                let ms = new Date(0, 0, 0, times[0], times[1], 0)
                if(ms > -2209078400000 && ms < -2209075700000) return '05:45'
                if(ms > -2209071200000 && ms < -2209068500000) return '07:45'
                if(ms > -2209049600000 && ms < -2209046900000) return '13:45'
                if(ms > -2209028000000 && ms < -2209025300000) return '19:45'
                if(ms > -2209020800000 && ms < -2209018100000) return '21:45'
                return time1
            }
            return '00:00'
        },

        out_time_1(time, time1) {
            if(!time) return '00:00'
            if (time.length > 12) return time1
            return '00:00'
        },
        
        in_time_2(time, time1) {
            if(!time) return '00:00'
            if (time.length > 18) return time1
            return '00:00'
        },
        
        out_time_2(time, start, end) {
            if(!time) return '00:00'
            if (time.length > 6) {
                if(!this.audit) return end
                let start1 = start.split(":");
                let end1 = end.split(":");
                var startDate = new Date(0, 0, 0, start1[0], start1[1], 0);
                var endDate = new Date(0, 0, 0, end1[0], end1[1], 0);
                var diff = endDate.getTime() - startDate.getTime();
                var hours = Math.floor(diff / 1000 / 60 / 60);
                diff -= hours * 1000 * 60 * 60;
                var minutes = Math.floor(diff / 1000 / 60);
                if (hours < 0) hours = hours + 24;

                if(hours > 11 || (hours > 10 && minutes > 15)) {
                    end1[0] = parseInt(end1[0]) - (hours - 11)
                    if (end1[0] < 0) end1[0] = end1[0] + 24;
                    return end1[0] + ':15'
                }
                return end
            }
            return '00:00'
        },

        total_hours(time, start, end) {
            if(!time) return '00:00'
            if (time.length < 7) return '0:00'

            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            diff -= hours * 1000 * 60 * 60;
            var minutes = Math.floor(diff / 1000 / 60);

            // If using time pickers with 24 hours format, add the below line get exact hours
            if (hours < 0)
            hours = hours + 24;

            if(this.audit && hours>10) hours = 10

            return (hours <= 9 ? "0" : "") + hours + ":" + (minutes <= 9 ? "0" : "") + minutes;
        },

        ot(time, start, end, ot) {
            if (ot) return ot
            if(!time) return 0
            if (time.length < 7) return null

            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            if (hours < 0) hours = hours + 24;
            if ((hours - 9) > 0 && (hours - 9) < 3) return hours - 9
            else if ((hours - 9) > 2) return 2
            return null
        },

        ot_extra(time, start, end, ot) {
            if (ot) return ot
            if(!time) return 0
            if (time.length < 7) return null

            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            if (hours < 0) hours = hours + 24;
            if ((hours - 9) > 2) return hours - 11
            return null
        },
    },

    computed: {

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        attendanceByDepartment() {
            let array = [], k=0
            if (this.DepartmentName == 'All') {
                array = this.attendanceList
            } else {
                for (let i = 0; i < this.attendanceList.length; i++) {
                    if (this.attendanceList[i]['department'] == this.DepartmentName) {
                        array[k++] = this.attendanceList[i]
                    }                
                }
            }

            this.totalRows = array.length
            return array
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            let action = { key: 'ot_extra', label : 'OT (Extra)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'}
            if(this.audit) action = []
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'date', label : this.$t('date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'time', label : this.$t('time'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time_1', label : this.$t('in'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time_1', label : this.$t('out') + '(L)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time_2', label : this.$t('in') + '(L)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time_2', label : this.$t('out'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_hours', label : this.$t('total_hours'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'late', label : this.$t('Late'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'ot', label : 'OT', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                action
            ]            
        },

        perAttendanceFields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            // let action = { key: 'ot_extra', label : 'OT (Extra)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'}
            // if(this.audit) action = []
            return [
                { key: 'date', label : this.$t('date'), sortable: true, class: 'text-center align-middle bg-white border border-dark', stickyColumn: true, thClass: 'border-top border-dark font-weight-bold'},
                { key: 'day', label : this.$t('day'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'status', label : this.$t('status'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'time', label : this.$t('time'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time_1', label : this.$t('in'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time_1', label : this.$t('out') + '(L)', sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time_2', label : this.$t('in') + '(L)', sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time_2', label : this.$t('out'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'present', label : this.$t('present'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'regular_days', label : this.$t('regular_days'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'absent', label : this.$t('absent'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'sick_leave', label : this.$t('sick_leave'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'casual_leave', label : this.$t('casual_leave'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'earned_leave', label : this.$t('earned_leave'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'other_leave', label : this.$t('other_leave'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_hours', label : this.$t('total_hours'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'ot', label : 'OT', sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'late', label : this.$t('late'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'worked_on_weekly_holiday', label : this.$t('worked_on_friday'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'worked_on_yearly_holiday', label : this.$t('worked_on_holiday'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'lay_off_days', label : this.$t('lay_off_days'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'not_for_join', label : this.$t('not_for_join_days'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'suspense', label : this.$t('suspense_days'), sortable: true, class: 'text-center align-middle border border-dark', thClass: 'border-top border-dark font-weight-bold'},
                // action
            ] 
        },
    }
}
</script>

<style lang="scss" scoped>

</style>