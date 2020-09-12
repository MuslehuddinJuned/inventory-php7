<template>
    <div class="justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('balance_sheet') + ': ' + $t('Inventory')}}</h3> 
                    <div class="ml-auto input-group col-md-6">
                        <input v-model="searchDateStart" class="form-control input-group-prepend" type="date">
                        <div class="">
                            <div class="input-group-text">to</div>
                        </div>
                        <input v-model="searchDateEnd" class="form-control input-group-append" type="date">
                        <button @click="searchDate" class="btn btn-secondary input-group-append"><b-icon icon="search"></b-icon></button>
                    </div>
                </div> 
                <div class="card-header row m-0">
                    <label for="store" class="col-form-label mr-2">{{ $t('store_name')}}</label>
                    <div style="min-width: 400px;"><model-select :options="store_options" class="form-control" v-model="store"></model-select></div>
                    <button @click="store_change" class="btn ml-3 btn-secondary noprint"><b-icon icon="search"></b-icon></button>
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            :title="storeName"
                            :data="inventoryListfiltered"
                            :fields="json_fields"
                            worksheet="Balance Sheet"
                            name="Balance Sheet.xls">
                            <b-icon icon="file-earmark-spreadsheet-fill"></b-icon>
                        </download-excel>
                        <b-tooltip target="tooltip-target-1" triggers="hover">
                            Save this table to Excel
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
                            <b-form-radio-group v-model="stockType">
                                <b-form-radio @change="stockTypeMethod('all')" value="all"><fa icon='greater-than-equal' fixed-width /><span class="font-weight-bold" style="font-size: large;">0</span></b-form-radio>
                                <b-form-radio @change="stockTypeMethod('notZero')" value="notZero"><fa icon='greater-than' fixed-width /><span class="font-weight-bold" style="font-size: large;">0</span></b-form-radio>
                                <b-form-radio @change="stockTypeMethod('zero')" value="zero"><fa icon='equals' fixed-width /><span class="font-weight-bold" style="font-size: large;">0</span></b-form-radio>
                            </b-form-radio-group>
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
                    :items="inventoryListfiltered"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    @row-clicked="(item) => viewDetails(item.id, searchDateStart, searchDateEnd, 'show')"
                    class="table-transition"
                    style="cursor : pointer"
                    >
                    <template v-slot:table-busy>
                        <div class="text-center text-success my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>{{$t('loading')}}</strong>
                        </div>
                    </template>
                    <template v-slot:cell(total_price)="row">
                        {{(row.item.closing * row.item.unit_price).toFixed(2)}}
                    </template>
                    <template v-slot:cell(item_image)="row">
                        <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="max-width: 150px; height: 50px;" alt=""></b-img></a>
                    </template>
                    <template slot="bottom-row">
                        <td class="text-white bg-info font-weight-bold text-center">Total</td>
                        <td class="text-white bg-info font-weight-bold text-center"></td>
                        <td class="text-white bg-info font-weight-bold text-center"></td>
                        <td class="text-white bg-info font-weight-bold text-center"></td>
                        <td class="text-white bg-info font-weight-bold text-center"></td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total[0]['totalOpening'].toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total[0]['totalIn'].toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total[0]['totalOut'].toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total[0]['totalClosing'].toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total[0]['totalPrice'].toFixed(2)}}</td>
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
        <b-modal ref="dataView" id="dataView" size="xl" :title="$t('balance_sheet') + ': ' + $t('Inventory')" no-close-on-backdrop ok-only>
            <div class="modal-body row m-0 p-0 mb-2">
                <div class="row col-md-9 m-0 p-0">
                    <div class="col-md-6">
                        <span class="font-weight-bold">{{ $t('store_name')}}:</span> {{inOutDetails[0]['store_name']}}<br>
                        <span class="font-weight-bold">{{ $t('item_code')}}:</span> {{inOutDetails[0]['item_code']}}<br>
                        <span class="font-weight-bold">{{ $t('item')}}:</span> {{inOutDetails[0]['item']}}<br>
                        <span class="font-weight-bold">{{ $t('date')}}:</span> {{searchDateStart + ' to ' + searchDateEnd}}
                    </div>
                    <div class="col-md-6">
                        <span class="font-weight-bold">{{ $t('specification')}}:</span> {{inOutDetails[0]['specification']}}<br>
                        <span class="font-weight-bold">{{ $t('unit')}}:</span> {{inOutDetails[0]['unit']}}<br>
                        <span class="font-weight-bold">{{ $t('unit_price')}}:</span> {{inOutDetails[0]['unit_price']}}
                    </div>
                    <!-- <div class="input-group col-md-8 mt-2">
                        <input v-model="searchDateStart" class="form-control input-group-prepend" type="date">
                        <div>
                            <div class="input-group-text">to</div>
                        </div>
                        <input v-model="searchDateEnd" class="form-control input-group-append" type="date">
                        <div>
                        <button @click="viewDetails(taskId, searchDateStart, searchDateEnd, 'showed')" class="btn btn-secondary input-group-append"><b-icon icon="search"></b-icon></button>
                        </div>
                    </div> -->
                </div>
                <div class="col-md-3">
                    <div class="form-group m-auto col-md-12 text-center float-center">
                        <a :href="'images/item/' + inOutDetails[0]['item_image']"> <img id="blah" style="width: 70%;" :src="'images/item/' + inOutDetails[0]['item_image']" alt="product image" /></a>
                    </div>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" :busy="isBusy" :items="inOutDetails" :fields="inOutDetailsFields">
                        <template v-slot:table-busy>
                            <div class="text-center text-success my-2">
                                <b-spinner class="align-middle"></b-spinner>
                                <strong>{{$t('loading')}}</strong>
                            </div>
                        </template>
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>                        
                        <!-- <template v-slot:cell(balance)="row">
                            {{balance_single_method(row.item.received_qty, row.item.issued_qty)}}
                        </template> -->
                        <template v-slot:cell(inout_date)="row">
                            <span v-if="row.item.inout_date">{{`${row.item.inout_date}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}</span>
                        </template>
                        <template v-slot:cell(etd)="row">
                            <span v-if="row.item.etd">{{`${row.item.etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}</span>
                        </template>
                    </b-table>
                </div>                              
            </div>
            <template v-slot:modal-footer="">
                <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End view Details Modal -->
    </div>
</template>

<script>
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('balance_sheet') }
    },

    data() {
        return{
            inventoryList : [],
            inventoryListfiltered : [],
            inOutDetails : [{'store_name' : '', 'item_code' : '', 'item' : '', 'specification' : '', 'unit' : '', 'unit_price' : ''}],
            searchDateStart : null,
            searchDateEnd : null,
            y1: null, m1: null, d1: null, y2: null, m2: null, d2: null,
            balance_single: 0,
            stockType : 'all',
            taskId : null,
            store: 3,
            storeName: '5-7530: Kitchen Utensil (Stainless Steel)',
            store_options: [],
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

            json_fields: {
                'Material No': 'item_code',
                'Material': 'item',
                'Unit': 'unit',
                'Unit Price': 'unit_price',
                'Opening': 'opening',
                'In': 'receiving_qty',
                'Out': 'issueing_qty',
                'Closing': 'closing',
                'Total Price': 'total_price',
            },
        }
    },

    mounted() {
        this.stockType = 'all'
        this.searchDateStart = new Date()
        this.searchDateEnd = new Date()
        this.searchDateStart.setDate(this.searchDateStart.getDate() - 31)
        
        fetch(`api/store`)
        .then(res => res.json())
        .then(res => {
            this.store_options = res['Store'];
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        this.fetchData(this.searchDateStart, this.searchDateEnd)
        
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

        balance_single_method(rec, iss){
            this.balance_single = parseFloat(this.balance_single) + parseFloat(rec) - parseFloat(iss)
            return this.balance_single
        },

        viewDetails(id, date_1, date_2, state) {
            this.noprint = 'noprint'
            this.isBusy = true
            this.taskId = id
            this.balance_single = 0
            let balance = 0

            this.isBusy = true
            fetch(`api/inventoryinout/${id}/${this.y1}/${this.m1}/${this.d1}/${this.y2}/${this.m2}/${this.d2}`)
            .then( res => res.json())
            .then(res => {  
                this.inOutDetails = res['inOutDetails']
                for (let i = 0; i < this.inOutDetails.length; i++) {
                    balance = balance + parseFloat(this.inOutDetails[i]['received_qty']) - parseFloat(this.inOutDetails[i]['issued_qty'])
                    this.inOutDetails[i]['balance'] = balance
                    
                }
                this.isBusy = false;
            })
            .catch(err => {
                alert(err.response.data.message);
            })

            this.$refs['dataView'].show()
        },

        store_change() {
            this.inventoryListfiltered = this.balance;
            this.totalRows = this.inventoryListfiltered.length;

            for (let i = 0; i < this.store_options.length; i++) {
                if (this.store_options[i]['value'] == this.store) {
                    this.storeName = this.store_options[i]['text'] 
                    break    
                }       
            }
            

        },

        fetchData(date_1, date_2) {

            this.searchDateStart = this.convertDate(date_1)
            this.searchDateEnd = this.convertDate(date_2)
            date_2.setDate(date_2.getDate() + 1)
            this.y1 = date_1.getFullYear()
            this.m1 = ("0" + (date_1.getMonth() + 1)).slice(-2)
            this.d1 = ("0" + date_1.getDate()).slice(-2)
            this.y2 = date_2.getFullYear()
            this.m2 = ("0" + (date_2.getMonth() + 1)).slice(-2)
            this.d2 = ("0" + date_2.getDate()).slice(-2)            

            this.isBusy = true
            fetch(`api/inventorybalance/${this.y1}/${this.m1}/${this.d1}/${this.y2}/${this.m2}/${this.d2}`)
            .then( res => res.json())
            .then(res => {  
                this.inventoryList = res['balance'];
                this.inventoryListfiltered = this.balance;
                this.totalRows = this.inventoryListfiltered.length;
                this.isBusy = false;
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        searchDate() {
            let date_1 = new Date(this.searchDateStart)
            let date_2 = new Date(this.searchDateEnd)

            this.fetchData(date_1, date_2)
        },

        stockTypeMethod(val) {
            this.stockType = val
            this.inventoryListfiltered = this.balance;
            this.totalRows = this.inventoryListfiltered.length;
        },

    },

    computed: {
        balance() {
            let check = this.store
            if (this.stockType == 'all') {
                return this.inventoryList.filter(function (item) {
                    return (item['store_id'] == check)
                })
            } else if (this.stockType == 'notZero') {
                return this.inventoryList.filter(function (item) {
                    return (item['closing'] > 0 && item['store_id'] == check)
                })
            } else {
                return this.inventoryList.filter(function (item) {
                    return (item['closing'] == 0 && item['store_id'] == check)
                })
            }
            
        },

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'item_image', label : this.$t('item_image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'item_code', label : this.$t('item_code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('item'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit_price', label : this.$t('unit_price') + '($)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'opening', label : this.$t('opening'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'receiving_qty', label : this.$t('in'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'issueing_qty', label : this.$t('out'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'closing', label : this.$t('closing'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_price', label : this.$t('total_price') + '($)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        inOutDetailsFields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'po_no', label : this.$t('PO No'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'rec_req_no', label : this.$t('invoice_no') + '/ ' + this.$t('requisition_no'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'inout_date', label : this.$t('date'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'etd', label : this.$t('ETD'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'received_qty', label : this.$t('in'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'issued_qty', label : this.$t('out'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'balance', label : this.$t('balance'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        Total() {
            let total = []
            let totalOpening = 0, totalIn =0, totalOut=0, totalClosing=0, totalPrice=0
            Object.entries(this.inventoryListfiltered).forEach(([key, val]) => {

                totalOpening += parseFloat(val.opening)
                totalIn += parseFloat(val.receiving_qty)
                totalOut += parseFloat(val.issueing_qty)
                totalClosing += parseFloat(val.closing)
                totalPrice += (parseFloat(val.closing) * parseFloat(val.unit_price))
            });

            total = [{'totalOpening' : totalOpening, 'totalIn' : totalIn, 'totalOut' : totalOut, 'totalClosing' : totalClosing, 'totalPrice' : totalPrice}]
            return total
        },
    },

    components: { ModelSelect }
}
</script>

<style lang="scss" scoped>

</style>