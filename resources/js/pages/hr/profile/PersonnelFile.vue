<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('personnel_file') }}</h3> 
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
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
        <b-modal ref="dataEdit" id="dataEdit" size="xxl" :title="title" no-close-on-backdrop>            
            <div class="modal-body row m-0 p-0">
                <div class="row col-12 m-0 p-0">
                    <div v-if="stepper==1" @click="stepper_method(1)" class="col-3 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="stepper > 1" class="form mdb btn btn-success rounded-circle font-weight-bold">1</button><button v-if="stepper < 2" class="form btn-primary rounded-circle font-weight-bold">1</button><br>{{$t('personal_info')}}</div>
                    <div v-if="stepper!=1" @click="stepper_method(1)" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 1" class="form btn-success rounded-circle font-weight-bold">1</button><button v-if="stepper < 2" class="form btn-outline-secondary rounded-circle">1</button><br>{{$t('personal_info')}}</div>
                    <div v-if="stepper==2" @click="stepper_method(2)" class="col-3 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="stepper > 2" class="form btn-success rounded-circle font-weight-bold">2</button><button v-if="stepper < 3" class="form btn-primary rounded-circle font-weight-bold">2</button><br>{{$t('official_info')}}</div>
                    <div v-if="stepper!=2" @click="stepper_method(2)" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 2" class="form btn-success rounded-circle font-weight-bold">2</button><button v-if="stepper < 3" class="form btn-outline-secondary rounded-circle">2</button><br>{{$t('official_info')}}</div>
                    <div v-if="stepper==3" @click="stepper_method(3)" class="col-3 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="stepper > 3" class="form btn-success rounded-circle font-weight-bold">3</button><button v-if="stepper < 4" class="form btn-primary rounded-circle font-weight-bold">3</button><br>{{$t('emergency_contact')}}</div>
                    <div v-if="stepper!=3" @click="stepper_method(3)" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 3" class="form btn-success rounded-circle font-weight-bold">3</button><button v-if="stepper < 4" class="form btn-outline-secondary rounded-circle">3</button><br>{{$t('emergency_contact')}}</div>
                    <div v-if="stepper >= 4" @click="stepper_method(4)" class="col-3 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="stepper > 4" class="form btn-success rounded-circle font-weight-bold">4</button><button v-if="stepper < 5" class="form btn-primary rounded-circle font-weight-bold">4</button><br>{{$t('photograph')}}</div>
                    <div v-if="stepper < 4" @click="stepper_method(4)" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 4" class="form btn-success rounded-circle font-weight-bold">4</button><button v-if="stepper < 5" class="form btn-outline-secondary rounded-circle">4</button><br>{{$t('photograph')}}</div>
                </div>
                <div v-if="stepper == 1" class="col-12 mt-3">
                    <div class="form-row col-md-12">                          
                        <div class="form-group col-md-3">
                            <label for="employee_id" class="col-form-label">{{$t('employee')}} ID</label>
                            <input v-model="task['employee_id']" type="text" class="form-control" id="employee_id" name="employee_id">
                            <span v-if="errors.employee_id" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}} </span>
                        </div>                      
                        <div class="form-group col-md-3">                        
                            <label for="first_name" class="col-form-label">{{$t('name')}}</label>
                            <input type="text" class="form-control" id="first_name" name="Name" v-model="task['first_name']">
                        </div>                      
                        <div class="form-group col-md-3">                        
                            <label for="last_name" class="col-form-label">নাম (বাংলা)</label>
                            <input type="text" class="form-control" id="last_name" name="Name" v-model="task['last_name']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="email" class="col-form-label">{{$t('email')}}</label>
                            <input type="email" class="form-control" id="email" name="email" v-model="task['email']">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="mobile_no" class="col-form-label">{{$t('phone')}}</label>
                            <input type="tel" class="form-control" id="mobile_no" name="mobile_no" v-model="task['mobile_no']">
                        </div>                        
                        <div class="form-group col-md-8">
                            <label for="address" class="col-form-label">{{$t('address')}}</label>
                            <input type="text" class="form-control" id="address" name="address" v-model="task['address']">
                        </div>       
                        <div class="form-group col-md-3">
                            <label for="date_of_birth" class="col-form-label">{{$t('date_of_birth')}}</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" v-model="task['date_of_birth']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="gender" class="col-form-label">{{$t('gender')}}</label>
                            <select class="form-control" id="gender" name="gender" v-model="task['gender']">
                                <option>{{$t('male')}}</option>
                                <option>{{$t('female')}}</option>
                                <option>{{$t('others')}}</option>
                            </select>
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="marital_status" class="col-form-label">{{$t('marital_status')}}</label>
                            <select class="form-control" id="marital_status" name="marital_status" v-model="task['marital_status']">
                                <option>{{$t('single')}}</option>
                                <option>{{$t('married')}}</option>
                                <option>{{$t('widowed')}}</option>
                                <option>{{$t('divorced')}}</option>
                            </select>
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="blood_group" class="col-form-label">{{$t('blood_group')}}</label>
                            <select class="form-control" id="blood_group" name="blood_group" v-model="task['blood_group']">
                                <option>O+ve</option>
                                <option>O-ve</option>
                                <option>A+ve</option>
                                <option>A-ve</option>
                                <option>B+ve</option>
                                <option>B-ve</option>
                                <option>AB+ve</option>
                                <option>AB-ve</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                        <button @click="stepper_method(2, 'save')" class="mdb btn btn-outline-primary nextBtn float-right" type="button" ><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{buttonTitle}} & {{$t('next')}}</button> 
                        </div>
                    </div>
                </div>
                <div v-if="stepper == 2" class="col-12 mt-3">
                    <div class="form-row col-md-12">                        
                        <div class="form-group col-md-3">
                            <label for="designation" class="col-form-label">{{$t('designation')}}</label>
                            <input type="text" class="form-control" id="designation" name="designation" v-model="task['designation']">
                        </div>   
                        <div class="form-group col-md-3">
                            <label for="department" class="col-form-label">{{$t('department')}}</label>
                            <input type="text" class="form-control" id="department" name="department" v-model="task['department']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="section" class="col-form-label">{{$t('section')}}</label>
                            <input type="text" class="form-control" id="section" name="section" v-model="task['section']">
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="work_location" class="col-form-label">{{$t('work_location')}}</label>
                            <input type="text" class="form-control" id="work_location" name="work_location" v-model="task['work_location']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="weekly_holiday" class="col-form-label">{{$t('weekly_holidays')}}</label>
                            <b-form-select v-model="task['weekly_holiday']" :options="weekOptions" multiple :select-size="7"></b-form-select>
                            <!-- <input type="text" class="form-control" id="weekly_holiday" name="weekly_holiday" v-model="task['weekly_holiday']"> -->
                        </div>
                        <div class="form-group col-md-3">
                            <label for="start_date" class="col-form-label">{{$t('joining_date')}}</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" v-model="task['start_date']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="start_time" class="col-form-label">{{$t('In Time')}}</label>
                            <b-form-timepicker v-model="task['start_time']" locale="en"></b-form-timepicker>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="end_time" class="col-form-label">{{$t('Out Time')}}</label>
                            <b-form-timepicker v-model="task['end_time']" locale="en"></b-form-timepicker>
                        </div>
                        <div class="form-group col-md-12">
                            <button @click="stepper_method(2, 'save')" class="mdb btn btn-outline-primary nextBtn float-right" type="button" ><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{buttonTitle}} & {{$t('next')}}</button> 
                        </div>
                    </div>
                </div>
                <div v-if="stepper == 3" class="col-12 mt-3">
                    <div class="form-row col-md-12">                        
                        <div class="form-group col-md-12">
                            <label for="contact_name" class="col-form-label">{{$t('contact_person')}}</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" v-model="task['contact_name']">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="contact_address" class="col-form-label">{{$t('address')}}</label>
                            <textarea class="form-control" id="contact_address" name="contact_address" v-model="task['contact_address']"></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="contact_phone" class="col-form-label">{{$t('phone')}}</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" v-model="task['contact_phone']">
                        </div>                        
                        <div class="form-group col-md-8">
                            <label for="relationship" class="col-form-label">{{$t('relationship')}}</label>
                            <input type="text" class="form-control" id="relationship" name="relationship" v-model="task['relationship']">
                        </div>
                        <div class="form-group col-md-12">
                        <button @click="stepper_method(2, 'save')" class="mdb btn btn-outline-primary nextBtn float-right" type="button" ><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{buttonTitle}} & {{$t('next')}}</button> 
                        </div>
                    </div>
                </div> 
                <div v-if="stepper > 3" class="col-12 mt-3">
                    <div class="form col-md-12 mx-auto">
                        <div class="form-group col-md-12 text-center">
                            <img id="blah" :src="src + imageName" alt="product image" class="col-md-2" />
                        </div>
                        <div class="fileBrowser col-md-12">
                            <div class="form-group col-md-12 upload-btn-wrapper text-center" id="employee_image">
                                <button class="mdb btn btn-outline-success">{{$t('browse')}}</button>
                                <input type="file" @change="handleFileUpload" id="upload" name="EmployeeImage" class="pointer mx-auto"/>
                            </div>
                        </div>
                    </div>
                </div>                                                
            </div>                        
            <template v-slot:modal-footer="">
                <button @click="saveExit" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal>        
        <!-- End Edit Details Modal --> 

        <!-- Start view Details Modal -->
        <b-modal class="b-0" ref="dataView" id="dataView" size="xl" :title="$t('personnel_file')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-3 text-center m-0">
                    <img style="width: 100%; " :src="'/images/employee/' + task['employee_image']" alt="Picture not found">
                </div>
                <div class="col-md-9 m-0">
                    <div class="col-md-12 p-0">
                        <h2>{{task['name']}}</h2>
                    </div>
                    <div class="col-md-12 mt-2 p-0">
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">ID</p>
                            </div>
                            <div class="col-md-8 bg-light">
                                <p class="my-auto">{{task['employee_id']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('designation')}}</p>
                            </div>
                            <div class="col-md-8 bg-info">
                                <p class="my-auto text-white">{{task['designation']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('section')}}</p>
                            </div>
                            <div class="col-md-8 bg-light">
                                <p class="my-auto">{{task['section']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('department')}}</p>
                            </div>
                            <div class="col-md-8 bg-info">
                                <p class="my-auto text-white">{{task['department']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <template v-slot:modal-header="">
                <div class="d-flex align-items-center col-12 m-0 p-0">
                    <h3 class="panel-title float-left">{{ $t('personnel_file') }}</h3>                     
                    <div class="ml-auto">
                        <button v-if="checkRoles('personnel_file_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
            </template>
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
        return { title: this.$t('personnel_file') }
    },

    data() {
        return{
            employeeList : [],
            roles: [],
            errors : [],
            exit: {'exit_type': this.$t('resign'), 'reason': null, 'resign_date': null, 'effective_date': this.convertDate(new Date())},
            task: [{'employee_id': null, 'first_name': null, 'last_name': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': this.$t('male'), 'date_of_birth': this.convertDate(new Date()), 'marital_status': this.$t('single'), 'designation': null, 'department': null, 'section': null, 'work_location': null, 'start_date': this.convertDate(new Date()), 'salary': null, 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': 'noimage.jpg', 'status': 'active', 'weekly_holiday': [5], 'start_time': '8:00:00', 'end_time': '17:00:00'}],
            taskId: null,
            Index: null,
            title: '',
            src : '/images/employee/',
            save_image : null,
            buttonTitle : this.$t('save'),
            disable: false,
            stepper: 1,
            holiday : [],

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
            
            noprint: 'noprint',

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

        fetch(`api/employee`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['EmployeeList'];
            this.totalRows = this.employeeList.length
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

        stepper_method(step, task){
            if (task == 'save') this.save()
            else if (this.taskId) this.stepper = step
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

        addDetails() {
            this.taskId = null
            this.stepper = 1
            this.title = this.$t('insert_new_employee')
            this.task = [{'employee_id': null, 'first_name': null, 'last_name': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': this.$t('male'), 'date_of_birth': this.convertDate(new Date()), 'marital_status': this.$t('single'), 'designation': null, 'department': null, 'section': null, 'work_location': null, 'start_date': this.convertDate(new Date()), 'salary': null, 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': 'noimage.jpg', 'status': 'active', 'weekly_holiday': [5], 'start_time': '8:00:00', 'end_time': '17:00:00'}]
        },

        editDetails() {
            this.src = '/images/employee/'
            this.save_image = null
            this.title = this.$t('update_employee_profile')
            // this.task = this.singleTask
            this.stepper = 1
            this.$refs['dataEdit'].show()
        },

        employeeExit() {
            this.exit = {'exit_type': this.$t('resign'), 'reason': null, 'resign_date': null, 'effective_date': this.convertDate(new Date())}
            this.$refs['dataExit'].show()
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        handleFileUpload(e) {
            let file = e.target.files[0];
            var fileReader = new FileReader();
            
            if(file['size'] <= 262144 &&  file['type'].split('/')[0]=='image' ){          //256 KB ~~ 262144 Byte
                fileReader.onload = (e) => {
                    this.src = '';
                    this.task['employee_image'] = e.target.result;
                    this.save_image = e.target.result;
                }
            } else {
                let warningMessages;
                file['size'] > 262144 ? warningMessages = this.$t('image_size_warning'): warningMessages = this.$t('image_format_warning');
                this.$toast.warning(warningMessages, this.$t('error_alert_title'), {
                    timeout: 10000,          
                    position: 'center',
                })
            }

            fileReader.readAsDataURL(e.target.files[0]);
        },

        saveExit() {
            this.save()
            this.$nextTick(() => {
                this.$refs['dataEdit'].hide()
            })
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.task['employee_image'] = this.save_image
            let options = { headers: {'enctype': 'multipart/form-data'} };

            if(this.taskId == null){
                axios.post(`api/employee`, this.task, options)
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
                axios.patch(`api/employee/${this.taskId}`, this.task, options)
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
            let day = []
            for (let i = 0; i < this.task['weekly_holiday'].length; i++) {
                day[i] = this.weekArray[this.task['weekly_holiday'][i]]                
            }
            this.holiday = day.join(', ')
            if(this.task['first_name'] && this.task['last_name']) this.task['name'] = this.task['last_name'] + ', ' + this.task['first_name']
            else if (this.task['first_name']) this.task['name'] = this.task['first_name']
            else if (this.task['last_name']) this.task['name'] = this.task['last_name']
        },

        destroy() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.exit['status'] = this.exit['exit_type']
            axios.patch(`api/employee/${this.taskId}`, this.exit)
            .then(({data}) => { 
                for (let i = 0; i < this.employeeList.length; i++) {
                    if(this.employeeList[i]['id'] == this.taskId){
                        this.employeeList.splice(i, 1);                           
                        break
                    }   
                }
                this.totalRows = this.employeeList.length;
                this.$refs['dataView'].hide()
                this.$refs['dataExit'].hide()
                this.errors = ''
                this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                this.disable = !this.disable
                this.buttonTitle = this.$t('save')
            })
            .catch(err => {
                if(err.response.status == 422){
                    this.errors = err.response.data.errors
                } else alert(err.response.data.message) 
                this.disable = !this.disable
                this.buttonTitle = this.$t('save')
            });
        },

    },

    computed: {
        imageName() {
            if(this.task['employee_image'] == null || this.task['employee_image'] == 'noimage.jpg') {
                this.task['employee_image'] = null
                return 'noimage.jpg'
            }
            else return this.task['employee_image']
        },
        
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

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    }
}
</script>

<style lang="scss" scoped>
.border-5{
    border-width: 3px !important;
}
</style>