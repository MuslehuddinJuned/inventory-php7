<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('parts_production_report')}}</h3>
                </div> 
                <div class="card-body">
                    <div class="col-md-6 float-left">
                        <label for="po_no">{{$t('department')}}</label>
                        <select @change="departmentChange" class="form-control" v-model="department">
                            <option>{{ $t('assembly') }}</option>
                            <option>{{ $t('wash') }}</option>
                            <option>{{ $t('polish') }}</option>
                            <option>{{ $t('injection') }}</option>
                            <option>{{ $t('cutting') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 float-left">
                        <label for="po_no">{{$t('production_date')}}</label>
                        <div class="col-md-12 pl-0 input-group">
                            <input type="date" class="form-control" v-model="prodDate">
                            <div @click="searchByDate" class="input-group-append input-group-text pointer noprint"><b-icon icon="search"></b-icon></div>
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
                    </div>
                    <b-table id="table-transition" primary-key="productdetails_id" :busy="isBusy" show-empty small striped hover responsive
                    :items="ProductionByDeparment"
                    :fields="fields"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    class="table-transition table-bordered"
                    style="cursor : pointer"
                    >
                    <template v-slot:table-busy>
                        <div class="text-center align-middle text-success my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>{{$t('loading')}}</strong>
                        </div>
                    </template>
                    <template v-slot:cell(item_image)="row">
                        <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                    </template>
                    <template v-slot:cell(parts_qty)="row">
                        {{(row.item.parts_qty || 0) * (row.item.po_qty || 0)}}
                    </template>
                    <template v-slot:cell(complete)="row">
                        {{row.item.complete  = parseFloat(row.item.total_prod_qty || 0) + parseFloat(row.item.quantity || 0)}}
                    </template>
                    <template v-slot:cell(balance)="row">
                        {{row.item.balance = row.item.complete - (row.item.parts_qty || 0) * (row.item.po_qty || 0)}}
                    </template>
                    </b-table>
                </div>
            </div>
        </div> 
    </div>
</template>

<script>
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('parts_production_report') }
    },

    data() {
        return{
            PoList : [],
            po_no: null,
            Production: [],
            production_id: null,
            ProductionByDeparment: [],
            roles: [],
            department: this.$t('assembly'),
            prodDate: this.convertDate(new Date()),
            noprint : '',
            disable: false,
            waiting: false,
            buttonTitle : this.$t('save'),

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        fetch(`api/prodparts`)
        .then(res => res.json())
        .then(res => {
            this.PoList = res['PoList']
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
            return [year, mnth, day].join("-");
        },

        fetchData() {
            this.isBusy = true
            fetch(`api/prodparts/${this.prodDate}`)
            .then(res => res.json())
            .then(res => {
                this.Production = res['Production']
                this.ProductionByDeparment = this.ProductionByDeparmentMethod
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        searchByDate() {
            this.fetchData()
        },

        departmentChange() {
            this.ProductionByDeparment = []
        },
    },

    computed: {
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        ProductionByDeparmentMethod() {
            let array = [], k=0

            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['department'] == this.department) {
                    array[k++] = this.Production[i]
                }                
            }
            return array 
        },

        // Total() {
        //     let t = 0
        //     for (let i = 0; i < this.productionByStore.length; i++) {
        //         t += (parseFloat(this.productionByStore[i]['prod_qty']) || 0)                
        //     } return t
        // },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                // , po_no
                { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_no', label : 'PO no', class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'parts_name', label : this.$t('name'), class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'parts_description', label : this.$t('description'), class: 'text-center align-middle', tdClass: 'p-0', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'parts_qty', label : this.$t('quantity'), class: 'text-center align-middle', tdClass: 'p-0', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'complete', label : this.$t('complete'), class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'balance', label : this.$t('balance'), class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'quantity', label : this.$t('production'), class: 'text-center align-middle', sortable: true, thClass: 'text-nowrap border-top border-dark font-weight-bold'},
            ]
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

<style>

</style>