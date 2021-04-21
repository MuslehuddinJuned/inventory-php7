<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header col-12 row m-0 p-0 my-3">
                    <div class="col-md-8">
                        <h3 class="panel-title">{{ $t('salary_management') }}</h3>
                    </div>
                    <div class="col-md-4 noprint"><b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select></div>
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            title="Employee Salary"
                            :data="employeeListByDept"
                            :fields="json_fields"
                            worksheet="Employee Salary"
                            name="Employee Salary.xls">
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
                    <b-table id="table-transition" primary-key="employee_id" :busy="isBusy" show-empty small striped hover stacked="md"
                    :items="employeeListByDept"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    @row-clicked="(item) => viewDetails(item.id, item.employee_id)"
                    class="table-transition"
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
                    <template v-slot:cell(employee_image)="row">
                        <a :href="'/images/employee/' + row.item.employee_image"><b-img :src="'/images/employee/' + row.item.employee_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
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

        <!-- Start Edit Details Modal -->
        <b-modal ref="dataEdit" id="dataEdit" size="lg" :title="$t('salary_management')" no-close-on-backdrop>            
            <div class="modal-body">
                <div class="row col-12 m-0 p-0">
                    <div class="form-group col-md-6">
                        <label for="bank_name" class="col-form-label">{{$t('bank_name')}}</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" v-model="task['bank_name']">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="acc_no" class="col-form-label">{{$t('acc_no')}}</label>
                        <input type="text" class="form-control" id="acc_no" name="acc_no" v-model="task['acc_no']">
                    </div>
                </div>
                <b-table-simple hover small caption-top responsive class="text-center">
                    <b-thead class="bg-info font-weight-bolder text-white">
                        <b-tr class="border border-dark">
                            <b-th class="border border-dark">{{$t('salary')}}</b-th> <b-th class="border border-dark">{{$t('amount')}} ($)</b-th> <b-th class="border border-dark">{{$t('amount')}} (৳)</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('current_salary')}}</b-th>
                            <b-td class="border border-dark"><input v-model="task['current_pay_doller']" @keyup="current_salary" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                            <b-td class="border border-dark">{{task['current_salary'].toFixed(2)}}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('basic_pay')}}</b-th>
                            <b-td class="border border-dark">{{task['basic_pay_doller']}}</b-td>
                            <b-td class="border border-dark">{{task['basic_pay']}}</b-td>
                            <!-- <b-td class="border border-dark"><input v-model="task['basic_pay']" @keyup="basic_pay" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td> -->
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('house_rent')}}</b-th>
                            <b-td class="border border-dark">{{task['house_rent_doller']}}</b-td>
                            <b-td class="border border-dark">{{task['house_rent']}}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('medic_alw')}}</b-th>
                            <b-td class="border border-dark"><input v-model="task['medic_alw_doller']" @keyup="medic_alw" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                            <b-td class="border border-dark">{{task['medic_alw']}}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('fixed_allowance')}}</b-th>
                            <b-td class="border border-dark"></b-td>
                            <b-td class="border border-dark"><input v-model="task['fixed_allowance']" @keyup="fixed_allowance_doller" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('ta')}}</b-th>
                            <b-td class="border border-dark"></b-td>
                            <b-td class="border border-dark"><input v-model="task['ta']" @keyup="ta_doller" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('da')}}</b-th>
                            <b-td class="border border-dark"></b-td>
                            <b-td class="border border-dark"><input v-model="task['da']" @keyup="da_doller" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('providant_fund')}}</b-th>
                            <b-td class="border border-dark"></b-td>
                            <b-td class="border border-dark"><input v-model="task['providant_fund']" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('tax')}}</b-th>
                            <b-td class="border border-dark"></b-td>
                            <b-td class="border border-dark"><input v-model="task['tax']" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                    </b-tbody>
                    <b-tfoot>
                        <b-tr>
                            <b-td class="border border-dark" colspan="2" variant="success">{{ $t('total_salary')}}</b-td>
                            <b-td class="border border-dark" variant="success">{{task['total_salary'] = (parseFloat(task['basic_pay'] || 0) + parseFloat(task['house_rent'] || 0) + parseFloat(task['medic_alw'] || 0) + parseFloat(task['ta'] || 0) + parseFloat(task['da'] || 0) + parseFloat(task['fixed_allowance'] || 0) - parseFloat(task['providant_fund'] || 0)- parseFloat(task['tax'] || 0))}}</b-td>
                        </b-tr>
                    </b-tfoot>
                </b-table-simple>
            </div>                        
            <template v-slot:modal-footer="">
                <button @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal>        
        <!-- End Edit Details Modal --> 

        <!-- Start view Details Modal -->
        <b-modal class="b-0" ref="dataView" id="dataView" size="lg" :title="$t('salary_management')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-4 text-center m-0">
                    <h4 class="">ID: {{task['official_id']}}</h4>
                    <img style="width: 100%; " :src="'/images/employee/' + task['employee_image']" alt="Picture not found">
                </div>
                <div class="col-md-8 m-0">
                    <div class="col-md-12 p-0">
                        <h2>{{task['first_name']}}</h2>
                        <h4>{{task['designation']}}</h4>
                        <h5>{{$t('department')}}: {{task['department']}}</h5>
                        <h5>{{$t('section')}}: {{task['section']}}</h5>
                    </div>
                    <div class="col-md-12 mt-5 p-0">
                        <h4>{{$t('salary_details')}}</h4>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-light my-auto">
                                <p class="my-auto font-weight-bold">{{$t('bank_name')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['bank_name']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-info">
                                <p class="my-auto text-white font-weight-bold">{{$t('acc_no')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['acc_no']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('current_salary')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{(task['current_pay_doller']*82).toFixed(2)}} (${{task['current_pay_doller']}})</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('basic_pay')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['basic_pay']}} (${{task['basic_pay_doller'].toFixed(2)}})</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('house_rent')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['house_rent']}} (${{task['house_rent_doller'].toFixed(2)}})</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('medic_alw')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['medic_alw']}} (${{task['medic_alw_doller'].toFixed(2)}})</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('ta')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['ta']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('da')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['da']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-light my-auto">
                                <p class="my-auto font-weight-bold">{{$t('fixed_allowance')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['fixed_allowance']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-info">
                                <p class="my-auto text-white font-weight-bold">{{$t('providant_fund')}} (-)</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['providant_fund']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-light my-auto">
                                <p class="my-auto font-weight-bold">{{$t('tax')}} (-)</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['tax']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-info">
                                <p class="my-auto text-white font-weight-bold">{{$t('total_salary')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['total_salary'].toFixed(2)}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <template v-slot:modal-footer="">
                <button v-if="checkRoles('salary_management_Update')" @click="$refs['dataEdit'].show()" class="mdb btn btn-outline-default float-right">{{ $t('edit') }}</button>
                <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End view Details Modal -->
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('salary_management') }
    },

    data() {
        return{
            employeeList : [],
            roles: [],
            errors : [],
            task: {'current_salary': 0, 'basic_pay': 0, 'medic_alw': 0, 'house_rent': 0, 'ta': 0, 'da': 0, 'fixed_allowance': 0, 'fixed_allowance_doller': 0, 'providant_fund': 0, 'tax': 0,'basic_pay_doller' : 0, 'medic_alw_doller': 0, 'house_rent_doller': 0, 'ta_doller': 0, 'da_doller': 0, 'providant_fund_doller': 0, 'tax_doller': 0, 'other_field': null, 'other_pay': null, 'total_salary': 0, 'bank_name': null, 'acc_no': null, 'employee_id': null},
            taskId: null,
            buttonTitle : this.$t('save'),
            disable: false,
            noprint: 'noprint',
            DepartmentList: [],
            DepartmentName: 'Management',

            json_fields: {
                'ID': 'official_id',
                'Name': 'first_name',
                'Designation': 'designation',
                'Department' : 'department',
                'Bank' : 'bank_name',
                'Acc No' : 'acc_no',
                'Basic Pay' : 'basic_pay',
                'Total Salary' : 'total_salary'                
            },

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [10, 25, 50],
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        this.isBusy = true
        this.src = '/images/employee/'

        fetch(`api/salary`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['Salary'];
            this.totalRows_Role = this.employeeList.length
            this.isBusy = false
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/salarysheet`)
        .then(res => res.json())
        .then(res => {
            this.DepartmentList = res['Department'];
            this.DepartmentList.unshift('All');
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

        viewDetails(id, employee_id) {
            this.taskId = id
            this.noprint = 'noprint'
            this.task = this.singleTask(employee_id)
            // all percent is considered as doller
            this.task['current_salary'] =  this.task['current_pay_doller'] * 82
            this.task['house_rent_doller'] =  this.task['house_rent'] / 82
            this.task['basic_pay_doller'] =  this.task['basic_pay'] / 82
            this.task['medic_alw_doller'] =  this.task['medic_alw'] / 82
            this.task['ta_doller'] =  ''
            this.task['da_doller'] =  ''
            this.task['fixed_allowance_doller'] =  ''
            this.task['providant_fund_doller'] =  ''
            this.task['tax_doller'] =  ''

            // this.task['house_rent_doller'] =  this.task['house_rent'] * 100 / this.task['basic_pay']
            // this.task['medic_alw_doller'] =  this.task['medic_alw'] * 100 / this.task['basic_pay']
            // this.task['ta_doller'] =  this.task['ta'] * 100 / this.task['basic_pay']
            // this.task['da_doller'] =  this.task['da'] * 100 / this.task['basic_pay']
            // this.task['fixed_allowance_doller'] =  this.task['fixed_allowance'] * 100 / this.task['basic_pay']
            // this.task['providant_fund_doller'] =  this.task['providant_fund'] * 100 / this.task['basic_pay']
            // this.task['tax_doller'] =  this.task['tax'] * 100 / this.task['basic_pay']

            this.$refs['dataView'].show()
        },

        singleTask(employee_id) {
            let array = []
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['employee_id'] == employee_id) {
                    array = this.employeeList[i]
                    break
                }                
            }
            return array
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        current_salary() {
            this.task['current_salary'] =  this.task['current_pay_doller'] * 82
            this.task['basic_pay_doller'] =  (this.task['current_pay_doller'] - this.task['medic_alw_doller']) / 1.5
            this.task['basic_pay'] =  this.task['basic_pay_doller'] * 82
            this.task['house_rent'] =  this.task['basic_pay'] / 2
            this.task['house_rent_doller'] =  this.task['basic_pay_doller'] / 2
        },

        basic_pay() {
            this.task['house_rent'] =  this.task['basic_pay'] * this.task['house_rent_doller'] / 100
            this.task['medic_alw'] =  this.task['basic_pay'] * this.task['medic_alw_doller'] / 100
            this.task['ta'] =  this.task['basic_pay'] * this.task['ta_doller'] / 100
            this.task['da'] =  this.task['basic_pay'] * this.task['da_doller'] / 100
            this.task['fixed_allowance'] =  this.task['basic_pay'] * this.task['fixed_allowance_doller'] / 100
            this.task['providant_fund'] =  this.task['basic_pay'] * this.task['providant_fund_doller'] / 100
            this.task['tax'] =  this.task['basic_pay'] * this.task['tax_doller'] / 100
        },
        house_rent_doller() { this.task['house_rent_doller'] =  this.task['house_rent'] * 100 / this.task['basic_pay'] },
        house_rent() { this.task['house_rent'] =  this.task['basic_pay'] * this.task['house_rent_doller'] / 100 },
        medic_alw_doller() { this.task['medic_alw_doller'] =  this.task['medic_alw'] * 100 / this.task['basic_pay'] },
        medic_alw() { this.task['medic_alw'] =  this.task['medic_alw_doller'] * 82 },
        ta_doller() { this.task['ta_doller'] =  this.task['ta'] * 100 / this.task['basic_pay'] }, 
        ta() { this.task['ta'] =  this.task['basic_pay'] * this.task['ta_doller'] / 100 },
        da_doller() { this.task['da_doller'] =  this.task['da'] * 100 / this.task['basic_pay'] },
        da() { this.task['da'] =  this.task['basic_pay'] * this.task['da_doller'] / 100 },
        fixed_allowance_doller() { this.task['fixed_allowance_doller'] =  this.task['fixed_allowance'] * 100 / this.task['basic_pay'] },
        fixed_allowance() { this.task['fixed_allowance'] =  this.task['basic_pay'] * this.task['fixed_allowance_doller'] / 100 },
        providant_fund_doller() { this.task['providant_fund_doller'] =  this.task['providant_fund'] * 100 / this.task['basic_pay'] },
        providant_fund() { this.task['providant_fund'] =  this.task['basic_pay'] * this.task['providant_fund_doller'] / 100 },
        tax_doller() { this.task['tax_doller'] =  this.task['tax'] * 100 / this.task['basic_pay'] },
        tax() { this.task['tax'] =  this.task['basic_pay'] * this.task['tax_doller'] / 100 },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            if(this.taskId == null){
                axios.post(`api/salary`, this.task)
                .then(({data}) =>{
                    this.errors = ''
                    this.task['id'] = data.SalaryId
                    this.taskId = data.SalaryId
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.$refs['dataEdit'].hide()
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
            } else {
                axios.patch(`api/salary/${this.taskId}`, this.task)
                .then(({data}) => {
                    this.errors = ''
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.$refs['dataEdit'].hide()
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }
        },
    },

    computed: {

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
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

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'official_id', label : 'ID', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'employee_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'bank_name', label : this.$t('bank_name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'acc_no', label : this.$t('acc_no'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'basic_pay', label : this.$t('basic_pay'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_salary', label : this.$t('total_salary'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    }
}
</script>

<style lang="scss" scoped>

</style>