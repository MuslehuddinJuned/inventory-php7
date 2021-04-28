<template>
    <div class="container">
        <div class="card">
            <h3 v-if="module_no==1" class="card-header m-0">{{$t('inventory_mgt')}}</h3>
            <h3 v-else class="card-header m-0">{{$t('hrm')}}</h3>
            <div class="card-body">
                <div class="row col-12 m-0 p-0 mb-5">
                    <div v-if="module_no==1 && checkRoles('inventory_mgt_View')" @click="chooseModule(1)" class="col-6 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="module_no == 1" class="form btn-primary rounded-circle font-weight-bold">1</button><br>{{$t('inventory_mgt')}}</div>
                    <div v-if="module_no!=1 && checkRoles('inventory_mgt_View')" @click="chooseModule(1)" class="col-6 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><buttoN class="form btn-outline-secondary rounded-circle">1</button><br>{{$t('inventory_mgt')}}</div>
                    <div v-if="module_no==2 && checkRoles('hrm_View')" @click="chooseModule(2)" class="col-6 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="module_no == 2" class="form btn-primary rounded-circle font-weight-bold">2</button><br>{{$t('hrm')}}</div>
                    <div v-if="module_no!=2 && checkRoles('hrm_View')" @click="chooseModule(2)" class="col-6 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button class="form btn-outline-secondary rounded-circle font-weight-bold">2</button><br>{{$t('hrm')}}</div>
                </div>
                <div v-if="module_no == 1" class="row col-12 m-0 p-0">
                    <div class="col-md-6"><button v-if="checkRoles('InventoryItem_View')" @click="$router.push({ name: 'inventory.InventoryList' })" class="mdb btn btn-outline-primary col-12" type="button" ><b-icon icon="cart-check"></b-icon> {{$t('InventoryItem')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('ItemReceive_View')" @click="$router.push({ name: 'inventory.InventoryReceive' })" class="mdb btn btn-outline-primary col-12" type="button" ><b-icon icon="cart-plus"></b-icon> {{$t('ItemReceive')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('ItemIssue_View')" @click="$router.push({ name: 'inventory.InventoryIssue' })" class="mdb btn btn-outline-primary col-12" type="button" ><b-icon icon="cart-dash"></b-icon> {{$t('ItemIssue')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('requisition_View')" @click="$router.push({ name: 'inventory.RequisitionList' })" class="mdb btn btn-outline-primary col-12" type="button" ><b-icon icon="basket2-fill"></b-icon> {{$t('requisition')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('InventoryItem_View')" @click="$router.push({ name: 'inventory.BalanceSheet' })" class="mdb btn btn-outline-primary col-12" type="button" ><fa icon="balance-scale-left" fixed-width/> {{$t('balance_sheet')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('product_details_View')" @click="$router.push({ name: 'product.ProductList' })" class="mdb btn btn-outline-success col-12" type="button" ><fa icon="utensils" fixed-width/> {{$t('product_list')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('product_details_View')" @click="$router.push({ name: 'product.ProductParts' })" class="mdb btn btn-outline-success col-12" type="button" ><b-icon icon="columns-gap"></b-icon> {{$t('product_parts')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('po_list_View')" @click="$router.push({ name: 'po.PoList' })" class="mdb btn btn-outline-success col-12" type="button" ><b-icon icon="alarm"></b-icon> {{$t('po_list')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('monitor_etd_View')" @click="$router.push({ name: 'po.EtdMonitor' })" class="mdb btn btn-outline-success col-12" type="button" ><b-icon icon="alarm-fill"></b-icon> {{$t('monitor_etd')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('production_Insert')" @click="$router.push({ name: 'production.HourlyProduction' })" class="mdb btn btn-outline-info col-12" type="button" ><b-icon icon="bricks"></b-icon> {{$t('hourly_production')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('production_View')" @click="$router.push({ name: 'production.DailyProduction' })" class="mdb btn btn-outline-info col-12" type="button" ><b-icon icon="graph-up"></b-icon> {{$t('daily_production')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('production_Insert')" @click="$router.push({ name: 'production.PartsProduction' })" class="mdb btn btn-outline-info col-12" type="button" ><b-icon icon="columns-gap"></b-icon> {{$t('parts_production')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('production_View')" @click="$router.push({ name: 'production.PartsProductionReport' })" class="mdb btn btn-outline-info col-12" type="button" ><b-icon icon="columns"></b-icon> {{$t('parts_production_report')}}</button></div>
                    <div class="col-md-6"><button v-if="checkRoles('production_View')" @click="$router.push({ name: 'production.MonitorPartsProduction' })" class="mdb btn btn-outline-info col-12" type="button" ><b-iconstack><b-icon stacked icon="columns" scale="0.5" shift-h="-1" shift-v="1"></b-icon><b-icon stacked icon="search" scale="1.15"></b-icon></b-iconstack> {{$t('monitor_parts_production')}}</button></div>
                </div>
                <div v-if="module_no == 2" class="col-12 mt-3">
                    <h3 class="text-center mx-auto">Welcome to Human Resource Management Software</h3><br>
                    <div v-if="checkRoles('hrm_View')" class="col-md-6 text-center mx-auto"><b-img @click="chooseModule(2)" thumbnail fluid src="https://i.pinimg.com/originals/db/47/87/db4787c8e7fbf41afafaa33d3883bf52.jpg" :alt="$t('hrm')" style="cursor: pointer;"></b-img></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
    name: "todos",
    data() {
        return{
            roles: [],
        }
    },

    mounted() {
        fetch(`api/settings/roles`)
        .then(res => res.json())
        .then(res => {
            this.roles = res['allRoles'];
        })
    },

    created() {
        this.$store.dispatch('role/chooseModule')
    },

    methods: {
        chooseModule(id) {
            localStorage.setItem("module_no" , id)
            this.$store.dispatch('role/chooseModule')
        },

        checkRoles(role) {
            for (let i = 0; i < this.roles.length; i++) {
                if (this.roles[i]['name'] == role) {
                    return true
                }                
            } return false
        },
    },

    computed: {
        ...mapGetters({
            module_no: 'role/module_no'
        }),
    }
}
</script>

<style lang="scss" scoped>
.border-5{
    border-width: 3px !important;
}
</style>