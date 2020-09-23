<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('InventoryItem') }}</h3>                     
                    <div class="ml-auto">
                        <button v-if="checkRoles('InventoryItem_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
                <div class="card-header d-flex align-items-center">
                    <label for="store" class="col-form-label mr-2">{{ $t('store_name')}}</label>
                    <div style="min-width: 400px;"><model-select :options="store_options" class="form-control" v-model="store"></model-select></div>
                    <button @click="store_change" class="btn ml-3 btn-secondary noprint"><b-icon icon="search"></b-icon></button>
                    <div class="ml-auto" :class="noprint">
                        <b-form-checkbox @change="showEtd" v-model="etd" class="noprint"> ETD </b-form-checkbox>
                        <div class="input-group">
                            <div class="input-group-prepend onlyprint mr-3">ETD</div>
                            <input type="date"  v-model="etdDate" class="">
                            <button @click="showEtd('search')" class="btn btn-secondary input-group-append noprint"><b-icon icon="search"></b-icon></button>
                        </div>
                    </div>
                </div> 
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            :title="storeName"
                            :data="inventoryList"
                            :fields="json_fields"
                            worksheet="Inventory Item"
                            name="Inventory Item.xls">
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
                    <template v-slot:cell(stock)="row">
                        {{row.item.stock + ' ' + row.item.unit}}
                    </template>
                    <template v-slot:cell(stock_cann)="row">
                        {{(row.item.stock * row.item.cann_per_sheet).toFixed(0)}}
                    </template>
                    <template v-slot:cell(total_weight)="row">
                        {{(row.item.stock * row.item.weight).toFixed(2)}}
                    </template>
                    <template v-slot:cell(total_price)="row">
                        {{(row.item.stock * row.item.unit_price).toFixed(2)}}
                    </template>
                    <template v-slot:cell(item_image)="row">
                        <a :href="'/images/item/' + row.item.item_image"><b-img :src="'/images/item/' + row.item.item_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                    </template>
                    <template v-slot:cell(action)="row">
                        <div v-if="etd">{{`${row.item.etd}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}</div>
                        <div v-else>
                            <a v-if="checkRoles('InventoryItem_Update')" @click="editDetails(row.item.id, row.item.sn)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
                            <a v-if="row.item.stock < 1 && checkRoles('InventoryItem_Delete')" @click="destroy(row.item.id, row.item.sn)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                        </div>
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

                    <!-- Start Edit Details Modal -->
                    <b-modal ref="dataEdit" id="dataEdit" size="xxl" :title="title" no-close-on-backdrop>
                        
                        <div class="modal-body row m-0 p-0">
                            <div class="col-md-8 row m-0 p-0">
                                <div class="col-md-12">
                                    <label for="store" class="col-form-label mr-2">{{ $t('store_name')}}</label>
                                    <div>
                                        <b-form-select id="store" v-model="store" :options="store_options" disabled></b-form-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label v-if="store == 3" class="col-form-label">{{ $t('style') + ' ' + $t('code')}}</label>
                                    <label v-else class="col-form-label">{{ $t('material') + ' ' + $t('code')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['item_code']">
                                    <span v-if="errors.item_code" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}} <br></span>
                                </div>
                                <div class="col-md-6">
                                    <label v-if="store == 3" class="col-form-label">{{ $t('style') + ' ' + $t('name')}}</label>
                                    <label v-else class="col-form-label">{{ $t('material') + ' ' + $t('name')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['item']">
                                </div>
                                <div v-if="store == 3" class="col-md-6">
                                    <label class="col-form-label">{{ $t('grade')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['grade']">
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('accounts_code')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['accounts_code']">
                                </div>
                                <div v-if="store == 3" class="col-md-6">
                                    <label class="col-form-label">{{ $t('cann_per_sheet')}}</label>
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control" v-model="task[0]['cann_per_sheet']">
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('weight')}} (kg)</label>
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control" v-model="task[0]['weight']">
                                </div>
                                <div class="col-md-6">
                                    <label v-if="store == 3" class="col-form-label">{{ $t('size')}}</label>
                                    <label v-else class="col-form-label">{{ $t('specification')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['specification']">
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('unit')}}</label>
                                    <input list="UnitList" class="form-control" v-model="task[0]['unit']">
                                    <datalist id="UnitList">
                                        <option v-for="unit in unitlistview" :key="unit.unit">{{ unit }}</option>
                                    </datalist>
                                    <span v-if="errors.unit" class="error text-danger"> {{$t('required_field')}} <br></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group m-auto col-md-12 text-center float-center">
                                    <img id="blah" style="width: 70%;" :src="src + imageName" alt="product image" />
                                </div>
                                <div class="fileBrowser col-md-12 p-0 m-0">
                                    <div class="form-group col-md-12 upload-btn-wrapper p-0 m-0 text-center" id="item_image">
                                        <button class="mdb btn btn-outline-success mx-auto">{{$t('browse')}}</button>
                                        <input type="file" @change="handleFileUpload" id="upload" name="ItemImage" class="pointer mx-auto"/>
                                    </div>
                                </div>
                            </div>
                                                            
                        </div>                        
                        <template v-slot:modal-footer="">
                            <button @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                            <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
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
import { ModelSelect } from 'vue-search-select'
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('InventoryItem') }
    },

    data() {
        return{
            inventoryList : [],
            inventoryListAll : [],
            roles: [],
            errors : [],
            store : 3,
            storeName: '5-7530: Kitchen Utensil (Stainless Steel)',
            json_fields: {
                'Material No': 'item_code',
                'Material': 'item',
                'Description': 'specification',
                'Grade': 'grade',
                'Stock': 'stock',
                'Unit': 'unit',
                'Weight': 'weight',
                'Total Weight': 'total_weight',
                'Unit Price': 'unit_price',
                'Total Price': 'total_price',
            },
            store_options: [],
            noprint: 'noprint',
            etd : false,
            etdDate : new Date(),
            colTitle : 'Action',
            Id : '',
            Index : '',
            title: '',
            disable: false,
            task : [{'store_id' : null,'item_code' : null,'item' : null,'specification' : null, 'grade' : null, 'accounts_code' : null, 'weight' : null, 'cann_per_sheet' : null, 'unit' : null, 'unit_price' : 0, 'item_image' : 'noimage.jpg'}],
            taskId : null,
            Index : null,
            buttonTitle : this.$t('save'),

            src : '/images/item/',
            save_image : null,

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
        this.src = '/images/item/'
        this.etdDate = this.convertDate(this.etdDate)

        fetch(`api/store`)
        .then(res => res.json())
        .then(res => {
            this.store_options = res['Store'];
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/inventory`)
        .then( res => res.json())
        .then(res => {  
            this.inventoryListAll = res['Inventory'];
            
            this.inventoryList = this.inventoryListByDept;
            this.totalRows = this.inventoryList.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.inventoryList[i]['sn'] = i                
            }
            this.isBusy = false;                
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

        addDetails(){
            this.taskId = null
            this.title = this.$t('InsertNewItem')
            this.task = [{'store_id' : null,'item_code' : null,'item' : null,'specification' : null, 'grade' : null, 'accounts_code' : null, 'weight' : null, 'cann_per_sheet' : null, 'unit' : null, 'unit_price' : 0, 'item_image' : 'noimage.jpg'}]
        },

        store_change() {
            this.inventoryList = this.inventoryListByDept;
            this.totalRows = this.inventoryListByDept.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.inventoryList[i]['sn'] = i                
            }

            for (let i = 0; i < this.store_options.length; i++) {
                if (this.store_options[i]['value'] == this.store) {
                    this.storeName = this.store_options[i]['text'] 
                    break    
                }       
            }
        },

        showEtd(val){
            if(!val) this.etd = !this.etd
            
            this.isBusy = true
            this.src = '/images/item/'
            let data = null
            if(this.etd) {
                data = 'etd'
                this.colTitle = 'ETD'
                this.noprint = ''
            }
            else {
                data = 'inventory'
                this.colTitle = 'Action'
                this.noprint = 'noprint'
            }
            fetch(`api/${data}`)
            .then( res => res.json())
            .then(res => {  
                this.inventoryListAll = res['Inventory'];
                this.inventoryList = this.inventoryListByDept;
                this.totalRows = this.inventoryList.length;
                for (let i = 0; i < this.totalRows; i++) {
                    this.inventoryList[i]['sn'] = i                
                }
                this.isBusy = false;                
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        handleFileUpload(e) {
            let file = e.target.files[0];
            var fileReader = new FileReader();
            
            if(file['size'] <= 262144 &&  file['type'].split('/')[0]=='image' ){          //256 KB ~~ 262144 Byte
                fileReader.onload = (e) => {
                    this.src = '';
                    this.task[0]['item_image'] = e.target.result;
                    this.save_image = e.target.result;
                }
            } else {
                let warningMessages;
                file['size'] > 262144 ? warningMessages = this.$t('image_size_warning'): warningMessages = this.$t('image_format_warning');
                this.$toast.warning(warningMessages, this.$t('error_alert_title'), {
                    timeout: 10000,          
                    position: 'center',
                })
            }

            fileReader.readAsDataURL(e.target.files[0]);
        },

        editDetails(id, index) {
            this.src = '/images/item/'
            this.save_image = null
            this.title = this.$t('UpdateItem')
            this.taskId = id
            this.Index = index
            this.task = this.singleTask
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.task[0]['item_image'] = this.save_image
            this.task[0]['store_id'] = this.store
            let options = { headers: {'enctype': 'multipart/form-data'} };

            if(this.taskId == null){
                axios.post(`api/inventory`, this.task[0], options)
                .then(({data}) =>{
                    this.errors = ''
                    this.inventoryList.unshift(data.InventoryList)
                    this.totalRows = this.inventoryList.length;
                    for (let i = 0; i < this.totalRows; i++) {
                        this.inventoryList[i]['sn'] = i                
                    } 
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    }
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    alert(err.response.data.message)                      
                })
            } else {
                axios.patch(`api/inventory/${this.taskId}`, this.task[0], options)
                .then(({data}) => {
                    this.errors = ''
                    this.src = '/images/item/'
                    this.task[0]['item_image'] = data.fileName
                    this.inventoryList[this.Index] = this.task[0];
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                    }
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }
        },

        destroy(id, index) {
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/inventory/${id}`)
                        
                        .then(res => {
                            this.inventoryList.splice(index, 1);
                            this.totalRows = this.inventoryList.length

                            for (let i = 0; i < this.totalRows; i++) {
                                this.inventoryList[i]['sn'] = i                
                            }
                            for (let i = 0; i < this.inventoryListAll.length; i++) {
                                if(this.inventoryListAll[i]['id'] == id){
                                    this.inventoryListAll.splice(i, 1);
                                    break
                                }               
                            }
                        })
                        .catch(err => {
                            alert(err.response.data.message);                       
                        });

                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }, true],
                    ['<button>'+ this.$t('cancel') +'</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }],
                ]            
            });
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
            let id = this.store, etdDate = this.etdDate
            if (this.etd) {
                return this.inventoryListAll.filter(function (item) {
                    return (item['store_id'] == id && item['etd'] == etdDate)
                })
            } else {
                return this.inventoryListAll.filter(function (item) {
                    return item['store_id'] == id
                })
            }
        },

        imageName() {
            if(this.task[0]['item_image'] == null || this.task[0]['item_image'] == 'noimage.jpg') {
                this.task[0]['item_image'] = null
                return 'noimage.jpg'
            }
            else return this.task[0]['item_image']
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
            let action = []
            if ((this.checkRoles('InventoryItem_Update') || this.checkRoles('InventoryItem_Delete')) && this.colTitle == 'Action') {
                action = { key: 'action', label: this.$t(`${this.colTitle}`),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
            } else if (this.colTitle != 'Action') {
                action = { key: 'action', label: this.$t(`${this.colTitle}`),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
            }
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
                    { key: 'stock', label : this.$t('stock_sheet'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock_cann', label : this.$t('stock_cann'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'weight', label : this.$t('weight') + '(kg)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_weight', label : this.$t('total_weight'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    action
                ]
                
            } else {
                return [
                    { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item_code', label : this.$t('material') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'item', label : this.$t('material') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'specification', label : this.$t('specification') + '(ISR)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    // { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'accounts_code', label : this.$t('accounts_code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'stock', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'weight', label : this.$t('weight') + '(kg)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_weight', label : this.$t('total_weight'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'unit_price', label : this.$t('unit_price') + '($)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    { key: 'total_price', label : this.$t('total_price') + '($)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                    action
                ]
            }
            
        },

        store_namelistview() {
            return uniq(this.inventoryList.map(({ store_name }) => store_name))
        },

        unitlistview() {
            return uniq(this.inventoryList.map(({ unit }) => unit))
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