<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12 noprint">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('increment_file') }}</h3> 
                </div>
                <div class="row m-0 p-0 card-body">
                    <!-- <div class="col-md-4  noprint"><b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select></div> -->
                    <div class="col-md-3 noprint">
                        <input type="date" class="form-control" v-model="increment_date">
                    </div>
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            title="Increment File"
                            :data="employeeListByDept"
                            :fields="json_fields"
                            worksheet="Increment File"
                            name="Increment File.xls">
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

        <!-- Start Edit Details Modal -->
        <b-modal ref="dataEdit" id="dataEdit" size="xxl" :title="$t('increment_file')" no-close-on-backdrop>            
            <div class="modal-body row m-0 p-0">
                <div class="form-row col-md-12 noprint">                          
                    <div class="form-group col-md-4">
                        <label for="effective_date" class="col-form-label">{{$t('effective_date')}}</label>
                        <input v-model="task['effective_date']" type="effective_date" class="form-control" id="effective_date" name="effective_date">
                    </div>                    
                    <div class="form-group col-md-4">                        
                        <label for="amount" class="col-form-label">{{$t('amount')}} (%)</label>
                        <input type="text" class="form-control" id="amount" name="Name" v-model="task['amount']" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="file_link" class="col-form-label">{{$t('file_link')}}</label>
                        <input type="file_link" class="form-control" id="file_link" name="file_link" v-model="task['file_link']">
                    </div>
                </div>
                <!-- <div class="col-12 text-center"> <h3>Letter of Increment</h3> </div> -->
                <div class="col-6 text-left"> <h5>Department of Human Resource</h5> </div>
                <div class="col-6 text-right"> <h5>Date: {{ convertDate(task['updated_at']) | dateParse('YYYY-MM-DD') | dateFormat('DD-MMM-YYYY')}}</h5> </div>
                <div class="col-12">Name: {{singlEmployee['first_name']}}</div>
                <div class="col-12">ID No: {{singlEmployee['employee_id']}}</div>
                <div class="col-12">Designation: {{singlEmployee['designation']}}</div>
                <div class="col-12">Department: {{singlEmployee['department']}}</div>
                <div class="col-12">Date of Join: {{singlEmployee['start_date']}}</div>
                <div class="col-12 mt-3">
                    <span v-if="task['previous_basic']" class="d-none">{{task['previous_basic']}}</span>
                    <span v-else class="d-none">{{task['previous_basic'] = (singlEmployee['basic_pay'] || 0)}}</span>
                    <b-table-simple hover small responsive class="table-bordered text-center">
                        <b-thead>
                            <b-tr>
                                <b-th>Present Salary</b-th>
                                <b-th>Basic Salary</b-th>
                                <b-th>Increment Amount</b-th>
                                <b-th>New Basic</b-th>
                                <b-th>Salary After Increment</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr>
                                <b-th>$ {{((task['previous_basic']*1.5 + 574)/82).toFixed(2)}}</b-th>
                                <b-th>$ {{(task['previous_basic']/82).toFixed(2)}}</b-th>
                                <b-th>$ {{(task['previous_basic']*task['amount']/8200).toFixed(2)}}</b-th>
                                <b-th>$ {{((task['previous_basic']*(1+task['amount']/100))/82).toFixed(2)}}</b-th>
                                <b-th>$ {{((task['previous_basic']*(1+task['amount']/100)*1.5 + 574)/82).toFixed(2)}}</b-th>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </div>
                <div class="col-12">
                    <p>This is to inform you that according to BEPZA Instruction-2, Para-4(C) and company policy you are eligible to get 10% yealy increment. This increment will be based on your monthly basic salary/wages. Which will be efected
                         from {{ convertDate(task['effective_date']) | dateParse('YYYY-MM-DD') | dateFormat('DD-MMM-YYYY')}}. Your new salary structure will be as below:</p>
                </div>
                
                <div class="col-12">
                    <p>আপনাকে এই মর্মে অবগত করা যাচ্ছে যে, বেপজা ইনস্ট্রাকশন-২, প্যারা-৪ (সি) এবং কোম্পানী নীতিমালা অনুসারে আপনি বার্ষিক ১০% বেতন বৃদ্ধি এর আওতাভুক্ত হয়েছেন। এই বেতন বৃদ্ধি আপনার মাসিক মূল বেতন/মজুরী অনুসারী হবে। যা 
                        {{ convertDate(task['effective_date']) | dateParse('YYYY-MM-DD') | dateFormat('DD-MMM-YYYY')}} ইং তারিখ থেকে কার্যকর হবে। আপনার নতুন বেতন নিম্নোক্ত পদ্ধতিতে প্রদেয় হবে:</p>
                </div>
                <div class="col-12">
                    <b-table-simple hover small responsive class="table-bordered text-center">
                        <b-thead>
                            <b-tr>
                                <b-th>বর্তমান বেতন</b-th>
                                <b-th>মূল বেতন</b-th>
                                <b-th>বর্ধিত বেতন</b-th>
                                <b-th>নতুন মূল বেতন</b-th>
                                <b-th>বর্ধিত বেতন সহ মোট</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr>
                                <b-th>৳ {{(task['previous_basic']*1.5 + 574).toFixed(2)}}</b-th>
                                <b-th>৳ {{(task['previous_basic']).toFixed(2)}}</b-th>
                                <b-th>৳ {{(task['previous_basic']*task['amount']/100).toFixed(2)}}</b-th>
                                <b-th>৳ {{(task['post_basic'] = task['previous_basic']*(1+task['amount']/100)).toFixed(2)}}</b-th>
                                <b-th>৳ {{(task['post_gross'] = task['previous_basic']*(1+task['amount']/100)*1.5 + 574).toFixed(2)}}</b-th>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </div>
                
                <div class="col-12">
                    <p>Authority hope you will do your best in future and will assist in beterment of the company</p>
                    <p class="mb-5">কর্তৃপক্ষ আশা করে আপনি ভবিষ্যতে আরো ভালোভাবে কাজ করবেন এবং কোম্পানীর সর্বাঙ্গীন কল্যাণে সহায়তা করবেন</p>
                    <p class="mt-5">Thank You</p>
                    <p>ধন্যবাদান্তে,</p>
                </div>
            </div>                        
            <template v-slot:modal-header>
                <div class="text-center"><h2>Letter of Increment</h2></div>
            </template>
            <template v-slot:modal-footer>
                <div class="row m-0 p-0 col-md-12">
                    <div class="onlyprint fixed-bottom">
                        <div class="mt-3 float-left ml-3 col-2 border-top border-dark text-center">Head of Dept.</div>
                        <div class="mt-3 float-left col-1"></div>
                        <div class="mt-3 float-left col-2 border-top border-dark text-center">HR Dept.</div>
                        <div class="mt-3 float-left col-1"></div>
                        <div class="mt-3 float-left col-2 border-top border-dark text-center">Supervisor/Production Officer</div>
                        <div class="mt-3 float-left col-1"></div>
                        <div class="mt-3 float-left col-2 border-top border-dark text-center">Admin. Manager</div>
                    </div>
                    <div class="col-md-5 float-left">
                        <button v-if="checkRoles('increment_file_Delete') && taskId" @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                    </div>
                    <div class="col-md-7 float-right">
                        <button @click="dataEdit_hide()" type="button" class="mdb btn btn-outline-mdb-color float-right" data-dismiss="modal">{{$t('Close')}}</button>
                        <button v-if="checkRoles('increment_file_Insert')" @click="save" class="mdb btn btn-outline-default float-right" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                    </div>
                </div>
            </template>
        </b-modal>        
        <!-- End Edit Details Modal --> 

        <!-- Start view Details Modal -->
        <b-modal class="b-0" :class="noprint" ref="dataView" id="dataView" size="xl" :title="$t('increment_file')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0" :class="noprint">
                <div class="col-3 text-center m-0">
                    <img style="width: 100%; " :src="'/images/employee/' + singlEmployee['employee_image']" alt="Picture not found">
                </div>
                <div class="col-9 m-0">
                    <div class="col-12 p-0">
                        <h2>{{singlEmployee['first_name']}}</h2>
                    </div>
                    <div class="col-12 mt-2 p-0">
                        <div class="row m-0 p-0 col-12">
                            <div class="col-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">ID</p>
                            </div>
                            <div class="col-8 bg-light">
                                <p class="my-auto">{{singlEmployee['employee_id']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-12">
                            <div class="col-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('designation')}}</p>
                            </div>
                            <div class="col-8 bg-info">
                                <p class="my-auto text-white">{{singlEmployee['designation']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-12">
                            <div class="col-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('section')}}</p>
                            </div>
                            <div class="col-8 bg-light">
                                <p class="my-auto">{{singlEmployee['section']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-12">
                            <div class="col-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('department')}}</p>
                            </div>
                            <div class="col-8 bg-info">
                                <p class="my-auto text-white">{{singlEmployee['department']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-12">
                            <div class="col-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('salary')}}</p>
                            </div>
                            <div class="col-8 bg-light">
                                <p class="my-auto">{{singlEmployee['salary']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-12">
                            <div class="col-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('joining_date')}}</p>
                            </div>
                            <div class="col-8 bg-info">
                                <p class="my-auto text-white">{{singlEmployee['start_date']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" primary-key="id" :items="incrementList" :fields="personnelFields"
                    @row-clicked="(item) => editDetails(item.id)"
                    class="table-transition"
                    style="cursor : pointer">                                   
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(file_link)="row">
                            <a v-if="row.item.file_link" :href="row.item.file_link">File</a>
                        </template>
                    </b-table>
                </div> 
            </div>
            <template v-slot:modal-header>
                <div class="d-flex align-items-center col-12 m-0 p-0" :class="noprint">
                    <h3 class="panel-title float-left">{{ $t('increment_file') }}</h3>                     
                    <div class="ml-auto">
                        <button v-if="checkRoles('increment_file_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
            </template>
            <template v-slot:modal-footer>
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
        return { title: this.$t('increment_file') }
    },

    data() {
        return{
            employeeList : [],
            singlEmployee : {},
            incrementList: [],
            roles: [],
            errors : [],
            task: {'effective_date': this.convertDate(new Date()), 'amount': null, 'remarks': null, 'file_link': null, 'employee_id': null},
            taskId: null,
            employeeId: null,
            buttonTitle : this.$t('save'),
            disable: false,
            noprint: '',
            DepartmentList: [],
            DepartmentName: 'Management',
            increment_date: this.convertDate(new Date()),

            json_fields: {
                'ID': 'employee_id',
                'Name': 'first_name',
                'Designation': 'designation',
                'Department' : 'department',
                'Salary' : 'salary',
                'Increment (%)' : 'amount',
                'Effective Date' : 'effective_date',
                'Next Increment' : 'next_increment_month',
                'Joining Date' : 'start_date'
            },

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

        fetch(`api/wagehike`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['Increment'];
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

        viewDetails(id) {
            this.employeeId = id
            fetch(`api/wagehike/${id}`)
            .then(res => res.json())
            .then(res => {
                this.incrementList = res['Wagehike'];
            })
            .catch(err => {
                alert(err.response.data.message);
            })
            this.singlEmployee = this.singlEmployeeMethod
            this.$refs['dataView'].show()
        },

        addDetails() {
            this.noprint = 'noprint'
            this.taskId = null
            this.task = {'effective_date': this.convertDate(new Date()), 'amount': null, 'remarks': null, 'file_link': null, 'employee_id': this.employeeId}
        },

        editDetails(id) {
            if (this.checkRoles('increment_file_Update')) {
                this.noprint = 'noprint'                
                this.taskId = id
                for (let i = 0; i < this.incrementList.length; i++) {
                    if (this.incrementList[i]['id'] == id) {
                        this.task = this.incrementList[i]
                        break
                    }                
                }
                this.$refs['dataEdit'].show()
            }
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        getYearMonth(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2)
            return [year, mnth].join("-");
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')

            if(this.taskId == null){
                axios.post(`api/wagehike`, this.task)
                .then(({data}) =>{
                    this.errors = ''
                    this.incrementList.unshift(data.Wagehike)
                    this.taskId = this.incrementList[0]['id']
                    this.totalRows = this.incrementList.length
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
                axios.patch(`api/wagehike/${this.taskId}`, this.task)
                .then(({data}) => {
                    // this.task = data.Wagehike
                    // console.log('hi', this.incrementList)                       
                    this.errors = ''
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
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
                });
            }
        },

        destroy() {
            this.$toast.warning(this.$t('sure_to_delete'), this.attendance_date, {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/wagehike/${this.taskId}`)                        
                        .then(res => {
                            for (let i = this.incrementList.length -1; i >= 0; i--) {
                                if(this.incrementList[i]['id'] == this.taskId){
                                    this.incrementList.splice(i, 1);                           
                                    break
                                }   
                            }
                            this.$refs['dataEdit'].hide()
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

        dataEdit_hide() {
            this.noprint = ''
            this.$refs['dataEdit'].hide()
        }

    },

    computed: {
        
        singlEmployeeMethod() {
            let array = []
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['id'] == this.employeeId) {
                    array = this.employeeList[i]
                    break
                }                
            }
            return array
        },

        employeeListByDept() {
            let array = [], k=0
            for (let i = 0; i < this.employeeList.length; i++) {
                // if (this.employeeList[i]['department'] == this.DepartmentName && this.getYearMonth(this.employeeList[i]['next_increment']) == this.getYearMonth(this.increment_date)) {
                if (this.getYearMonth(this.employeeList[i]['next_increment']) == this.getYearMonth(this.increment_date)) {
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
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'salary', label : this.$t('salary'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'amount', label : this.$t('increment') + ' (%)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'effective_date', label : this.$t('effective_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'next_increment_month', label : this.$t('next_increment'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'start_date', label : this.$t('joining_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        personnelFields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'effective_date', label : this.$t('date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'previous_basic', label : this.$t('previous_basic'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'previous_gross', label : this.$t('previous_gross'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'amount', label : this.$t('amount') + '(%)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'post_basic', label : this.$t('basic'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'post_gross', label : this.$t('gross'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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