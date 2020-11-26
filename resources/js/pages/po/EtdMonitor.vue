<template>
    <div class="container-fluid justify-content-center">
       <div v-if="checkRoles('monitor_etd_View')" class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('monitor_etd') }}</h3>
                </div> 
                <div class="card-header d-flex align-items-center">
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
                            :data="inventoryList"
                            :fields="json_fields"
                            worksheet="ETD Monitor"
                            name="ETD Monitor.xls">
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
                            <b-form-select
                                v-model="perPage"
                                id="perPageSelect"
                                size="sm"
                                :options="pageOptions"
                            ></b-form-select>
                        </b-form-group>                        
                    </div>
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover responsive
                    :items="inventoryList"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    :no-border-collapse="noCollapse"
                    @filtered="onFiltered"
                    class="table-transition table-bordered"
                    >
                    <template v-slot:table-busy>
                        <div class="text-center text-success my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>{{$t('loading')}}</strong>
                        </div>
                    </template>
                    
                    <template v-slot:head()="scope">
                        <div class="text-wrap">
                        {{ scope.label }}
                        </div>
                    </template>
                    <template v-slot:cell(index)="row">
                        {{ row.index+1 }}
                    </template>
                    <template v-slot:cell(item_code)="row">
                        <div class="text-nowrap">{{row.item.item_code}}: {{row.item.item}}</div>
                    </template>
                    <template v-slot:cell(stock)="row">
                        <div v-if="row.item.store_id == 3" class="text-nowrap">{{(row.item.stock * row.item.cann_per_sheet).toFixed(2)}}</div>
                        <div v-else class="text-nowrap">{{row.item.stock}}</div>
                    </template>
                    <template v-slot:cell(balance)="row">
                        {{ (row.item.inventory_qty - row.item.total_qty).toFixed(2) }}
                    </template>
                    <template v-slot:cell(item_image)="row">
                        <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
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
    </div>
</template>

<script>
import uniq from 'lodash/uniq';
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('monitor_etd') }
    },

    data() {
        return{
            inventoryList : [],
            inventoryListAll : [],
            etdList : [],
            etdQty : [],
            roles: [],
            store: 3,
            storeName: '5-7530: Kitchen Utensil (Stainless Steel)',
            json_fields: {
                'Material No': 'item_code',
                'Material': 'item',
                'Description': 'specification',
                'Unit': 'unit',
                'Stock': 'stock',
            },
            store_options: [],
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
            noCollapse: true,
        }
    },

    mounted() {
        fetch(`api/store`)
        .then(res => res.json())
        .then(res => {
            this.store_options = res['Store'];
        })
        .catch(err => {
            alert(err.response.data.message);
        })
        
        fetch(`api/polist/1`)
        .then( res => res.json())
        .then(res => {  
            this.inventoryListAll = res['Inventory']
            this.etdList = res['etd']
            this.etdQty = res['etdQty'] 
            let stock = 0, varient = {}
            
            for (let j = 0; j < this.inventoryListAll.length; j++) {
                if (this.inventoryListAll[j]['store_id'] == 3) {                    
                    stock = parseFloat(this.inventoryListAll[j]['stock']) * parseFloat(this.inventoryListAll[j]['cann_per_sheet'])
                } else stock = this.inventoryListAll[j]['stock']
                varient = []
                for (let k = 0; k < this.etdQty.length; k++) {
                    if(this.inventoryListAll[j]['id'] === this.etdQty[k]['inventory_id']){
                        this.inventoryListAll[j][this.etdQty[k]['etd']] = this.etdQty[k]['quantity'].toFixed(2)
                        this.inventoryListAll[j][this.etdQty[k]['etd'] + '-product_code'] = this.etdQty[k]['product_code']
                        stock -= this.etdQty[k]['quantity']
                        if (stock < 0) {
                            this.inventoryListAll[j][this.etdQty[k]['etd'] + '-Balance'] = stock.toFixed(2)
                            varient[this.etdQty[k]['etd'] + '-Balance'] = 'danger';
                        } else {
                            this.inventoryListAll[j][this.etdQty[k]['etd'] + '-Balance'] = '+' + stock.toFixed(2)
                        }

                    } else { continue }
                    this.inventoryListAll[j]['_cellVariants'] = varient
                }            
            }
            this.inventoryList = this.inventoryListByDept;
            this.totalRows = this.inventoryList.length;            
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

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [mnth, day].join("-");
        },

        store_change() {
            this.inventoryList = this.inventoryListByDept;
            this.totalRows = this.inventoryListByDept.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.inventoryList[i]['sn'] = i                
            }
        },
    },

    computed: {
        inventoryListByDept() {
            let id = this.store

            return this.inventoryListAll.filter(function (item) {
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
            let data = []

            data = [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item_code', label : this.$t('material') + ' ' + this.$t('code'), stickyColumn: true, sortable: true, class: 'text-center align-middle bg-white', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'item', label : this.$t('material') + ' ' + this.$t('name'), stickyColumn: true, sortable: true, class: 'text-center align-middle bg-white', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'specification', label : this.$t('specification'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'stock', label : this.$t('stock'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]  
            
            for (let i = 0; i < this.etdList.length; i++) {
                data.push({ 'key': this.etdList[i]['etd'], 'label' : this.convertDate(this.etdList[i]['etd']),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })
                data.push({ 'key': this.etdList[i]['etd'] + '-Balance', 'label' : this.$t('balance'),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })
                // data.push({ 'key': this.etdList[i]['etd'] + '-product_code', 'label' : this.$t('style') + ' ' + this.$t('code'),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })

                this.json_fields[this.convertDate(this.etdList[i]['etd'])] = this.etdList[i]['etd']
                this.json_fields[this.convertDate(this.etdList[i]['etd']) + '-Balance'] = this.etdList[i]['etd'] + '-Balance'
                this.json_fields[this.convertDate(this.etdList[i]['etd']) + '-Style Code'] = this.etdList[i]['etd'] + '-product_code'
            }

            return data                        
        },
    },

    components: { ModelSelect }

}
</script>

<style >
</style>