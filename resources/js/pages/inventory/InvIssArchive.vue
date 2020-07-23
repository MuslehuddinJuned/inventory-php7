<template>
    <div class="container justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('requisition') + ' ' + $t('archive')}}</h3>
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
                            <b-form-select
                                v-model="perPage"
                                id="perPageSelect"
                                size="sm"
                                :options="pageOptions"
                            ></b-form-select>
                        </b-form-group>                        
                    </div>
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover stacked="md"
                    :items="inventoryissueListCheck"
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
                        {{`${row.item.updated_at}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}}
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

                    <!-- Start view Details Modal -->
                    <b-modal ref="dataView" id="dataView" size="xl" :title="$t('requisition')" no-close-on-backdrop ok-only>
                        <div v-for="task in singleTask" :key="task.id" class="modal-body row m-0 p-0 mb-2">
                            <div class="col-md-6">
                                <span class="font-weight-bold">{{ $t('store_name')}}:</span> {{task.store_name}}
                            </div>
                            <div class="col-md-6">                                
                                <span class="font-weight-bold">{{ $t('requisition_no')}}:</span> {{task.requisition_no}}
                            </div>
                            <div class="col-md-6">
                                <span v-if="task.decision == 'Accepted'" class="font-weight-bold text-success">{{ $t('accept') }}: {{`${task.updated_at}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}}</span>
                                <span v-if="task.decision == 'Rejected'" class="font-weight-bold text-danger">{{ $t('reject') }}: {{`${task.updated_at}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}}</span>
                            </div>
                            <div class="col-md-6">                                
                                <span class="font-weight-bold">{{ $t('remarks')}}:</span> {{task.remarks}}
                            </div>
                            <div class="col-md-12 m-0 p-0 mt-3">
                                <b-table show-empty small striped hover stacked="md" :items="taskDetails" :fields="taskDetailsfieldsView">
                                    <template v-slot:cell(index)="row">
                                        {{ row.index+1 }}
                                    </template>
                                    <template v-slot:cell(inventory_id)="row">
                                        {{ row.item.item_code + ' | ' + row.item.item + ' | ' + row.item.unit}}
                                    </template>
                                    <template v-slot:cell(total_price)="row">
                                        {{ (row.item.quantity * row.item.unit_price).toFixed(2) }}
                                    </template>
                                    <template slot="bottom-row">
                                        <td class="text-white bg-info font-weight-bold text-center">{{$t('grand_total')}}</td>
                                        <td class="text-white bg-info font-weight-bold text-center"></td>
                                        <td class="text-white bg-info font-weight-bold text-center"></td>
                                        <td class="text-white bg-info font-weight-bold text-center"></td>
                                        <td class="text-white bg-info font-weight-bold text-center">{{grand_total}}</td>
                                    </template>
                                </b-table>
                            </div>                              
                        </div>
                        <template v-slot:modal-footer="">
                            <div class="col-md-7">
                                <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                            </div>
                        </template>
                    </b-modal>
                    <!-- End view Details Modal -->
                    
                </div>

                
            </div>
        </div>  
    </div>
</template>

<script>
import uniq from 'lodash/uniq';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('pending') + ' ' + this.$t('requisition') }
    },

    data() {
        return{
            inventoryissueList : [],
            disable: false,
            taskHead : [],
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
        fetch(`api/inventoryissue/1`)
        .then(res => res.json())
        .then(res => {
            this.inventoryissueList = res['Inventoryissue'];
            this.totalRows = this.inventoryissueList.length;
            this.isBusy = false;
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

        viewDetails(id) {
            this.taskHeadId = id
            fetch(`api/recdetails/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetails = res['requisition']
                this.taskHead = this.singleTask
            })
            .then(res =>{
                this.grand_total = this.grand_total_cal
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
                axios.post(`api/inventoryissue/`, {'val' : val, 'id' : this.taskHeadId
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
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }            
        },        

        showModal() {
            this.$refs['dataView'].show()
        },

        hideModal() {
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

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'store_name', label : this.$t('store_name'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'requisition_no', label : this.$t('requisition_no'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'remarks', label : this.$t('remarks'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'updated_at', label : this.$t('date'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'decision', label : this.$t('Action'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        inventoryissueListCheck(){
            for (let i = 0; i < this.inventoryissueList.length; i++) {
                if(this.inventoryissueList[i]['decision'] == 'Rejected') {
                    this.inventoryissueList[i]['_rowVariant'] = 'danger'
                } else {
                    this.inventoryissueList[i]['_rowVariant'] = ''
                }            
            }

            return this.inventoryissueList
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'inventory_id', label : this.$t('item'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit_price', label : this.$t('unit_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_price', label : this.$t('total_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]
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
    }
}
</script>

<style>

</style>