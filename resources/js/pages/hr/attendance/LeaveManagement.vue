<template>
    <div class="container">
        <div class="card filterable" :class="noprint">
            <div class="card-header row m-0">
                <div class="col-md-6">
                    <h3 class="panel-title float-left">
                        {{ $t('leave_management') }}
                        <fa v-if="checkRoles('leave_management_Insert')" @click="addDetails" icon="edit" class="ml-2 pointer" fixed-width />                    
                    </h3>
                </div>                     
                <div class="col-md-6">
                    <div class="ml-md-auto m-sm-0 input-group col-md-12 col-lg-6 float-md-right">
                        <div class="input-group-prepend">
                            <div @click="yearWiseDisplay(-1)" class="input-group-text pointer"><b-icon icon="dash"></b-icon></div>
                        </div>
                        <input type="text" v-model="year" @change="yearWiseDisplay(year)" class="form-control text-center" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        <div class="input-group-append">
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
                <template v-slot:cell(casual_leave)="row">
                    {{row.item.casual_leave}}  ({{Leave[0]['casual_leave'] - row.item.casual_leave}})
                </template>
                <template v-slot:cell(sick_leave)="row">
                    {{row.item.sick_leave}}  ({{Leave[0]['sick_leave'] - row.item.sick_leave}})
                </template>
                <template v-slot:cell(annual_leave)="row">
                    {{row.item.annual_leave}}  ({{Leave[0]['annual_leave'] - row.item.annual_leave}})
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
        <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="$t('requisition')" no-close-on-backdrop>            
            <div v-if="taskHeadId" class="modal-body row m-0 p-0 mb-2">
                <div class="col-md-4">
                    <label for="leave_category" class="col-form-label mr-2">{{ $t('leave_category')}}</label>
                    <select v-model="taskDetails[0]['leave_type']" class="form-control" id="leave_category">
                        <option value="casual_leave">{{$t('casual_leave')}}</option>
                        <option value="sick_leave">{{$t('sick_leave')}}</option>
                        <option value="annual_leave">{{$t('annual_leave')}}</option>
                        <option value="maternity_leave">{{$t('maternity_leave')}}</option>
                        <option value="paternity_leave">{{$t('paternity_leave')}}</option>
                        <option value="compensatory_leave">{{$t('compensatory_leave')}}</option>
                        <option value="unpaid_leave">{{$t('unpaid_leave')}}</option>
                        <option value="half_leave">{{$t('half_leave')}}</option>
                    </select>
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
                <button @click="savePersonalLeave" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End Edit Details Modal -->

        <!-- Start view Details Modal -->
        <b-modal ref="dataView" id="dataView" size="xl" :title="$t('personal_leave_management')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0 mb-2" >
                <div class="col-md-6">
                    <span class="font-weight-bold">{{ $t('ID')}}:</span> {{taskHead['employee_id']}} <br>
                    <span class="font-weight-bold">{{ $t('name')}}:</span> {{taskHead['first_name']}}
                </div>
                <div class="col-md-6">                                
                    <span class="font-weight-bold">{{ $t('designation')}}:</span> {{taskHead['designation']}}<br>
                    <span class="font-weight-bold">{{ $t('department')}}:</span> {{taskHead['department']}}
                </div>
            </div>
            <div class="modal-body row m-0 p-0 mb-2 border-top border-secondary" >
                <div v-if="taskHeadId" class="row col-12 m-0 p-0">
                    <div class="col-md-3">{{$t('casual_leave')}}: {{taskHead['casual_leave']}} ({{Leave[0]['casual_leave'] - taskHead['casual_leave']}})</div>
                    <div class="col-md-3">{{$t('sick_leave')}}: {{taskHead['sick_leave']}} ({{Leave[0]['sick_leave'] - taskHead['sick_leave']}})</div>
                    <div class="col-md-3">{{$t('annual_leave')}}: {{taskHead['annual_leave']}} ({{Leave[0]['annual_leave'] - taskHead['annual_leave']}})</div>
                    <div class="col-md-3">{{$t('maternity_leave')}}: {{taskHead['maternity_leave']}} ({{Leave[0]['maternity_leave'] - taskHead['maternity_leave']}})</div>
                    <div class="col-md-3">{{$t('paternity_leave')}}: {{taskHead['paternity_leave']}} ({{Leave[0]['paternity_leave'] - taskHead['paternity_leave']}})</div>
                    <div class="col-md-3">{{$t('compensatory_leave')}}: {{taskHead['compensatory_leave']}} ({{Leave[0]['compensatory_leave'] - taskHead['compensatory_leave']}})</div>
                    <div class="col-md-3">{{$t('unpaid_leave')}}: {{taskHead['unpaid_leave']}} ({{Leave[0]['unpaid_leave'] - taskHead['unpaid_leave']}})</div>
                    <div class="col-md-3">{{$t('half_leave')}}: {{taskHead['half_leave']}} ({{Leave[0]['half_leave'] - taskHead['half_leave']}})</div>
                </div>
            </div>
            <div class="modal-body row m-0 p-0 mb-2 border-top border-secondary" >
                <div class="col-md-12 m-0 p-0 mt-3">
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
            </div>
            <template v-slot:modal-header="">
                <h3 class="panel-title float-left">{{ $t('personal_leave_management') }}</h3> 
                <div class="ml-auto">
                    <button @click="editDetails(null)" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
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
            Leave: [],
            task: [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}],
            taskHead: [],
            taskHeadId: null,
            taskDetails: [{'leave_type': null, 'reason': null, 'replacing_person': null, 'leave_start': this.convertDate(new Date()), 'leave_end': this.convertDate(new Date()), 'day_count': 1, 'employee_id': this.taskHeadId}],
            taskDetailsId: null,
            personalLeave: [],
            roles: [],
            year: new Date().getFullYear(),
            reportEdit: false,
            buttonTitle : this.$t('save'),
            disable: false,

            noprint: '',

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
        fetch(`api/leave`)
        .then(res => res.json())
        .then(res => {
            this.leaveList = res['LeaveList'];
            this.task = this.singleTask
            if (this.task.length < 1) {
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/usedleave/${this.year}`)
        .then(res => res.json())
        .then(res => {
            this.Usedleave = res['Usedleave']
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
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
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
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
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
            })
            .catch(err => {
                alert(err.response.data.message);
            })

            this.$refs['dataView'].show()
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
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')

            let oldDayCount = this.taskDetails[0]['day_count'], start = new Date(this.taskDetails[0]['leave_start']), end = new Date(this.taskDetails[0]['leave_end'])
            this.taskDetails[0]['day_count'] = (end.getTime() - start.getTime())/(1000 * 3600 * 24)
            this.taskDetails[0]['day_count'] += 1

            if(this.taskDetails[0]['id'] == null){
                axios.post(`api/usedleave`, this.taskDetails[0])
                .then(({data}) =>{
                    this.errors = ''
                    this.personalLeave.unshift(data.Usedleave)
                    this.yearWiseDisplay(this.year)
                    let count = parseInt(this.taskHead[this.taskDetails[0]['leave_type']])
                    count += this.taskDetails[0]['day_count']
                    this.taskHead[this.taskDetails[0]['leave_type']] = count
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.reportEdit = !this.reportEdit
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
                    this.errors = ''
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.reportEdit = !this.reportEdit
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
                { key: 'employee_id', label : this.$t('ID'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'casual_leave', label : this.$t('casual_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'sick_leave', label : this.$t('sick_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'annual_leave', label : this.$t('annual_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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
            this.buttonTitle = this.$t('save')
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
    }
}
</script>

<style lang="scss" scoped>

</style>