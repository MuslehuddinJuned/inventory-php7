<template>
    <div class="container justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('pending') + ' ' + $t('requisition')}}</h3> 
                    <div class="ml-auto">
                        <button class="mdb btn btn-outline-info float-right">
                            <router-link :to="{name:'inventory.InvIssArchive'}">
                                <fa icon="history" fixed-width />{{ $t('archive') }}
                            </router-link>
                         </button>
                    </div>
                </div> 
                <div class="card-header row m-0">
                    <label for="store" class="col-form-label mr-2">{{ $t('store_name')}}</label>
                    <div style="min-width: 400px;"><model-select :options="store_options" class="form-control" v-model="store"></model-select></div>
                    <button @click="store_change" class="btn ml-3 btn-secondary noprint"><b-icon icon="search"></b-icon></button>
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
                    :items="inventoryissueList"
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
                    <template v-slot:cell(updated_at)="row">
                        {{`${row.item.updated_at}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
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
        <b-modal ref="dataView" id="dataView" size="xl" :title="$t('requisition')" no-close-on-backdrop>
            <div v-for="task in singleTask" :key="task.id" class="modal-body row m-0 p-0 mb-2">
                <div class="col-md-6">
                    <span class="font-weight-bold">{{ $t('store_name')}}:</span> {{taskHead[0]['store_name']}} <br>
                    <span class="font-weight-bold">{{ $t('ETD')}}:</span> <br>
                    <span class="font-weight-bold">{{ $t('remarks')}}:</span> {{taskHead[0]['remarks']}}
                </div>
                <div class="col-md-6 text-right">                                
                    <span class="font-weight-bold">{{ $t('requisition_no')}}:</span> {{taskHead[0]['requisition_no']}}<br>
                    <span class="font-weight-bold">{{ $t('date')}}:</span> {{`${taskHead[0]['created_at']}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}<br>
                    <span class="font-weight-bold">{{ $t('requisition_by')}}:</span> {{taskHead[0]['requisition_by']}}
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" :items="taskDetailsCheck" :fields="taskDetailsfieldsView">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(inventory_id)="row">
                            {{ row.item.item_code + ' | ' + row.item.item + ' | ' + row.item.unit}}
                        </template>
                        <template v-slot:cell(total_price)="row">
                            {{ (row.item.quantity * row.item.unit_price).toFixed(2) }}
                        </template>
                        <template v-slot:cell(issue_etd)="row">
                            {{`${row.item.issue_etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                        </template>
                        <!-- <template slot="bottom-row">
                            <td class="text-white bg-info font-weight-bold text-center">{{$t('grand_total')}}</td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center"></td>
                            <td class="text-white bg-info font-weight-bold text-center">{{grand_total}}</td>
                        </template> -->
                    </b-table>
                </div>                              
            </div>
            <template v-slot:modal-footer>
                <div class="row m-0 p-0 col-md-12">
                    <div class="onlyprint fixed-bottom">
                        <div class="mt-3 float-left ml-3 col-2 border-top border-dark text-center">{{$t('prepared_by')}}</div>
                        <div class="mt-3 float-left col-1"></div>
                        <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('checked_by')}}</div>
                        <div class="mt-3 float-left col-1"></div>
                        <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('dept_head')}}</div>
                        <div class="mt-3 float-left col-1"></div>
                        <div class="mt-3 float-left col-2 border-top border-dark text-center">{{$t('approved_by')}}</div>
                    </div>
                    <div class="col-md-5">
                        <button @click="editDetails(-1)" v-if="checkRoles('ItemIssue_Update')" class="mdb btn btn-outline-danger float-left" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ $t('reject') }}</button>
                    </div>
                    <div class="col-md-7">
                        <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                        <button @click="editDetails(1)" v-if="checkRoles('ItemIssue_Update')" class="mdb btn btn-outline-default float-right" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ $t('accept') }}</button>
                    </div>
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
        return { title: this.$t('pending') + ' ' + this.$t('requisition') }
    },

    data() {
        return{
            inventoryissueList : [],
            roles: [],
            disable: false,
            store: 3,
            store_options: [],
            noprint : '',
            taskHead : [{}],
            taskHeadId : null,
            taskDetails : [],
            grand_total : 0,
            buttonTitle : this.$t('save'),
            stockOverFlow : false,

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
        this.isBusy = true;
        fetch(`api/inventoryissue`)
        .then(res => res.json())
        .then(res => {
            this.inventoryissueListAll = res['Inventoryissue'];
            this.inventoryissueList = this.inventoryissueListByDept
            this.totalRows = this.inventoryissueList.length;
            this.isBusy = false;
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/store`)
        .then(res => res.json())
        .then(res => {
            this.store_options = res['Store'];
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

        store_change() {
            this.inventoryissueList = this.inventoryissueListByDept
            this.totalRows = this.inventoryissueList.length
        },

        viewDetails(id) {
            this.stockOverFlow = false
            this.taskHeadId = id
            this.noprint = 'noprint'
            fetch(`api/recdetails/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetails = res['requisition']
                this.taskHead = this.singleTask
            })
            .then(res =>{
                
                // this.grand_total = this.grand_total_cal
            })
            .catch(err => {
                alert(err.response.data.message)
            })
            this.$refs['dataView'].show()
        },

        editDetails(val) {
            if(this.stockOverFlow == true && val == 1){
                this.$toast.error(this.$t('stock_insifficient'), this.$t('error_alert_title'), {timeout: 3000, position: 'center'})
            } else{
                this.disable = !this.disable;
                this.buttonTitle = this.$t('saving')

                axios.post(`/api/inventoryissue`, {'accept' : val, 'rechead_id' : this.taskHeadId
                })
                .then(res => {
                    let index = 0 
                    for (let i = 0; i < this.inventoryissueList.length; i++) {
                        if(this.inventoryissueList[i]['id'] == this.taskHeadId){
                            index = i
                            break
                        }   
                    }
                    this.inventoryissueList.splice(index, 1);                           
                    this.totalRows = this.inventoryissueList.length;

                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.$refs['dataView'].hide()
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message)
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }            
        },        

        showModal() {
            this.$refs['dataView'].show()
        },

        hideModal() {
            this.noprint = ''
            this.$refs['dataView'].hide()
        },


    },

    computed: {
        singleTask() {
            let id = this.taskHeadId
            return this.inventoryissueList.filter(function (item) {
            return item['id'] == id
            })
        },

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        inventoryissueListByDept() {
            let id = this.store
            return this.inventoryissueListAll.filter(function (item) {
                return item['store_id'] == id
            })
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                // { key: 'store_name', label : this.$t('store_name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'po_no', label : this.$t('PO No'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'requisition_no', label : this.$t('requisition_no'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'requisition_by', label : this.$t('requisition_by'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'remarks', label : this.$t('remarks'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'updated_at', label : this.$t('date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            if(this.store == 3){
                return [
                    { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'po_no', label : this.$t('PO No'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_code', label : this.$t('material_number'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('size'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'grade', label : this.$t('grade'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'weight', label : this.$t('weight') + '/pcs', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock', label : this.$t('stock'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    // { key: 'stock_cann', label : this.$t('stock_cann'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'issue_etd', label : this.$t('ETD'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                ]
            } else if(this.store == 10){
                return [
                    { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'item_code', label : this.$t('material_number'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('specification'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock', label : this.$t('stock'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'price', label : this.$t('unit_price') + '($)', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_price', label : this.$t('total_price') + '($)', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                ]
            } else {
                return [
                    { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                    { key: 'po_no', label : this.$t('PO No') + ' ' + this.$t('code'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_code', label : this.$t('material_number'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('specification'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock', label : this.$t('stock'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'price', label : this.$t('unit_price') + '($)', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_price', label : this.$t('total_price') + '($)', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'issue_etd', label : this.$t('ETD'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'remarks', label : this.$t('remarks'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                ]
            }
        },

        taskDetailsCheck(){
            for (let i = 0; i < this.taskDetails.length; i++) {
                if(this.taskDetails[i]['stock'] < this.taskDetails[i]['quantity']) {
                    this.taskDetails[i]['_rowVariant'] = 'danger'
                    this.stockOverFlow = true
                } else {
                    this.taskDetails[i]['_rowVariant'] = ''
                }            
            }

            return this.taskDetails
        },

        grand_total_cal() {
            let total = 0
            Object.entries(this.taskDetails).forEach(([key, val]) => {
                if(!isNaN(parseFloat(val.unit_price)) && !isNaN(parseFloat(val.quantity)))
                total += parseFloat(val.unit_price*val.quantity)
            });
            return total.toFixed(2);
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