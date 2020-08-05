<template>
    <div class="container justify-content-center">
       <div class="col-md-12">
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
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center">
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
                    class="table-transition"
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
                    <template v-slot:cell(item)="row">
                        <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="width: 56px" alt=""></b-img></a> {{row.item.item}}
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
                    
                    <div class="col-12 mx-auto p-0">
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
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('InventoryItem') }
    },

    data() {
        return{
            inventoryList : [],
            inventoryListfiltered : [],
            searchDateStart : null,
            searchDateEnd : null,
            stockType : 'all',

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

        fetchData(date_1, date_2) {
            let y1=null, m1=null, d1=null, y2=null, m2=null, d2=null

            y1 = date_1.getFullYear()
            m1 = ("0" + (date_1.getMonth() + 1)).slice(-2)
            d1 = ("0" + date_1.getDate()).slice(-2)
            y2 = date_2.getFullYear()
            m2 = ("0" + (date_2.getMonth() + 1)).slice(-2)
            d2 = ("0" + date_2.getDate()).slice(-2)

            this.searchDateStart = this.convertDate(date_1)
            this.searchDateEnd = this.convertDate(date_2.setDate(date_2.getDate() - 1))

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
            date_2.setDate(date_2.getDate() + 1)

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
            let check = this.searchDate
            if (this.stockType == 'all') {
                return this.inventoryList
            } else if (this.stockType == 'notZero') {
                return this.inventoryList.filter(function (item) {
                    return item['closing'] > 0
                })
            } else {
                return this.inventoryList.filter(function (item) {
                    return item['closing'] == 0
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
                { key: 'store_name', label : this.$t('store_name'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'item_code', label : this.$t('item_code'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('item'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit_price', label : this.$t('unit_price'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'opening', label : this.$t('opening'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'receiving_qty', label : this.$t('in'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'issueing_qty', label : this.$t('out'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'closing', label : this.$t('closing'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_price', label : this.$t('total_price'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
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