<template>
    <div class="container-fluid justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('monitor_po') + ': ' + $t('ItemReceive') }}</h3>
                </div>
                <div class="card-header row m-0 p-0">
                    <div class="col-md-5">
                        <label for="store" class="col-form-label">{{ $t('store_name')}}</label>
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
                    <div class="col-md-7">
                        <label class="col-form-label">{{ $t('PO No:')}}</label>
                        <div class="input-group">
                            <model-select :options="PoNoListView" class="form-control float-left col-11" v-model="po_no"></model-select>
                            <button @click="po_change" class="btn btn-secondary input-group-append noprint col-1"><b-icon icon="search"></b-icon></button>
                        </div>
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
                    <template v-slot:cell(po_date)="row">
                        {{`${row.item.po_date}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
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
        return { title: this.$t('monitor_po') }
    },

    data() {
        return{
            PoList : [],
            PoListAll : [],
            po_no_list: [],
            po_no : null,
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
        }
    },

    mounted() {
        fetch(`api/polist/1`)
        .then( res => res.json())
        .then(res => {  
            this.po_no_list = res['po_no']              
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

        po_change() {
            this.isBusy = true
            fetch(`api/polist/${this.po_no}`)
            .then( res => res.json())
            .then(res => {  
                this.PoListAll = res['polist']
                this.PoList = this.PoListByPoNo;
                this.totalRows = this.PoList.length;
                this.isBusy = false;                
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        store_change() {
            this.PoList = this.PoListByPoNo;
            this.totalRows = this.PoList.length;
        },


    },

    computed: {
        PoListByPoNo() {
            let po = this.po_no, store = this.store, array = [], j=0 

            for (let i = 0; i < this.PoListAll.length; i++) {
                if(this.PoListAll[i]['po_no'] == po && this.PoListAll[i]['store_id'] == store){
                    array[j] = this.PoListAll[i]
                    if(array[j]['inventory_qty'] < array[j]['total_qty']) {
                        array[j]['_rowVariant'] = 'danger'
                    } else {
                        array[j]['_rowVariant'] = ''
                    }                      
                    j++
                }
            } 
            return array
        },

        PoNoListView(){
            let array = []
            for (let i = 0; i < this.po_no_list.length; i++) {                    
                array.unshift({'value' : this.po_no_list[i]['po_no'], 'text' : this.po_no_list[i]['po_no']})
            }
            return array
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
                { key: 'index', label : '#', sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_no', label : this.$t('PO No'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_date', label : this.$t('PO Date'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item_no', label : this.$t('material_number'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('material_name'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'specification', label : this.$t('specification'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'inventory_qty', label : this.$t('stock'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_qty', label : this.$t('quantity'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]           
            
        },
    },

    components: { ModelSelect }

}
</script>

<style >
</style>