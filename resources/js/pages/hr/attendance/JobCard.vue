<template>
    <div class="container-fluid">
        <div class="card filterable">
            <div class="card-header row m-0 py-3 noprint">
                <h3 class="col-md-6">{{ DepartmentName + ' ' + $t('job_card') }} <span v-if="attendanceMonth">({{convertDate(attendanceMonth) | dateParse('YYYY-MM-DD') | dateFormat('MMMM-YYYY') }})</span></h3>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">{{$t('set_month')}}</div>
                        </div>
                        <input type="date" @change="changeView" v-model="attendanceMonth" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card-header row m-0 p-0 noprint">
                <div class="col-md-6 my-auto">
                    <b-form-select @change="changeView" v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select>
                </div>
                <div class="col-md-6 my-auto">
                    <div class="input-group">
                        <input @change="changeView" v-model="start_limit" type="number" class="form-control input-group-prepend">
                        <div class="input-group-prepend">
                            <div class="input-group-text">{{$t('to')}}</div>
                        </div>
                        <input @change="changeView" v-model="end_limit" type="number" class="form-control">
                        <div @click="changeView('all')" class="input-group-append input-group-text pointer noprint">{{$t('all') + ' (' + all + ')'}}</div>
                    </div>
                </div>
                <div class="col-12 px-auto">
                    <button @click="fetchData" type="button" class="mdb btn btn-outline-mdb-color mx-auto">{{$t('search')}}</button>
                </div>
            </div>
            <div v-if="isBusy" class="my-5 mx-auto text-primary">
                <h2><b-icon icon="circle-fill" animation="throb"></b-icon> Loading...</h2>
            </div>
            <div v-for="(item, index) in employeeListByDept" :key="item.id">

                <div v-if="jobCards.length > 0" class="modal-body m-0 p-0 mb-2">
                <h3>Individual Job Card for the Month of {{attendanceMonth | dateParse('YYYY-MM-DD') | dateFormat('MMMM-YYYY')}}</h3>
                    <div class="col-md-12 m-0 p-0">
                        <span class="font-weight-bold mr-4">{{ $t('name')}}: {{jobCards[index][0]['first_name']}}</span>
                        <span class="font-weight-bold mr-4">ID: {{jobCards[index][0]['employee_id']}}</span>
                        <span class="font-weight-bold mr-4">{{ $t('designation')}}: {{jobCards[index][0]['designation']}}</span>
                        <span class="font-weight-bold mr-4 mb-3">{{ $t('department')}}: {{jobCards[index][0]['department']}}</span>
                        
                    </div>
                    <div class="col-md-12 m-0 p-0 mt-3">
                        <b-table show-empty small striped hover responsive :items="jobCards[index]" :fields="perAttendanceFields" class="table-bordered border-dark">                                   
                            <template slot="bottom-row">
                                <td class="text-white bg-info font-weight-bold text-center" colspan="6">Total</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_present[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_regular_days[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_absent[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_sick_leave[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_casual_leave[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_earned_leave[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_other_leave[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center"></td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_ot[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">0</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_worked_on_weekly_holiday[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">{{n_worked_on_yearly_holiday[index]}}</td>
                                <td class="text-white bg-info font-weight-bold text-center">0</td>
                                <td class="text-white bg-info font-weight-bold text-center">0</td>
                                <td class="text-white bg-info font-weight-bold text-center">0</td>
                            </template>
                        </b-table>
                    </div>                              
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import { ModelSelect } from 'vue-search-select'
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('job_card') }
    },

    data() {
        return{
            attendanceSheet : [],
            task: {},
            dataEdit: false,
            roles: [],
            DepartmentList: [],
            employeeList: [],
            DepartmentName: 'Cleaning',
            attendanceMonth: this.convertDate(new Date()),
            jobCards: [],
            perAttendanceList: [],
            personnelLeave: [],
            weekArray: [this.$t('sunday'), this.$t('monday'), this.$t('tuesday'), this.$t('wednesday'), this.$t('thursday'), this.$t('friday'), this.$t('saturday')],
            start_limit: 1,
            end_limit: 0,
            all: 0,
            audit: true,
            holiday: [],
            weeklyHoliday: [],
            n_present: [],
            n_absent: [],
            n_holiday: [],
            n_weeklyholiday: [],
            n_leave: [],
            n_ot: [],
            n_worked_on_weekly_holiday: [],
            n_worked_on_yearly_holiday: [],
            n_casual_leave: [],
            n_sick_leave: [],
            n_earned_leave: [],
            n_other_leave: [],
            n_regular_days: [],
            // buttonTitle : this.$t('generate_salary_sheet'),
            disable: false,
            reportType: this.$t('details'),

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
            noCollapse: true,
        }
    },

    created() {
        fetch(`api/employee/jobcard`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['EmployeeList'];
        })
        .catch(err => {
            alert(err.response.data.message);
        })
    },

    mounted() {
        fetch(`api/salarysheet`)
        .then(res => res.json())
        .then(res => {
            this.DepartmentList = res['Department'];
        })
        .catch(err => {
            alert(err.response.data.message);
        })

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

        fetchData() {
            var date = new Date(this.attendanceMonth),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2)
            let start = year + '-' + mnth + '-01'
            let end = this.convertDate(new Date(year, mnth, 0))
            let first = new Date(start)
            let last = new Date(end)
            
            this.all = this.employeeListByDept.length

            if (this.end_limit == 0) this.end_limit  = this.employeeListByDept.length

            if (this.end_limit > this.all) this.end_limit = this.all

            if (this.start_limit < 1) this.start_limit = 1
            if (this.end_limit < 1) this.end_limit = 1
                
            // for (let i = this.start_limit-1; i < this.end_limit; i++) {
                
                fetch(`api/jobcard/${this.DepartmentName}/${start}/${end}`)
                .then(res => res.json())
                .then(res => {
                    this.isBusy = true
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
                    
                    let j=0, sn = 0, check = false, dates = []
                    for (let z = 0; z < this.employeeListByDept.length; z++) {
                        dates[z] = new Date(start)
                        if (perAttendanceList[j]['id'] == this.employeeListByDept[z]['id']) {
                            // For weekly holiday
                            this.weeklyHoliday[z] = this.employeeListByDept[z]['weekly_holiday']
                            
                            this.n_present[z] = 0
                            this.n_absent[z] = 0
                            this.n_holiday[z] = 0
                            this.n_weeklyholiday[z] = 0
                            this.n_leave[z] = 0
                            this.n_ot[z] = 0
                            this.n_worked_on_weekly_holiday[z] = 0
                            this.n_worked_on_yearly_holiday[z] = 0
                            this.n_casual_leave[z] = 0
                            this.n_sick_leave[z] = 0
                            this.n_earned_leave[z] = 0
                            this.n_other_leave[z] = 0
                            this.n_regular_days[z] = 0
                            this.perAttendanceList = []
                            
                            sn = 0
                            check = false
                            for (dates[z]; dates[z] <= last; dates[z].setDate(dates[z].getDate() + 1)) {
                                // sn = 0
                                // j = 0
                                check = false
                                for (j; j < perAttendanceList.length; j++) {
                                        if(perAttendanceList[j]['date'] == this.convertDate(dates[z])) {
                                            this.perAttendanceList[sn] = perAttendanceList[j]
                                            this.perAttendanceList[sn]['regular_days'] = 1
                                            this.n_regular_days[z]++
                                            if (this.perAttendanceList[sn]['in_time_1'].length > 0 && this.perAttendanceList[sn]['in_time_1'] != '00:00') {
                                                this.perAttendanceList[sn]['status'] = 'present'
                                                this.perAttendanceList[sn]['present'] = 1
                                                this.n_present[z]++
                                                if(this.perAttendanceList[sn]['ot']) this.n_ot[z] += parseInt(this.perAttendanceList[sn]['ot'])
                                            } else {
                                                this.perAttendanceList[sn]['status'] = 'absent'
                                                this.perAttendanceList[sn]['absent'] = 1
                                                this.n_absent[z]++
                                            }
                                                
                                            check = true
                                            break
                                        } 
                                        // else if (perAttendanceList[j]['date'] != this.convertDate(i) && check) continue
                                    // } 
                                    // else if(perAttendanceList[j]['id'] != this.employeeListByDept[z]['id'] && check) continue
                                } if(!check) {
                                    this.perAttendanceList[sn] = {id: perAttendanceList[0]['id'], employee_id: perAttendanceList[0]['employee_id'], first_name: perAttendanceList[0]['first_name'], last_name: perAttendanceList[0]['last_name'], designation: perAttendanceList[0]['designation'], department: perAttendanceList[0]['department'], 
                                        employee_image: perAttendanceList[0]['employee_image'], weekly_holiday: perAttendanceList[0]['weekly_holiday'], status: null, date: this.convertDate(i), day: this.weekArray[i.getDay()],
                                        time: '00:00', in_time_1: '00:00', in_time_2: '00:00', out_time_1: '00:00', out_time_2: '00:00', ot: null, ot_extra: null} 
                                    this.perAttendanceList[sn]['regular_days'] = 1
                                    this.n_regular_days[z]++
                                }
                                
                                for (let k = 0; k < this.weeklyHoliday[z].length; k++) {
                                    if(this.perAttendanceList[sn]['day'] == this.weekArray[this.weeklyHoliday[k]]) {
                                        if (this.perAttendanceList[sn]['status'] == 'absent') {
                                            this.perAttendanceList[sn]['absent'] = null
                                            this.n_absent[z]--
                                        }

                                        if (this.perAttendanceList[sn]['status'] == 'present') {
                                            this.perAttendanceList[sn]['worked_on_weekly_holiday'] = 1
                                            this.n_worked_on_weekly_holiday[z]++
                                        }

                                        this.perAttendanceList[sn]['status'] = 'holiday'
                                        this.perAttendanceList[sn]['weekly_holiday'] = 1
                                        this.n_weeklyholiday[z]++
                                    }                        
                                }

                                for (let l = 0; l < this.holiday.length; l++) {
                                    if(this.perAttendanceList[sn]['date'] == this.holiday[l]['yearly_holiday']) {
                                        if (this.perAttendanceList[sn]['status'] == 'absent') {
                                            this.perAttendanceList[sn]['absent'] = null
                                            this.n_absent[z]--
                                        }
                                        if (this.perAttendanceList[sn]['status'] == 'holiday') {
                                            this.n_weeklyholiday[z]--
                                        }
                                        if (this.perAttendanceList[sn]['status'] == 'present') {
                                            this.perAttendanceList[sn]['worked_on_yearly_holiday'] = 1
                                            this.n_worked_on_yearly_holiday[z]++
                                        }
                                        this.perAttendanceList[sn]['status'] = this.holiday[l]['event']
                                        this.perAttendanceList[sn]['yearly_holiday'] = 1
                                        this.n_holiday[z]++
                                    }                        
                                }

                                let idcheck = false
                                for (let m = 0; m < this.personnelLeave.length; m++) {
                                    if (this.personnelLeave[m]['id'] == this.employeeListByDept[z]['id']) {
                                        if(this.perAttendanceList[sn]['date'] == this.personnelLeave[m]['leave_start']) {
                                            this.perAttendanceList[sn]['status'] = this.personnelLeave[m]['leave_type']
                                            //switch statment
                                            switch (this.perAttendanceList[sn]['status']) {
                                                case 'casual_leave':
                                                    this.perAttendanceList[sn]['casual_leave'] = 1
                                                    this.n_casual_leave[z]++
                                                    break;
                                                case 'sick_leave':
                                                    this.perAttendanceList[sn]['sick_leave'] = 1
                                                    this.n_sick_leave[z]++
                                                    break;
                                                case 'earned_leave':
                                                    this.perAttendanceList[sn]['earned_leave'] = 1
                                                    this.n_earned_leave[z]++
                                                    break;                            
                                                default:
                                                    this.perAttendanceList[sn]['other_leave'] = 1
                                                    this.n_other_leave[z]++
                                                    break;
                                            }
                                            this.n_leave[z]++
                                        } idcheck = true
                                    }  else if (this.personnelLeave[m]['id'] != this.employeeListByDept[z]['id'] && idcheck) {
                                        break
                                    }                 
                                }

                                sn++
                            }
                            this.jobCards[z] = JSON.parse( JSON.stringify( this.perAttendanceList ) );
                        } else {
                            j++; z--
                        }
                    }
                    this.isBusy = false
                })
        },

        changeView(val = null) {
            if (val == 'all') {
                this.start_limit = 1
                this.end_limit = 0
            }

            this.fetchData()
            // this.attendanceSheet = []
            // this.attendanceByDepartment
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
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

        month_year() {
            if(this.attendanceMonth) {
                var date = new Date(this.attendanceMonth),
                year = date.getFullYear(),
                mnth = date.getMonth(),
                month = ['January', 'Fabruary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                return [month[mnth], year].join("-");
            }
        },

        month_year_bn() {
            if(this.attendanceMonth) {
                var date = new Date(this.attendanceMonth),
                year = date.getFullYear(),
                mnth = date.getMonth(),
                month = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর']
                return [month[mnth], year].join("-");
            }
        },

        employeeListByDept() {
            let array = [], k=0
            if (this.DepartmentName == 'All') {
                array = this.employeeList
            } else {
                for (let i = 0; i < this.employeeList.length; i++) {
                    if (this.employeeList[i]['department'] == this.DepartmentName) {
                        array[k++] = this.employeeList[i]
                    }                
                }
            }           

            this.totalRows = array.length
            return array
        },

        attendanceByDepartment() {
            let array = [], array2 = [], k=0, l=0

            for (let i = 0; i < this.attendanceSheet.length; i++) {
                if (this.attendanceSheet[i]['department'] == this.DepartmentName) {
                    array[k++] = this.attendanceSheet[i]
                }                
            }
            this.all = array.length

            if (this.end_limit == 0) this.end_limit  = array.length

            if (this.end_limit > this.all) this.end_limit = this.all

            if (this.start_limit < 1) return
            if (this.end_limit < 1) return
                
            for (let j = this.start_limit-1; j < this.end_limit; j++) {
                array2[l++] = array[j];                
            }

            return array2
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
    },

    // components: { ModelSelect },
}
</script>

<style lang="scss" scoped>
.border-3 {
    border-width:3px !important;
}

div.pagebreak {
  page-break-before: always;
}
</style>