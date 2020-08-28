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
                    <div>
                        <select @change="store_change" class="form-control" id="store" v-model="store">
                            <option value="2">{{ $t('injection_raw_materials') }}</option>
                            <option value="3">{{ $t('cutting_raw_materials') }}</option>
                            <option value="4">{{ $t('polish_raw_materials') }}</option>
                            <option value="5">{{ $t('wash_chemicals') }}</option>
                            <option value="7">{{ $t('spray_chemicals') }}</option>
                            <option value="8">{{ $t('printing_chemicals') }}</option>
                            <option value="9">{{ $t('packaging_materials') }}</option>
                            <option value="11">{{ $t('fabric_raw_materials') }}</option>
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
        <b-modal ref="dataView" id="dataView" size="xl" :title="$t('product_details')" no-close-on-backdrop ok-only>
            <div class="modal-body row m-0 p-0 mb-2">
                <div class="row col-md-9 m-0 p-0">
                    <div class="col-md-6">
                        <span class="font-weight-bold">{{ $t('store_name')}}:</span> {{inOutDetails[0]['store_name']}}<br>
                        <span class="font-weight-bold">{{ $t('item_code')}}:</span> {{inOutDetails[0]['item_code']}}<br>
                        <span class="font-weight-bold">{{ $t('item')}}:</span> {{inOutDetails[0]['item']}}
                    </div>
                    <div class="col-md-6">
                        <span class="font-weight-bold">{{ $t('specification')}}:</span> {{inOutDetails[0]['specification']}}<br>
                        <span class="font-weight-bold">{{ $t('unit')}}:</span> {{inOutDetails[0]['unit']}}<br>
                        <span class="font-weight-bold">{{ $t('unit_price')}}:</span> {{inOutDetails[0]['unit_price']}}
                    </div>
                    <div class="input-group col-md-8 mt-2">
                        <input v-model="searchDateStart" class="form-control input-group-prepend" type="date">
                        <div>
                            <div class="input-group-text">to</div>
                        </div>
                        <input v-model="searchDateEnd" class="form-control input-group-append" type="date">
                        <div>
                        <button @click="searchDateDetails" class="btn btn-secondary input-group-append"><b-icon icon="search"></b-icon></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group m-auto col-md-12 text-center float-center">
                        <a :href="'images/item/' + inOutDetails[0]['item_image']"> <img id="blah" style="width: 70%;" :src="'images/item/' + inOutDetails[0]['item_image']" alt="product image" /></a>
                    </div>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" :busy="isBusy" :items="inOutDetailsfiltered" :fields="inOutDetailsFields">
                        <template v-slot:table-busy>
                            <div class="text-center text-success my-2">
                                <b-spinner class="align-middle"></b-spinner>
                                <strong>{{$t('loading')}}</strong>
                            </div>
                        </template>
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(inout_date)="row">
                            {{`${row.item.inout_date}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                        </template>
                        <template v-slot:cell(etd)="row">
                            {{`${row.item.etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
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
            inOutDetailsfiltered : [],
            searchDateStart : null,
            searchDateEnd : null,
            stockType : 'all',
            taskId : null,
            store: 3,
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
        this.stockType = 'all'
        this.searchDateStart = new Date()
        this.searchDateEnd = new Date()
        this.searchDateStart.setDate(this.searchDateStart.getDate() - 31)

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

        viewDetails(id) {
            this.noprint = 'noprint'
            this.isBusy = true
            this.taskId = id
            fetch(`api/inventory/${id}`)
            .then(res => res.json())
            .then(res => {
                this.inOutDetails = res['inOutDetails']
                this.inOutDetailsfiltered = this.inOutDetailsSearch
            })
            .then(res =>{
                this.isBusy = false;
            })
            .catch(err => {
                alert(err.response.data.message)
            })
            this.$refs['dataView'].show()
        },

        searchDateDetails() {
            this.inOutDetailsfiltered = this.inOutDetailsSearch
        },

        store_change() {
            this.inventoryListfiltered = this.balance;
            this.totalRows = this.inventoryListfiltered.length;
        },

        fetchData(date_1, date_2) {
            let y1=null, m1=null, d1=null, y2=null, m2=null, d2=null

            y1 = date_1.getFullYear()
            m1 = ("0" + (date_1.getMonth() + 1)).slice(-2)
            d1 = ("0" + date_1.getDate()).slice(-2)
            y2 = date_2.getFullYear()
            m2 = ("0" + (date_2.getMonth() + 1)).slice(-2)
            d2 = ("0" + date_2.getDate()).slice(-2)

            this.searchDateStart = this.convertDate(date_1)
            this.searchDateEnd = this.convertDate(date_2)

            this.isBusy = true
            fetch(`api/inventorybalance/${y1}/${m1}/${d1}/${y2}/${m2}/${d2}`)
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

        inOutDetailsSearch() {
            let start_date = this.searchDateStart
            let end_date = new Date(this.searchDateEnd)
            end_date = this.convertDate(end_date.setDate(end_date.getDate() + 1))
            return this.inOutDetails.filter(function (item) {
                return (item['inout_date'] >= start_date && item['inout_date'] < end_date)
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
            return [
                { key: 'item_image', label : this.$t('item_image'), sortable: true, class: 'text-center', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'item_code', label : this.$t('item_code'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('item'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit_price', label : this.$t('unit_price') + '($)', sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'opening', label : this.$t('opening'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'receiving_qty', label : this.$t('in'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'issueing_qty', label : this.$t('out'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'closing', label : this.$t('closing'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_price', label : this.$t('total_price') + '($)', sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        inOutDetailsFields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'inout_date', label : this.$t('date'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'etd', label : this.$t('ETD'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'received_qty', label : this.$t('in'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'issued_qty', label : this.$t('out'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
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
    }

}
</script>

<style lang="scss" scoped>

</style>