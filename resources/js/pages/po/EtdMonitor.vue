<template>
    <div class="container-fluid justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('monitor_etd') }}</h3>
                </div> 
                <div class="card-header d-flex align-items-center">
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
            store: 3,
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
        fetch(`api/polist/1`)
        .then( res => res.json())
        .then(res => {  
            this.inventoryListAll = res['Inventory']
            this.etdList = res['etd']
            this.etdQty = res['etdQty'] 
            let stock = 0, varient = {}
            
            for (let j = 0; j < this.inventoryListAll.length; j++) {
                stock = this.inventoryListAll[j]['stock']
                varient = []
                for (let k = 0; k < this.etdQty.length; k++) {
                    if(this.inventoryListAll[j]['id'] === this.etdQty[k]['inventory_id']){
                        this.inventoryListAll[j][this.etdQty[k]['etd']] = this.etdQty[k]['quantity']
                        stock -= this.etdQty[k]['quantity']
                        if (stock < 0) {
                            varient[this.etdQty[k]['etd'] + '-Balance'] = 'danger';
                        }
                        this.inventoryListAll[j][this.etdQty[k]['etd'] + '-Balance'] = stock

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
                data.push({ 'key': this.etdList[i]['etd'], label : this.convertDate(this.etdList[i]['etd']),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })
                data.push({ 'key': this.etdList[i]['etd'] + '-Balance', label : this.convertDate(this.etdList[i]['etd']) + ' Balance','sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold align-middle' })
            }

            return data
            
        },
    },

    components: { ModelSelect }

}
</script>

<style >
</style>