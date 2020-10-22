<template>
    <div class="container-fluid">
        <div class="card filterable" :class="noprint">
            <div class="card-header row m-0">
                <div class="col-md-6">
                    <h3 class="panel-title float-left">{{ DepartmentName + ' ' + $t('salary_sheet') }} <span v-if="savingDate">({{savingDate | dateParse('YYYY-MM-DD') | dateFormat('MMMM-YYYY') }})</span></h3>
                </div>
            </div>
            <div v-if="checkRoles('salary_sheet_Insert')" class="card-body my-3 text-center">
                <input type="date" v-model="savingDate" class="p-1" required>
                <button @click.prevent="save" class="mdb btn btn-outline-primary my-0 py-2" type="button" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon>{{ buttonTitle }}</button>
            </div>
            <div class="card-header row m-0 p-0">
                <div class="col-md-3">
                    <button v-if="reportType == $t('summary')" @click="reportType = $t('details')" class="mdb btn btn-outline-unique">{{$t('details')}} {{$t('report')}}</button>
                    <button v-if="reportType == $t('details')" @click="reportType = $t('summary')" class="mdb btn btn-outline-unique">{{$t('summary')}} {{$t('report')}}</button>
                </div>
                <div class="col-md-7 my-auto">
                    <b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select>
                    <!-- <div style="min-width: 400px;"><model-select :options="DepartmentList" v-model="DepartmentName" class="form-control"></model-select></div> -->
                </div>
                <div class="col-md-2 my-auto">
                    <h3 class="panel-title float-right my-auto">
                        <b-icon icon="circle-fill" animation="throb" class="text-success" :class="loading"></b-icon>
                        <fa v-if="checkRoles('salary_sheet_Update') && !dataEdit" @click="dataEdit = true" icon="edit" class="ml-2 pointer" fixed-width v-b-tooltip.hover :title="$t('edit')"/> 
                        <fa v-if="checkRoles('salary_sheet_Update') && dataEdit" @click="dataEdit = false" icon="save" class="ml-2 pointer text-success" fixed-width v-b-tooltip.hover :title="$t('save')"/>
                        <fa v-if="checkRoles('salary_sheet_Delete')" @click="destroy" icon="trash-alt" class="ml-2 pointer text-danger" fixed-width v-b-tooltip.hover :title="$t('delete')"/> 
                    </h3>
                </div>
            </div>
            <div class="card-body m-0 p-0">
                <div class="card-header d-flex align-items-center noprint">
                    <download-excel
                        id="tooltip-target-1"
                        class="btn btn-outline-default btn-sm mr-3"
                        :title="DepartmentName + ' Salary Sheet ' + month_year"
                        :data="salarySheetByDepartment"
                        :fields="json_fields"
                        worksheet="Salary Sheet"
                        name="Salary Sheet.xls">
                        <b-icon icon="file-earmark-spreadsheet-fill"></b-icon>
                    </download-excel>
                    <b-tooltip target="tooltip-target-1" triggers="hover">
                        Save this table to Excel
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
                <b-table id="table-transition" primary-key="id" :busy="isBusy" sticky-header="80vh" show-empty small striped hover responsive
                :items="salarySheetByDepartment"
                :fields="fields"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :filterIncludedFields="filterOn"
                :tbody-transition-props="transProps"
                :no-border-collapse="noCollapse"
                @filtered="onFiltered"
                class="table-transition table-bordered"
                >
                <template v-slot:table-busy>
                    <div class="text-center text-success my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>{{$t('loading')}}</strong>
                    </div>
                </template>
                <template v-slot:head()="scope">
                    <div class="text-wrap">
                    {{ scope.label }}
                    </div>
                </template>
                <template v-slot:cell(index)="row">
                    {{ row.index+1 }}
                </template>
                <template v-slot:cell(first_name)="row">
                    <b-form-checkbox v-if="dataEdit" @change="updating(row.item)" v-model="row.item.checked" value=1 unchecked-value=2>
                    </b-form-checkbox>
                    {{row.item.first_name}}
                </template>
                <template v-slot:cell(basic_daily)="row">
                     {{(row.item.basic_daily || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(basic_monthly)="row">
                     {{(row.item.basic_monthly || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(house_rent)="row">
                     {{(row.item.house_rent || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(salary)="row">
                     {{(row.item.salary || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(salary_usd)="row">
                     {{(row.item.salary_usd || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(pf)="row">
                     {{(row.item.pf || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(tax)="row">
                     {{(row.item.tax || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(ot_rate)="row">
                     {{(row.item.ot_rate || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(ta)="row">
                    <span v-if="!dataEdit" v-html="row.item.ta"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.ta" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="width : 60px;">
                </template>
                <template v-slot:cell(da)="row">
                    <span v-if="!dataEdit" v-html="row.item.da"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.da" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="width : 60px;">
                </template>
                <template v-slot:cell(attendance_bonus)="row">
                    <span v-if="!dataEdit" v-html="row.item.attendance_bonus"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.attendance_bonus" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="width : 60px;">
                </template>
                <template v-slot:cell(production_bonus)="row">
                    <span v-if="!dataEdit" v-html="row.item.production_bonus"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.production_bonus" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="width : 60px;">
                </template>
                <template v-slot:cell(worked_friday_hour)="row">
                    <span v-if="!dataEdit" v-html="row.item.worked_friday_hour"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.worked_friday_hour" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="width : 60px;">
                </template>
                <template v-slot:cell(worked_friday_amount)="row">
                     {{((row.item.worked_friday_amount = row.item.basic_daily*(row.item.worked_friday_hour || 0) / 4) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(worked_holiday_hour)="row">
                    <span v-if="!dataEdit" v-html="row.item.worked_holiday_hour"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.worked_holiday_hour" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="width : 60px;">
                </template>
                <template v-slot:cell(worked_holiday_amount)="row">
                     {{((row.item.worked_holiday_amount = row.item.basic_daily*(row.item.worked_holiday_hour || 0)*3 / 8) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(ot_hour)="row">
                    <span v-if="!dataEdit" v-html="row.item.ot_hour"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.ot_hour" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(ot_amount)="row">
                     {{((row.item.ot_amount = row.item.basic_monthly * (row.item.ot_hour || 0)*2 / 208) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(fixed_allowance)="row">
                    <span v-if="!dataEdit" v-html="row.item.fixed_allowance"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.fixed_allowance" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(attendance_allowance)="row">
                    <span v-if="!dataEdit" v-html="row.item.attendance_allowance"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.attendance_allowance" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(total_allowance)="row">
                     {{((row.item.total_allowance = parseFloat(row.item.fixed_allowance || 0)+parseFloat(row.item.attendance_allowance || 0)) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(present_days)="row">
                    <span v-if="!dataEdit" v-html="row.item.present_days"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.present_days" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(holidays)="row">
                    <span v-if="!dataEdit" v-html="row.item.holidays"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.holidays" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(absent_days)="row">
                    <span v-if="!dataEdit" v-html="row.item.absent_days"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.absent_days" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(absent_amount)="row">
                     {{((row.item.absent_amount = (parseFloat((row.item.ta/row.item.no_fo_days) || 0) + parseFloat((row.item.da/row.item.no_fo_days) || 0) + parseFloat(row.item.basic_daily || 0))*row.item.absent_days) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(advance)="row">
                    <span v-if="!dataEdit" v-html="row.item.advance"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.advance" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(deducted)="row">
                    <span v-if="!dataEdit" v-html="row.item.deducted"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.deducted" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(not_for_join_days)="row">
                    <span v-if="!dataEdit" v-html="row.item.not_for_join_days"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.not_for_join_days" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(not_for_join_amount)="row">
                     {{((row.item.not_for_join_amount = (row.item.salary/row.item.no_fo_days)*(row.item.not_for_join_days || 0)) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(lay_off_days)="row">
                    <span v-if="!dataEdit" v-html="row.item.lay_off_days"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.lay_off_days" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(lay_off_amount)="row">
                     {{((row.item.lay_off_amount = (row.item.salary/row.item.no_fo_days)*(row.item.lay_off_days || 0)) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(suspense_days)="row">
                    <span v-if="!dataEdit" v-html="row.item.suspense_days"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.suspense_days" style="width : 60px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(suspense_amount)="row">
                     {{((row.item.suspense_amount = (row.item.salary/row.item.no_fo_days)*(row.item.suspense_days || 0)) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(gross_pay)="row">
                     {{((row.item.gross_pay = (parseFloat(row.item.salary || 0) + parseFloat(row.item.ta || 0) + parseFloat(row.item.da || 0) + parseFloat(row.item.attendance_bonus || 0) + parseFloat(row.item.production_bonus || 0) + parseFloat(row.item.worked_friday_amount || 0) + parseFloat(row.item.worked_holiday_amount || 0) + parseFloat(row.item.ot_amount || 0) + parseFloat(row.item.total_allowance || 0))) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(total_deduction)="row">
                     {{((row.item.total_deduction = parseFloat(row.item.absent_amount || 0) + parseFloat(row.item.advance || 0) + parseFloat(row.item.pf || 0) + parseFloat(row.item.tax || 0) + parseFloat(row.item.deducted || 0) + parseFloat(row.item.not_for_join_amount || 0) + parseFloat(row.item.lay_off_amount || 0) + parseFloat(row.item.suspense_amount || 0)) || 0).toFixed(2)}}
                </template>
                <template v-slot:cell(net_pay)="row">
                     {{((row.item.net_pay = parseFloat(row.item.gross_pay || 0)-parseFloat(row.item.total_deduction || 0)) || 0).toFixed(2)}}
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
    </div>
</template>

<script>
import { ModelSelect } from 'vue-search-select'
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('salary_sheet') }
    },

    data() {
        return{
            salarySheet : [],
            dataEdit: false,
            roles: [],
            DepartmentList: [],
            DepartmentName: 'Management',
            // month_year: null,
            savingDate: null,
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

            json_fields: {
                'ID': 'employee_id',
                'Name': 'first_name',
                'Designation': 'designation',
                'Joining Date': 'start_date',
                'Days': 'no_fo_days',
                'Basic (Daily)': 'basic_daily',
                'Basic (Monthly)': 'basic_monthly',
                'House Rent': 'house_rent',
                'Medical Allowance': 'medic_allowance',
                'Salary (৳)': 'salary',
                'Rate': 'covert_rate',
                'Salary ($)': 'salary_usd',
                'T/A': 'ta',
                'D/A': 'da',
                'Attendance Bonus': 'attendance_bonus',
                'Production Bonus': 'production_bonus',
                'Worked on Friday (hr)': 'worked_friday_hour',
                'Friday Amount': 'worked_friday_amount',
                'Worked on Holiday (hr)': 'worked_holiday_hour',
                'Holiday Amount': 'worked_holiday_amount',
                'OT Rate': 'ot_rate',
                'OT Hour': 'ot_hour',
                'OT Amount': 'ot_amount',
                'Fixed Allowance': 'fixed_allowance',
                'Attendance Allowance': 'attendance_allowance',
                'Total Allowance': 'total_allowance',
                'Gross Pay': 'gross_pay',
                'Present Days': 'present_days',
                'Holidays': 'holidays',
                'Absent Days': 'absent_days',
                'Absent Amount': 'absent_amount',
                'Leave Days': 'leave_days',
                'Advance': 'advance',
                'Provident Fund': 'pf',
                'Income Tax': 'tax',
                'Deducted Adjustment': 'deducted',
                'Not for Join Days': 'not_for_join_days',
                'Not for Join Amount': 'not_for_join_amount',
                'Lay-off Days': 'lay_off_days',
                'Lay-off Amount': 'lay_off_amount',
                'Suspense Days': 'suspense_days',
                'Suspense Amount': 'suspense_amount',
                'Total Deduction': 'total_deduction',
                'Net Payment': 'net_pay',
            },
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

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        save() {
            if (this.savingDate) {  
                this.isBusy = true
                this.disable = !this.disable
                this.buttonTitle = this.$t('saving') 
                var date = new Date(this.savingDate),
                    year = date.getFullYear(),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    month = date.getMonth() + 1
                let start = year + '-' + mnth + '-01'
                let end = this.convertDate(new Date(year, mnth, 0))             
                axios.post(`api/salarysheet`, {
                    'start' : start,
                    'end' : end,
                    'date' : year + '-' + month
                })
                .then(({data}) =>{
                    this.errors = ''
                    this.salarySheet = data.Salarysheet
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('generate_salary_sheet')
                    this.isBusy = false
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.isBusy = false
                    this.buttonTitle = this.$t('generate_salary_sheet')
                })
            }
        },

        updating(value) {
            this.buttonTitle = this.$t('saving')
            
            axios.patch(`api/salarysheet/${value['id']}`, value)
            .then(({data}) => {
                this.errors = ''
                this.buttonTitle = this.$t('generate_salary_sheet')
            })
            .catch(err => {
                if(err.response.status == 422){
                    this.errors = err.response.data.errors
                } else alert(err.response.data.message) 
                this.buttonTitle = this.$t('generate_salary_sheet')
            });
        },

        destroy() {
            let date = new Date(this.savingDate),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2)
            this.$toast.warning(this.$t('sure_to_delete'), year + '-' + mnth, {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                                                    
                        axios.delete(`api/salarysheet/${year + '-' + mnth}`)                        
                        .then(res => {
                            this.salarySheet = []
                        })
                        .catch(err => {
                            alert(err.response.data.message);                       
                        });

                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }, true],
                    ['<button>'+ this.$t('cancel') +'</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }],
                ]            
            });
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
            if(this.savingDate) {
                var date = new Date(this.savingDate),
                year = date.getFullYear(),
                mnth = date.getMonth(),
                month = ['January', 'Fabruary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                return [month[mnth], year].join("-");
            }
        },

        salarySheetByDepartment() {
            let array = [], k=0
            for (let i = 0; i < this.salarySheet.length; i++) {
                if (this.salarySheet[i]['department'] == this.DepartmentName) {
                    array[k] = this.salarySheet[i]
                    if (array[k]['checked']) {
                        array[k]['_rowVariant'] = 'success'
                    } k++
                }                
            }

            this.totalRows = array.length
            return array
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('generate_salary_sheet')
            
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, stickyColumn: true, class: 'text-center align-middle bg-white bg-white', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'start_date', label : this.$t('joining_date'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'no_fo_days', label : this.$t('days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'basic_daily', label : this.$t('basic_daily'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'basic_monthly', label : this.$t('basic_monthly'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'house_rent', label : this.$t('house_rent'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'medic_allowance', label : this.$t('medic_alw'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'salary', label : this.$t('salary_tk'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'covert_rate', label : this.$t('covert_rate'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'salary_usd', label : this.$t('salary_usd'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'ta', label : this.$t('ta'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'da', label : this.$t('da'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'attendance_bonus', label : this.$t('attendance_bonus'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'production_bonus', label : this.$t('production_bonus'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'worked_friday_hour', label : this.$t('worked_friday_hour'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'worked_friday_amount', label : this.$t('worked_friday_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'worked_holiday_hour', label : this.$t('worked_holiday_hour'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'worked_holiday_amount', label : this.$t('worked_holiday_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'ot_hour', label : this.$t('ot_hour'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'ot_rate', label : this.$t('ot_rate'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'ot_amount', label : this.$t('ot_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'fixed_allowance', label : this.$t('fixed_allowance'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'attendance_allowance', label : this.$t('attendance_allowance'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'total_allowance', label : this.$t('total_allowance'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'gross_pay', label : this.$t('gross_pay'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'present_days', label : this.$t('present_days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'holidays', label : this.$t('holidays'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'absent_days', label : this.$t('absent_days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'absent_amount', label : this.$t('absent_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'leave_days', label : this.$t('leave_days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'advance', label : this.$t('advance'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'pf', label : this.$t('providant_fund'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'tax', label : this.$t('tax'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'deducted', label : this.$t('deducted'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'not_for_join_days', label : this.$t('not_for_join_days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'not_for_join_amount', label : this.$t('not_for_join_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'lay_off_days', label : this.$t('lay_off_days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'lay_off_amount', label : this.$t('lay_off_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'suspense_days', label : this.$t('suspense_days'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'suspense_amount', label : this.$t('suspense_amount'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'total_deduction', label : this.$t('total_deduction'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'net_pay', label : this.$t('net_pay'), sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
            ]            
        },
    },

    components: { ModelSelect },
}
</script>

<style lang="scss" scoped>

</style>