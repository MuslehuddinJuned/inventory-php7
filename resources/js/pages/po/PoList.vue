<template>
    <div class="container-fluid justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('po_list') }}</h3>                     
                    <div class="ml-auto">
                        <button v-if="checkRoles('po_list_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
                <div class="card-header row m-0 p-0">
                    <div class="col-md-5">
                        <label class="col-form-label">ETD</label>
                        <div>
                            <input @change="etd_change" class="form-control" type="date" v-model="etd">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="col-form-label">PO No:</label>
                        <div class="input-group">
                            <model-select :options="PoNoListView" class="form-control float-left col-11" v-model="po_no"></model-select>
                            <button @click="po_change" class="btn btn-secondary input-group-append noprint col-1"><b-icon icon="search"></b-icon></button>
                        </div>
                    </div>
                </div> 
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            :title="etd"
                            :data="PoList"
                            :fields="json_fields"
                            worksheet="PO List"
                            name="PO List.xls">
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
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover responsive
                    :items="PoList"
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
                    <template v-slot:cell(po_no)="row">
                        {{ row.item.po_no }}
                        <!-- <div @click.prevent="viewPoDetails(row.item.id)" style="cursor: pointer;">{{ row.item.po_no }}</div> -->
                    </template>
                    <template v-slot:cell(po_date)="row">
                        {{`${row.item.po_date}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                    </template>
                    <template v-slot:cell(etd)="row">
                        {{`${row.item.etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                    </template>
                    <template v-slot:cell(product_image)="row">
                        <a :href="'/images/product/' + row.item.product_image"><b-img :src="'/images/product/' + row.item.product_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                    </template>
                    <template v-slot:cell(action)="row">
                        <a v-if="checkRoles('po_list_Update')" @click="editDetails(row.item.id, row.item.sn)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
                        <a v-if="checkRoles('po_list_Delete')" @click="destroy(row.item.id, row.item.sn)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
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
        <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="title" no-close-on-backdrop>            
            <div class="modal-body row m-0 p-0">
                <div class="col-md-12 row m-0 p-0">
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('lot')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['lot']">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('container')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['container']">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">PO No.</label>
                        <input list="PoList" class="form-control text-nowrap" v-model="po_no">
                        <datalist id="PoList">
                            <option v-for="po in PoNoListView" :key="po.text">{{ po.text }}</option>
                        </datalist>
                        <span v-if="errors.po_no" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('buyer')}}</label>
                        <b-form-select @change="change_buyer" id="buyer" v-model="buyer" :options="buyerlistview"></b-form-select>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('style') + ' ' + $t('code')}}</label>
                        <b-form-select v-model="task[0]['producthead_id']" :options="product_codelistview"></b-form-select>
                        <span v-if="errors.producthead_id" class="error text-danger"> {{$t('required_field')}}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('quantity')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['quantity']" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        <span v-if="errors.quantity" class="error text-danger"> {{$t('required_field')}}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('ctn')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['ctn']" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('pcs_per_ctn')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['pcs_per_ctn']" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">CBM</label>
                        <input type="text" class="form-control" v-model="task[0]['cbm']" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">PO Date</label>
                        <input type="date" class="form-control" v-model="task[0]['po_date']">
                        <span v-if="errors.po_date" class="error text-danger"> {{$t('required_field')}}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('shipment_booking')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['shipment_booking']">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('loading_date')}}</label>
                        <input type="date" class="form-control" v-model="task[0]['loading_date']">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('inspection_date')}}</label>
                        <input type="date" class="form-control" v-model="task[0]['inspection_date']">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">ETD</label>
                        <input type="date" class="form-control" v-model="task[0]['etd']">
                        <span v-if="errors.etd" class="error text-danger"> {{$t('required_field')}}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">{{ $t('remarks')}}</label>
                        <input type="text" class="form-control" v-model="task[0]['remarks']">
                    </div>
                </div>
                
                                                
            </div>                        
            <template v-slot:modal-footer>
                <button v-if="checkRoles('po_list_Insert')" @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal>                    
        <!-- End Edit Details Modal -->

        <!-- Start View PO Details Modal -->
        <!-- <b-modal ref="dataView" id="dataView" size="xxl" :title="$t('monitor_po') + ': ' + $t('ItemReceive')" no-close-on-backdrop ok-only>
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
                :items="PoDetails"
                :fields="poFields"
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
                <template v-slot:cell(po_date)="row">
                    {{`${row.item.po_date}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                </template>
                <template v-slot:cell(balance)="row">
                    {{ row.item.inventory_qty - row.item.total_qty}}
                </template>
                <template v-slot:cell(item_image)="row">
                    <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                </template>
                </b-table>
                
                <div class="col-12 mx-auto p-0 noprint">
                    <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows_po"
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
            <template v-slot:modal-footer>
                <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal> -->
        <!-- End View PO Details Modal --> 
    </div>
</template>

<script>
import uniq from 'lodash/uniq';
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('po_list') }
    },

    data() {
        return{
            PoList : [],
            PoListAll : [],
            roles: [],
            errors : [],
            po_no : null,
            etd: this.convertDate(new Date()),
            buyer : null,
            productList : [],
            product_codelistview: [],
            PoDetailsAll : [],
            PoDetails : [],
            totalRows_po: 1,
            noprint: '',
            Id : '',
            Index : '',
            title: '',
            disable: false,
            task : [{'lot': null, 'container': null, 'ctn': 0, 'pcs_per_ctn': 0, 'cbm': 0, 'shipment_booking': null, 'loading_date' : this.convertDate(new Date()), 'inspection_date' : this.convertDate(new Date()), 'po_no' : null,'po_date' : this.convertDate(new Date()), 'etd' : this.convertDate(new Date()), 'remarks' : null,'producthead_id' : null, 'quantity' : null}],
            taskId : null,
            buttonTitle : this.$t('save'),

            src : '/images/item/',

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
            json_fields: {
                'Lot': 'lot',
                'Container': 'container',
                'PO No': 'po_no',
                'Buyer': 'buyer',
                'Style Code': 'product_code',
                'Quantity': 'quantity',
                'Carton': 'ctn',
                'Pcs/Ctn': 'pcs_per_ctn',
                'CBM': 'cbm',
                'PO Date': 'po_date',
                'Shipment Booking': 'shipment_booking',
                'Inspection Date': 'inspection_date',
                'Loading Date': 'loading_date',
                'ETD': 'etd',
            },
        }
    },

    mounted() {
        this.isBusy = true
        this.src = '/images/product/'
        fetch(`api/polist`)
        .then( res => res.json())
        .then(res => {  
            this.PoListAll = res['PoList']
            this.productList = res['productList']
            this.PoList = this.PoListByPoNo;
            this.totalRows = this.PoList.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.PoList[i]['sn'] = i                
            }
            this.isBusy = false;                
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
            this.title = this.$t('InsertNewItem')
            this.task = [{'lot': null, 'container': null, 'ctn': 0, 'pcs_per_ctn': 0, 'cbm': 0, 'shipment_booking': null, 'loading_date' : this.convertDate(new Date()), 'inspection_date' : this.convertDate(new Date()), 'po_no' : null, 'po_date' : this.convertDate(new Date()), 'etd' : this.convertDate(new Date()), 'remarks' : null,'producthead_id' : null, 'quantity' : null}]
        },

        po_change() {
            this.PoList = this.PoListByPoNo;
            this.totalRows = this.PoListByPoNo.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.PoList[i]['sn'] = i                
            }
        },

        change_buyer() {
            let array = [], j=0
            for (let i = 0; i < this.productList.length; i++) {
                if (this.productList[i]['buyer'] == this.buyer) {
                    array[j] = this.productList[i] 
                    j++
                }               
            }
            this.product_codelistview = array
        },

        etd_change() {
            this.PoList = this.PoListByEtd;
            this.totalRows = this.PoListByEtd.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.PoList[i]['sn'] = i                
            }
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        // viewPoDetails(po) {
        //     this.noprint = 'noprint'
        //     this.isBusy = true
        //     fetch(`api/polist/${po}`)
        //     .then( res => res.json())
        //     .then(res => {  
        //         this.PoDetailsAll = res['polist']
        //         this.PoDetails = this.PoDetailsByPoNo;
        //         this.totalRows_po = this.PoDetails.length;
        //         this.isBusy = false;                
        //     })
        //     .catch(err => {
        //         alert(err.response.data.message);
        //     })
        //     this.$refs['dataView'].show()
        // },

        editDetails(id, index) {
            this.title = this.$t('UpdateItem')
            this.taskId = id
            this.Index = index
            this.task = this.singleTask
            this.buyer = this.task[0]['buyer']
            this.change_buyer()
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.task[0]['po_no'] = this.po_no

            if(this.taskId == null){
                axios.post(`api/polist`, this.task[0])
                .then(({data}) =>{
                    this.errors = ''
                    this.PoListAll = data.polist
                    this.po_change()
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
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
                axios.patch(`api/polist/${this.taskId}`, this.task[0])
                .then(({data}) => {
                    this.errors = ''
                    this.PoList[this.Index] = this.task[0];
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
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

        destroy(id, index) {
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/polist/${id}`)
                        
                        .then(res => {
                            for (let i = 0; i < this.PoListAll.length; i++) {
                                if(this.PoListAll[i]['id'] == id){
                                    this.PoListAll.splice(i, 1);
                                    break
                                }               
                            }

                        this.po_change()
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

        showModal() {
            this.$refs['dataEdit'].show()
        },

        hideModal() {
            this.noprint = ''
            // this.$refs['dataView'].hide()
            this.$refs['dataEdit'].hide()
        },


    },

    computed: {
        PoListByPoNo() {
            let po = this.po_no            
            return this.PoListAll.filter(function (item) {
                return item['po_no'] == po
            })            
        },

        PoDetailsByPoNo() {
            let array = [], j=0 

            for (let i = 0; i < this.PoDetailsAll.length; i++) {
                array[j] = this.PoDetailsAll[i]
                if(array[j]['inventory_qty'] < array[j]['total_qty']) {
                    array[j]['_rowVariant'] = 'danger'
                } else {
                    array[j]['_rowVariant'] = ''
                }                      
                j++
            } 
            return array
        },

        PoListByEtd() {
            let etd = this.etd            
            return this.PoListAll.filter(function (item) {
                return item['etd'] == etd
            })            
        },

        // PoNoList(){
        //     return uniq(this.PoListAll.map(({ po_no }) => po_no))
        // },

        PoNoListView(){
            let array = []
            for (let i = 0; i < this.PoListAll.length; i++) {                    
                array.unshift({'value' : this.PoListAll[i]['po_no'], 'text' : this.PoListAll[i]['po_no']})
            }
            return array
        },

        buyerlistview() {
            return uniq(this.productList.map(({ buyer }) => buyer))
        },

        singleTask() {
            let id = this.taskId
            return this.PoList.filter(function (item) {
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

            let action = []
            if ((this.checkRoles('po_list_Update') || this.checkRoles('po_list_Delete'))) {
                action = { key: 'action', label: this.$t('Action'),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
            }
            // , , , , , , , 
            return [
                // { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'lot', label : this.$t('lot'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'container', label : this.$t('container'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_no', label : 'PO No', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'ctn', label : this.$t('ctn'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'pcs_per_ctn', label : this.$t('pcs_per_ctn'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'cbm', label : 'CBM', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_date', label : 'PO Date', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'shipment_booking', label : this.$t('shipment_booking'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'inspection_date', label : this.$t('inspection_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'loading_date', label : this.$t('loading_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'etd', label : 'ETD', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                action
            ]           
            
        },

        // poFields() {
        //     const lang = this.$i18n.locale
        //     if (!lang) { return [] }
        //     this.buttonTitle = this.$t('save')
            
        //     return [
        //         { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'po_no', label : this.$t('PO No'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'po_date', label : this.$t('PO Date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'item_no', label : this.$t('material_number'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'item', label : this.$t('material_name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'specification', label : this.$t('specification'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'inventory_qty', label : this.$t('stock'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'total_qty', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'balance', label : this.$t('balance'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //     ]           
            
        // },

        store_namelistview() {
            return uniq(this.PoList.map(({ store_name }) => store_name))
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    },

    components: { ModelSelect }

}
</script>

<style lang="sass" scoped>

</style>