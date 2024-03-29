<template>
    <div class="container-fluid justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('InventoryItem') + ' (ETD)'}}</h3> 
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
                            <option value="10">{{ $t('stationery_items') }}</option>
                        </select>
                    </div>
                    <div class="ml-auto">
                        <button @click="showEtd" class="mdb btn btn-outline-success">{{ $t(`${etdButton}`) + ' ETD' }}</button>
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
                            <b-form-select
                                v-model="perPage"
                                id="perPageSelect"
                                size="sm"
                                :options="pageOptions"
                            ></b-form-select>
                        </b-form-group>                        
                    </div>
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover stacked="md"
                    :items="inventoryList"
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
                    <template v-slot:cell(quantity)="row">
                        {{row.item.quantity + ' ' + row.item.unit}}
                    </template>
                    <template v-slot:cell(stock_cann)="row">
                        {{(row.item.quantity * row.item.cann_per_sheet).toFixed(0)}}
                    </template>
                    <template v-slot:cell(total_weight)="row">
                        {{(row.item.quantity * row.item.weight).toFixed(2)}}
                    </template>
                    <template v-slot:cell(total_price)="row">
                        {{(row.item.quantity * row.item.unit_price).toFixed(2)}}
                    </template>
                    <template v-slot:cell(etd)="row">
                        <span v-if="row.item.etd">
                            {{`${row.item.etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MMM-YYYY')}}
                        </span>
                    </template>
                    <template v-slot:cell(item_image)="row">
                        <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                    </template>
                    <template v-slot:cell(action)="row">
                        <a v-if="etdButton == 'hide'" @click="editDetails(row.item.id, row.item.sn)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
                        <a v-else @click="editDetails(row.item.id, row.item.sn)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
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

                    <!-- Start Edit Details Modal -->
                    <b-modal ref="dataEdit" id="dataEdit" size="sm" title="ETD">                        
                        <div class="modal-body">
                            <label for="etdDate" class="col-form-label mr-2">{{ $t('ETD')}}</label>
                            <input type="date" class="form-control" v-model="etdDate">                                                            
                        </div>                        
                        <template v-slot:modal-footer>
                            <button v-if="etdButton == 'hide'" @click="save('delete')" class="mdb btn btn-outline-danger" :disabled="disable">{{$t('delete')}}</button>
                            <button @click="save('edit')" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                        </template>
                    </b-modal>
                    
                    <!-- End Edit Details Modal -->
                    
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
        return { title: this.$t('InventoryItem') + ' (ETD)'}
    },

    data() {
        return{
            inventoryList : [],
            inventoryListAll : [],
            errors : [],
            etdButton : 'hide',
            etdDate : new Date(),
            store : 3,
            Id : '',
            Index : '',
            title: '',
            disable: false,
            task : [{'store_id' : null,'item_code' : null,'item' : null,'specification' : null, 'grade' : null, 'accounts_code' : null, 'weight' : null, 'cann_per_sheet' : null, 'unit' : null, 'unit_price' : 0, 'item_image' : 'noimage.jpg'}],
            taskId : null,
            Index : null,
            buttonTitle : this.$t('save'),

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
        this.isBusy = true
        fetch(`api/inventoryreceive/1`)
        .then( res => res.json())
        .then(res => {  
            this.inventoryListAll = res['etd'];
            this.isBusy = false;
            this.inventoryList = this.inventoryListByDept;
            this.totalRows = this.inventoryList.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.inventoryList[i]['sn'] = i                
            }
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
            return [year, mnth, day].join("-");
        },

        showEtd() {
            if (this.etdButton == 'hide') {
                this.etdButton = 'show'
                this.inventoryList = this.inventoryListByDept;
                this.totalRows = this.inventoryList.length;
                for (let i = 0; i < this.totalRows; i++) {
                    this.inventoryList[i]['sn'] = i                
                }                
            } else {
                this.etdButton = 'hide'
                this.inventoryList = this.inventoryListByDept;
                this.totalRows = this.inventoryList.length;
                for (let i = 0; i < this.totalRows; i++) {
                    this.inventoryList[i]['sn'] = i                
                }
            }
        },

        store_change() {
            this.inventoryList = this.inventoryListByDept;
            this.totalRows = this.inventoryListByDept.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.inventoryList[i]['sn'] = i                
            }
        },

        editDetails(id, index) {
            this.taskId = id
            this.Index = index
            this.task = this.singleTask
            if(this.task[0]['etd']) this.etdDate = this.task[0]['etd']
            else {
                this.etdDate = new Date()
                this.etdDate = this.convertDate(this.etdDate)
            }
        },

        save(todo) {
            this.disable = !this.disable

            if (todo == 'delete') {
                this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                    timeout: 20000,           
                    position: 'center',
                    buttons: [
                        ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                            axios.patch(`api/invenrecall/${this.taskId}`, {
                                'etd' : null
                            })
                            .then(({data}) => {
                                this.errors = ''
                                this.inventoryList[this.Index]['etd'] = null;
                                this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                                this.disable = !this.disable
                                this.buttonTitle = this.$t('save')
                                this.hideModal()
                            })
                            .catch(err => {
                                if(err.response.status == 422){
                                    this.errors = err.response.data.errors
                                    this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                                } else alert(err.response.data.message)
                                this.disable = !this.disable
                                this.buttonTitle = this.$t('save')
                            }); 

                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button>'+ this.$t('cancel') +'</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ]            
                });
                               
            } else {
                this.buttonTitle = this.$t('saving')
                axios.patch(`api/invenrecall/${this.taskId}`, {
                    'etd' : this.etdDate
                })
                .then(({data}) => {
                    this.errors = ''
                    this.inventoryList[this.Index]['etd'] = this.etdDate;
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.hideModal()
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
            this.$refs['dataEdit'].show()
        },

        hideModal() {
            this.$refs['dataEdit'].hide()
        },


    },

    computed: {
        inventoryListByDept() {
            let id = this.store, check = this.etdButton
            return this.inventoryListAll.filter(function (item) {
                if (check == 'hide') {                    
                    return (item['store_id'] == id && item['etd'])
                } else {
                    return (item['store_id'] == id && !item['etd'])
                }
            })
        },

        singleTask() {
            let id = this.taskId
            return this.inventoryList.filter(function (item) {
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
            if(this.store == 3){
                return [
                    { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('style') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'grade', label : this.$t('grade'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'accounts_code', label : this.$t('accounts_code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('size'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    // { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock_master_sheet', label : this.$t('stock_master_sheet'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('stock_sheet'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock_cann', label : this.$t('stock_cann'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'weight', label : this.$t('weight') + '(kg)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_weight', label : this.$t('total_weight'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'etd', label : this.$t('ETD'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'action', label: this.$t('Action'),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
                ]
            } else {
                return [
                    { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle p-0', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_code', label : this.$t('material') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('material') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('specification') + '(ISR)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    // { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit_price', label : this.$t('unit_price'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_price', label : this.$t('total_price'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'etd', label : this.$t('ETD'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'action', label: this.$t('Action'),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
                ]
            }
            
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

<style >
</style>