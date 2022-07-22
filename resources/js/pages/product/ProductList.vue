<template>
    <div class="container justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('product_list') }}</h3> 
                    <div v-if="buyer" class="ml-auto">
                        <button v-if="checkRoles('product_details_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div> 
                <div class="card-body">
                    <label for="buyer" class="mr-4"><h5 class="font-weight-bold">{{ $t('buyer') }}</h5></label>
                    <b-form-select @change="change_buyer" id="buyer" v-model="buyer" :options="buyerlistview"></b-form-select>
                </div>
                <div v-if="productList.length > 0" class="card-body m-0 p-0">
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
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" small striped hover stacked="md"
                    :items="productList"
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
        <!-- Start Edit Details Modal -->
        <b-modal ref="dataEdit" id="dataEdit" size="xxl" :title="title" no-close-on-backdrop>                        
            <div class="modal-body row m-0 p-0 mb-2">
                <div class="row col-md-9 m-0 p-0">
                    <div class="col-md-6">
                        <!-- <label class="col-form-label">{{ $t('category')}}</label>
                        <input list="CategoryList" class="form-control text-nowrap" v-model="taskHead[0]['product_category']">
                        <datalist id="CategoryList">
                            <option v-for="category in categorylistview" :key="category.category">{{ category }}</option>
                        </datalist> -->
                        <label class="col-form-label">{{ $t('buyer')}}</label>
                        <input list="BuyerList" class="form-control text-nowrap" v-model="buyer">
                        <datalist id="BuyerList">
                            <option v-for="buyer in buyerlistview" :key="buyer.buyer">{{ buyer }}</option>
                        </datalist>
                        <span v-if="errors.buyer" class="error text-danger"> {{$t('required_field')}} <br></span>
                        
                        <label class="col-form-label">{{ $t('style') + ' ' + $t('code')}}</label>
                        <input type="text" class="form-control" v-model="taskHead[0]['product_code']">
                        <span v-if="errors.product_code" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}} <br></span>
                        
                        <label class="col-form-label">{{ $t('style') + ' ' + $t('name')}}</label>
                        <input type="text" class="form-control" v-model="taskHead[0]['product_style']">
                    </div>
                    <div class="col-md-6">
                        <label class="col-form-label">SMV</label>
                        <input type="text" class="form-control" v-model="taskHead[0]['smv']">
                        <label class="col-form-label">{{ $t('manpower')}}</label>
                        <input type="text" class="form-control" v-model="taskHead[0]['manpower']">
                        <label class="col-form-label">{{ $t('specification')}}</label>
                        <input type="text" class="form-control" v-model="taskHead[0]['specification']">
                        <!-- <label for="store" class="col-form-label">{{ $t('store_name')}}</label>
                        <div>
                            <b-form-select @change="store_change" id="store" v-model="store" :options="store_options"></b-form-select>
                        </div> -->
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">{{ $t('remarks')}}</label>
                        <input type="text" class="form-control" v-model="taskHead[0]['remarks']">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group m-auto col-md-12 text-center float-center">
                        <img id="blah" style="width: 70%;" :src="src + imageName" alt="product image" />
                    </div>
                    <div class="fileBrowser col-md-12 p-0 m-0">
                        <div class="form-group col-md-12 upload-btn-wrapper p-0 m-0 text-center" id="employee_image">
                            <button class="mdb btn btn-outline-success mx-auto">{{$t('browse')}}</button>
                            <input type="file" @change="handleFileUpload" id="upload" name="EmployeeImage" class="pointer mx-auto"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3" :class="hideDetails">
                    <table class="table table-striped table-bordered table-responsive-stack mx-auto">
                        <thead class="bg-info text-center">
                        <tr>
                            <th style="width: 4%;" class="font-weight-bold">S/N</th>
                            <th style="width: 30%;" class="font-weight-bold">{{ $t('material_name') }}</th>
                            <th style="width: 25%;" class="font-weight-bold">{{$t('description')}}</th>
                            <th style="width: 10%;" class="font-weight-bold">{{$t('quantity')}}</th>
                            <th style="width: 11%;" class="font-weight-bold text-right">{{this.$t('Action')}}</th>
                        </tr>
                        </thead>
                        <draggable v-model="taskDetails" tag="tbody">
                            <tr v-for="(item, index) in taskDetails" :key="index" class="m-0 p-0" style="cursor: move;">
                                <td scope="row" class="p-0 m-0">
                                    <div class="d-none">{{ item.sn = index+1 }}</div>                                       
                                    <input type="text" class="form-control m-0 border-0 bg-transparent rounded-0" name="sn" v-model="item.sn">
                                </td>
                                <td class=" m-0 p-0">
                                    <model-select v-model="item.inventory_id" :options="itemlistview" class="form-control row-fluid m-0 border-0 bg-transparent rounded-0" style='min-width: 300px;'></model-select>
                                </td>
                                <td class="m-0 p-0">
                                    <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="item.description">
                                </td>
                                <td class="m-0 p-0">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="item.quantity">
                                </td>
                                <td class="text-right m-0 p-0">
                                    <a @click="addRow" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
                                    <a @click="destroy_d(item.id, index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                                </td>
                            </tr>
                        </draggable>
                    </table>
                </div>
                <!-- <div class="col-md-12 m-0 p-0 mt-3" :class="hideDetails">
                    <b-table show-empty small striped hover stacked="md" :items="taskDetails" :fields="taskDetailsfields">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(inventory_id)="row">
                            <model-select :options="itemlistview" class="form-control row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.inventory_id"></model-select>
                        </template>
                        <template v-slot:cell(material_number)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.material_number">
                        </template>
                        <template v-slot:cell(material_name)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.material_name">
                        </template>
                        <template v-slot:cell(material_name_ch)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.material_name_ch">
                        </template>
                        <template v-slot:cell(description)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.description">
                        </template>
                        <template v-slot:cell(description_ch)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.description_ch">
                        </template>
                        <template v-slot:cell(unit_weight)="row">
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.unit_weight">
                        </template>
                        <template v-slot:cell(total_weight)="row">
                            {{ (row.item.unit_weight * row.item.quantity).toFixed(0)}}
                        </template>
                        <template v-slot:cell(quantity)="row">
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.quantity">
                        </template>
                        <template v-slot:cell(unit)="row">
                            <input type="text" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.unit">
                        </template>
                        <template v-slot:cell(action)="row">
                            <a @click="addRow" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
                            <a @click="destroy_d(row.item.id, row.index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                        </template>
                    </b-table>
                </div>                               -->
            </div>
            <template v-slot:modal-header>
                    <h3 class="panel-title float-left">{{ title }}</h3> 
            </template>
            <template v-slot:modal-footer>
                <button v-if="checkRoles('product_details_Insert')" @click.prevent="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>                    
        <!-- End Edit Details Modal -->

        <!-- Start view Details Modal -->
        <b-modal class="b-0" ref="dataView" id="dataView" size="xl" :title="$t('product_details')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0 mb-2">
                <div class="row col-md-9 m-0 p-0">
                    <div class="col-md-6">
                        <!-- <span class="font-weight-bold">{{ $t('category')}}:</span> {{taskHead[0]['product_category']}}<br> -->
                        <span class="font-weight-bold">{{ $t('buyer')}}:</span> {{buyer}}<br>
                        <span class="font-weight-bold">{{ $t('code')}}:</span> {{taskHead[0]['product_code']}}<br>
                        <span class="font-weight-bold">{{ $t('style')}}:</span> {{taskHead[0]['product_style']}}
                    </div>
                    <div class="col-md-6">
                        <span class="font-weight-bold">SMV:</span> {{taskHead[0]['smv']}}<br>
                        <span class="font-weight-bold">{{ $t('manpower')}}:</span> {{taskHead[0]['manpower']}}<br>
                        <span class="font-weight-bold">{{ $t('specification')}}:</span> {{taskHead[0]['specification']}}
                        
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group m-auto col-md-12 text-center float-center">
                        <a :href="'images/product/' + taskHead[0]['product_image']"> <img id="blah" style="width: 70%;" :src="'images/product/' + taskHead[0]['product_image']" alt="product image" /></a>
                    </div>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <!-- <div class="mb-2 d-flex">
                        <div class="float-left py-auto"><h5 class="my-auto">{{$t('material_list_for_quantity')}} </h5></div>
                        <div><input type="number" class="ml-2 form-control" v-model="product_qty"></div>
                    </div> -->
                    <!-- <div v-for="(store_name, index) in storeList" :key="index"> -->
                        <!-- <h4 class="text-center col-12 bg-info text-light mt-3">{{ store_name }}</h4> -->
                        <b-table show-empty small striped hover stacked="md" :items="taskDetailsAll" :fields="taskDetailsfieldsView" class="mt-3">
                            <template v-slot:cell(index)="row">
                                {{ row.index+1 }}
                            </template>
                            <!-- <template v-slot:cell(quantity)="row">
                                <span v-if="row.item.quantity * product_qty > row.item.stock" class="text-danger">{{ (row.item.quantity * product_qty)}}</span>
                                <span v-else>{{ (row.item.quantity * product_qty)}}</span>
                            </template> -->
                            <template v-slot:cell(total_weight)="row">
                                {{(row.item.quantity * row.item.weight).toFixed(2)}}
                            </template>
                        </b-table>
                    <!-- </div> -->
                </div>  
                <div class="col-12">
                    <span class="font-weight-bold">{{ $t('remarks')}}:</span> {{taskHead[0]['remarks']}}
                </div>                            
            </div>
            <template v-slot:modal-header>
                    <h3 class="panel-title float-left">{{ $t('product_details') }}</h3> 
            </template>
            <template v-slot:modal-footer>
                <div class="row m-0 p-0 col-md-12">
                    <div class="col-md-5">
                        <button v-if="checkRoles('product_details_Delete')" @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                    </div>
                    <div class="col-md-7">
                        <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                        <button v-if="checkRoles('product_details_Update')" @click="editDetails" class="mdb btn btn-outline-default float-right">{{ $t('edit') }}</button>
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
import draggable from "vuedraggable";
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('product_list') }
    },

    data() {
        return{
            inventoryList : [],
            productList : [],
            roles: [],
            productListAll : [],
            noprint : '',
            // taskDetailsByStore : [],
            store : 3,
            store_options: [],
            buyer : null,
            errors : [],
            title: '',
            disable: false,
            taskHead : [{'product_category' : null, 'buyer' : null, 'product_style' : null, 'product_code' : null, 'smv': 0, 'manpower': 0, 'specification' : null, 'remarks' : null, 'product_image' : 'noimage.jpg'}],
            taskDetailsAll : [],
            taskDetails : [],
            taskHeadId : null,
            taskDetailsId : null,
            grand_total : 0,
            buttonTitle : this.$t('save'),
            hideDetails : 'd-none',
            src : '/images/product/',
            save_image : null,
            product_qty : 1,

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
        fetch(`api/store`)
        .then(res => res.json())
        .then(res => {
            this.store_options = res['Store'];
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        this.fetchData()
        fetch(`api/inventory`)
        .then( res => res.json())
        .then(res => {  
            this.inventoryList = res['Inventory'];
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

        change_buyer() {
            this.productList = this.productListByBuyer
            this.totalRows = this.productList.length
        },

        fetchData() {
            this.isBusy = true;
            fetch(`api/producthead`)
            .then(res => res.json())
            .then(res => {
                this.productListAll = res['productHead']
                this.productList = this.productListByBuyer
                this.totalRows = this.productList.length
                this.isBusy = false
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

        addDetails(){
            this.save_image = null
            this.hideDetails = 'd-none'
            this.taskHead = [{'product_category' : null, 'buyer' : this.buyer, 'product_style' : null, 'product_code' : null, 'smv': 0, 'manpower': 0, 'specification' : null, 'remarks' : null, 'product_image' : 'noimage.jpg'}]
            this.taskHeadId = null
            this.title = this.$t('product_new')
            this.src = '/images/product/'
            this.grand_total = null
            this.taskDetails = []
        },

        store_name_ch(store_name) {
            let store = {'Cutting Raw Materials' : 'cutting_raw_materials', 
                            'Stationery Items' : 'stationery_items',
                            'Fabric Raw Materials' : 'fabric_raw_materials',
                            'Packaging Materials' : 'packaging_materials',
                            'Printing Chemicals' : 'printing_chemicals',
                            'Spray Chemicals' : 'spray_chemicals',
                            'Wash Chemicals' : 'wash_chemicals',
                            'Polish Chemicals' : 'polish_chemicals',
                            'Polish Raw Materials' : 'polish_raw_materials',
                            'Injection Raw Materials' : 'injection_raw_materials'
                        }
            return store[store_name]
        },

        addRow() {            
            this.taskDetails.push({'quantity' : null, 'material_number' : null, 'material_name' : null, 'description' : null, 'material_name_ch' : null, 'description_ch' : null, 'unit_weight' : null, 'unit' : null, 'remarks' : null, 'inventory_id' : null, 'store_id' : this.store, 'remarks' : null, 'producthead_id' : this.taskHeadId})
        },

        viewDetails(id) {
            this.product_qty = 1
            this.taskHeadId = id
            this.noprint = 'noprint'
            fetch(`api/productdetails/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetailsAll = res['productDetails']
                this.taskHead = this.singleTask
            })
            // .then(res =>{
            //     this.grand_total = this.grand_total_cal
            // })
            .catch(err => {
                alert(err.response.data.message)
            })
            this.$refs['dataView'].show()
        },

        editDetails() {
            this.$refs['dataView'].hide()
            this.save_image = null
            this.title = this.$t('UpdateItem')
            this.hideDetails = ''
            // this.taskDetails = this.taskDetailsByStore
            this.taskDetails = this.taskDetailsAll
            if (this.taskDetails.length == 0) {
                this.taskDetails = [{'quantity' : null, 'inventory_id' : null, 'store_id' : this.store, 'remarks' : null, 'producthead_id' : this.taskHeadId}]
            }
            this.$refs['dataEdit'].show()         
        },

        // materialsByStore(store) {
        //     let array =[]
        //     for (let i = 0; i < this.taskDetailsAll.length; i++) {
        //         if (this.taskDetailsAll[i]['store_name'] == store) {                
        //             array[i] = this.taskDetailsAll[i]                
        //         }
        //     }
        //     return array
        // },

        // store_change() {
        //     this.taskDetails = this.taskDetailsByStore
        //     if (this.taskDetails.length == 0) {
        //         this.taskDetails = [{'quantity' : null, 'inventory_id' : null, 'store_id' : this.store, 'remarks' : null, 'producthead_id' : this.taskHeadId}]
        //     }
        // },

        row_material(id) {
            for (let i = 0; i < this.inventoryList.length; i++) {
                if (this.inventoryList[i]['id'] == id) {
                    return this.inventoryList[i]['item_code'] + ' | ' + this.inventoryList[i]['item'] + ' | ' + this.inventoryList[i]['unit'] + ' | ' + this.inventoryList[i]['specification']
                }                
            }
        },

        handleFileUpload(e) {
            let file = e.target.files[0];
            var fileReader = new FileReader();
            
            if(file['size'] <= 262144 &&  file['type'].split('/')[0]=='image' ){          //256 KB ~~ 262144 Byte
                fileReader.onload = (e) => {
                    this.src = '';
                    this.taskHead[0]['product_image'] = e.target.result;
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

        save() {
            this.disable = !this.disable;
            this.buttonTitle = this.$t('saving')
            this.taskHead[0]['product_image'] = this.save_image
            this.taskHead[0]['buyer'] = this.buyer
            let options = { headers: {'enctype': 'multipart/form-data'} };

            if(this.taskHeadId == null){
                axios.post(`api/producthead`, this.taskHead[0], options)
                .then(({data}) =>{
                    this.errors = ''
                    this.taskHead[0] = data.productHead                   
                    this.taskHeadId = this.taskHead[0]['id']
                    this.productList.unshift(this.taskHead[0])
                    this.fetchData()
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.hideDetails = ''
                    this.taskDetails = [{'quantity' : null, 'material_number' : null, 'material_name' : null, 'description' : null, 'material_name_ch' : null, 'description_ch' : null, 'unit_weight' : null, 'unit' : null, 'remarks' : null, 'inventory_id' : null, 'store_id' : this.store, 'remarks' : null, 'producthead_id' : this.taskHeadId}]
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message)
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')                      
                })
            } else {
                axios.patch(`api/producthead/${this.taskHeadId}`, this.taskHead[0], options)
                .then(({data}) => {
                    this.src = '/images/product/'
                    this.taskHead[0]['product_image'] = data.fileName
                    for (let i = 0; i < this.taskDetails.length; i++) {
                        if (this.taskDetails[i]['quantity']) {
                            if(this.taskDetails[i]['id']){                            
                                axios.patch(`api/productdetails/${this.taskDetails[i]['id']}`, this.taskDetails[i])
                            } else {
                                axios.post(`api/productdetails`, this.taskDetails[i])
                                .then(({data})=>{
                                    this.taskDetails[i]['id'] = data.ProductdetailsID
                                })
                            }
                        }                                                
                    }                    
                    for (let i = 0; i < this.productList.length; i++) {
                        if(this.productList[i]['id'] == this.taskHead[0]['id']){
                            this.productList[i] = this.taskHead[0]
                            break
                        }   
                    }
                    
                })
                .then(res => {
                    // this.fetchData()
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.errors = ''
                    this.buttonTitle = this.$t('save')
                    this.$refs['dataEdit'].hide()
                    this.viewDetails(this.taskHeadId)
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

        destroy() {
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/producthead/${this.taskHeadId}`)                        
                        .then(res => {
                            if(this.productList.length > 0){
                                let index = 0 
                                for (let i = 0; i < this.productList.length; i++) {
                                    if(this.productList[i]['id'] == this.taskHead[0]['id']){
                                        index = i
                                        break
                                    }   
                                }
                                this.fetchData()
                                this.$refs['dataView'].hide()
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

        destroy_d(id, index){
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {                        
                        if(id){
                            axios.delete(`api/productdetails/${id}`)                        
                            .then(res => {
                                this.taskDetails.splice(index, 1)                                
                            })
                            .catch(err => {
                                alert(err.response.data.message);                       
                            });
                        } else {
                            this.taskDetails.splice(index, 1)
                        }

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
        imageName() {
            if(this.taskHead[0]['product_image'] == null || this.taskHead[0]['product_image'] == 'noimage.jpg') {
                // this.taskHead[0]['product_image'] = null
                return 'noimage.jpg'
            }
            return this.taskHead[0]['product_image']
        },

        singleTask() {
            let id = this.taskHeadId
            return this.productList.filter(function (item) {
            return item['id'] == id
            })
        },

        // taskDetailsByStore() {
        //     let id = this.store
        //     return this.taskDetailsAll.filter(function (item) {
        //     return item['store_id'] == id
        //     })
        // },

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
                // { key: 'product_category', label : this.$t('category'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                // { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_style', label : this.$t('style') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
            ]
        },

        // taskDetailsfields() {
        //     const lang = this.$i18n.locale
        //     if (!lang) { return [] }
        //     this.buttonTitle = this.$t('save')
        //     return [
        //         { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
        //         { key: 'inventory_id', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'material_number', label : this.$t('material_number'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'material_name', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'material_name_ch', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'description', label : this.$t('description'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'description_ch', label : this.$t('description') + '(CH)', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'unit_weight', label : this.$t('unit_weight') + '(g)', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'quantity', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         // { key: 'total_weight', label : this.$t('total_weight'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
        //         { key: 'action', label: this.$t('Action'),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
        //     ]
        // },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')

            return [
                { key: 'sn', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'item_code', label : this.$t('material_number'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('material_name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'specification', label : this.$t('description'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'weight', label : this.$t('unit_weight'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_weight', label : this.$t('total_weight'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        buyerlistview() {
            return uniq(this.productListAll.map(({ buyer }) => buyer))
        },

        productListByBuyer() {
            let buyer = this.buyer
            return this.productListAll.filter(function (item) {
            return item['buyer'] == buyer
            })
        },
        // categorylistview() {
        //     return uniq(this.productList.map(({ product_category }) => product_category))
        // },

        storeList() {
            return uniq(this.taskDetailsAll.map(({ store_name }) => store_name))
        },

        itemlistview(){
            let array = []
            for (let i = 0; i < this.inventoryList.length; i++) {
                // if (this.inventoryList[i]['store_id'] == this.store) {                    
                    array.unshift({'value' : this.inventoryList[i]['id'], 'text' : this.inventoryList[i]['item_code'] + ' | ' + this.inventoryList[i]['item'] + ' | ' + this.inventoryList[i]['unit'] + ' | ' + this.inventoryList[i]['specification']})
                // }
            }
            return array
        },

        grand_total_cal() {
            // let total = 0
            // Object.entries(this.taskDetails).forEach(([key, val]) => {
            //     if(!isNaN(parseFloat(val.unit_price)) && !isNaN(parseFloat(val.quantity)))
            //     total += parseFloat(val.unit_price*val.quantity)
            // });
            // return total.toFixed(2);
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    },

    components: { ModelSelect, draggable }
}
</script>

<style>

</style>