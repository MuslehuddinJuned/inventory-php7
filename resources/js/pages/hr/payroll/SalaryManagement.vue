<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('salary_management') }}</h3>
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <!-- <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            title="List of Employee"
                            :data="employeeList"
                            :fields="json_fields"
                            worksheet="List of Employee"
                            name="List of Employee.xls">
                            <b-icon icon="file-earmark-spreadsheet-fill"></b-icon>
                        </download-excel>
                        <b-tooltip target="tooltip-target-1" triggers="hover">
                            Save this table to Excel
                        </b-tooltip> -->
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
                    :items="employeeList"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    @row-clicked="(item) => viewDetails(item.id)"
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
                <b-table-simple hover small caption-top responsive class="text-center">
                    <b-thead class="bg-info font-weight-bolder text-white">
                        <b-tr class="border border-dark">
                            <b-th class="border border-dark">{{$t('salary')}}</b-th> <b-th class="border border-dark">%</b-th> <b-th class="border border-dark">{{$t('amount')}}</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('basic_pay')}}</b-th>
                            <b-td class="border border-dark"></b-td>
                            <b-td class="border border-dark"><input v-model="task['basic_pay']" @keyup="basic_pay" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('house_rent')}}</b-th>
                            <b-td class="border border-dark"><input v-model="task['house_rent_percent']" @keyup="house_rent" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                            <b-td class="border border-dark"><input v-model="task['house_rent']" @keyup="house_rent_percent" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('medic_alw')}}</b-th>
                            <b-td class="border border-dark"><input v-model="task['medic_alw_percent']" @keyup="medic_alw" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                            <b-td class="border border-dark"><input v-model="task['medic_alw']" @keyup="medic_alw_percent" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('ta')}}</b-th>
                            <b-td class="border border-dark"><input v-model="task['ta_percent']" @keyup="ta" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                            <b-td class="border border-dark"><input v-model="task['ta']" @keyup="ta_percent" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-th class="border border-dark align-middle">{{$t('da')}}</b-th>
                            <b-td class="border border-dark"><input v-model="task['da_percent']" @keyup="da" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                            <b-td class="border border-dark"><input v-model="task['da']" @keyup="da_percent" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-td>
                        </b-tr>
                    </b-tbody>
                    <b-tfoot>
                        <b-tr>
                            <b-td class="border border-dark" colspan="2" variant="success">{{ $t('total_salary')}}</b-td>
                            <b-td class="border border-dark" variant="success">{{task['total_salary'] = parseFloat(task['basic_pay']) + parseFloat(task['house_rent']) + parseFloat(task['medic_alw']) + parseFloat(task['ta']) + parseFloat(task['da'])}}</b-td>
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
        <b-modal class="b-0" ref="dataView" id="dataView" size="xl" :title="$t('salary_management')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-4 text-center m-0">
                    <h4 class="">ID: {{task['employee_id']}}</h4>
                    <img style="width: 100%; " :src="'/images/employee/' + task['employee_image']" alt="Picture not found">
                </div>
                <div class="col-md-8 m-0">
                    <div class="col-md-12 p-0">
                        <h2>{{task['name']}}</h2>
                        <h4>{{task['designation']}}</h4>
                        <h5>{{$t('department')}}: {{task['department']}}</h5>
                    </div>
                    <div class="row m-0 p-0 p-0 col-md-12 mt-5">
                        <div class="col-md-4"><p class="font-weight-bold mb-0">{{$t('section')}}</p><p>{{task['section']}}</p></div>
                        <div class="col-md-8"><p class="font-weight-bold mb-0">{{$t('work_location')}}</p><p>{{task['work_location']}}</p></div>
                    </div>
                    <div class="col-md-12 mt-2 p-0">
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('phone')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['mobile_no']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('email')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['email']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('address')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['address']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-5 p-0">
                        <h4>{{$t('personal_info')}}</h4>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('gender')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['gender']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('date_of_birth')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['date_of_birth']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('marital_status')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['marital_status']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('blood_group')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['blood_group']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('joining_date')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{`${task['start_date']}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-5 p-0">
                        <h4>{{$t('attendance')}}</h4>
                        
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('In Time')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['start_time']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('Out Time')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['end_time']}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-5 p-0">
                        <h4>{{$t('contact_person')}}</h4>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('name')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['contact_name']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('phone')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['contact_phone']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('relationship')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['relationship']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('address')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['contact_address']}}</p>
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
            task: {'basic_pay': null, 'medic_alw': null, 'house_rent': null, 'ta': null, 'da': null, 'other_field': null, 'other_pay': null, 'total_salary': null, 'bank_name': null, 'acc_no': null, 'employee_id': null},
            taskId: null,
            buttonTitle : this.$t('save'),
            disable: false,
            noprint: 'noprint',

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

        viewDetails(id) {
            let day = []
            this.taskId = id
            this.noprint = 'noprint'
            this.task = this.singleTask
            this.task['house_rent_percent'] =  this.task['house_rent'] * 100 / this.task['basic_pay']
            this.task['medic_alw_percent'] =  this.task['medic_alw'] * 100 / this.task['basic_pay']
            this.task['ta_percent'] =  this.task['ta'] * 100 / this.task['basic_pay']
            this.task['da_percent'] =  this.task['da'] * 100 / this.task['basic_pay']
            this.$refs['dataView'].show()
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        basic_pay() {
            this.task['house_rent'] =  this.task['basic_pay'] * this.task['house_rent_percent'] / 100
            this.task['medic_alw'] =  this.task['basic_pay'] * this.task['medic_alw_percent'] / 100
            this.task['ta'] =  this.task['basic_pay'] * this.task['ta_percent'] / 100
            this.task['da'] =  this.task['basic_pay'] * this.task['da_percent'] / 100
        },
        house_rent_percent() { this.task['house_rent_percent'] =  this.task['house_rent'] * 100 / this.task['basic_pay'] },
        house_rent() { this.task['house_rent'] =  this.task['basic_pay'] * this.task['house_rent_percent'] / 100 },
        medic_alw_percent() { this.task['medic_alw_percent'] =  this.task['medic_alw'] * 100 / this.task['basic_pay'] },
        medic_alw() { this.task['medic_alw'] =  this.task['basic_pay'] * this.task['medic_alw_percent'] / 100 },
        ta_percent() { this.task['ta_percent'] =  this.task['ta'] * 100 / this.task['basic_pay'] }, 
        ta() { this.task['ta'] =  this.task['basic_pay'] * this.task['ta_percent'] / 100 },
        da_percent() { this.task['da_percent'] =  this.task['da'] * 100 / this.task['basic_pay'] },
        da() { this.task['da'] =  this.task['basic_pay'] * this.task['da_percent'] / 100 },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            // basic_pay, house_rent_percent, house_rent, medic_alw_percent, ta_percent, da_percent, total_salary
            if(this.taskId == null){
                axios.post(`api/employee`, this.task)
                .then(({data}) =>{
                    this.errors = ''
                    this.employeeList.unshift(data.employeeList)
                    this.taskId = this.employeeList[0]['id']
                    this.totalRows = this.employeeList.length;
                    for (let i = 0; i < this.totalRows; i++) {
                        this.employeeList[i]['sn'] = i                
                    } 
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.stepper++
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
                axios.patch(`api/employee/${this.taskId}`, this.task)
                .then(({data}) => {
                    this.errors = ''
                    this.src = '/images/employee/'
                    this.task['employee_image'] = data.fileName
                    this.employeeList[this.Index] = this.task;
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.stepper++
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
        
        singleTask() {
            let array = []
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['id'] == this.taskId) {
                    array = this.employeeList[i]
                    break
                }                
            }
            return array
        },

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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