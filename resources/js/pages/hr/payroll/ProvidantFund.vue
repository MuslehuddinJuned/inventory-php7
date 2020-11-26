<template>
    <div class="container justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('providant_fund')}}</h3>
                </div> 
                <div class="card-body">
                    <b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select>
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
                    :items="pfSummaryByDepartment"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    @row-clicked="(item) => viewDetails(item.id, item.pf)"
                    class="table-transition"
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
                    <template v-slot:cell(pf)="row">
                        {{(row.item.pf || 0).toFixed(2)}}
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
        <b-modal ref="dataView" id="dataView" size="lg" :title="$t('providant_fund')" no-close-on-backdrop ok-only>
            <div v-if="pfDetailsById[0]" class="modal-body row m-0 p-0">
                <div class="col-md-3 text-center m-0">
                    <img style="width: 100%; " :src="'/images/employee/' + pfDetailsById[0]['employee_image']" alt="Picture not found">
                </div>
                <div class="col-md-9 row m-0 p-0">
                    <div class="col-4 bg-info text-white">{{$t('name')}}</div><div class="col-8 bg-info text-white">{{pfDetailsById[0]['first_name']}}</div>
                    <div class="col-4 bg-light">ID</div><div class="col-8 bg-light">{{pfDetailsById[0]['employee_id']}}</div>
                    <div class="col-4 bg-info text-white">{{$t('designation')}}</div><div class="col-8 bg-info text-white">{{pfDetailsById[0]['designation']}}</div>
                    <div class="col-4 bg-light">{{$t('department')}}</div><div class="col-8 bg-light">{{pfDetailsById[0]['department']}}</div>
                    <div class="col-4 bg-info text-white">{{$t('joining_date')}}</div><div class="col-8 bg-info text-white">{{pfDetailsById[0]['start_date']}}</div>
                    <div class="col-4 bg-light">{{$t('service_length')}}</div><div class="col-8 bg-light">{{service_length(pfDetailsById[0]['start_date'])}}</div>
                    <div class="col-4 bg-info text-white">{{$t('providant_fund') + ' ' + $t('total')}}</div><div class="col-8 bg-info text-white">{{(grand_total*2 || 0).toFixed(2)}}</div>
                </div>
                <div class="col-md-12 m-0 p-0">
                    <div class="card-body m-0 p-0">
                        <div class="card-header d-flex align-items-center noprint">
                            <b-form-group size="sm" class="mb-0">
                                <b-form-select
                                    v-model="perPageSingle"
                                    id="perPageSelect"
                                    size="sm"
                                    :options="pageOptionsSingle"
                                ></b-form-select>
                            </b-form-group>                        
                        </div>
                        <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover stacked="md"
                        :items="pfDetailsById"
                        :fields="pfDetailsfields"
                        :current-page="currentPage"
                        :per-page="perPageSingle"
                        class="table-transition"
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
                        <template v-slot:cell(pf)="row">
                            {{(row.item.pf || 0).toFixed(2)}}
                        </template>
                        <template v-slot:cell(pf_com)="row">
                            {{(row.item.pf || 0).toFixed(2)}}
                        </template>
                        </b-table>
                        
                        <div class="col-12 mx-auto p-0 noprint">
                            <b-pagination
                            v-model="currentPage"
                            :total-rows="totalRowsSingle"
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
            <template v-slot:modal-footer="">
                <div class="onlyprint fixed-bottom">
                    <div class="mt-3 float-left ml-3 col-2 border-top border-dark text-center">{{$t('prepared_by')}}</div>
                    <div class="mt-3 float-left col-1"></div>
                    <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('checked_by')}}</div>
                    <div class="mt-3 float-left col-1"></div>
                    <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('dept_head')}}</div>
                    <div class="mt-3 float-left col-1"></div>
                    <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('approved_by')}}</div>
                </div>
                <div class="col-md-7">
                    <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                </div>
            </template>
        </b-modal>
        <!-- End view Details Modal -->  
    </div>
</template>

<script>
import uniq from 'lodash/uniq';
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('providant_fund') }
    },

    data() {
        return{
            pfDetails : [],
            pfSummary : [],
            pfId: null,
            roles: [],
            DepartmentList: [],
            DepartmentName: 'Management',
            noprint : '',
            grand_total : 0,

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            totalRows: 1,
            totalRowsSingle: 1,
            currentPage: 1,
            perPage: 25,
            pageOptions: [25, 50, 100],
            perPageSingle: 12,
            pageOptionsSingle: [12, 24, 60],
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        this.isBusy = true;

        fetch(`api/salarysheet/1`)
        .then(res => res.json())
        .then(res => {
            this.pfDetails = res['pfDetails'];
            this.pfSummary = res['pfSummary'];
            this.isBusy = false
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/salarysheet`)
        .then(res => res.json())
        .then(res => {
            this.DepartmentList = res['Department'];
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

        service_length(str) {
            var startDate = new Date(str);
            var endDate = new Date(),
                year = endDate.getFullYear() - startDate.getFullYear(),
                mnth = endDate.getMonth() - startDate.getMonth(),
                day = endDate.getDate() - startDate.getDate()
                if(day < 0) {day += 30; mnth--}
                if(mnth < 0) {mnth += 12; year--}
            return year + " years " + mnth + " months " + day + " days ";
        },

        viewDetails(id, total) {
            this.noprint = 'noprint'
            this.pfId = id
            this.grand_total = total
            this.$refs['dataView'].show()
        },

        hideModal() {
            this.noprint = ''
            this.$refs['dataView'].hide()
        },


    },

    computed: {
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        pfSummaryByDepartment() {
            let array = [], k=0
            for (let i = 0; i < this.pfSummary.length; i++) {
                if (this.pfSummary[i]['department'] == this.DepartmentName) {
                    array[k++] = this.pfSummary[i]
                }                
            }

            this.totalRows = array.length
            return array
        },

        pfDetailsById() {
            let array = [], k=0
            if (!this.pfId) return array
            for (let i = 0; i < this.pfDetails.length; i++) {
                if (this.pfDetails[i]['id'] == this.pfId) {
                    array[k++] = this.pfDetails[i]
                }                
            }

            this.totalRowsSingle = array.length
            return array
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'pf', label : this.$t('providant_fund'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        pfDetailsfields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'year_mnth', label : this.$t('PO No'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'pf', label : this.$t('own_contribution'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'pf_com', label : this.$t('company_contribution'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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