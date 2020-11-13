<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('daily_production')}}</h3>
                </div> 
                <div class="card-body">
                    <div class="col-md-6 float-left">
                        <b-form-select v-model="prodStore" :options="prodStoreList" value-field="id" text-field="name"></b-form-select>
                    </div>
                    <div class="col-md-6 float-left input-group">
                        <input type="date" class="form-control" v-model="etd">
                        <div @click="searchByDate" class="input-group-append input-group-text pointer"><b-icon icon="search"></b-icon></div>
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
                    :items="productionByStore"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    class="table-transition"
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
                        {{row.item.material - row.item.quantity}} <br> {{row.item.material_remarks}}
                    </template>
                    <template v-slot:cell(carton)="row">
                        {{row.item.carton - row.item.quantity}} <br> {{row.item.carton_remarks}}
                    </template>
                    <template v-slot:cell(color_card)="row">
                        {{row.item.color_card - row.item.quantity}} <br> {{row.item.color_card_remarks}}
                    </template>
                    <template v-slot:cell(cutting)="row">
                        {{row.item.cutting - row.item.quantity}} <br> {{row.item.cutting_remarks}}
                    </template>
                    <template v-slot:cell(polish)="row">
                        {{row.item.polish - row.item.quantity}} <br> {{row.item.polish_remarks}}
                    </template>
                    <template v-slot:cell(injection)="row">
                        {{row.item.injection - row.item.quantity}} <br> {{row.item.injection_remarks}}
                    </template>
                    <template v-slot:cell(assembly)="row">
                        {{row.item.assembly - row.item.quantity}} <br> {{row.item.assembly_remarks}}
                    </template>
                    <template slot="bottom-row">
                        <td class="text-white bg-info font-weight-bold text-center" colspan="8">Total</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total}}</td>
                        <td class="text-white bg-info font-weight-bold text-center" colspan="2"></td>
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
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('daily_production') }
    },

    data() {
        return{
            prodStoreList : [],
            prodStore : 1,
            Production: [],
            // productionByStore: [],
            etd: this.convertDate(new Date()),
            noprint : '',

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
        fetch(`api/prodstore`)
        .then(res => res.json())
        .then(res => {
            this.prodStoreList = res['Prodstore']
            for (let i = 0; i < this.prodStoreList.length; i++) {
                this.prodStoreList[i]['name'] = this.$t(this.prodStoreList[i]['name']);                
            }
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

        searchByDate() {
            console.log('hi')
            fetch(`api/prodstore/${this.etd}`)
            .then(res => res.json())
            .then(res => {
                this.Production = res['Production']
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },
    },

    computed: {
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        productionByStore() {
            let array = [], k=0
            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['prodstore_id'] == this.prodStore) {
                    array[k++] = this.Production[i]
                }                
            }
            this.totalRows = array.length
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
            
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'buyer', label : this.$t('buyer'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'product_code', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'product_style', label : this.$t('style') + ' ' + this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'etd', label : 'ETD', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'po_no', label : 'PO No', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'prod_qty', label : this.$t('production'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'remain_qty', label : this.$t('remain'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'remarks', label : this.$t('remarks'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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