<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('production_plan')}}</h3>
                </div> 
                <div class="card-body">
                    <div class="col-md-4 float-left">
                        <label for="buyer">{{ $t('buyer') }}</label>
                        <b-form-select id="buyer" v-model="buyer" :options="buyerlistview" text-field="buyer" value-field="buyer"></b-form-select>
                    </div>
                    <div class="col-md-4 float-left">
                        <label >{{ $t('department') }}</label>
                        <select class="form-control" v-model="department">
                            <option value="assembly">{{ $t('assembly') }}</option>
                            <option value="polish">{{ $t('polish') }}</option>
                            <option value="spray">{{ $t('spray') }}</option>
                            <option value="injection">{{ $t('injection') }}</option>
                            <option value="cutting">{{ $t('cutting') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 float-left input-group">
                        <label class="col-12 pl-0">ETD</label>
                        <input type="date" class="form-control" v-model="searchDate">
                        <div @click="searchByDate" class="input-group-append input-group-text pointer"><b-icon icon="search"></b-icon></div>
                    </div>
                    
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            :title="'Production Plan, ETD: ' + searchDate"
                            :data="productionByDept"
                            :fields="json_fields"
                            worksheet="Production Plan"
                            name="Production Plan.xls">
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
                    </div>
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover responsive
                    :items="productionByDept"
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
                    <template v-slot:cell(index)="row">
                        {{ row.index+1 }}
                    </template>
                    <template v-slot:cell(hourly_target)="row">
                        {{(row.item.hourly_target || 0).toFixed(0)}}
                    </template>
                    <template v-slot:cell(req_hour)="row">
                        {{(row.item.req_hour || 0).toFixed(0)}}
                    </template>
                    <template v-slot:cell(req_manhour)="row">
                        {{(row.item.req_manhour || 0).toFixed(0)}}
                    </template>
                    <template v-slot:cell(req_day)="row">
                        {{(row.item.req_day || 0).toFixed(0)}}
                    </template>
                    <template slot="bottom-row">
                        <td class="text-white bg-info font-weight-bold text-center" colspan="5">Total</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_quantity.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_ctn.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center"></td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_cbm.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center" colspan="4"></td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_total_prod.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_balance.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center"></td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_manpower.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_req_hour.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_req_manhour.toFixed(0)}}</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{t_req_day.toFixed(0)}}</td>
                    </template>
                    </b-table>
                </div>
            </div>
        </div> 
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('production_plan') }
    },

    data() {
        return{
            Production: [],
            buyerlistview: [],
            buyer: 'All',
            department: 'assembly',
            t_quantity: 0,
            t_ctn: 0,
            t_cbm: 0,
            t_total_prod: 0,
            t_balance: 0,
            t_manpower: 0,
            t_req_day: 0,
            t_req_hour: 0,
            t_req_manhour: 0,
            searchDate: this.convertDate(new Date()),
            noprint : '',

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            filter: null,
            filterOn: [],
            isBusy: false,

            json_fields: {
                'Lot': 'lot',
                'Container': 'container',
                'PO No': 'po_no',
                'Buyer': 'buyer',
                'Item': 'product_code',
                'Quantity': 'quantity',
                'Carton': 'ctn',
                'Pcs/Ctn': 'pcs_per_ctn',
                'CBM': 'cbm',
                'Shipment Booking': 'shipment_booking',
                'Inspection Date': 'inspection_date',
                'Loading Date': 'loading_date',
                'ETD': 'etd',
                'Production': 'total_prod',
                'Balance': 'balance',
                'Hourly Target': 'hourly_target',
                'Manpower': 'manpower',
                'Req. Hour': 'req_hour',
                'Req. Man-Hour': 'req_manhour',
                'Req. Day': 'req_day',
            },
        }
    },

    mounted() {
        this.isBusy = true
        fetch(`api/polist/${this.searchDate}`)
        .then(res => res.json())
        .then(res => {
            this.buyerlistview = res['buyer']
            this.Production = res['Production']
            this.buyerlistview.unshift({'buyer': 'All'})
            this.isBusy = false
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

        searchByDate() {
            this.isBusy = true
            fetch(`api/polist/${this.searchDate}`)
            .then(res => res.json())
            .then(res => {
                this.Production = res['Production']
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        change_buyer() {
            this.productList = this.productListByBuyer
            this.totalRows = this.productList.length
        },
    },

    computed: {
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        productionByDept() {
            let array = [], k=0
            this.t_quantity = 0
            this.t_ctn = 0
            this.t_cbm = 0
            this.t_total_prod = 0
            this.t_balance = 0
            this.t_manpower = 0
            this.t_req_day = 0
            this.t_req_hour = 0
            this.t_req_manhour = 0
            if (this.buyer == 'All'){
                for (let i = 0; i < this.Production.length; i++) {
                    if ((this.Production[i]['department'] == this.department || this.Production[i]['department'] == null)) {
                        array[k++] = this.Production[i]
                        this.t_quantity += (this.Production[i]['quantity'] || 0)
                        this.t_ctn += (this.Production[i]['ctn'] || 0)
                        this.t_cbm += (this.Production[i]['cmb'] || 0)
                        this.t_total_prod += (this.Production[i]['total_prod'] || 0)
                        this.t_balance += (this.Production[i]['balance'] || 0)
                        this.t_manpower += (this.Production[i]['manpower'] || 0)
                        this.t_req_day += (this.Production[i]['req_day'] || 0)
                        this.t_req_hour += (this.Production[i]['req_hour'] || 0)
                        this.t_req_manhour += (this.Production[i]['req_manhour'] || 0)
                    }                
                }
            } else {
                for (let i = 0; i < this.Production.length; i++) {
                    if (this.Production[i]['buyer'] == this.buyer && (this.Production[i]['department'] == this.department || this.Production[i]['department'] == null)) {
                        array[k++] = this.Production[i]
                        this.t_quantity += (this.Production[i]['quantity'] || 0)
                        this.t_ctn += (this.Production[i]['ctn'] || 0)
                        this.t_cbm += (this.Production[i]['cmb'] || 0)
                        this.t_total_prod += (this.Production[i]['total_prod'] || 0)
                        this.t_balance += (this.Production[i]['balance'] || 0)
                        this.t_manpower += (this.Production[i]['manpower'] || 0)
                        this.t_req_day += (this.Production[i]['req_day'] || 0)
                        this.t_req_hour += (this.Production[i]['req_hour'] || 0)
                        this.t_req_manhour += (this.Production[i]['req_manhour'] || 0)
                    }                
                }
            }
            
            return array 
        },

        Total() {
            let t = 0, a= 0
            for (let i = 0; i < this.productionByDept.length; i++) {
                t += (parseFloat(this.productionByDept[i]['total_target']) || 0)                
                a += (parseFloat(this.productionByDept[i]['daily_prod']) || 0)                
            } 
            return (a*100/t).toFixed(0)
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            // lot, container, ctn, pcs_per_ctn, cbm, shipment_booking, loading_date, inspection_date, quantity, remarks, po_date, po_no, etd, producthead_id buyer, product_style, product_code, product_image, department, smv, manpower, total_prod, balance, hourly_target, req_hour, req_manhour, req_day
            return [
                // { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                // { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'lot', label : this.$t('lot'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'container', label : this.$t('container'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'po_no', label : 'PO No', sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'product_code', label : this.$t('item'), sortable: true, stickyColumn: true, class: 'bg-white text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'ctn', label : this.$t('ctn'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'pcs_per_ctn', label : this.$t('pcs_per_ctn'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'cbm', label : 'CBM', sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'shipment_booking', label : this.$t('shipment_booking'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'inspection_date', label : this.$t('inspection_date'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'loading_date', label : this.$t('loading_date'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'etd', label : 'ETD', sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_prod', label : this.$t('production'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'balance', label : this.$t('balance'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'hourly_target', label : this.$t('hourly_target'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'manpower', label : this.$t('manpower'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'req_hour', label : this.$t('req_hour'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'req_manhour', label : this.$t('req_manhour'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'req_day', label : this.$t('req_day'), sortable: true, class: 'text-center align-middle text-nowrap', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    },
}
</script>

<style>

</style>