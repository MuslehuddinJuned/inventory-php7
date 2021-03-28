<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('parts_production')}}</h3>
                </div> 
                <div class="card-body">
                    <div class="col-md-4 float-left">
                        <label for="po_no">{{$t('department')}}</label>
                        <select @change="departmentChange" class="form-control" v-model="department">
                            <option value="assembly">{{ $t('assembly') }}</option>
                            <option value="spray">{{ $t('spray') }}</option>
                            <option value="polish">{{ $t('polish') }}</option>
                            <option value="injection">{{ $t('injection') }}</option>
                            <option value="cutting">{{ $t('cutting') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 float-left">
                        <label for="po_no">PO No</label>
                        <model-select :options="PoList" v-model="po_no" class="form-control"></model-select>
                    </div>
                    <div class="col-md-4 float-left">
                        <label for="po_no">{{$t('production_date')}}</label>
                        <div class="col-md-12 pl-0 input-group">
                            <input type="date" class="form-control" v-model="prodDate">
                            <div @click="searchByDate" class="input-group-append input-group-text pointer noprint"><b-icon icon="search"></b-icon></div>
                        </div>
                    </div> 
                    <!-- <div v-if="Production.length > 0" class="col-12 float-left input-group mt-3">
                        <model-select :options="ProductionByDeparment" v-model="production_id" class="form-control col-11 float-left"></model-select>
                        <div @click="addRow" class="input-group-append input-group-text pointer noprint float-left"><b-icon icon="plus"></b-icon></div>
                    </div> -->
                    <div v-if="Production.length > 0" class="col-12 mt-3 rounded-pill py-3 bg-info text-white float-left">
                        <div class="text-center">
                            <span class="mr-4">{{$t('buyer')}} : {{Production[0]['buyer']}}</span>
                            <span class="mr-4">{{$t('style')+ ' ' + $t('code')}} : {{Production[0]['product_code']}}</span>
                            <span class="mr-4">{{$t('quantity')}} : {{Production[0]['po_qty']}}</span>
                            <span class="mr-4">PO No : {{Production[0]['po_no']}}</span>
                        </div>
                    </div>
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
                        <button class="mdb btn btn-outline-default"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>                     
                    </div>
                    <b-table id="table-transition" primary-key="subpart_id" :busy="isBusy" show-empty small striped hover responsive
                    :items="ProductionByDeparment"
                    :fields="fields"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    class="table-transition table-bordered"
                    style="cursor : pointer"
                    >
                    <template v-slot:table-busy>
                        <div class="text-center align-middle text-success my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>{{$t('loading')}}</strong>
                        </div>
                    </template>
                    <template v-slot:cell(action)="row">
                        <a @click="destroy_d(row.item.id, row.index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                        <a @click="row.toggleDetails" class="btn btn-sm text-black-50"><fa icon="eye" fixed-width /></a>
                    </template>
                    <template v-slot:cell(quantity)="row">
                        <span v-if="!checkRoles('production_Insert')">{{row.item.quantity}}</span>
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.quantity" v-if="checkRoles('production_Insert')"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(parts_qty)="row">
                        {{(row.item.parts_qty || 0) * (row.item.po_qty || 0)}}
                    </template>
                    <template v-slot:cell(complete)="row">
                        {{row.item.complete  = parseFloat(row.item.total_prod_qty || 0) + parseFloat(row.item.quantity || 0)}}
                    </template>
                    <template v-slot:cell(balance)="row">
                        {{row.item.balance = row.item.complete - (row.item.parts_qty || 0) * (row.item.po_qty || 0)}}
                    </template>
                    <template #row-details="row">
                        <b-card>
                            <ul>
                                <li v-for="value in ProductionByDeparmentOld" :key="value.id">{{ value.text }} || {{ value.quantity }} || {{ value.prod_date }}</li>
                            </ul>
                        </b-card>
                    </template>
                    </b-table>
                </div>
            </div>
        </div> 

    </div>
</template>

<script>
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('parts_production') }
    },

    data() {
        return{
            PoList : [],
            po_no: null,
            Production: [],
            ProductionOld: [],
            production_id: null,
            ProductionByDeparment: [],
            ProductionByDeparmentOld: [],
            roles: [],
            department: 'injection',
            prodDate: this.convertDate(new Date()),
            noprint : '',
            disable: false,
            waiting: false,
            buttonTitle : this.$t('save'),

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        fetch(`api/prodparts`)
        .then(res => res.json())
        .then(res => {
            this.PoList = res['PoList']
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

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        fetchData() {
            this.isBusy = true
            fetch(`api/prodparts/production/${this.department}/${this.po_no}/${this.prodDate}`)
            .then(res => res.json())
            .then(res => {
                this.Production = res['Production']
                this.ProductionByDeparment = this.ProductionByDeparmentMethod
                this.ProductionOld = res['Production_old']
                this.ProductionByDeparmentOld = this.ProductionByDeparmentMethodOld
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        searchByDate() {
            this.fetchData()
        },

        departmentChange() {
            this.ProductionByDeparment = []
        },

        all_production() {
            this.isBusy = true
            fetch(`api/prodparts/production/${this.department}/${this.po_no}/all`)
            .then(res => res.json())
            .then(res => {
                this.ProductionOld = res['Production']
                this.ProductionByDeparmentOld = this.ProductionByDeparmentMethodOld
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        addRow() {
            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['subpart_id'] == this.production_id) {
                    for (let j = 0; j < this.ProductionByDeparment.length; j++) {
                        if(this.Production[i]['subpart_id'] == this.ProductionByDeparment[j]['subpart_id']){
                            this.$toast.error(this.$t('error_alert_text'), this.$t('error_alert_title'), {timeout: 3000, position: 'center'})
                            return
                        }                        
                    }                     
                    this.ProductionByDeparment.push(this.Production[i])
                    break
                }                
            }
        },

        lazySaving(value) {
            this.disable = !this.disable;
            this.buttonTitle = this.$t('saving')
                
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }

            this.timer = setTimeout(() => {
                this.save(value);
            }, 1000);
            
        },

        save(value) {
            if (this.waiting) return
            value['department'] = this.department
            value['polist_id'] = this.po_no
            value['prod_date'] = this.prodDate
            if(!value['id']){
                this.waiting = true
                axios.post(`api/prodparts`, value)
                .then(({data}) =>{
                    value['id'] = data.id
                    this.waiting = false
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    }
                    this.waiting = false
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    alert(err.response.data.message)                      
                })
            } else {
                axios.patch(`api/prodparts/${value['id']}`, value)
                .then(res => {
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

        destroy_d(id, index){
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {                        
                        if(id){
                            axios.delete(`api/prodparts/${id}`)                        
                            .then(res => {
                                this.ProductionByDeparment.splice(index, 1)                                
                            })
                            .catch(err => {
                                alert(err.response.data.message);                       
                            });
                        } else {
                            this.ProductionByDeparment.splice(index, 1)
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
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        ProductionByDeparmentMethod() {
            let array = [], k=0

            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['department'] == this.department) {
                    array[k++] = this.Production[i]
                }                
            }
            return array 
        },

        ProductionByDeparmentMethodOld() {
            let array = [], k=0

            for (let i = 0; i < this.ProductionOld.length; i++) {
                if (this.ProductionOld[i]['department'] == this.department) {
                    array[k++] = this.ProductionOld[i]
                }                
            }
            return array 
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'action', label : '#', class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'parts_name', label : this.$t('name'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'parts_description', label : this.$t('description'), class: 'text-center align-middle', tdClass: 'p-0', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'unit', label : this.$t('unit'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'parts_qty', label : this.$t('quantity'), class: 'text-center align-middle', tdClass: 'p-0', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'complete', label : this.$t('complete'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'balance', label : this.$t('balance'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'quantity', label : this.$t('production'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
            ]
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