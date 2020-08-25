<template>
    <div class="container-fluid justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('po_list') }}</h3>                     
                    <div class="ml-auto">
                        <button @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
                <div class="card-header d-flex align-items-center input-group">
                    <label class="col-form-label mr-2">{{ $t('PO No:')}}</label>
                    <div class="col-md-6">
                        <model-select :options="PoNoListView" class="form-control" v-model="po_no"></model-select>
                    </div>
                        <button @click="po_change" class="btn btn-secondary input-group-append noprint"><b-icon icon="search"></b-icon></button>
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
                    :items="PoList"
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
                    <template v-slot:cell(po_date)="row">
                        {{`${row.item.po_date}` | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')}}
                    </template>
                    <template v-slot:cell(product_image)="row">
                        <a :href="'/images/product/' + row.item.product_image"><b-img :src="'/images/product/' + row.item.product_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                    </template>
                    <template v-slot:cell(action)="row">
                        <a @click="editDetails(row.item.id, row.item.sn)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
                        <a @click="destroy(row.item.id, row.item.sn)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
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
                    <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="title" no-close-on-backdrop>
                        
                        <div class="modal-body row m-0 p-0">
                            <div class="col-md-8 row m-0 p-0">
                                <div class="col-md-12">
                                    
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('PO No')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['po_no']">
                                    <span v-if="errors.po_no" class="error text-danger"> {{$t('required_field')}} <br></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('grade')}}</label>
                                    <input type="date" class="form-control" v-model="task[0]['po_date']">
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('buyer')}}</label>
                                    <b-form-select @change="change_buyer" id="buyer" v-model="buyer" :options="buyerlistview"></b-form-select>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('style') + ' ' + $t('code')}}</label>
                                    <b-form-select v-model="task[0]['product_code']" :options="product_codelistview"></b-form-select>
                                    <span v-if="errors.item_code" class="error text-danger"> {{$t('required_field')}} <br></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">{{ $t('quantity')}}</label>
                                    <input type="text" class="form-control" v-model="task[0]['quantity']">
                                    <span v-if="errors.quantity" class="error text-danger"> {{$t('required_field')}} <br></span>
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
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('po_list') }
    },

    data() {
        return{
            PoList : [],
            PoListAll : [],
            errors : [],
            po_no : null,
            buyer : null,
            productList : [],
            product_codelistview: [],
            noprint: 'noprint',
            Id : '',
            Index : '',
            title: '',
            disable: false,
            task : [{'store_id' : null,'item_code' : null,'item' : null,'specification' : null, 'grade' : null, 'accounts_code' : null, 'weight' : null, 'cann_per_sheet' : null, 'unit' : null, 'unit_price' : 0, 'item_image' : 'noimage.jpg'}],
            taskId : null,
            buttonTitle : this.$t('save'),

            src : '/images/item/',

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
        this.src = '/images/product/'
        fetch(`api/polist`)
        .then( res => res.json())
        .then(res => {  
            this.PoListAll = res['PoList']
            this.productList = res['productList']
            this.PoList = this.PoListByPoNo;
            this.totalRows = this.PoList.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.PoList[i]['sn'] = i                
            }
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

        addDetails(){
            this.taskId = null
            this.title = this.$t('InsertNewItem')
            this.task = [{'store_id' : null,'item_code' : null,'item' : null,'specification' : null, 'grade' : null, 'accounts_code' : null, 'weight' : null, 'cann_per_sheet' : null, 'unit' : null, 'unit_price' : 0, 'item_image' : 'noimage.jpg'}]
        },

        po_change() {
            this.PoList = this.PoListByPoNo;
            this.totalRows = this.PoListByPoNo.length;
            for (let i = 0; i < this.totalRows; i++) {
                this.PoList[i]['sn'] = i                
            }
        },

        change_buyer() {
            let array = []
            for (let i = 0; i < this.productList.length; i++) {
                if (this.productList[i]['buyer'] == this.buyer) {
                    array[i] = this.productList[i] 
                }               
            }
            this.product_codelistview = array
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
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
                    this.PoList.unshift(data.PoList)
                    this.totalRows = this.PoList.length;
                    for (let i = 0; i < this.totalRows; i++) {
                        this.PoList[i]['sn'] = i                
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
                    this.PoList[this.Index] = this.task[0];
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
                            this.PoList.splice(index, 1);
                            this.totalRows = this.PoList.length

                            for (let i = 0; i < this.totalRows; i++) {
                                this.PoList[i]['sn'] = i                
                            }
                            for (let i = 0; i < this.PoListAll.length; i++) {
                                if(this.PoListAll[i]['id'] == id){
                                    this.PoListAll.splice(i, 1);
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
        PoListByPoNo() {
            let po = this.po_no            
            return this.PoListAll.filter(function (item) {
                return item['po_no'] == po
            })            
        },

        PoNoList(){
            return uniq(this.PoListAll.map(({ po_no }) => po_no))
        },

        PoNoListView(){
            let array = []
            for (let i = 0; i < this.PoNoList.length; i++) {                    
                array.unshift({'value' : this.PoNoList[i], 'text' : this.PoNoList[i]})
            }
            return array
        },

        buyerlistview() {
            return uniq(this.productList.map(({ buyer }) => buyer))
        },

        singleTask() {
            let id = this.taskId
            return this.PoList.filter(function (item) {
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
                { key: 'index', label : '#', sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_date', label : this.$t('PO Date'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'action', label: this.$t('Action'),  class: 'text-right', thClass: 'border-top border-dark font-weight-bold'}
            ]           
            
        },

        store_namelistview() {
            return uniq(this.PoList.map(({ store_name }) => store_name))
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