<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('employee_profile') }}</h3>                     
                    <div class="ml-auto">
                        <button v-if="checkRoles('employee_profile_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
                <div class="col-md-4 card-body noprint"><b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select></div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            title="List of Employee"
                            :data="employeeListByDept"
                            :fields="json_fields"
                            worksheet="List of Employee"
                            name="List of Employee.xls">
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
                        <div class="form-group col-md-4">                        
                            <label for="first_name" class="col-form-label">{{$t('name')}}</label>
                            <input type="text" class="form-control" id="first_name" name="Name" v-model="task['first_name']">
                        </div>                      
                        <div class="form-group col-md-5">                        
                            <label for="last_name" class="col-form-label">নাম (বাংলা)</label>
                            <input type="text" class="form-control" id="last_name" name="Name" v-model="task['last_name']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="father_name" class="col-form-label">{{$t('father_name')}}</label>
                            <input type="tel" class="form-control" id="father_name" name="father_name" v-model="task['father_name']">
                        </div> 
                        <div class="form-group col-md-3">
                            <label for="mobile_no" class="col-form-label">{{$t('phone')}}</label>
                            <input type="tel" class="form-control" id="mobile_no" name="mobile_no" v-model="task['mobile_no']">
                        </div> 
                        <div class="form-group col-md-3">
                            <label for="email" class="col-form-label">{{$t('email')}}</label>
                            <input type="email" class="form-control" id="email" name="email" v-model="task['email']">
                        </div> 
                        <div class="form-group col-md-3">
                            <label for="qualification" class="col-form-label">{{$t('qualification')}}</label>
                            <input type="qualification" class="form-control" id="qualification" name="qualification" v-model="task['qualification']">
                        </div>
                        
                        <span class="col-12 border-bottom">{{$t('present_address')}}</span>
                        <div class="form-group col-md-3">
                            <label for="present_area" class="col-form-label">{{$t('area')}}</label>
                            <input type="present_area" class="form-control" id="present_area" name="present_area" v-model="task['present_area']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="present_zip_code" class="col-form-label">{{$t('zip_code')}}</label>
                            <input type="present_zip_code" class="form-control" id="present_zip_code" name="present_zip_code" v-model="task['present_zip_code']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="present_sub_district" class="col-form-label">{{$t('sub_district')}}</label>
                            <input type="present_sub_district" class="form-control" id="present_sub_district" name="present_sub_district" v-model="task['present_sub_district']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="present_district" class="col-form-label">{{$t('district')}}</label>
                            <input type="present_district" class="form-control" id="present_district" name="present_district" v-model="task['present_district']">
                        </div>
                        <span class="col-12 border-bottom">{{$t('permanent_address')}}</span>
                        <div class="form-group col-md-3">
                            <label for="area" class="col-form-label">{{$t('area')}}</label>
                            <input type="area" class="form-control" id="area" name="area" v-model="task['area']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="zip_code" class="col-form-label">{{$t('zip_code')}}</label>
                            <input type="zip_code" class="form-control" id="zip_code" name="zip_code" v-model="task['zip_code']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="sub_district" class="col-form-label">{{$t('sub_district')}}</label>
                            <input type="sub_district" class="form-control" id="sub_district" name="sub_district" v-model="task['sub_district']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="district" class="col-form-label">{{$t('district')}}</label>
                            <input type="district" class="form-control" id="district" name="district" v-model="task['district']">
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
                        <div class="form-group col-md-4">
                            <label for="designation" class="col-form-label">{{$t('designation')}}</label>
                            <input type="text" class="form-control" id="designation" name="designation" v-model="task['designation']">
                        </div>   
                        <div class="form-group col-md-4">
                            <label for="department" class="col-form-label">{{$t('department')}}</label>
                            <input list="DepartmentList" type="text" class="form-control" id="department" name="department" v-model="task['department']">
                            <datalist id="DepartmentList">
                                <option v-for="department in DepartmentList" :key="department.department">{{ department['department'] }}</option>
                            </datalist>
                        </div>
                        <!-- <div class="form-group col-md-3">
                            <label for="section" class="col-form-label">{{$t('section')}}</label>
                            <input type="text" class="form-control" id="section" name="section" v-model="task['section']">
                        </div>                         -->
                        <div class="form-group col-md-4">
                            <label for="work_location" class="col-form-label">{{$t('work_location')}}</label>
                            <input type="text" class="form-control" id="work_location" name="work_location" v-model="task['work_location']">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="weekly_holiday" class="col-form-label">{{$t('weekly_holidays')}}</label>
                            <b-form-select v-model="task['weekly_holiday']" :options="weekOptions" multiple :select-size="7"></b-form-select>
                        </div>
                        <div class="row m-0 p-0 col-md-9">
                            <div class="form-group col-md-4">
                                <label for="epf_entitled_in" class="col-form-label">EPF Entitled in</label>
                                <input type="date" class="form-control" id="epf_entitled_in" name="epf_entitled_in" v-model="task['epf_entitled_in']">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="team_member_of" class="col-form-label">{{$t('team_member_of')}}</label>
                                <input type="text" class="form-control" id="team_member_of" name="team_member_of" v-model="task['team_member_of']">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="transferred" class="col-form-label">{{$t('transferred')}}</label>
                                <input type="text" class="form-control" id="transferred" name="transferred" v-model="task['transferred']">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="start_date" class="col-form-label">{{$t('joining_date')}}</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" v-model="task['start_date']">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="start_time" class="col-form-label">{{$t('In Time')}}</label>
                                <b-form-timepicker v-model="task['start_time']" locale="en"></b-form-timepicker>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="end_time" class="col-form-label">{{$t('Out Time')}}</label>
                                <b-form-timepicker v-model="task['end_time']" locale="en"></b-form-timepicker>
                            </div>
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
        <b-modal class="b-0" ref="dataView" id="dataView" size="xl" :title="$t('employee_profile')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-4 text-center m-0">
                    <img style="width: 100%; " :src="'/images/employee/' + task['employee_image']" alt="Picture not found">
                    <h2>{{task['first_name']}}</h2>
                    <h2>{{task['last_name']}}</h2>
                    <div class="text-left">
                        <h5>ID: {{task['employee_id']}}</h5>
                        <h6>{{$t('designation')}}: {{task['designation']}}</h6>
                        <h6>{{$t('department')}}: {{task['department']}}</h6>
                        <h6>{{$t('joining_date')}}: {{task['start_date']}}</h6>
                        <h6>{{$t('service_length')}}: {{task['service_length']}}</h6>
                        <h6>{{$t('service_category')}}: {{task['service_category']}}</h6>
                        <h6 v-if="task['qualification']">{{$t('qualification')}}: {{task['qualification']}}</h6>
                        <h6 v-if="task['team_member_of']">{{$t('team_member_of')}}: {{task['team_member_of']}}</h6>
                        <h6 v-if="task['transferred']">{{$t('transferred')}}: {{task['transferred']}}</h6>
                    </div>
                </div>
                <div class="col-md-8 m-0">
                    <div class="col-md-12 p-0">
                        <h4>{{$t('contact_info')}}</h4>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">Father / CO</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['father_name']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-light my-auto">
                                <p class="my-auto font-weight-bold">{{$t('phone')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['mobile_no']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white font-weight-bold">{{$t('present_address')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['present_area']}}, {{task['present_zip_code']}}, {{task['present_sub_district']}}, {{task['present_district']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 bg-light">
                                <p class="my-auto font-weight-bold">{{$t('permanent_address')}}</p>
                            </div>
                            <div class="col-md-6 bg-light">
                                <p class="my-auto">{{task['area']}}, {{task['zip_code']}}, {{task['sub_district']}}, {{task['district']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-6 my-auto bg-info">
                                <p class="my-auto text-white font-weight-bold">{{$t('email')}}</p>
                            </div>
                            <div class="col-md-6 bg-info">
                                <p class="my-auto text-white">{{task['email']}}</p>
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
                <div class="col-md-12">
                    <div class="col-md-5 float-left">
                        <button v-if="checkRoles('employee_profile_Delete')" @click="employeeExit" class="mdb btn btn-outline-danger float-left">{{ $t('employee_exit') }}</button>
                    </div>
                    <div class="col-md-7 float-left">
                        <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right" data-dismiss="modal">{{$t('Close')}}</button>
                        <button v-if="checkRoles('employee_profile_Update')" @click="editDetails" class="mdb btn btn-outline-default float-right">{{ $t('edit') }}</button>
                    </div>
                </div>
            </template>
        </b-modal>
        <!-- End view Details Modal -->
        <!-- Start exit Details Modal -->
        <b-modal class="b-0" ref="dataExit" id="dataExit" size="lg" :title="$t('employee_exit')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0">
                <div class="form-group col-md-6">
                    <label for="exit_type" class="col-form-label">{{$t('exit_type')}}</label>
                    <select class="form-control" id="exit_type" name="exit_type" v-model="exit['exit_type']">
                        <option>{{$t('resign')}}</option>
                        <option>{{$t('terminate')}}</option>
                        <option>{{$t('retirement')}}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="reason" class="col-form-label">{{$t('reason')}}</label>
                    <input type="text" class="form-control" id="reason" name="reason" v-model="exit['reason']">
                </div>
                <div class="form-group col-md-6">
                    <label for="resign_date" class="col-form-label">{{$t('resign_date')}}</label>
                    <input type="date" class="form-control" id="resign_date" name="resign_date" v-model="exit['resign_date']">
                </div>
                <div class="form-group col-md-6">
                    <label for="effective_date" class="col-form-label">{{$t('effective_date')}}</label>
                    <input type="date" class="form-control" id="effective_date" name="effective_date" v-model="exit['effective_date']">
                </div>
            </div>
            <template v-slot:modal-footer="">
                <button v-if="checkRoles('employee_profile_Delete')" @click="destroy" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }} </button>
                <button @click="$refs['dataExit'].hide()" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End exit Details Modal -->
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
            exit: {'exit_type': this.$t('resign'), 'reason': null, 'resign_date': null, 'effective_date': this.convertDate(new Date())},
            task: {'employee_id': null, 'first_name': null, 'last_name': null, 'father_name': null, 'district': null, 'sub_district': null, 'zip_code': null, 'area': null, 'present_district': null, 'present_sub_district': null, 'present_zip_code': null, 'present_area': null, 'qualification': null, 'epf_entitled_in': null, 'team_member_of': null, 'transferred': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': this.$t('male'), 'effective_join_date': null, 'date_of_birth': this.convertDate(new Date()), 'marital_status': this.$t('single'), 'designation': null, 'department': 'No Department', 'section': null, 'work_location': null, 'start_date': this.convertDate(new Date()), 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': 'noimage.jpg', 'status': 'active', 'weekly_holiday': [5], 'start_time': '8:00:00', 'end_time': '17:00:00'},
            taskId: null,
            Index: null,
            DepartmentList: [],
            DepartmentName: 'Management',
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

            json_fields: {
                'ID': 'employee_id',
                'Name': 'first_name',
                'Designation': 'designation',
                'Department' : 'department',
                'Date of Join' : 'start_date',
                'Service Length' : 'service_length',
                'Service Category' : 'service_category',
                'status' : 'status',
                'Gender' : 'gender',
                'Father Name/CO' : 'father_name',
                'Contact No' : 'mobile_no',
                'Village/Area' : 'area',
                'PO/Area Code' : 'zip_code',
                'Sub District' : 'sub_district',
                'District' : 'district',
                'Date of Birth' : 'date_of_birth',
                'Blood Group' : 'blood_group',
                'Marital Status' : 'marital_status',
                'Qualification' : 'qualification',
                'Egergency Cont. Name' : 'contact_name',
                'Relationship' : 'relationship',
                'Contact No.' : 'contact_phone',
                'Contact Address' : 'contact_address',
                'EPF Entitled In' : 'epf_entitled_in',
                'Type Of Employee' : 'work_location',
                'Team Member Of' : 'team_member_of',
                'Transferred' : 'transferred'
            },

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
            this.task = {'employee_id': null, 'first_name': null, 'last_name': null, 'father_name': null, 'district': null, 'sub_district': null, 'zip_code': null, 'area': null, 'present_district': null, 'present_sub_district': null, 'present_zip_code': null, 'present_area': null, 'qualification': null, 'epf_entitled_in': null, 'team_member_of': null, 'transferred': null, 'address': null, 'mobile_no': null, 'email': null, 'blood_group': null, 'gender': this.$t('male'), 'date_of_birth': this.convertDate(new Date()), 'marital_status': this.$t('single'), 'designation': null, 'department': 'No Department', 'section': null, 'work_location': null, 'effective_join_date': null, 'start_date': this.convertDate(new Date()), 'contact_name': null, 'contact_address': null, 'contact_phone': null, 'relationship': null, 'employee_image': 'noimage.jpg', 'status': 'active', 'weekly_holiday': [5], 'start_time': '8:00:00', 'end_time': '17:00:00'}
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
                    this.task['id'] = this.employeeList[0]['id']
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
                { key: 'start_date', label : this.$t('joining_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'service_length', label : this.$t('service_length'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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