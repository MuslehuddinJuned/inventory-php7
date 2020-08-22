<template>
    <div class="container justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('requisition_list') }}</h3> 
                    <div class="ml-auto">
                        <button @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
                <div class="card-header row m-0">
                    <label for="store" class="col-form-label mr-2">{{ $t('store_name')}}</label>
                    <div>
                        <select @change="store_change" class="form-control" id="store" v-model="store">
                            <option value="2">{{ $t('injection_raw_materials') }}</option>
                            <option value="3">{{ $t('cutting_raw_materials') }}</option>
                            <option value="4">{{ $t('polish_raw_materials') }}</option>
                            <option value="5">{{ $t('wash_chemicals') }}</option>
                            <option value="7">{{ $t('spray_chemicals') }}</option>
                            <option value="8">{{ $t('printing_chemicals') }}</option>
                            <option value="9">{{ $t('packaging_materials') }}</option>
                            <option value="10">{{ $t('stationery_items') }}</option>
                        </select>
                    </div> 
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
                    :items="requisitionList"
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
                    <template v-slot:cell(created_at)="row">
                        {{`${row.item.created_at}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}}
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
        <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="$t('requisition')" no-close-on-backdrop>
            
            <div class="modal-body row m-0 p-0 mb-2">
                <div class="col-md-6">
                    <label for="store" class="col-form-label mr-2">{{ $t('store_name')}}</label>
                    <div>
                        <select class="form-control" id="store" v-model="store" :disabled="storeDisabled">
                            <option value="2">{{ $t('injection_raw_materials') }}</option>
                            <option value="3">{{ $t('cutting_raw_materials') }}</option>
                            <option value="4">{{ $t('polish_raw_materials') }}</option>
                            <option value="5">{{ $t('wash_chemicals') }}</option>
                            <option value="7">{{ $t('spray_chemicals') }}</option>
                            <option value="8">{{ $t('printing_chemicals') }}</option>
                            <option value="9">{{ $t('packaging_materials') }}</option>
                            <option value="10">{{ $t('stationery_items') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">{{ $t('requisition_no')}}</label>
                    <input type="text" class="form-control" v-model="taskHead[0]['requisition_no']">
                </div>
                <div class="col-md-12">
                    <label class="col-form-label">{{ $t('remarks')}}</label>
                    <input type="text" class="form-control" v-model="taskHead[0]['remarks']">
                </div>
                <div class="col-md-12 m-0 p-0 mt-3" :class="hideDetails">
                    <b-table show-empty small striped hover stacked="md" :items="taskDetails" :fields="taskDetailsfields">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(inventory_id)="row">
                            <b-form-select v-model="row.item.inventory_id" :options="itemlistview" class="form-control row-fluid m-0 border-0 bg-transparent rounded-0"></b-form-select>
                        </template>                                    
                        <template v-slot:cell(issue_etd)="row">
                            <input type="date" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.issue_etd">
                        </template>                                    
                        <template v-slot:cell(master_sheet)="row">
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.master_sheet">
                        </template>
                        <template v-slot:cell(quantity)="row">
                            <input type="text" @keyup="grand_total_value" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.quantity">
                        </template>
                        <template v-slot:cell(remarks)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.remarks">
                        </template>
                        <template v-slot:cell(action)="row">
                            <!-- <a @click="viewDetails(row.item.machine_name, row.item.machine_description)" class="btn btn-sm text-black-50" data-toggle="modal" data-target="#dataView"><fa icon="eye" fixed-width /></a> -->
                            <a @click="addRow" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
                            <a @click="destroy_d(row.item.id, row.index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                        </template>
                    </b-table>
                </div>                              
            </div>
            <template v-slot:modal-header="">
                <div class="col-md-9">
                    <h3 class="panel-title float-left">{{ $t('requisition') }}</h3> 
                </div>
                <div class="col-md-3">
                    <button @click="archive" class="mdb btn btn-outline-info float-right"><fa icon="history" fixed-width /> {{ $t('archive') }}</button>
                </div>
            </template>
            <template v-slot:modal-footer="">
                <button @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="archive" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>                    
        <!-- End Edit Details Modal -->

        <!-- Start view Details Modal -->
        <b-modal ref="dataView" id="dataView" size="xl" :title="$t('requisition')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0 mb-2" >
                <div class="col-md-6">
                    <span class="font-weight-bold">{{ $t('store_name')}}:</span> {{taskHead[0]['store_name']}}
                </div>
                <div class="col-md-6">                                
                    <span class="font-weight-bold">{{ $t('requisition_no')}}:</span> {{taskHead[0]['requisition_no']}}
                </div>
                <div class="col-md-12">
                    <span class="font-weight-bold">{{ $t('remarks')}}:</span> {{taskHead[0]['remarks']}}
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" :items="taskDetailsCheck" :fields="taskDetailsfieldsView">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>                                    
                        <template v-slot:cell(issue_etd)="row">
                            {{`${row.item.issue_etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMM-YYYY')}}
                        </template>                                   
                        <template v-slot:cell(stock_cann)="row">
                            {{ (row.item.quantity * row.item.cann_per_sheet).toFixed(0) }}
                        </template>
                        <template v-slot:cell(total_price)="row">
                            {{ (row.item.quantity * row.item.unit_price).toFixed(2) }}
                        </template>
                        <!-- <template slot="bottom-row">
                            <td class="text-white bg-info font-weight-bold text-center">{{$t('grand_total')}}</td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center">{{grand_total}}</td>
                        </template> -->
                    </b-table>
                </div>                              
            </div>
            <template v-slot:modal-header="">
                <div class="col-md-9">
                    <h3 class="panel-title float-left">{{ $t('requisition') }}</h3> 
                </div>
                <div class="col-md-3">
                    <button @click="archive" class="mdb btn btn-outline-info float-right"><fa icon="history" fixed-width /> {{ $t('archive') }}</button>
                </div>
            </template>
            <template v-slot:modal-footer="">
                <div class="row m-0 p-0 col-md-12">
                    <div class="col-md-5">
                        <button @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                    </div>
                    <div class="col-md-7">
                        <button @click="archive" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                        <button @click="editDetails" class="mdb btn btn-outline-default float-right">{{ $t('edit') }}</button>
                    </div>
                </div>
            </template>
        </b-modal>
        <!-- End view Details Modal -->  
    </div>
</template>

<script>
import uniq from 'lodash/uniq';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('requisition_list') }
    },

    data() {
        return{
            inventoryList : [],
            requisitionList : [],
            title: '',
            store: 3,
            today: new Date(),
            storeDisabled: false,
            noprint : '',
            disable: false,
            taskHead : [{'requisition_no' : null,'remarks' : null, 'accept' : null}],
            taskDetails : [],
            taskHeadId : null,
            taskDetailsId : null,
            grand_total : 0,
            buttonTitle : this.$t('save'),
            hideDetails : 'd-none',

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
        this.today = this.convertDate(this.today)
        fetch(`api/inventory`)
            .then( res => res.json())
            .then(res => {  
                this.inventoryList = res['Inventory'];
            })
            .catch(err => {
                alert(err.response.data.message);
            })

        this.title = this.$t('receive_item')
        this.showModal()
        
    },

    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        addDetails(){
            this.hideDetails = 'd-none'
            this.taskHead = [{'requisition_no' : null,'remarks' : null, 'accept' : null}]
            this.taskHeadId = null
            this.title = this.$t('receive_item')
            this.grand_total = null
            this.taskDetails = []
        },

        addRow() {            
            this.taskDetails.push({'quantity' : 0, 'issue_etd' : this.today, 'master_sheet' : 0, 'remarks' : null, 'rechead_id' : this.taskHeadId, 'inventory_id' : null})
        },

        grand_total_value() {
            this.grand_total = this.grand_total_cal
        },

        store_change() {
            this.requisitionList = this.requisitionListByDept
            this.totalRows = this.requisitionList.length
        },

        viewDetails(id) {
            this.taskHeadId = id
            this.noprint = 'noprint'
            if(this.requisitionList.length < 1){
                this.grabRequsitionData()
            }  
            fetch(`api/recdetails/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetails = res['requisition']
                this.taskHead = this.singleTask
            })
            // .then(res =>{
            //     this.grand_total = this.grand_total_cal
            // })
            .catch(err => {
                alert(err.response.data.message)
            })
            this.$refs['dataView'].show()
        },

        grabRequsitionData() {                            
            this.isBusy = true;
            fetch(`api/rechead`)
            .then(res => res.json())
            .then(res => {
                this.requisitionListAll = res['Rechead']
                this.requisitionList = this.requisitionListByDept
                this.totalRows = this.requisitionList.length
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })            
        },
        
        archive(check = 0) {
            if(this.requisitionList.length < 1){
                this.grabRequsitionData()
            }  
            this.storeDisabled = true 
            this.noprint = ''         
            this.$refs['dataEdit'].hide()
            this.$refs['dataView'].hide()
            
        },

        editDetails() {
            if(this.taskHead[0]['accept']){
                this.$toast.error(this.$t('already_accepted'), this.$t('error_alert_title'), {timeout: 3000, position: 'center'})
            } else {
                this.title = this.$t('UpdateItem')
                this.hideDetails = ''
                if (this.taskDetails.length == 0) {
                    this.taskDetails = [{'quantity' : 0, 'issue_etd' : this.today, 'master_sheet' : 0, 'remarks' : null, 'rechead_id' : this.taskHeadId, 'inventory_id' : null}]
                }
                this.$refs['dataView'].hide()
                this.$refs['dataEdit'].show()
            }            
        },

        row_material(id) {
            for (let i = 0; i < this.inventoryList.length; i++) {
                if (this.inventoryList[i]['id'] == id) {
                    return this.inventoryList[i]['item_code'] + ' | ' + this.inventoryList[i]['item'] + ' | ' + this.inventoryList[i]['unit']
                }                
            }
        },

        save() {
            if(this.taskHead[0]['accept']){
                this.$toast.error(this.$t('already_accepted'), this.$t('error_alert_title'), {timeout: 3000, position: 'center'})
            } else {
                this.disable = !this.disable;
                this.buttonTitle = this.$t('saving')
                this.storeDisabled = true

                if(this.taskHeadId == null){
                    axios.post(`api/rechead`, this.taskHead[0])
                    .then(({data}) =>{
                        this.taskHeadId = data.RecheadID
                        this.taskHead[0]['id'] = this.taskHeadId
                        if(this.requisitionList.length > 0){
                            this.requisitionList.unshift(this.taskHead[0])
                        }
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                        this.hideDetails = ''
                        this.taskDetails = [{'quantity' : 0, 'issue_etd' : this.today, 'master_sheet' : 0, 'remarks' : null, 'rechead_id' : this.taskHeadId, 'inventory_id' : null}]
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
                    axios.patch(`api/rechead/${this.taskHeadId}`, this.taskHead[0])
                    .then(res => {
                        for (let i = 0; i < this.taskDetails.length; i++) {
                            if(this.taskDetails[i]['id']){
                                axios.patch(`api/recdetails/${this.taskDetails[i]['id']}`, this.taskDetails[i])
                            } else if(this.taskDetails[i]['inventory_id']){
                                axios.post(`api/recdetails`, this.taskDetails[i])
                                .then(({data})=>{
                                    this.taskDetails[i]['id'] = data.RecdetailsID
                                })
                            }                            
                        }

                        if(this.requisitionList.length > 0){
                            for (let i = 0; i < this.requisitionList.length; i++) {
                                if(this.requisitionList[i]['id'] == this.taskHead[0]['id']){
                                    this.requisitionList[i] = this.taskHead[0]
                                }   
                            }
                        }
                    })
                    .then(res => {
                        this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                        this.grabRequsitionData()
                        this.viewDetails(this.taskHeadId)
                        this.$refs['dataEdit'].hide()
                    })
                    .catch(err => {
                        if(err.response.status == 422){
                            this.errors = err.response.data.errors
                        }
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                    });
                }
            }
        },

        destroy() {
            if(this.taskHead[0]['accept']){
                this.$toast.error(this.$t('already_accepted'), this.$t('error_alert_title'), {timeout: 3000, position: 'center'})
            } else {
                this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                    timeout: 20000,           
                    position: 'center',
                    buttons: [
                        ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                            axios.delete(`api/rechead/${this.taskHeadId}`)                        
                            .then(res => {
                                if(this.requisitionList.length > 0){
                                    let index = 0 
                                    for (let i = 0; i < this.requisitionList.length; i++) {
                                        if(this.requisitionList[i]['id'] == this.taskHead[0]['id']){
                                            index = i
                                            break
                                        }   
                                    }
                                    this.requisitionList.splice(index, 1);                           
                                    this.totalRows = this.requisitionList.length;
                                    this.$refs['dataView'].hide()
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
            }
        },

        destroy_d(id, index){
            if(this.taskHead[0]['accept']){
                this.$toast.error(this.$t('already_accepted'), this.$t('error_alert_title'), {timeout: 3000, position: 'center'})
            } else {
                this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                    timeout: 20000,           
                    position: 'center',
                    buttons: [
                        ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {                        
                            if(id){
                                axios.delete(`api/recdetails/${id}`)                        
                                .then(res => {
                                    this.taskDetails.splice(index, 1)                                
                                })
                                .catch(err => {
                                    alert(err.response.data.message);                       
                                });
                            } else {
                                this.taskDetails.splice(index, 1)
                            }

                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button>'+ this.$t('cancel') +'</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ]            
                });
            }
        },

        showModal() {
            this.$refs['dataEdit'].show()
        },

        hideModal() {
            this.$refs['dataEdit'].hide()
        },


    },

    computed: {
        singleTask() {
            let id = this.taskHeadId
            return this.requisitionList.filter(function (item) {
            return item['id'] == id
            })
        },

        requisitionListByDept() {
            let id = this.store
            return this.requisitionListAll.filter(function (item) {
                return item['store_id'] == id
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
                // { key: 'store_name', label : this.$t('store_name'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'requisition_no', label : this.$t('requisition_no'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'remarks', label : this.$t('remarks'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'created_at', label : this.$t('date'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        taskDetailsfields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            if(this.store == 3)

                return [
                    { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'inventory_id', label : this.$t('item'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'master_sheet', label : this.$t('stock_master_sheet'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('stock_sheet'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'issue_etd', label : this.$t('ETD'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'action', label: this.$t('Action'),  class: 'text-right', thClass: 'border-top border-dark font-weight-bold'}
                ]

            else

                return [
                    { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'inventory_id', label : this.$t('item'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('quantity'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    // { key: 'price', label : this.$t('unit_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    // { key: 'total_price', label : this.$t('total_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'issue_etd', label : this.$t('ETD'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'action', label: this.$t('Action'),  class: 'text-right', thClass: 'border-top border-dark font-weight-bold'}
                ]
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            if(this.store == 3){
                return [
                    { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'item_code', label : this.$t('style') + ' ' + this.$t('code'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('style') + ' ' + this.$t('name'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('size'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'master_sheet', label : this.$t('stock_master_sheet'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock', label : this.$t('stock_sheet'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('requisition_sheet'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit', label : this.$t('unit'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock_cann', label : this.$t('stock_cann'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'issue_etd', label : this.$t('ETD'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                ]
            } else {
                return [
                    { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'item_code', label : this.$t('material') + ' ' + this.$t('code'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('material') + ' ' + this.$t('name'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('specification'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock', label : this.$t('stock'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('quantity'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit', label : this.$t('unit'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'price', label : this.$t('unit_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_price', label : this.$t('total_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'issue_etd', label : this.$t('ETD'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                ]
            }
        },

        taskDetailsCheck(){
            for (let i = 0; i < this.taskDetails.length; i++) {
                if(this.taskDetails[i]['stock'] < this.taskDetails[i]['quantity']) {
                    this.taskDetails[i]['_rowVariant'] = 'danger'
                } else {
                    this.taskDetails[i]['_rowVariant'] = ''
                }            
            }

            return this.taskDetails
        },

        store_namelistview() {
            return uniq(this.inventoryList.map(({ store_name }) => store_name))
        },

        itemlistview(){
            let array = []
            for (let i = 0; i < this.inventoryList.length; i++) {
                if(this.inventoryList[i]['store_id'] == this.store){
                    array.unshift({'value' : this.inventoryList[i]['id'], 'text' : this.inventoryList[i]['item_code'] + ' | ' + this.inventoryList[i]['item'] + ' | ' + this.inventoryList[i]['unit']})
                }                
            }

            return array
        },

        grand_total_cal() {
            let total = 0
            Object.entries(this.taskDetails).forEach(([key, val]) => {
                if(!isNaN(parseFloat(val.unit_price)) && !isNaN(parseFloat(val.quantity)))
                total += parseFloat(val.unit_price*val.quantity)
            });
            return total.toFixed(2);
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    }
}
</script>

<style>

</style>