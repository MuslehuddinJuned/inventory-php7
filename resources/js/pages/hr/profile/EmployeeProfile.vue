<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('employee_profile') }}</h3>                     
                    <div class="ml-auto">
                        <button @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
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
                    class="table-transition"
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
                    <div v-if="stepper==1" @click="stepper = 1" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 1" class="form mdb btn btn-success rounded-circle font-weight-bold">1</button><button v-if="stepper < 2" class="form btn-primary rounded-circle font-weight-bold">1</button><br>{{$t('personal_info')}}</div>
                    <div v-if="stepper!=1" @click="stepper = 1" class="col-3 border-bottom border-secondary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 1" class="form btn-success rounded-circle font-weight-bold">1</button><button v-if="stepper < 2" class="form btn-outline-secondary rounded-circle">1</button><br>{{$t('personal_info')}}</div>
                    <div v-if="stepper==2" @click="stepper = 2" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 2" class="form btn-success rounded-circle font-weight-bold">2</button><button v-if="stepper < 3" class="form btn-primary rounded-circle font-weight-bold">2</button><br>{{$t('official_info')}}</div>
                    <div v-if="stepper!=2" @click="stepper = 2" class="col-3 border-bottom border-secondary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 2" class="form btn-success rounded-circle font-weight-bold">2</button><button v-if="stepper < 3" class="form btn-outline-secondary rounded-circle">2</button><br>{{$t('official_info')}}</div>
                    <div v-if="stepper==3" @click="stepper = 3" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 3" class="form btn-success rounded-circle font-weight-bold">3</button><button v-if="stepper < 4" class="form btn-primary rounded-circle font-weight-bold">3</button><br>{{$t('emergency_contact')}}</div>
                    <div v-if="stepper!=3" @click="stepper = 3" class="col-3 border-bottom border-secondary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 3" class="form btn-success rounded-circle font-weight-bold">3</button><button v-if="stepper < 4" class="form btn-outline-secondary rounded-circle">3</button><br>{{$t('emergency_contact')}}</div>
                    <div v-if="stepper==4" @click="stepper = 4" class="col-3 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 4" class="form btn-success rounded-circle font-weight-bold">4</button><button v-if="stepper < 5" class="form btn-primary rounded-circle font-weight-bold">4</button><br>{{$t('photograph')}}</div>
                    <div v-if="stepper!=4" @click="stepper = 4" class="col-3 border-bottom border-secondary p-3 border-5 text-center" style="cursor: pointer;"><button v-if="stepper > 4" class="form btn-success rounded-circle font-weight-bold">4</button><button v-if="stepper < 5" class="form btn-outline-secondary rounded-circle">4</button><br>{{$t('photograph')}}</div>
                </div>
                <div v-if="stepper == 1" class="setup-content mt-5" id="step-1">
                    <div class="form-row col-md-12">                          
                        <div class="form-group col-md-3">
                            <label for="employee_id" class="col-form-label">{{$t('save')}}{{$t('employee')}} ID</label>
                            <input v-model="task[0]['employee_id']" type="text" class="form-control is-valid" id="employee_id" name="employee_id">
                            <span v-if="errors.employee_id" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}} </span>
                        </div>                      
                        <div class="form-group col-md-6">                        
                            <label for="first_name" class="col-form-label">Full Name</label>
                            <input type="text" class="form-control" id="first_name" name="Name" v-model="task[0]['first_name']">
                        </div>                        
                        <div class="form-group col-md-3">                   
                            <label for="last_name" class="col-form-label">Nick Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" v-model="task[0]['last_name']">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="address" class="col-form-label">Address:</label>
                            <textarea class="form-control" id="address" name="address" v-model="task[0]['address']"></textarea>
                        </div>       
                        <div class="form-group col-md-4">
                            <label for="mobile_no" class="col-form-label">Phone</label>
                            <input type="tel" class="form-control" id="mobile_no" name="mobile_no" v-model="task[0]['mobile_no']">
                        </div>                        
                        <div class="form-group col-md-8">
                            <label for="email" class="col-form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" v-model="task[0]['email']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="date_of_birth" class="col-form-label">Birth Day</label>
                            <input @change="lazySaving('date_of_birth', date_of_birth)" type="date" class="form-control" id="date_of_birth" name="date_of_birth" v-model="task[0]['date_of_birth']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="gender" class="col-form-label">Gender</label>
                            <select @change="lazySaving('gender', gender)"  class="form-control" id="gender" name="gender" v-model="task[0]['gender']">
                                <option>Male</option>
                                <option>Female</option>
                                <option>Others</option>
                            </select>
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="marital_status" class="col-form-label">Marital Status</label>
                            <select @change="lazySaving('marital_status', marital_status)" class="form-control" id="marital_status" name="marital_status" v-model="task[0]['marital_status']">
                                <option>Single</option>
                                <option>Married</option>
                                <option>Widowed</option>
                                <option>Divorced</option>
                            </select>
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="blood_group" class="col-form-label">Blood Group</label>
                            <select @change="lazySaving('blood_group', blood_group)" class="form-control" id="blood_group" name="blood_group" v-model="task[0]['blood_group']">
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
                        <button class="mdb btn btn-outline-primary nextBtn float-right col-md-2" type="button" ><i class="fas fa-spinner fa-spin" :class="loading"></i> {{buttonTitle}}</button> 
                        </div>
                    </div>
                </div>
                <div v-if="stepper == 2" class="setup-content mt-5" id="step-2">
                    <div class="form-row col-md-12">                        
                        <div class="form-group col-md-4">
                            <label for="designation" class="col-form-label">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" v-model="task[0]['designation']">
                        </div>   
                        <div class="form-group col-md-4">
                            <label for="department" class="col-form-label">Department</label>
                            <input type="text" class="form-control" id="department" name="department" v-model="task[0]['department']">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="section" class="col-form-label">Section</label>
                            <input type="text" class="form-control" id="section" name="section" v-model="task[0]['section']">
                        </div>                        
                        <div class="form-group col-md-4">
                            <label for="work_location" class="col-form-label">Work Location</label>
                            <input type="text" class="form-control" id="work_location" name="work_location" v-model="task[0]['work_location']">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="start_date" class="col-form-label">Joining Date</label>
                            <input type="date" @change="lazySaving('start_date', start_date)" class="form-control" id="start_date" name="start_date" v-model="task[0]['start_date']">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="salary" class="col-form-label">Salary</label>
                            <input type="text" class="form-control" id="salary" name="salary" v-model="task[0]['salary']">
                        </div>
                        <div class="form-group col-md-12">
                        <button class="mdb btn btn-outline-primary nextBtn float-right col-md-2" type="button" ><i class="fas fa-spinner fa-spin" :class="loading"></i> {{buttonTitle}}</button> 
                        </div>
                    </div>
                </div>
                <div v-if="stepper == 3" class="setup-content mt-5" id="step-3">
                    <div class="form-row col-md-12">                        
                        <div class="form-group col-md-12">
                            <label for="contact_name" class="col-form-label">Contact Name</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" v-model="task[0]['contact_name']">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="contact_address" class="col-form-label">Address</label>
                            <textarea class="form-control" id="contact_address" name="contact_address" v-model="task[0]['contact_address']"></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="contact_phone" class="col-form-label">Phone No.</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" v-model="task[0]['contact_phone']">
                        </div>                        
                        <div class="form-group col-md-8">
                            <label for="relationship" class="col-form-label">Relationship</label>
                            <input type="text" class="form-control" id="relationship" name="relationship" v-model="task[0]['relationship']">
                        </div>
                        <div class="form-group col-md-12">
                        <button class="mdb btn btn-outline-primary nextBtn float-right col-md-2" type="button" ><i class="fas fa-spinner fa-spin" :class="loading"></i> {{buttonTitle}}</button> 
                        </div>
                    </div>
                </div> 
                <div v-if="stepper == 4" class="setup-content mt-5" id="step-4">
                    <div class="form col-md-12 m-auto text-center float-center mt-5">
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <img id="blah" style="width: 70%;" :src="src + employee_image" alt="your image" />
                            </div>
                            <div class="col-md-6">
                                <div class="fileBrowser d-flex align-items-baseline col-md-12">
                                    <div class="form-group col-md-12 upload-btn-wrapper p-0" id="employee_image">
                                        <button class="mdb btn btn-outline-success col-md-8">Upload a Photograph</button>
                                        <input type="file" @change="handleFileUpload" id="upload" name="employee_image" class="pointer" :disabled="disable" />
                                    </div>
                                </div>
                                <div class="form-group mt-0 col-md-12">
                                    <button class="mdb btn btn-outline-default col-md-8"><i class="fas fa-spinner fa-spin" :class="loading"></i> {{buttonTitle.replace("Next", "Exit")}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                                                
            </div>                        
            <template v-slot:modal-footer="">
                <button @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal>        
        <!-- End Edit Details Modal --> 
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('employee_profile') }
    },

    data() {
        return{
            employeeList : [],
            roles: [],
            errors : [],
            task: [{'employee_id': null, 'first_name': null, 'last_name': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': 'Male', 'date_of_birth': this.convertDate(new Date()), 'marital_status': 'Single', 'designation': null, 'department': null, 'section': null, 'work_location': null, 'start_date': this.convertDate(new Date()), 'salary': null, 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': null, 'status': 'active'}],
            taskId: null,
            Index: null,
            title: '',
            src : '/images/employee/',
            save_image : null,
            buttonTitle : this.$t('save'),
            disable: false,
            stepper: 1,
            
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

        fetch(`api/employee`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['EmployeeList'];
            this.totalRows_Role = this.employeeList.length
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

        addDetails(){
            this.taskId = null
            this.title = this.$t('insert_new_employee')
            this.task = [{'employee_id': null, 'first_name': null, 'last_name': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': 'Male', 'date_of_birth': this.convertDate(new Date()), 'marital_status': 'Single', 'designation': null, 'department': null, 'section': null, 'work_location': null, 'start_date': this.convertDate(new Date()), 'salary': null, 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': null, 'status': 'active'}]
        },

        editDetails(id, index) {
            this.src = '/images/employee/'
            this.save_image = null
            this.title = this.$t('update_employee_profile')
            this.taskId = id
            this.Index = index
            this.task = this.singleTask
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
                    this.task[0]['item_image'] = e.target.result;
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

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.task[0]['item_image'] = this.save_image
            this.task[0]['store_id'] = this.store
            let options = { headers: {'enctype': 'multipart/form-data'} };

            if(this.taskId == null){
                axios.post(`api/inventory`, this.task[0], options)
                .then(({data}) =>{
                    this.errors = ''
                    this.employeeList.unshift(data.employeeList)
                    this.totalRows = this.employeeList.length;
                    for (let i = 0; i < this.totalRows; i++) {
                        this.employeeList[i]['sn'] = i                
                    } 
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    }
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    alert(err.response.data.message)                      
                })
            } else {
                axios.patch(`api/inventory/${this.taskId}`, this.task[0], options)
                .then(({data}) => {
                    this.errors = ''
                    this.src = '/images/item/'
                    this.task[0]['item_image'] = data.fileName
                    this.employeeList[this.Index] = this.task[0];
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                    }
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }
        },

        destroy(id, index) {
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/inventory/${id}`)
                        
                        .then(res => {
                            this.employeeList.splice(index, 1);
                            this.totalRows = this.employeeList.length

                            for (let i = 0; i < this.totalRows; i++) {
                                this.employeeList[i]['sn'] = i                
                            }
                            for (let i = 0; i < this.employeeListAll.length; i++) {
                                if(this.employeeListAll[i]['id'] == id){
                                    this.employeeListAll.splice(i, 1);
                                    break
                                }               
                            }
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
        imageName() {
            if(this.task[0]['item_image'] == null || this.task[0]['item_image'] == 'noimage.jpg') {
                this.task[0]['item_image'] = null
                return 'noimage.jpg'
            }
            else return this.task[0]['item_image']
        },
        
        singleTask() {
            let id = this.taskId
            return this.employeeList.filter(function (item) {
            return item['id'] == id
            })
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
                // { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'item_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'item', label : this.$t('style') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'grade', label : this.$t('grade'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'accounts_code', label : this.$t('accounts_code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'specification', label : this.$t('size'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // // { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'stock_master_sheet', label : this.$t('stock_master_sheet'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'stock', label : this.$t('stock_sheet'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'stock_cann', label : this.$t('stock_cann'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'weight', label : this.$t('weight') + '(kg)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'total_weight', label : this.$t('total_weight'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'action', label: this.$t(`${this.colTitle}`),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
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
    border-width: 5px !important;
}
</style>