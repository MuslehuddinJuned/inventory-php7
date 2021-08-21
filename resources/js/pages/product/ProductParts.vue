<template>
    <div class="container justify-content-center">
       <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('product_parts') }}</h3> 
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
                <div class="col-md-6 float-left">
                    <label for="po_no">{{$t('department')}}</label>
                    <select @change="change_department" class="form-control" v-model="department">
                        <option value="assembly">{{ $t('assembly') }}</option>
                        <option value="spray">{{ $t('spray') }}</option>
                        <option value="polish">{{ $t('polish') }}</option>
                        <option value="injection">{{ $t('injection') }}</option>
                        <option value="cutting">{{ $t('cutting') }}</option>
                    </select>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <table class="table table-striped table-bordered table-responsive-stack mx-auto">
                        <thead class="bg-info text-center">
                        <tr>
                            <th style="width: 4%;" class="font-weight-bold">S/N</th>
                            <th style="width: 30%;" class="font-weight-bold">{{ $t('image') }}</th>
                            <th style="width: 30%;" class="font-weight-bold">{{ $t('name') }}</th>
                            <th style="width: 25%;" class="font-weight-bold">{{$t('description')}}</th>
                            <th style="width: 10%;" class="font-weight-bold">{{$t('quantity')}}</th>
                            <th style="width: 10%;" class="font-weight-bold">{{$t('unit')}}</th>
                            <th style="width: 11%;" class="font-weight-bold text-right">{{this.$t('Action')}}</th>
                        </tr>
                        </thead>
                        <draggable v-model="taskDetails" tag="tbody">
                            <tr v-for="(item, index) in taskDetails" :key="index" class="m-0 p-0" style="cursor: move;">
                                <td scope="row" class="p-0 m-0">
                                    <div class="d-none">{{ item.sn = index+1 }}</div>                                       
                                    <input type="text" class="form-control m-0 border-0 bg-transparent rounded-0" name="sn" v-model="item.sn">
                                </td>
                                <td class="m-0 p-0">
                                    <div v-if="item.parts_image" class="fileBrowser col-md-12 p-0 m-0">
                                        <div class="form-group col-md-12 upload-btn-wrapper p-0 m-0 text-center" id="employee_image">
                                            <b-img :src="'/images/product/' + item.parts_image" style="height: 50px; max-width: 150px;" alt=""></b-img>
                                            <input type="file" @click="taskDetailsId = item.id" @change="uploadImage" id="upload" name="EmployeeImage" class="pointer mx-auto"/>
                                        </div>
                                    </div>
                                </td>
                                <td class="m-0 p-0">
                                    <input type="text" v-model="item.parts_name" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0">
                                </td>
                                <td class="m-0 p-0">
                                    <input type="text" v-model="item.parts_description" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0">
                                </td>
                                <td class="m-0 p-0">
                                    <input type="text" v-model="item.parts_qty" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0">
                                </td>
                                <td class="m-0 p-0">
                                    <input type="text" v-model="item.unit" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0">
                                </td>
                                <td class="text-right m-0 p-0 text-nowrap">
                                    <a @click="addRow" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
                                    <a @click="destroy_d(item.id, index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                                </td>
                            </tr>
                        </draggable>
                    </table>
                </div>
            </div>
            <template v-slot:modal-header="">
                    <h3 class="panel-title float-left">{{ title }}</h3> 
            </template>
            <template v-slot:modal-footer="">
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
                <div class="col-md-6 float-left">
                    <label for="po_no">{{$t('department')}}</label>
                    <select @change="change_department" class="form-control" v-model="department">
                        <option value="assembly">{{ $t('assembly') }}</option>
                        <option value="spray">{{ $t('spray') }}</option>
                        <option value="polish">{{ $t('polish') }}</option>
                        <option value="injection">{{ $t('injection') }}</option>
                        <option value="cutting">{{ $t('cutting') }}</option>
                    </select>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" :items="taskDetails" :fields="taskDetailsfieldsView" class="mt-3">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(parts_image)="row">
                            <a :href="'/images/product/' + row.item.parts_image"><b-img :src="'/images/product/' + row.item.parts_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                        </template>
                        <template v-slot:cell(total_weight)="row">
                            {{(row.item.quantity * row.item.weight).toFixed(2)}}
                        </template>
                    </b-table>
                </div>  
                <div class="col-12">
                    <span class="font-weight-bold">{{ $t('remarks')}}:</span> {{taskHead[0]['remarks']}}
                </div>                            
            </div>
            <template v-slot:modal-header="">
                    <h3 class="panel-title float-left">{{ $t('product_details') }}</h3> 
            </template>
            <template v-slot:modal-footer="">
                <button v-if="checkRoles('product_details_Update')" @click="editDetails" class="mdb btn btn-outline-default">{{ $t('edit') }}</button>
                <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
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
        return { title: this.$t('product_parts') }
    },

    data() {
        return{
            productList : [],
            roles: [],
            productListAll : [],
            department: 'injection',
            noprint : '',
            buyer : null,
            errors : [],
            title: '',
            disable: false,
            taskHead : [{'product_category' : null, 'buyer' : null, 'product_style' : null, 'product_code' : null, 'smv': 0, 'manpower': 0, 'specification' : null, 'remarks' : null, 'product_image' : 'noimage.jpg'}],
            taskDetailsAll : [],
            taskDetails : [],
            taskHeadId : null,
            taskDetailsId : null,
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
        this.fetchData()

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

        change_department() {
            this.taskDetails = this.departmentChange
            if (this.taskDetails.length == 0) {
                this.taskDetails = [{'id': null, 'parts_name' : null, 'department': this.department, 'parts_image': null, 'parts_description' : null, 'parts_qty' : null, 'unit': null, 'remarks' : null, 'producthead_id' : this.taskHeadId}]
            }
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

        addRow() {            
            this.taskDetails.push({'id': null, 'parts_name' : null, 'department': this.department, 'parts_image': null, 'parts_description' : null, 'parts_qty' : null, 'unit': null, 'remarks' : null, 'producthead_id' : this.taskHeadId})
        },

        viewDetails(id) {
            this.taskHeadId = id
            this.noprint = 'noprint'
            fetch(`api/subpart/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetailsAll = res['productDetails']
                this.taskDetails = this.departmentChange
                this.taskHead = this.singleTask
            })
            .catch(err => {
                alert(err.response.data.message)
            })
            this.$refs['dataView'].show()
        },

        editDetails() {
            // this.$refs['dataView'].hide()
            this.title = this.$t('UpdateItem')
            if (this.taskDetails.length == 0) {
                this.taskDetails = [{'id': null, 'parts_name' : null, 'department': this.department, 'parts_image': null, 'parts_description' : null, 'parts_qty' : null, 'unit': null, 'remarks' : null, 'producthead_id' : this.taskHeadId}]
            }
            this.$refs['dataEdit'].show()         
        },

        uploadImage(e) {
            let file = e.target.files[0];
            var fileReader = new FileReader();
            
            if(file['size'] <= 262144 &&  file['type'].split('/')[0]=='image' ){          //256 KB ~~ 262144 Byte
                fileReader.onload = (e) => {
                    let options = {headers: {'enctype': 'multipart/form-data'}}
                    let data = {parts_image: e.target.result}

                    axios.patch(`api/subpart/${this.taskDetailsId}`, data, options)
                    .then(({data}) => {
                        for (let i = 0; i < this.taskDetails.length; i++) {
                            if (this.taskDetails[i]['id'] == this.taskDetailsId) {
                                this.taskDetails[i]['parts_image'] = data.fileName
                                break
                            }                            
                        }
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

            for (let i = 0; i < this.taskDetails.length; i++) {
                this.taskDetails[i]['department'] = this.department
                if (this.taskDetails[i]['parts_qty']) {
                    if(this.taskDetails[i]['id']){                            
                        axios.patch(`api/subpart/${this.taskDetails[i]['id']}`, this.taskDetails[i])
                        .then(({data})=>{
                            if (i == this.taskDetails.length - 1) {
                                this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                                this.disable = !this.disable
                                this.errors = ''
                                this.buttonTitle = this.$t('save')
                                this.$refs['dataEdit'].hide()
                            }
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
                        this.taskDetails[i]['parts_image'] = 'noimage.jpg'
                        axios.post(`api/subpart`, this.taskDetails[i])
                        .then(({data})=>{
                            this.taskDetails[i]['id'] = data.ProductdetailsID
                            this.taskDetailsAll.push(this.taskDetails[i])
                            
                            if (i == this.taskDetails.length - 1) {
                                this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                                this.disable = !this.disable
                                this.errors = ''
                                this.buttonTitle = this.$t('save')
                                this.$refs['dataEdit'].hide()
                            }
                        })
                        .catch(err => {
                            if(err.response.status == 422){
                                this.errors = err.response.data.errors
                                this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                            } else alert(err.response.data.message)
                            this.disable = !this.disable
                            this.buttonTitle = this.$t('save')
                        })
                    }
                }
            }            
        },

        destroy_d(id, index){
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {                        
                        if(id){
                            axios.delete(`api/subpart/${id}`)                        
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
        singleTask() {
            let id = this.taskHeadId
            return this.productList.filter(function (item) {
            return item['id'] == id
            })
        },

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        departmentChange() {
            let array = [], j=0
            for (let i = 0; i < this.taskDetailsAll.length; i++) {
                if (this.taskDetailsAll[i]['department'] == this.department) {
                    array[j++] = this.taskDetailsAll[i]
                }                    
            } return array
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_style', label : this.$t('style') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
            ]
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')

            return [
                { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'parts_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'parts_name', label : this.$t('name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'parts_description', label : this.$t('description'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'parts_qty', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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