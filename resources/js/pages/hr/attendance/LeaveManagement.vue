<template>
    <div class="container-fluid">
        <div class="card filterable" :class="noprint">
            <div class="card-header row m-0">
                <div class="col-md-6">
                    <h3 class="panel-title float-left">
                        {{ $t('leave_management') }}
                        <fa v-if="checkRoles('leave_management_Insert')" @click="addDetails" icon="edit" class="ml-2 pointer noprint" fixed-width />                    
                    </h3>
                </div>                     
                <div class="col-md-6">
                    <div class="ml-md-auto m-sm-0 input-group col-md-12 col-lg-6 float-md-right">
                        <div class="input-group-prepend noprint">
                            <div @click="yearWiseDisplay(-1)" class="input-group-text pointer"><b-icon icon="dash"></b-icon></div>
                        </div>
                        <input type="text" v-model="year" @change="yearWiseDisplay(year)" class="form-control text-center" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        <div class="input-group-append noprint">
                            <div @click="yearWiseDisplay(1)" class="input-group-text pointer"><b-icon icon="plus"></b-icon></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row m-0 p-0">
                    <div class="col-md-4 my-1">
                        {{$t('casual_leave')}}:
                        <input type="text" v-model="task[0]['casual_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('sick_leave')}}:
                        <input type="text" v-model="task[0]['sick_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('annual_leave')}}:
                        <input type="text" v-model="task[0]['annual_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('maternity_leave')}}:
                        <input type="text" v-model="task[0]['maternity_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('paternity_leave')}}:
                        <input type="text" v-model="task[0]['paternity_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('half_leave')}}:
                        <input type="text" v-model="task[0]['half_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                </div>
                <div class="col-12 mt-5 text-center">
                    <button v-if="checkRoles('leave_management_Insert') && reportEdit" @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                    <button v-if="reportEdit" @click="addDetails" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
                </div>
            </div>
            <div class="card-header">
                <h3 class="panel-title float-left">
                    {{ $t('personal_leave_management') }}
                </h3>
            </div>
            <div class="card-body m-0 p-0">
                <div class="card-header d-flex align-items-center noprint">
                    <!-- <download-excel
                        id="tooltip-target-1"
                        class="btn btn-outline-default btn-sm mr-3"
                        :title="storeName"
                        :data="inventoryList"
                        :fields="json_fields"
                        worksheet="Inventory Item"
                        name="Inventory Item.xls">
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
                :items="Usedleave"
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
                <template v-slot:cell(casual_leave_remain)="row">
                    {{Leave[0]['casual_leave'] - row.item.casual_leave}}
                </template>
                <template v-slot:cell(sick_leave_remain)="row">
                    {{Leave[0]['sick_leave'] - row.item.sick_leave}}
                </template>
                <template v-slot:cell(earned_leave_remain)="row">
                    {{row.item.earned_day - row.item.earned_leave}}
                </template>
                <template v-slot:cell(maternity_leave)="row">
                    {{row.item.maternity_leave}}  ({{Leave[0]['maternity_leave'] - row.item.maternity_leave}})
                </template>
                <template v-slot:cell(paternity_leave)="row">
                    {{row.item.paternity_leave}}  ({{Leave[0]['paternity_leave'] - row.item.paternity_leave}})
                </template>
                <template v-slot:cell(half_leave)="row">
                    {{row.item.half_leave}}  ({{Leave[0]['half_leave'] - row.item.half_leave}})
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

        <!-- Start Edit Details Modal -->
        <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="$t('personal_leave_management')" no-close-on-backdrop>            
            <div v-if="taskHeadId" class="modal-body row m-0 p-0 mb-2">
                <div class="col-md-4">
                    <label for="leave_category" class="col-form-label mr-2">{{ $t('leave_category')}}</label>
                    <select v-model="taskDetails[0]['leave_type']" class="form-control" id="leave_category">
                        <option value="casual_leave">{{$t('casual_leave')}}</option>
                        <option value="sick_leave">{{$t('sick_leave')}}</option>
                        <option value="earned_leave">{{$t('earned_leave')}}</option>
                        <option value="maternity_leave">{{$t('maternity_leave')}}</option>
                        <option value="paternity_leave">{{$t('paternity_leave')}}</option>
                        <option value="compensatory_leave">{{$t('compensatory_leave')}}</option>
                        <option value="unpaid_leave">{{$t('unpaid_leave')}}</option>
                        <option value="half_leave">{{$t('half_leave')}}</option>
                    </select>
                    <span v-if="errors.leave_type" class="error text-danger"> {{$t('required_field')}} </span>
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">{{ $t('reason')}}</label>
                    <input type="text" class="form-control" v-model="taskDetails[0]['reason']">
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">{{ $t('replacing_person')}}</label>
                    <input type="text" class="form-control" v-model="taskDetails[0]['replacing_person']">
                </div> 
                <div class="col-md-4">
                    <label class="col-form-label">{{ $t('leave_start')}}</label>
                    <input type="date" class="form-control" v-model="taskDetails[0]['leave_start']">
                </div> 
                <div class="col-md-4">
                    <label class="col-form-label">{{ $t('leave_end')}}</label>
                    <input type="date" class="form-control" v-model="taskDetails[0]['leave_end']">
                </div>                              
            </div>
            <template v-slot:modal-footer="">
                <div class="row m-0 p-0 col-12">
                    <div class="col-md-4">
                        <button v-if="checkRoles('leave_management_Delete') && taskDetailsId" @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                    </div>
                    <div class="col-md-8">
                        <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                        <button v-if="checkRoles('leave_management_Insert')" @click="savePersonalLeave" class="mdb btn btn-outline-default float-right" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                    </div>
                </div>
            </template>
        </b-modal>
        <!-- End Edit Details Modal -->

        <!-- Start view Details Modal -->
        <b-modal ref="dataView" id="dataView" size="xl" :title="$t('personal_leave_management')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0 mb-2" >
                <div class="col-md-4">
                    <span class="font-weight-bold">ID:</span> {{taskHead['employee_id']}} <br>
                    <span class="font-weight-bold">{{ $t('name')}}:</span> {{taskHead['first_name']}}
                </div>
                <div class="col-md-4">                                
                    <span class="font-weight-bold">{{ $t('designation')}}:</span> {{taskHead['designation']}}<br>
                    <span class="font-weight-bold">{{ $t('department')}}:</span> {{taskHead['department']}}
                </div>
                <div class="col-md-4">                                
                    <span class="font-weight-bold">{{ $t('joining_date')}}:</span> {{taskHead['start_date']}}<br>
                    <span class="font-weight-bold">{{ $t('service_length')}}:</span> {{service_length(taskHead['start_date'])}}
                </div>
            </div>
            <div class="modal-body row m-0 p-0 mb-4 border-top border-secondary" >
                <div v-if="taskHeadId" class="row col-12 m-0 p-0">
                    <div class="col-md-3">{{$t('casual_leave')}}: {{taskHead['casual_leave']}} ({{Leave[0]['casual_leave'] - taskHead['casual_leave']}})</div>
                    <div class="col-md-3">{{$t('sick_leave')}}: {{taskHead['sick_leave']}} ({{Leave[0]['sick_leave'] - taskHead['sick_leave']}})</div>
                    <div class="col-md-3">{{$t('earned_leave')}}: {{taskHead['earned_leave']}}</div>
                    <div class="col-md-3">{{$t('maternity_leave')}}: {{taskHead['maternity_leave']}} ({{Leave[0]['maternity_leave'] - taskHead['maternity_leave']}})</div>
                    <div class="col-md-3">{{$t('paternity_leave')}}: {{taskHead['paternity_leave']}} ({{Leave[0]['paternity_leave'] - taskHead['paternity_leave']}})</div>
                    <div class="col-md-3">{{$t('compensatory_leave')}}: {{taskHead['compensatory_leave']}}</div>
                    <div class="col-md-3">{{$t('unpaid_leave')}}: {{taskHead['unpaid_leave']}}</div>
                    <div class="col-md-3">{{$t('half_leave')}}: {{taskHead['half_leave']}} ({{Leave[0]['half_leave'] - taskHead['half_leave']}})</div>
                </div>
            </div>
            <div class="modal-body row m-0 p-0 mb-2" >                
                <div>
                    <button v-if="reportType == $t('summary')" @click="reportType = $t('details')" class="mdb btn btn-outline-unique">{{$t('details')}} {{$t('report')}}</button>
                    <button v-if="reportType == $t('details')" @click="reportType = $t('summary')" class="mdb btn btn-outline-unique">{{$t('summary')}} {{$t('report')}}</button>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3" :class="viewLeaveDetails">
                    <b-table @row-clicked="(item) => editDetails(item.id)" style="cursor : pointer" show-empty small striped hover stacked="md" :items="personalLeave" :fields="taskDetailsfieldsView">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>                                    
                        <!-- <template v-slot:cell(issue_etd)="row">
                            {{`${row.item.issue_etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                        </template>                                    -->
                        <template v-slot:cell(leave_type)="row">
                            {{ $t(row.item.leave_type) }}
                        </template>
                    </b-table>
                </div>
                <div  v-if="reportType == $t('summary')" class="col-md-12 m-0 p-0 mt-3">
                    <b-table style="cursor : pointer" show-empty small striped hover stacked="md" :items="leaveSummary" :fields="leaveSummaryFilds">
                        <template v-slot:cell(total)="row">
                            {{ row.item.casual_leave+row.item.sick_leave+row.item.earned_leave+row.item.unpaid_leave+row.item.other_leave }}
                        </template>
                    </b-table>
                </div>                              
            </div>
            <template v-slot:modal-header="">
                <h3 class="panel-title float-left">{{ $t('personal_leave_management') }}</h3> 
                <div class="ml-auto">
                    <button v-if="checkRoles('leave_management_Insert')" @click="editDetails(null)" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                </div>
            </template>
            <template v-slot:modal-footer="">
                <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End view Details Modal -->
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('leave_management') }
    },

    data() {
        return{
            leaveList : [],
            Usedleave: [],
            leaveSummary: [{'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0}],
            Leave: [],
            task: [{'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}],
            taskHead: [],
            taskHeadId: null,
            taskDetails: [{'leave_type': null, 'reason': null, 'replacing_person': null, 'leave_start': this.convertDate(new Date()), 'leave_end': this.convertDate(new Date()), 'day_count': 1, 'employee_id': this.taskHeadId}],
            taskDetailsId: null,
            personalLeave: [],
            roles: [],
            year: new Date().getFullYear(),
            reportEdit: false,
            reportType: this.$t('summary'),
            buttonTitle : this.$t('save'),
            disable: false,

            noprint: '',
            errors: '',

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
        fetch(`api/leave`)
        .then(res => res.json())
        .then(res => {
            this.leaveList = res['LeaveList'];
            this.task = this.singleTask
            if (this.task.length < 1) {
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/usedleave/${this.year}`)
        .then(res => res.json())
        .then(res => {
            this.Usedleave = res['Usedleave']
            this.totalRows = this.Usedleave.length
            this.Leave = res['Leave']
            this.isBusy = false
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
                month = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, month, day].join("-");
        },

        getMonthMethod(str) {
            var date = new Date(str),
                month = date.getMonth()
            return month
        },

        yearWiseDisplay(year) {
            this.isBusy = true
            if(year > 1){ this.year = year }
            else { this.year = parseInt(this.year) + year }
            fetch(`api/usedleave/${this.year}`)
            .then(res => res.json())
            .then(res => {
                this.Usedleave = res['Usedleave']
                this.Leave = res['Leave']
                this.isBusy = false
            })
            this.task = this.singleTask
            if (this.task.length < 1) {
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
        },

        service_length(str) {
            var startDate = new Date(str);
            var endDate = new Date(),
                year = endDate.getFullYear() - startDate.getFullYear(),
                mnth = endDate.getMonth() - startDate.getMonth(),
                day = endDate.getDate() - startDate.getDate()
                if(day < 0) {day += 30; mnth--}
                if(mnth < 0) {mnth += 12; year--}
            return year + " years " + mnth + " months " + day + " days ";
        },

        viewDetails(id) {
            this.taskHeadId = id
            this.noprint = 'noprint'
            this.taskDetailsId = null
            this.taskHead = this.taskHeadSingle

            fetch(`api/leave/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetailsAll = res['AllLeaves']
                this.personalLeave = this.taskDetailsSingle('read')
                this.leaveSummaryMethod()
            })
            .catch(err => {
                alert(err.response.data.message);
            })

            this.$refs['dataView'].show()
        },

        leaveSummaryMethod() {
            let leaveSummary = [], k=0, date = null
            for (let i = 0; i < this.personalLeave.length; i++) {
                for (let j = 0; j < this.personalLeave[i]['day_count']; j++) {
                    leaveSummary[k] = JSON.parse( JSON.stringify( this.personalLeave[i] ) );
                    date =  new Date(leaveSummary[k]['leave_start'])
                    date.setDate(date.getDate() + j)
                    leaveSummary[k]['leave_start'] = this.convertDate(date)
                    k++
                }
            }

            this.leaveSummary= [
                {'month' : 'January', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'February', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'March', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'April', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'May', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'June', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'July', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'August', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'September', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'October', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'November', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : 'December', 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
                {'month' : this.$t('total'), 'casual_leave' : 0, 'sick_leave': 0, 'earned_leave': 0, 'unpaid_leave': 0, 'other_leave': 0},
            ]

            for (let l = 0; l < leaveSummary.length; l++) {
                switch(leaveSummary[l]['leave_type']) {
                    case 'casual_leave':
                        this.leaveSummary[this.getMonthMethod(leaveSummary[l]['leave_start'])]['casual_leave']++
                        this.leaveSummary[12]['casual_leave']++
                        break;
                    case 'sick_leave':
                        this.leaveSummary[this.getMonthMethod(leaveSummary[l]['leave_start'])]['sick_leave']++
                        this.leaveSummary[12]['sick_leave']++
                        break;
                    case 'earned_leave':
                        this.leaveSummary[this.getMonthMethod(leaveSummary[l]['leave_start'])]['earned_leave']++
                        this.leaveSummary[12]['earned_leave']++
                        break;
                    case 'unpaid_leave':
                        this.leaveSummary[this.getMonthMethod(leaveSummary[l]['leave_start'])]['unpaid_leave']++
                        this.leaveSummary[12]['unpaid_leave']++
                        break;
                    default:
                        this.leaveSummary[this.getMonthMethod(leaveSummary[l]['leave_start'])]['other_leave']++
                        this.leaveSummary[12]['other_leave']++
                        break;
                }
            }
        },

        addDetails() {
            this.reportEdit = !this.reportEdit            
            this.noprint = ''
        },

        editDetails(id) {
            this.taskDetailsId = id
            this.taskDetails = this.taskDetailsSingle('edit')
            if (this.taskDetails.length < 1) {
                this.taskDetails = [{'leave_type': null, 'reason': null, 'replacing_person': null, 'leave_start': this.convertDate(new Date()), 'leave_end': this.convertDate(new Date()), 'day_count': 1, 'employee_id': this.taskHeadId}]
            }
            this.$refs['dataEdit'].show()
        },
        
        taskDetailsSingle(view) {
            let array = []
            for (let i = 0; i < this.taskDetailsAll.length; i++) {
                if (view == 'edit') {
                    if (this.taskDetailsAll[i]['id'] == this.taskDetailsId) {
                        array[0] = this.taskDetailsAll[i]
                        break
                    }
                } else {
                    if (this.taskDetailsAll[i]['year'] == this.year) {
                        array[i] = this.taskDetailsAll[i]
                    }
                }                
            } return array
        },

        savePersonalLeave() {
            //need to add checking for leaves in current month
            

            let oldDayCount = this.taskDetails[0]['day_count'], 
                start = new Date(this.taskDetails[0]['leave_start']), 
                end = new Date(this.taskDetails[0]['leave_end'])
            
            if (start.getFullYear() != end.getFullYear()) {
                this.$toast.error(this.$t('both_date_should_be_in_same_year'), this.$t('required'), {timeout: 3000, position: 'center'})
                return
            }
            this.taskDetails[0]['day_count'] = (end.getTime() - start.getTime())/(1000 * 3600 * 24)
            this.taskDetails[0]['day_count'] += 1
            
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')

            if(this.taskDetails[0]['id'] == null){
                axios.post(`api/usedleave`, this.taskDetails[0])
                .then(({data}) =>{
                    this.errors = ''
                    this.personalLeave.unshift(data.Usedleave)
                    this.leaveSummaryMethod()
                    let count = parseInt(this.taskHead[this.taskDetails[0]['leave_type']])
                    count += this.taskDetails[0]['day_count']
                    this.taskHead[this.taskDetails[0]['leave_type']] = count
                    this.yearWiseDisplay(this.year)
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
                axios.patch(`api/usedleave/${this.taskDetails[0]['id']}`, this.taskDetails[0])
                .then(({data}) => {
                    this.yearWiseDisplay(this.year)
                    let count = parseInt(this.taskHead[this.taskDetails[0]['leave_type']])
                    count += (this.taskDetails[0]['day_count'] - oldDayCount)
                    this.taskHead[this.taskDetails[0]['leave_type']] = count
                    this.leaveSummaryMethod()
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

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.task[0]['year'] = this.year

            if(this.task[0]['id'] == null){
                axios.post(`api/leave`, this.task[0])
                .then(({data}) =>{
                    this.errors = ''
                    this.leaveList.unshift(data.LeaveList)
                    this.task[0]['id'] = this.leaveList[0]['id']
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.reportEdit = !this.reportEdit
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
                axios.patch(`api/leave/${this.task[0]['id']}`, this.task[0])
                .then(({data}) => {
                    this.errors = ''
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.reportEdit = !this.reportEdit
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

        destroy() {
            let oldDayCount = this.taskDetails[0]['day_count']
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {

                        axios.delete(`api/usedleave/${this.taskDetails[0]['id']}`)
                        .then(({data}) => {
                            this.yearWiseDisplay(this.year)
                            for (let i = 0; i < this.personalLeave.length; i++) {
                                if (this.personalLeave[i]['id'] == this.taskDetails[0]['id']) {
                                    this.personalLeave.splice(i, 1);
                                    break
                                }                                
                            }
                            let count = parseInt(this.taskHead[this.taskDetails[0]['leave_type']])
                            count -=  oldDayCount
                            this.taskHead[this.taskDetails[0]['leave_type']] = count
                            this.leaveSummaryMethod()
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
    },

    computed: {
        singleTask() {
            let year = this.year
            return this.leaveList.filter(function (item) {
            return item['year'] == year
            })
        },

        taskHeadSingle() {
            let array = []
            for (let i = 0; i < this.Usedleave.length; i++) {
                if (this.Usedleave[i]['id'] == this.taskHeadId) {
                    array = this.Usedleave[i]
                    break
                }                
            } return array
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },

        reportClass(){
            return[ 
                this.reportEdit == true ? 'w-100' : 'border-0 bg-transparent rounded-0'
            ]
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
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'casual_leave', label : this.$t('casual_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'casual_leave_remain', label : this.$t('remain'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'sick_leave', label : this.$t('sick_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'sick_leave_remain', label : this.$t('remain'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'earned_leave', label : this.$t('earned_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'earned_leave_remain', label : this.$t('remain'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'maternity_leave', label : this.$t('maternity_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'paternity_leave', label : this.$t('paternity_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'compensatory_leave', label : this.$t('compensatory_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unpaid_leave', label : this.$t('unpaid_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'half_leave', label : this.$t('half_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leave_type', label : this.$t('leave_category'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'reason', label : this.$t('reason'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leave_start', label : this.$t('leave_start'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leave_end', label : this.$t('leave_end'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'day_count', label : this.$t('days'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'created_at', label : this.$t('application_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },

        leaveSummaryFilds() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'month', label : this.$t('month'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'casual_leave', label : this.$t('casual_leave'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'sick_leave', label : this.$t('sick_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'earned_leave', label : this.$t('earned_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unpaid_leave', label : this.$t('unpaid_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'other_leave', label : this.$t('other_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total', label : this.$t('total'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },

        viewLeaveDetails() {
            return[ this.reportType == this.$t('details') ? '' : 'd-none' ]
        }
    }
}
</script>

<style lang="scss" scoped>

</style>