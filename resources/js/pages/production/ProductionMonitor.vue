<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('production_monitor')}}</h3>
                </div> 
                <div class="card-body">
                    <input type="date" v-model="etd">
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
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover responsive
                    :items="productionMonitoringByEtd"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    @row-clicked="(item) => editDetails(item.id)"
                    class="table-transition table-bordered"
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
                    <template v-slot:cell(product_image)="row">
                        <a :href="'/images/product/' + row.item.product_image"><b-img :src="'/images/product/' + row.item.product_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
                    </template>
                    <template v-slot:cell(material)="row">
                        {{row.item.material - row.item.quantity}} <br> 
                        <pre>{{row.item.material_remarks}}</pre>
                    </template>
                    <template v-slot:cell(carton)="row">
                        {{row.item.carton - row.item.quantity}} <br> 
                        <pre>{{row.item.carton_remarks}}</pre>
                    </template>
                    <template v-slot:cell(color_card)="row">
                        {{row.item.color_card - row.item.quantity}} <br> 
                        <pre>{{row.item.color_card_remarks}}</pre>
                    </template>
                    <template v-slot:cell(cutting)="row">
                        {{row.item.cutting - row.item.quantity}} <br> 
                        <pre>{{row.item.cutting_remarks}}</pre>
                    </template>
                    <template v-slot:cell(polish)="row">
                        {{row.item.polish - row.item.quantity}} <br> 
                        <pre>{{row.item.polish_remarks}}</pre>
                    </template>
                    <template v-slot:cell(injection)="row">
                        {{row.item.injection - row.item.quantity}} <br> 
                        <pre>{{row.item.injection_remarks}}</pre>
                    </template>
                    <template v-slot:cell(assembly)="row">
                        {{row.item.assembly - row.item.quantity}} <br> 
                        <pre>{{row.item.assembly_remarks}}</pre>
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
        <!-- Start edit Details Modal -->
        <b-modal ref="dataEdit" id="dataView" size="lg" :title="$t('production_monitor')" no-close-on-backdrop ok-only>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-3 text-center m-0">
                    <img style="width: 100%; " :src="'/images/product/' + productionMonitoringById['product_image']" alt="Picture not found">
                </div>
                <div class="col-md-9 row m-0 p-0">
                    <div class="col-4 bg-info text-white">{{$t('buyer')}}</div><div class="col-8 bg-info text-white">{{productionMonitoringById['buyer']}}</div>
                    <div class="col-4 bg-light">{{$t('style') + ' ' + $t('name')}}</div><div class="col-8 bg-light">{{productionMonitoringById['product_style']}}</div>
                    <div class="col-4 bg-info text-white">{{$t('style') + ' ' + $t('code')}}</div><div class="col-8 bg-info text-white">{{productionMonitoringById['product_code']}}</div>
                    <div class="col-4 bg-light">PO No</div><div class="col-8 bg-light">{{productionMonitoringById['po_no']}}</div>
                    <div class="col-4 bg-info text-white">ETD</div><div class="col-8 bg-info text-white">{{productionMonitoringById['etd']}}</div>
                    <div class="col-4 bg-light">{{$t('quantity')}}</div><div class="col-8 bg-light">{{productionMonitoringById['quantity']}}</div>
                </div>
                <div class="col-md-12 m-0 p-0">
                </div>
                <div class="col-md-12 m-0 p-0">
                    <div class="card-body m-0 p-0">
                        <b-table :items="productionByStore" :fields="productionFields" show-empty small striped hover stacked="md">
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(prod_qty)="row">
                            <input type="text"  v-model="row.item.prod_qty" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0">
                        </template>
                        <template v-slot:cell(remarks)="row">
                            <b-form-textarea v-model="row.item.remarks" rows="1" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></b-form-textarea>
                            <!-- <textarea v-model="row.item.remarks" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0"></textarea> -->
                        </template>
                        <template v-slot:cell(prod_date)="row">
                            <input type="date"  v-model="row.item.prod_date" class="form-control text-center row-fluid m-0 border-0 bg-transparent rounded-0">
                        </template>
                        <template v-slot:cell(action)="row">
                            <a v-if="checkRoles('production_Insert')" @click="addRow" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="plus" fixed-width /></a>
                            <a v-if="checkRoles('production_Delete')" @click="destroy(row.item.id, row.index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                        </template>
                        <template slot="bottom-row">
                            <td class="text-white bg-info font-weight-bold text-center" colspan="2">Total</td>
                            <td class="text-white bg-info font-weight-bold text-center">{{Total}}</td>
                            <td class="text-white bg-info font-weight-bold text-center" colspan="2"></td>
                        </template>
                        </b-table>
                    </div>
                </div>
            </div>
            <template v-slot:modal-header="">
                <b-form-select @change="prodStoreMethod" v-model="prodStore" :options="prodStoreList" value-field="id" text-field="name"></b-form-select>
            </template>
            <template v-slot:modal-footer="">
                <button v-if="checkRoles('production_Update')" @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="hideModal" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End edit Details Modal -->  
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('production_monitor') }
    },

    data() {
        return{
            productionMonitoring : [],
            prodStoreList : [],
            productionByStore: [],
            prodStore : 1,
            Production: [],
            poId: null,
            etd: this.convertDate(new Date()),
            noprint : '',
            buttonTitle : this.$t('save'),
            disable: false,
            check: false,

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            totalRows: 1,
            currentPage: 1,
            perPage: 25,
            pageOptions: [25, 50, 100],
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        this.isBusy = true;

        fetch(`api/production`)
        .then(res => res.json())
        .then(res => {
            this.productionMonitoring = res['Production']
            this.isBusy = false
            this.prodStoreList = res['Prodstore']
            for (let i = 0; i < this.prodStoreList.length; i++) {
                this.prodStoreList[i]['name'] = this.$t(this.prodStoreList[i]['name']);                
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

        checkRoles(role) {
            for (let i = 0; i < this.roles.length; i++) {
                if (this.roles[i]['name'] == role) {
                    return true
                }                
            } return false
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        editDetails(id) {
            this.poId = id
            this.noprint = 'noprint'
            this.check = false
            fetch(`api/production/${id}`)
            .then(res => res.json())
            .then(res => {
                this.Production = res['Production']
                this.productionByStore = this.productionByStoreMethod
            })
            .catch(err => {
                alert(err.response.data.message);
            })
            this.$refs['dataEdit'].show()
        },

        prodStoreMethod() {
            this.productionByStore = this.productionByStoreMethod
        },

        hideModal() {
            this.isBusy = true
            if (this.check) {                
                fetch(`api/production`)
                .then(res => res.json())
                .then(res => {
                    this.productionMonitoring = res['Production']
                    this.isBusy = false
                    this.noprint = ''
                    this.$refs['dataEdit'].hide()
                })
            } else {
                this.isBusy = false
                this.noprint = ''
                this.$refs['dataEdit'].hide()
            }
            
        },

        addRow() {            
            this.productionByStore.push({'prod_date' : this.convertDate(new Date()), 'prod_qty' : 0, 'remarks': null, 'polist_id': this.poId, 'producthead_id': this.productionMonitoringById['producthead_id'], 'prodstore_id': this.prodStore})
        },

        save() {
            this.disable = !this.disable;
            this.buttonTitle = this.$t('saving')
            for (let i = 0; i < this.productionByStore.length; i++) {
                if(this.productionByStore[i]['id']){
                    axios.patch(`api/production/${this.productionByStore[i]['id']}`, this.productionByStore[i])
                    .then(res => {
                        if(i == this.productionByStore.length-1) {
                            this.disable = !this.disable;
                            this.buttonTitle = this.$t('save')
                            this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                            this.check = true 
                        }
                    })
                    .catch(err => {
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                        alert(err.response.data.message)  
                    })
                } else {
                    axios.post(`api/production`, this.productionByStore[i])
                    .then(res => {
                        if(i == this.productionByStore.length-1) {
                            this.disable = !this.disable;
                            this.buttonTitle = this.$t('save')
                            this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                            this.check = true 
                        }
                    })
                    .catch(err => {
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                        alert(err.response.data.message)  
                    })
                }                          
            }
        },

        destroy(id, index){
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {                        
                        if(id){
                            axios.delete(`api/production/${id}`)                        
                            .then(res => {
                                this.productionByStore.splice(index, 1)                                
                            })
                            .catch(err => {
                                alert(err.response.data.message);                       
                            });
                        } else {
                            this.productionByStore.splice(index, 1)
                        }

                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }, true],
                    ['<button>'+ this.$t('cancel') +'</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }],
                ]            
            });
        },
    },

    computed: {
        roles() {
            return JSON.parse(localStorage.getItem("roles"))
        },
        
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        productionMonitoringByEtd() {
            let array = [], k=0
            for (let i = 0; i < this.productionMonitoring.length; i++) {
                if (this.productionMonitoring[i]['etd'] == this.etd) {
                    array[k++] = this.productionMonitoring[i]
                }                
            }
            return array
        },

        productionMonitoringById() {
            let array = [], k=0
            for (let i = 0; i < this.productionMonitoringByEtd.length; i++) {
                if (this.productionMonitoringByEtd[i]['id'] == this.poId) {
                    array = this.productionMonitoringByEtd[i]
                    break
                }                
            }
            return array
        },

        productionByStoreMethod() {
            let array = [], k=0
            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['prodstore_id'] == this.prodStore) {
                    array[k++] = this.Production[i]
                }                
            }
            if (array.length == 0) return [{'prod_date' : this.convertDate(new Date()), 'prod_qty' : 0, 'remarks': null, 'polist_id': this.poId, 'producthead_id': this.productionMonitoringById['producthead_id'], 'prodstore_id': this.prodStore}]
            return array 
        },

        Total() {
            let t = 0
            for (let i = 0; i < this.productionByStore.length; i++) {
                t += (parseFloat(this.productionByStore[i]['prod_qty']) || 0)                
            } return t
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                // { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_style', label : this.$t('style') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'po_no', label : 'PO No', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'material', label : this.$t('material'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'carton', label : this.$t('carton'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'color_card', label : this.$t('color_card'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'cutting', label : this.$t('cutting'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'polish', label : this.$t('polish'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'injection', label : this.$t('injection'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'assembly', label : this.$t('assembly'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        productionFields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'prod_date', label : this.$t('date'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'prod_qty', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'remarks', label : this.$t('remarks'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'action', label: this.$t('Action'),  class: 'text-right align-middle', thClass: 'border-top border-dark font-weight-bold'}
            ]
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    },
}
</script>

<style>

</style>