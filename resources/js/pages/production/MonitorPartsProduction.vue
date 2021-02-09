<template>
    <div class="container-fluid justify-content-center">
       <div v-if="checkRoles('production_View')" class="col-md-12">
           <div class="card filterable">
                <div class="card-header align-items-center">
                    <div class="col-md-8 float-left">
                        <h3 class="panel-title float-left">{{ $t('monitor_parts_production') }}</h3>
                    </div>
                    <div class="col-md-4 float-left">
                        Old ETD
                        <b-form-select @change="etd_change" v-model="etdOld" :options="etdOldList" text-field="etd" value-field="etd"></b-form-select>
                    </div>
                </div> 
                <div class="card-body d-flex align-items-center">
                    <select @change="departmentChange" class="form-control mr-3" v-model="department">
                        <option value="assembly">{{ $t('assembly') }}</option>
                        <option value="wash">{{ $t('wash') }}</option>
                        <option value="polish">{{ $t('polish') }}</option>
                        <option value="injection">{{ $t('injection') }}</option>
                        <option value="cutting">{{ $t('cutting') }}</option>
                    </select>
                    <b-form-select @change="buyer_change" v-model="buyer" :options="buyerList" text-field="buyer" value-field="buyer"></b-form-select>
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            :title="'Department: ' + department + 'Buyer' + buyer"
                            :data="stockList"
                            :fields="json_fields"
                            worksheet="Monitor Parts Production"
                            name="Monitor Parts Production.xls">
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
                    :items="stockList"
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
                    <template v-slot:cell(product_image)="row">
                        <a :href="'/images/product/' + row.item.product_image"><b-img :src="'/images/product/' + row.item.product_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
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
        return { title: this.$t('monitor_parts_production') }
    },

    data() {
        return{
            stockList : [],
            stock : [],
            etdList : [],
            etdOld: 'Old ETD',
            etdOldList: [],
            buyerList: [],
            etdQty : [],
            roles: [],
            buyer: 'APL',
            department: 'injection',
            json_fields: {
                'Style Code': 'product_code',
                'Parts Name': 'parts_name',
                'Unit': 'unit',
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
        this.fetchData()
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

        fetchData() {
            this.isBusy = true
            fetch(`api/polist/monitor/${this.department}/${this.etdOld}`)
            .then( res => res.json())
            .then(res => {  
                this.stock = res['stock']
                this.etdList = res['etd']
                this.etdOldList = res['etdOld']
                this.etdQty = res['etdQty'] 
                this.buyerList = res['buyer']
                let stock = 0, varient = {}
                
                for (let j = 0; j < this.stock.length; j++) {
                    stock = this.stock[j]['stock']
                    varient = []
                    for (let k = 0; k < this.etdQty.length; k++) {
                        if(this.stock[j]['id'] === this.etdQty[k]['id']){
                            this.stock[j][this.etdQty[k]['etd']] = this.etdQty[k]['quantity'].toFixed(2)
                            // this.stock[j][this.etdQty[k]['etd'] + '-product_code'] = this.etdQty[k]['product_code']
                            stock -= this.etdQty[k]['quantity']
                            if (stock < 0) {
                                this.stock[j][this.etdQty[k]['etd'] + '-Balance'] = stock.toFixed(2)
                                varient[this.etdQty[k]['etd'] + '-Balance'] = 'danger';
                            } else {
                                this.stock[j][this.etdQty[k]['etd'] + '-Balance'] = '+' + stock.toFixed(2)
                            }

                        } else { continue }
                        this.stock[j]['_cellVariants'] = varient
                    }            
                }
                this.stockList = this.stockListByBuyer
                this.totalRows = this.stockList.length; 
                this.isBusy = false           
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        departmentChange() {
            this.fetchData()
        },

        etd_change(){
            this.fetchData()
        },

        buyer_change() {
            this.stockList = this.stockListByBuyer;
            this.totalRows = this.stockList.length;
        },
    },

    computed: {
        stockListByBuyer() {
            let id = this.buyer
            return this.stock.filter(function (item) {
                return item['buyer'] == id
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
            //  parts_name, parts_description, (SUM(req_qty)-prod_qty)stock, unit
            data = [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle text-nowrap', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), stickyColumn: true, sortable: true, class: 'text-center align-middle bg-white text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
            ] 
            if (this.department != 'assembly') {
                data.push({ key: 'parts_name', label : this.$t('name'), stickyColumn: true, sortable: true, class: 'text-center align-middle bg-white text-nowrap', thClass: 'border-top border-dark font-weight-bold'})
                data.push({ key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'})
            } 
            
            data.push({ key: 'stock', label : this.$t('stock'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'})
            
            for (let i = 0; i < this.etdList.length; i++) {
                data.push({ 'key': this.etdList[i]['etd'], 'label' : this.convertDate(this.etdList[i]['etd']),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })
                data.push({ 'key': this.etdList[i]['etd'] + '-Balance', 'label' : this.$t('balance'),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })
                // data.push({ 'key': this.etdList[i]['etd'] + '-product_code', 'label' : this.$t('style') + ' ' + this.$t('code'),'sortable': true, 'class': 'text-center align-middle', 'thClass': 'border-top border-dark font-weight-bold' })

                this.json_fields[this.convertDate(this.etdList[i]['etd'])] = this.etdList[i]['etd']
                this.json_fields[this.convertDate(this.etdList[i]['etd']) + '-Balance'] = this.etdList[i]['etd'] + '-Balance'
                // this.json_fields[this.convertDate(this.etdList[i]['etd']) + '-Style Code'] = this.etdList[i]['etd'] + '-product_code'
            }

            return data                        
        },
    },

    components: { ModelSelect }

}
</script>

<style >
</style>