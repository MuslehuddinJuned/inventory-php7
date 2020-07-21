<template>
    <div class="container justify-content-center">
       <div class="col-md-12">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('InventoryItem') }}</h3> 
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
                    :items="inventoryissueList"
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
                            <strong>Loading...</strong>
                        </div>
                    </template>
                    <template v-slot:cell(action)="row">
                        <!-- <a @click="viewDetails(row.item.machine_name, row.item.machine_description)" class="btn btn-sm text-black-50" data-toggle="modal" data-target="#dataView"><fa icon="eye" fixed-width /></a> -->
                        <a @click="editDetails(row.item.id, row.item.sn)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
                        <a @click="destroy(row.item.id, row.item.sn)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
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
                    <b-modal ref="dataEdit" id="dataEdit" :title="title" no-close-on-backdrop ok-only>
                        <div class="container">
                            <div class="modal-body">
                                <label class="col-form-label">{{ $t('store_name')}}</label>
                                <input list="StoreList" class="form-control" v-model="task[0]['store_name']">
                                <datalist id="StoreList">
                                    <option v-for="store_name in store_namelistview" :key="store_name.store_name">{{ store_name }}</option>
                                </datalist>
                                
                                <label class="col-form-label">{{ $t('item_code')}}</label>
                                <input type="text" class="form-control" v-model="task[0]['item_code']">
                                <span v-if="errors.item_code" class="error text-danger"> {{$t('required_field')}} <br></span>

                                <label class="col-form-label">{{ $t('item')}}</label>
                                <input type="text" class="form-control" v-model="task[0]['item']">
                                
                                <label class="col-form-label">{{ $t('specification')}}</label>
                                <input type="text" class="form-control" v-model="task[0]['specification']">
                                
                                <label class="col-form-label">{{ $t('unit')}}</label>
                                <input type="text" class="form-control" v-model="task[0]['unit']">                                
                            </div>
                            <div class="modal-footer">
                                <button @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                                <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color" data-dismiss="modal">{{$t('Close')}}</button>
                            </div>
                        </div>
                        <template v-slot:modal-footer="" class="d-none">
                            <b-button class="d-none">
                                Close
                            </b-button>
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
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('InventoryItem') }
    },

    data() {
        return{
            inventoryissueList : [],
            errors : [],
            name : '',
            description : '',
            Id : '',
            Index : '',
            title: '',
            disable: false,
            task : [{'store_name' : null,'item_code' : null,'item' : null,'specification' : null,'unit' : null, 'unit_price' : 0}],
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
        this.isBusy = true;
        fetch(`api/inventoryissue`)
            .then( res => res.json())
            .then(res => {  
                this.inventoryissueList = res['Inventoryissue'];
                this.totalRows = this.inventoryissueList.length;
                this.isBusy = false;

                for (let i = 0; i < this.totalRows; i++) {
                this.inventoryissueList[i]['sn'] = i                
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
        addDetails(){
            this.title = this.$t('InsertNewItem')
            this.task = [{'store_name' : null,'item_code' : null,'item' : null,'specification' : null,'unit' : null, 'unit_price' : 0}]
        },

        viewDetails() {

        },

        editDetails(id, index) {
            this.title = this.$t('UpdateItem')
            this.taskId = id
            this.Index = index
            this.task = this.singleTask
        },

        save() {
            this.disable = !this.disable;
            this.buttonTitle = this.$t('saving')

            if(this.taskId == null){
                axios.post(`api/inventory`, this.task[0])
                .then(({data}) =>{
                    this.errors = ''
                    this.inventoryissueList.unshift(data.InventoryList)
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
                axios.patch(`api/inventory/${this.taskId}`, this.task[0])
                    .then(res => {
                        this.errors = '';
                        this.inventoryissueList[this.Index] = this.task[0];
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
                            this.inventoryissueList.splice(index, 1);                            
                            this.totalRows = this.inventoryissueList.length;
                            for (let i = 0; i < this.totalRows; i++) {
                                this.inventoryissueList[i]['sn'] = i                
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
        singleTask() {
            let id = this.taskId
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
                // { key: 'store_name', label : this.$t('store_name'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold' },
                // { key: 'item_code', label : this.$t('item_code'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'item', label : this.$t('item'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'unit', label : this.$t('unit'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'unit_price', label : this.$t('unit_price'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'total_price', label : this.$t('total_price'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'action', label: this.$t('Action'),  class: 'text-right', thClass: 'border-top border-dark font-weight-bold'}
            ]
        },

        store_namelistview() {
            return uniq(this.inventoryissueList.map(({ store_name }) => store_name))
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