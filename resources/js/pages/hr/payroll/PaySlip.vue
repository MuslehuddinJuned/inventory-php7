<template>
    <div class="container-fluid">
        <div class="card filterable">
            <div class="card-header row m-0 py-3 noprint">
                <h3 class="col-md-6">{{ DepartmentName + ' ' + $t('pay_slip') }} <span v-if="salaryMonth">({{convertDate(salaryMonth) | dateParse('YYYY-MM-DD') | dateFormat('MMMM-YYYY') }})</span></h3>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">{{$t('set_month')}}</div>
                        </div>
                        <input type="date" @change="fetchData" v-model="salaryMonth" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card-header row m-0 p-0 noprint">
                <div class="col-md-6 my-auto">
                    <b-form-select @change="changeVew" v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select>
                </div>
                <div class="col-md-6 my-auto">
                    <div class="input-group">
                        <input @change="changeVew" v-model="start_limit" type="number" class="form-control input-group-prepend">
                        <div class="input-group-prepend">
                            <div class="input-group-text">{{$t('to')}}</div>
                        </div>
                        <input @change="changeVew" v-model="end_limit" type="number" class="form-control">
                        <div @click="changeVew('all')" class="input-group-append input-group-text pointer noprint">{{$t('all') + ' (' + all + ')'}}</div>
                    </div>
                </div>
            </div>
            <div v-if="isBusy" class="my-5 mx-auto text-primary">
                <h2><b-icon icon="circle-fill" animation="throb"></b-icon> Loading...</h2>
            </div>
            <div v-for="data in salarySheetByDepartment" :key="data.id">
                <div class="col-12 row m-0 p-0">
                    <div class="col-4 border-bottom">
                        Payslip (বেতন রশিদ)
                    </div>
                    <div class="col-4 text-center border-bottom">
                        Shun Ho (BD) Manufactory Ltd.
                    </div>
                    <div class="col-4 text-right border-bottom">
                        {{month_year}} {{month_year_bn}}
                    </div>
                    <div class="col-3">
                        <div>ID No (আই.ডি নং)</div>
                        <div>Name (নাম)</div>
                        <div>Designation (পদবী)</div>
                    </div>
                    <div class="col-4">
                        <div>{{data.employee_id}}</div>
                        <div>{{data.first_name}} ({{data.last_name}})</div>
                        <div>{{data.designation}}</div>
                    </div>
                    <div class="col-3">
                        <div>Salary in USD (ডলারে বেতন)</div>
                        <div>Convert Rate (রুপান্তর হার)</div>
                        <div></div>
                    </div>
                    <div class="col-2">
                        <div>{{(data.salary_usd || 0).toFixed(2)}}</div>
                        <div>{{data.covert_rate}}</div>
                    </div>
                    <div class="col-6 row m-0 p-0 border-top border-right border-dark mb-1">
                        <div class="col-8">Basic Pay (মূল বেতন)</div>
                        <div class="col-4 text-right">{{(data.basic_monthly + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">House Rent 50% Basic (বাড়ী ভাড়া)</div>
                        <div class="col-4 text-right">{{(data.house_rent + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Med.Allow (চিকিৎসা ভাতা)</div>
                        <div class="col-4 text-right">{{(data.medic_allowance + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Total Salary (মোট বেতন)</div>
                        <div class="col-4 text-right border-top border-dark">{{(data.salary + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-12 border-bottom border-top">Add : ( যোগ )</div>
                        <div class="col-8">T/A  (পরিবহন ভাতা)</div>
                        <div class="col-4 text-right">{{(data.ta || 0).toFixed(2)}}</div>
                        <div class="col-8">D/A  (মহার্ঘ ভাতা)</div>
                        <div class="col-4 text-right">{{(data.da || 0).toFixed(2)}}</div>
                        <div class="col-8">Attendance Bonus (হাজিরা বোনাস)</div>
                        <div class="col-4 text-right">{{(data.attendance_bonus || 0).toFixed(2)}}</div>
                        <div class="col-8">Production Bonus (উৎপাদন বোনাস)</div>
                        <div class="col-4 text-right">{{(data.production_bonus || 0).toFixed(2)}}</div>
                        <div class="col-8">Paid Leave (প্রদেয় ছুটি)</div>
                        <div class="col-4 text-right">{{(0).toFixed(2)}}</div>
                        <div class="col-8">No. of days (দিনের সংখ্যা)</div>
                        <div class="col-4 text-right">{{(data.leave_days || 0).toFixed(2)}}</div>
                        <div class="col-8">Overtime pay (ওভারটাইম মজুরী)</div>
                        <div class="col-4 text-right">{{(data.ot_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No. Of Hours (মোট কর্মঘন্টা)</div>
                        <div class="col-4 text-right">{{(data.ot_hour || 0).toFixed(2)}}</div>
                        <div class="col-8">Holidays -Worked (অবকাশকালীন কর্মদিবস)</div>
                        <div class="col-4 text-right">{{(data.worked_holiday_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No. Of Hours (মোট কর্মঘন্টা)</div>
                        <div class="col-4 text-right">{{(data.worked_holiday_hour || 0).toFixed(2)}}</div>
                        <div class="col-8">Fridays - Worked (সাপ্তাহিক ছুটি)</div>
                        <div class="col-4 text-right">{{(data.worked_friday_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No. Of Hours (মোট কর্মঘন্টা)</div>
                        <div class="col-4 text-right">{{(data.worked_friday_hour || 0).toFixed(2)}}</div>
                        <div class="col-8">Add'l Adjustment (অন্যান্য সম্বনয়)</div>
                        <div class="col-4 text-right">{{(data.total_allowance + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Gross  Pay: (প্রাপ্য বেতন)</div>
                        <div class="col-4 text-right border_bottom border-top border-dark">{{(data.gross_pay + 1e-9 || 0).toFixed(2)}}</div>
                    </div>
                    <div class="col-6 row m-0 p-0 border-top border-dark mb-1">
                        <div class="col-8">Gross  Pay: (প্রাপ্য বেতন)</div>
                        <div class="col-4 text-right">{{(data.gross_pay + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-12 border-bottom border-top">Deductions  : (বিয়োজন)</div>
                        <div class="col-8">Absent/UPL/NW/Late (অনুপুস্থিত/অবৈতনিকছুটি/বিলম্ব)</div>
                        <div class="col-4 text-right">{{(data.absent_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No.of days (দিনের সংখ্যা)</div>
                        <div class="col-4 text-right">{{(data.absent_days || 0).toFixed(2)}}</div>
                        <div class="col-8">Sick Leave (অসুস্থতাজনিত ছুটি)</div>
                        <div class="col-4 text-right">{{(0).toFixed(2)}}</div>
                        <div class="col-8">Advances (অগ্রিম)</div>
                        <div class="col-4 text-right">{{(data.advance + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Provident  Fund (ভবিষ্যত তহবিল)</div>
                        <div class="col-4 text-right">{{(data.pf + 1e-9 || 0).toFixed(2)}}</div>
                        <!-- <div class="col-8">Income Tax (আয়কর)</div>
                        <div class="col-4 text-right">{{(data.tax + 1e-9 || 0).toFixed(2)}}</div> -->
                        <div class="col-8">Penalty (জরিমানা)</div>
                        <div class="col-4 text-right">0.00</div>
                        <div class="col-8">Ded'l Adjustment (কর্তনযোগ্য সম্বনয়)</div>
                        <div class="col-4 text-right">{{(data.deducted + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Subscription For W. A. (কল্যানতহবিলে চাঁদা)</div>
                        <div class="col-4 text-right">{{(0).toFixed(2)}}</div>
                        <div class="col-8">Not for join Days (অসম্পৃক্ত দিনের সংখ্যা)</div>
                        <div class="col-4 text-right">{{(data.not_for_join_days).toFixed(2)}}</div>
                        <div class="col-8">Amount (টাকা)</div>
                        <div class="col-4 text-right">{{(data.not_for_join_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Lay off days  (সাময়িক ছুটি)</div>
                        <div class="col-4 text-right">{{(data.lay_off_days || 0).toFixed(2)}}</div>
                        <div class="col-8">Amount (টাকা)</div>
                        <div class="col-4 text-right">{{(data.lay_off_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Total Deduction (মোট বিয়োজন )</div>
                        <div class="col-4 text-right border-top border-bottom border-dark">{{(data.total_deduction + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">NET PAY (প্রাপ্ত বেতন)</div>
                        <div class="col-4 text-right border-bottom border-dark border-3">{{(data.net_pay + 1e-9 || 0).toFixed(2)}}</div>
                    </div>
                    <div class="col-2 mt-5"></div>
                    <div class="col-3 text-center border-top border-dark mt-5">Prepared by</div>
                    <div class="col-2 mt-5"></div>
                    <div class="col-3 text-center border-top border-dark mt-5">Received by</div>
                    <div class="col-2 mt-5"></div>                    
                </div>
                <div class="my-3" style="border-top: 4px dotted black;"></div>
                <div class="col-12 row m-0 p-0">
                    <div class="col-4 border-bottom">
                        Payslip (বেতন রশিদ)
                    </div>
                    <div class="col-4 text-center border-bottom">
                        Shun Ho (BD) Manufactory Ltd.
                    </div>
                    <div class="col-4 text-right border-bottom">
                        {{month_year}} {{month_year_bn}}
                    </div>
                    <div class="col-3">
                        <div>ID No (আই.ডি নং)</div>
                        <div>Name (নাম)</div>
                        <div>Designation (পদবী)</div>
                    </div>
                    <div class="col-4">
                        <div>{{data.employee_id}}</div>
                        <div>{{data.first_name}} ({{data.last_name}})</div>
                        <div>{{data.designation}}</div>
                    </div>
                    <div class="col-3">
                        <div>Salary in USD (ডলারে বেতন)</div>
                        <div>Convert Rate (রুপান্তর হার)</div>
                        <div></div>
                    </div>
                    <div class="col-2">
                        <div>{{(data.salary_usd || 0).toFixed(2)}}</div>
                        <div>{{data.covert_rate}}</div>
                    </div>
                    <div class="col-6 row m-0 p-0 border-top border-right border-dark mb-1">
                        <div class="col-8">Basic Pay (মূল বেতন)</div>
                        <div class="col-4 text-right">{{(data.basic_monthly + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">House Rent 50% Basic (বাড়ী ভাড়া)</div>
                        <div class="col-4 text-right">{{(data.house_rent + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Med.Allow (চিকিৎসা ভাতা)</div>
                        <div class="col-4 text-right">{{(data.medic_allowance + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Total Salary (মোট বেতন)</div>
                        <div class="col-4 text-right border-top border-dark">{{(data.salary + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-12 border-bottom border-top">Add : ( যোগ )</div>
                        <div class="col-8">T/A  (পরিবহন ভাতা)</div>
                        <div class="col-4 text-right">{{(data.ta || 0).toFixed(2)}}</div>
                        <div class="col-8">D/A  (মহার্ঘ ভাতা)</div>
                        <div class="col-4 text-right">{{(data.da || 0).toFixed(2)}}</div>
                        <div class="col-8">Attendance Bonus (হাজিরা বোনাস)</div>
                        <div class="col-4 text-right">{{(data.attendance_bonus || 0).toFixed(2)}}</div>
                        <div class="col-8">Production Bonus (উৎপাদন বোনাস)</div>
                        <div class="col-4 text-right">{{(data.production_bonus || 0).toFixed(2)}}</div>
                        <div class="col-8">Paid Leave (প্রদেয় ছুটি)</div>
                        <div class="col-4 text-right">{{(0).toFixed(2)}}</div>
                        <div class="col-8">No. of days (দিনের সংখ্যা)</div>
                        <div class="col-4 text-right">{{(data.leave_days || 0).toFixed(2)}}</div>
                        <div class="col-8">Overtime pay (ওভারটাইম মজুরী)</div>
                        <div class="col-4 text-right">{{(data.ot_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No. Of Hours (মোট কর্মঘন্টা)</div>
                        <div class="col-4 text-right">{{(data.ot_hour || 0).toFixed(2)}}</div>
                        <div class="col-8">Holidays -Worked (অবকাশকালীন কর্মদিবস)</div>
                        <div class="col-4 text-right">{{(data.worked_holiday_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No. Of Hours (মোট কর্মঘন্টা)</div>
                        <div class="col-4 text-right">{{(data.worked_holiday_hour || 0).toFixed(2)}}</div>
                        <div class="col-8">Fridays - Worked (সাপ্তাহিক ছুটি)</div>
                        <div class="col-4 text-right">{{(data.worked_friday_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No. Of Hours (মোট কর্মঘন্টা)</div>
                        <div class="col-4 text-right">{{(data.worked_friday_hour || 0).toFixed(2)}}</div>
                        <div class="col-8">Add'l Adjustment (অন্যান্য সম্বনয়)</div>
                        <div class="col-4 text-right">{{(data.total_allowance + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Gross  Pay: (প্রাপ্য বেতন)</div>
                        <div class="col-4 text-right border_bottom border-top border-dark">{{(data.gross_pay + 1e-9 || 0).toFixed(2)}}</div>
                    </div>
                    <div class="col-6 row m-0 p-0 border-top border-dark mb-1">
                        <div class="col-8">Gross  Pay: (প্রাপ্য বেতন)</div>
                        <div class="col-4 text-right">{{(data.gross_pay + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-12 border-bottom border-top">Deductions  : (বিয়োজন)</div>
                        <div class="col-8">Absent/UPL/NW/Late (অনুপুস্থিত/অবৈতনিকছুটি/বিলম্ব)</div>
                        <div class="col-4 text-right">{{(data.absent_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">No.of days (দিনের সংখ্যা)</div>
                        <div class="col-4 text-right">{{(data.absent_days || 0).toFixed(2)}}</div>
                        <div class="col-8">Sick Leave (অসুস্থতাজনিত ছুটি)</div>
                        <div class="col-4 text-right">{{(0).toFixed(2)}}</div>
                        <div class="col-8">Advances (অগ্রিম)</div>
                        <div class="col-4 text-right">{{(data.advance + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Provident  Fund (ভবিষ্যত তহবিল)</div>
                        <div class="col-4 text-right">{{(data.pf + 1e-9 || 0).toFixed(2)}}</div>
                        <!-- <div class="col-8">Income Tax (আয়কর)</div>
                        <div class="col-4 text-right">{{(data.tax + 1e-9 || 0).toFixed(2)}}</div> -->
                        <div class="col-8">Penalty (জরিমানা)</div>
                        <div class="col-4 text-right">0.00</div>
                        <div class="col-8">Ded'l Adjustment (কর্তনযোগ্য সম্বনয়)</div>
                        <div class="col-4 text-right">{{(data.deducted + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Subscription For W. A. (কল্যানতহবিলে চাঁদা)</div>
                        <div class="col-4 text-right">{{(0).toFixed(2)}}</div>
                        <div class="col-8">Not for join Days (অসম্পৃক্ত দিনের সংখ্যা)</div>
                        <div class="col-4 text-right">{{(data.not_for_join_days).toFixed(2)}}</div>
                        <div class="col-8">Amount (টাকা)</div>
                        <div class="col-4 text-right">{{(data.not_for_join_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Lay off days  (সাময়িক ছুটি)</div>
                        <div class="col-4 text-right">{{(data.lay_off_days || 0).toFixed(2)}}</div>
                        <div class="col-8">Amount (টাকা)</div>
                        <div class="col-4 text-right">{{(data.lay_off_amount + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">Total Deduction (মোট বিয়োজন )</div>
                        <div class="col-4 text-right border-top border-bottom border-dark">{{(data.total_deduction + 1e-9 || 0).toFixed(2)}}</div>
                        <div class="col-8">NET PAY (প্রাপ্ত বেতন)</div>
                        <div class="col-4 text-right border-bottom border-dark border-3">{{(data.net_pay + 1e-9 || 0).toFixed(2)}}</div>
                    </div>
                    <div class="col-2 mt-5"></div>
                    <div class="col-3 text-center border-top border-dark mt-5">Prepared by</div>
                    <div class="col-2 mt-5"></div>
                    <div class="col-3 text-center border-top border-dark mt-5">Received by</div>
                    <div class="col-2 mt-5"></div>                    
                </div>
                <div style="height:81px;"></div>

            </div>
        </div>
    </div>
</template>

<script>
import { ModelSelect } from 'vue-search-select'
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('pay_slip') }
    },

    data() {
        return{
            salarySheet : [],
            task: {},
            dataEdit: false,
            roles: [],
            DepartmentList: [],
            DepartmentName: 'Management',
            salaryMonth: this.convertDate(new Date()),
            start_limit: 1,
            end_limit: 0,
            all: 0,
            buttonTitle : this.$t('generate_salary_sheet'),
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
            this.isBusy = true
            var date = new Date(this.salaryMonth),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2)

            let start = year + '-' + mnth 
            fetch(`api/salarysheet/${start}`)
            .then(res => res.json())
            .then(res => {
                this.salarySheet = res['Salarysheet']
                this.salarySheetByDepartment
                this.disable = !this.disable
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message) 
                this.disable = !this.disable
                this.isBusy = false
            })
        },

        changeVew(val = null) {
            if (val == 'all') {
                this.start_limit = 1
                this.end_limit = 0
            }
            this.salarySheetByDepartment
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
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
            if(this.salaryMonth) {
                var date = new Date(this.salaryMonth),
                year = date.getFullYear(),
                mnth = date.getMonth(),
                month = ['January', 'Fabruary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                return [month[mnth], year].join("-");
            }
        },

        month_year_bn() {
            if(this.salaryMonth) {
                var date = new Date(this.salaryMonth),
                year = date.getFullYear(),
                mnth = date.getMonth(),
                month = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর']
                return [month[mnth], year].join("-");
            }
        },

        salarySheetByDepartment() {
            let array = [], array2 = [], k=0, l=0

            for (let i = 0; i < this.salarySheet.length; i++) {
                if (this.salarySheet[i]['department'] == this.DepartmentName) {
                    array[k++] = this.salarySheet[i]
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
        }
    },

    components: { ModelSelect },
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