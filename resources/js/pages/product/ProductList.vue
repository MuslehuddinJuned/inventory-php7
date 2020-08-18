<template>
    <div class="container justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('product_list') }}</h3> 
                    <div class="ml-auto">
                        <button @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
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
                        <a :href="'/images/product/' + row.item.product_image"><b-img :src="'/images/product/' + row.item.product_image" style="width: 56px" alt=""></b-img></a>
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
                    <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="title" no-close-on-backdrop>                        
                        <div class="modal-body row m-0 p-0 mb-2">
                            <div class="row col-md-9 m-0 p-0">
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('category')}}</label>
                                    <input list="CategoryList" class="form-control text-nowrap" v-model="taskHead[0]['product_category']">
                                    <datalist id="CategoryList">
                                        <option v-for="category in categorylistview" :key="category.category">{{ category }}</option>
                                    </datalist>
                                    <span v-if="errors.product_category" class="error text-danger"> {{$t('required_field')}} <br></span>
                                    <label class="col-form-label">{{ $t('buyer')}}</label>
                                    <input type="text" class="form-control" v-model="taskHead[0]['buyer']">
                                    <label class="col-form-label">{{ $t('style')}}</label>
                                    <input type="text" class="form-control" v-model="taskHead[0]['product_style']">
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('code')}}</label>
                                    <input type="text" class="form-control" v-model="taskHead[0]['product_code']">
                                    <span v-if="errors.product_code" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}} <br></span>
                                    <label class="col-form-label">{{ $t('specification')}}</label>
                                    <input type="text" class="form-control" v-model="taskHead[0]['specification']">
                                    <label class="col-form-label">{{ $t('remarks')}}</label>
                                    <input type="text" class="form-control" v-model="taskHead[0]['remarks']">
                                </div>
                                <div class="col-md-12">
                                    <label for="store" class="col-form-label">{{ $t('store_name')}}</label>
                                    <div>
                                        <select @change="store_change" class="form-control" id="store" v-model="store">
                                            <option value="2">{{ $t('injection_raw_materials') }}</option>
                                            <option value="3">{{ $t('cutting_raw_materials') }}</option>
                                            <option value="4">{{ $t('polish_raw_materials') }}</option>
                                            <option value="5">{{ $t('polish_chemicals') }}</option>
                                            <option value="6">{{ $t('washing_chemicals') }}</option>
                                            <option value="7">{{ $t('stray_chemicals') }}</option>
                                            <option value="8">{{ $t('printing_chemicals') }}</option>
                                            <option value="9">{{ $t('packaging_materials') }}</option>
                                            <option value="10">{{ $t('stationery_items') }}</option>
                                        </select>
                                    </div> 
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
                                <b-table show-empty small striped hover stacked="md" :items="taskDetails" :fields="taskDetailsfields">
                                    <template v-slot:cell(index)="row">
                                        {{ row.index+1 }}
                                    </template>
                                    <template v-slot:cell(inventory_id)="row">
                                        <model-select :options="itemlistview" class="form-control row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.inventory_id"></model-select>
                                    </template>
                                    <template v-slot:cell(quantity)="row">
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0" v-model="row.item.quantity">
                                    </template>
                                    <template v-slot:cell(action)="row">
                                        <!-- <a @click="viewDetails(row.item.machine_name, row.item.machine_description)" class="btn btn-sm text-black-50" data-toggle="modal" data-target="#dataView"><fa icon="eye" fixed-width /></a> -->
                                        <a @click="addRow" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
                                        <a @click="destroy_d(row.item.id, row.index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                                    </template>
                                </b-table>
                            </div>                              
                        </div>
                        <template v-slot:modal-header="">
                                <h3 class="panel-title float-left">{{ title }}</h3> 
                        </template>
                        <template v-slot:modal-footer="">
                            <button @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                            <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
                        </template>
                    </b-modal>                    
                    <!-- End Edit Details Modal -->

                    <!-- Start view Details Modal -->
                    <b-modal ref="dataView" id="dataView" size="xl" :title="$t('product_details')" no-close-on-backdrop>
                        <div class="modal-body row m-0 p-0 mb-2">
                            <div class="row col-md-9 m-0 p-0">
                                <div class="col-md-6">
                                    <span class="font-weight-bold">{{ $t('category')}}:</span> {{taskHead[0]['product_category']}}<br>
                                    <span class="font-weight-bold">{{ $t('buyer')}}:</span> {{taskHead[0]['buyer']}}<br>
                                    <span class="font-weight-bold">{{ $t('style')}}:</span> {{taskHead[0]['product_style']}}
                                </div>
                                <div class="col-md-6">
                                    <span class="font-weight-bold">{{ $t('code')}}:</span> {{taskHead[0]['product_code']}}<br>
                                    <span class="font-weight-bold">{{ $t('specification')}}:</span> {{taskHead[0]['specification']}}<br>
                                    <span class="font-weight-bold">{{ $t('remarks')}}:</span> {{taskHead[0]['remarks']}}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group m-auto col-md-12 text-center float-center">
                                   <a :href="'images/product/' + taskHead[0]['product_image']"> <img id="blah" style="width: 70%;" :src="'images/product/' + taskHead[0]['product_image']" alt="product image" /></a>
                                </div>
                            </div>
                            <div class="col-md-12 m-0 p-0 mt-3">
                                <div class="mb-2 d-flex">
                                    <div class="float-left py-auto"><h5 class="my-auto">{{$t('material_list_for_quantity')}} </h5></div>
                                    <div><input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="ml-2 form-control" v-model="product_qty"></div>
                                </div>
                                <div v-for="(store_name, index) in storeList" :key="index">
                                    <h4 class="text-center col-12 bg-info text-light mt-3">{{store_name}}</h4>
                                    <b-table v-if="store_name == 'Cutting Raw Materials'" show-empty small striped hover stacked="md" :items="materialsByStore(store_name)" :fields="taskDetailsfieldsViewCutting">
                                        <template v-slot:cell(index)="row">
                                            {{ row.index+1 }}
                                        </template>
                                        <template v-slot:cell(quantity)="row">
                                            <span v-if="row.item.quantity * product_qty > row.item.stock" class="text-danger">{{ (row.item.quantity * product_qty)}}</span>
                                            <span v-else>{{ (row.item.quantity * product_qty)}}</span>
                                        </template>
                                        <template v-slot:cell(total_weight)="row">
                                            {{(row.item.quantity * row.item.weight * product_qty).toFixed(2)}}
                                        </template>
                                    </b-table>
                                    <b-table v-else show-empty small striped hover stacked="md" :items="materialsByStore(store_name)" :fields="taskDetailsfieldsView">
                                        <template v-slot:cell(index)="row">
                                            {{ row.index+1 }}
                                        </template>
                                        <!-- <template v-slot:cell(inventory_id)="row">
                                            {{ row_material(row.item.inventory_id) }}
                                        </template> -->
                                        
                                        <template v-slot:cell(quantity)="row">
                                            <span v-if="row.item.quantity * product_qty > row.item.stock" class="text-danger">{{ (row.item.quantity * product_qty)}}</span>
                                            <span v-else>{{ (row.item.quantity * product_qty)}}</span>
                                        </template>
                                        
                                        <template v-slot:cell(total_weight)="row">
                                            {{(row.item.quantity * row.item.weight * product_qty).toFixed(2)}}
                                        </template>
                                        <template v-slot:cell(total_price)="row">
                                            {{ (row.item.quantity * row.item.unit_price * product_qty).toFixed(2) }}
                                        </template>
                                        <!-- <template slot="bottom-row">
                                            <td class="text-white bg-info font-weight-bold text-center">{{$t('grand_total')}}</td>
                                            <td class="text-white bg-info font-weight-bold text-center"></td>
                                            <td class="text-white bg-info font-weight-bold text-center"></td>
                                            <td class="text-white bg-info font-weight-bold text-center"></td>
                                            <td class="text-white bg-info font-weight-bold text-center"></td>
                                            <td class="text-white bg-info font-weight-bold text-center"></td>
                                            <td class="text-white bg-info font-weight-bold text-center">{{grand_total*product_qty}}</td>
                                        </template> -->
                                    </b-table>
                                </div>
                            </div>                              
                        </div>
                        <template v-slot:modal-header="">
                                <h3 class="panel-title float-left">{{ $t('product_details') }}</h3> 
                        </template>
                        <template v-slot:modal-footer="">
                            <div class="row m-0 p-0 col-md-12">
                                <div class="col-md-5">
                                    <button @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                                </div>
                                <div class="col-md-7">
                                    <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right">{{$t('Close')}}</button>
                                    <button @click="editDetails" class="mdb btn btn-outline-default float-right">{{ $t('edit') }}</button>
                                </div>
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
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('product_list') }
    },

    data() {
        return{
            inventoryList : [],
            productList : [],
            // taskDetailsByStore : [],
            store : 3,
            errors : [],
            title: '',
            disable: false,
            taskHead : [{'product_category' : null, 'buyer' : null, 'product_style' : null, 'product_code' : null, 'specification' : null, 'remarks' : null, 'product_image' : 'noimage.jpg'}],
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
        this.isBusy = true;
        fetch(`api/producthead`)
        .then(res => res.json())
        .then(res => {
            this.productList = res['productHead']
            this.totalRows = this.productList.length
            this.isBusy = false
        })
        .catch(err => {
            alert(err.response.data.message);
        }) 

        fetch(`api/inventory`)
        .then( res => res.json())
        .then(res => {  
            this.inventoryList = res['Inventory'];
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

        addDetails(){
            this.save_image = null
            this.hideDetails = 'd-none'
            this.taskHead = [{'product_category' : null, 'buyer' : null, 'product_style' : null, 'product_code' : null, 'specification' : null, 'remarks' : null, 'product_image' : 'noimage.jpg'}]
            this.taskHeadId = null
            this.title = this.$t('product_new')
            this.src = '/images/product/'
            this.grand_total = null
            this.taskDetails = []
        },

        addRow() {            
            this.taskDetails.push({'quantity' : 0, 'remarks' : null, 'producthead_id' : this.taskHeadId, 'inventory_id' : null})
        },

        viewDetails(id) {
            this.product_qty = 1
            this.taskHeadId = id
            fetch(`api/productdetails/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetailsAll = res['productDetails']
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

        editDetails() {
            this.$refs['dataView'].hide()
            this.save_image = null
            this.title = this.$t('UpdateItem')
            this.hideDetails = ''
            this.taskDetails = this.taskDetailsByStore
            if (this.taskDetails.length == 0) {
                this.taskDetails = [{'quantity' : 0, 'remarks' : null, 'producthead_id' : this.taskHeadId, 'inventory_id' : null}]
            }
            this.$refs['dataEdit'].show()         
        },

        materialsByStore(store) {
            let array =[]
            for (let i = 0; i < this.taskDetailsAll.length; i++) {
                if (this.taskDetailsAll[i]['store_name'] == store) {                
                    array[i] = this.taskDetailsAll[i]                
                }
            }
            return array
        },

        store_change() {
            this.taskDetails = this.taskDetailsByStore
            if (this.taskDetails.length == 0) {
                this.taskDetails = [{'quantity' : 0, 'remarks' : null, 'producthead_id' : this.taskHeadId, 'inventory_id' : null}]
            }
        },

        row_material(id) {
            for (let i = 0; i < this.inventoryList.length; i++) {
                if (this.inventoryList[i]['id'] == id) {
                    return this.inventoryList[i]['item_code'] + ' | ' + this.inventoryList[i]['item'] + ' | ' + this.inventoryList[i]['specification'] + ' | ' + this.inventoryList[i]['unit']
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
            let options = { headers: {'enctype': 'multipart/form-data'} };

            if(this.taskHeadId == null){
                axios.post(`api/producthead`, this.taskHead[0], options)
                .then(({data}) =>{
                    this.errors = ''
                    this.taskHead[0] = data.productHead                   
                    this.taskHeadId = this.taskHead[0]['id']
                    this.productList.unshift(this.taskHead[0])
                    
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.hideDetails = ''
                    this.taskDetails = [{'quantity' : 0, 'remarks' : null, 'producthead_id' : this.taskHeadId, 'inventory_id' : null}]
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
                axios.patch(`api/producthead/${this.taskHeadId}`, this.taskHead[0], options)
                .then(({data}) => {
                    this.src = '/images/product/'
                    this.taskHead[0]['product_image'] = data.fileName
                    for (let i = 0; i < this.taskDetails.length; i++) {
                        if(this.taskDetails[i]['id']){
                            axios.patch(`api/productdetails/${this.taskDetails[i]['id']}`, this.taskDetails[i])
                        } else if(this.taskDetails[i]['inventory_id']){
                            axios.post(`api/productdetails`, this.taskDetails[i])
                            .then(({data})=>{
                                this.taskDetails[i]['id'] = data.ProductdetailsID
                            })
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
                    }
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
                                this.productList.splice(index, 1);                           
                                this.totalRows = this.productList.length;
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

        taskDetailsByStore() {
            let id = this.store
            return this.taskDetailsAll.filter(function (item) {
            return item['store_id'] == id
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
                { key: 'product_category', label : this.$t('category'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'product_style', label : this.$t('style'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_code', label : this.$t('code'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
            ]
        },

        taskDetailsfields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'inventory_id', label : this.$t('item'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'action', label: this.$t('Action'),  class: 'text-right', thClass: 'border-top border-dark font-weight-bold'}
            ]
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')

            return [
                { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                // { key: 'store_name', label : this.$t('store_name'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item_code', label : this.$t('item_code'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('item'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'specification', label : this.$t('specification'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'stock', label : this.$t('stock'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'unit_price', label : this.$t('unit_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'total_price', label : this.$t('total_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },

        taskDetailsfieldsViewCutting() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')

            return [
                { key: 'index', label : '#', class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                // { key: 'store_name', label : this.$t('store_name'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item_code', label : this.$t('item_code'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'item', label : this.$t('item'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'specification', label : this.$t('specification'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'stock', label : this.$t('stock'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'weight', label : this.$t('weight') + '(kg)', class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_weight', label : this.$t('total_weight') + '(kg)', class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'unit_price', label : this.$t('unit_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'total_price', label : this.$t('total_price'), class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        categorylistview() {
            return uniq(this.productList.map(({ product_category }) => product_category))
        },

        storeList() {
            return uniq(this.taskDetailsAll.map(({ store_name }) => store_name))
        },

        itemlistview(){
            let array = []
            for (let i = 0; i < this.inventoryList.length; i++) {
                if (this.inventoryList[i]['store_id'] == this.store) {                    
                    array.unshift({'value' : this.inventoryList[i]['id'], 'text' : this.inventoryList[i]['item_code'] + ' | ' + this.inventoryList[i]['item'] + ' | ' + this.inventoryList[i]['specification'] + ' | ' + this.inventoryList[i]['unit']})
                }
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

    components: { ModelSelect }
}
</script>

<style>

</style>