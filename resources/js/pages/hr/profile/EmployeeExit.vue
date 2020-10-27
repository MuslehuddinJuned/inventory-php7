<template>
    <div>
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('employee_exit') }}</h3>                     
                    <!-- <div class="ml-auto">
                        <button v-if="checkRoles('employee_profile_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div> -->
                </div>
                <div class="col-md-4 card-body noprint"><b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select></div>
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
                    :items="employeeListByDept"
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
        <!-- Start view Details Modal -->
        <b-modal class="b-0" ref="dataView" id="dataView" size="xl" :title="$t('employee_profile')" no-close-on-backdrop ok-only>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-4 text-center m-0">
                    <h4 class="">ID: {{task['employee_id']}}</h4>
                    <img style="width: 100%; " :src="'/images/employee/' + task['employee_image']" alt="Picture not found">
                    <div class="col-md-12 mt-2 p-0 text-left">
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('status')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{$t(task['status'])}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('reason')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['reason']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('resign_date')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['resign_date']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('effective_date')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['effective_date']}}</p>
                            </div>
                        </div>
                    </div>
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
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('weekly_holidays')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{holiday}}</p>
                            </div>
                        </div>
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
        return { title: this.$t('employee_exit') }
    },

    data() {
        return{
            employeeList : [],
            roles: [],
            errors : [],
            task: [{'employee_id': null, 'first_name': null, 'last_name': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': this.$t('male'), 'date_of_birth': this.convertDate(new Date()), 'marital_status': this.$t('single'), 'designation': null, 'department': null, 'section': null, 'work_location': null, 'start_date': this.convertDate(new Date()), 'salary': null, 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': 'noimage.jpg', 'status': 'active', 'weekly_holiday': [5], 'start_time': '8:00:00', 'end_time': '17:00:00'}],
            taskId: null,
            src : '/images/employee/',
            holiday : [],
            DepartmentList: [],
            DepartmentName: 'Management',

            weekOptions : [
                { value: 6, text: this.$t('saturday') },
                { value: 0, text: this.$t('sunday') },
                { value: 1, text: this.$t('monday') },
                { value: 2, text: this.$t('tuesday')},
                { value: 3, text: this.$t('wednesday') },
                { value: 4, text: this.$t('thursday') },
                { value: 5, text: this.$t('friday') }
            ],

            weekArray: [this.$t('sunday'), this.$t('monday'), this.$t('tuesday'), this.$t('wednesday'), this.$t('thursday'), this.$t('friday'), this.$t('saturday')],

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
        this.isBusy = true
        this.src = '/images/employee/'

        fetch(`api/employee/1`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['EmployeeList'];
            this.isBusy = false
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/salarysheet`)
        .then(res => res.json())
        .then(res => {
            this.DepartmentList = res['Department'];
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
            for (let i = 0; i < this.task['weekly_holiday'].length; i++) {
                day[i] = this.weekArray[this.task['weekly_holiday'][i]]                
            }
            this.holiday = day.join(', ')
            this.$refs['dataView'].show()
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
        singleTask() {
            let array = [], weekly_holiday = null
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['id'] == this.taskId) {
                    weekly_holiday = this.employeeList[i]['weekly_holiday']
                    if (typeof weekly_holiday == 'string') {  
                        weekly_holiday = this.employeeList[i]['weekly_holiday'].replace(/[\[\]\"]/g, "")
                        this.employeeList[i]['weekly_holiday'] = weekly_holiday.split(",")
                    }
                    array = this.employeeList[i]
                    break
                }                
            }
            return array
        },

        employeeListByDept() {
            let array = [], k=0
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['department'] == this.DepartmentName) {
                    array[k++] = this.employeeList[i]
                }                
            }

            this.totalRows = array.length
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
                { key: 'name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'blood_group', label : this.$t('blood_group'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'start_date', label : this.$t('joining_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },
    }
}
</script>

<style>

</style>