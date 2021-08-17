<template>
    <div class="container">
        <div class="card">
            <h3 v-if="module_no==1" class="card-header d-flex align-items-center">{{$t('inventory_mgt')}}</h3>
            <h3 v-else class="card-header d-flex align-items-center">{{$t('hrm')}}</h3>
            <div class="card-body">
                <div class="row col-12 m-0 p-0 mb-5">
                    <div v-if="module_no==1 && checkRoles('inventory_mgt_View')" @click="chooseModule(1)" class="col-6 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="module_no == 1" class="form btn-primary rounded-circle font-weight-bold" style="height: 30px; width: 30px;">1</button><br>{{$t('inventory_mgt')}}</div>
                    <div v-if="module_no!=1 && checkRoles('inventory_mgt_View')" @click="chooseModule(1)" class="col-6 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><buttoN class="form btn-outline-secondary rounded-circle" style="height: 30px; width: 30px;">1</button><br>{{$t('inventory_mgt')}}</div>
                    <div v-if="module_no==2 && checkRoles('hrm_View')" @click="chooseModule(2)" class="col-6 border-top border-left border-right border-primary p-3 border-5 text-center" style="cursor: pointer; border-radius:15px 15px 0px 0px;"><button v-if="module_no == 2" class="form btn-primary rounded-circle font-weight-bold" style="height: 30px; width: 30px;">2</button><br>{{$t('hrm')}}</div>
                    <div v-if="module_no!=2 && checkRoles('hrm_View')" @click="chooseModule(2)" class="col-6 border-bottom border-primary p-3 border-5 text-center" style="cursor: pointer;"><button class="form btn-outline-secondary rounded-circle font-weight-bold" style="height: 30px; width: 30px;">2</button><br>{{$t('hrm')}}</div>
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
                <div v-if="module_no == 2" class="col-12 m-0 p-0 mt-3 row">
                    <div class="col-md-6">
                        <div class="card m-2 p-0">
                            <div class="card-header m-0">
                                {{$t('notice_board')}}
                            </div>
                            <div class="card-body row">
                                <div class="col-3 bg-gradient-success m-0 d-flex align-items-center" style="min-height: 100px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">{{notice_date[0]}}<br>{{notice_time[0]}}</span>
                                </div>
                                <div class="col-9 bg-success m-0 py-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam, delectus, recusandae ipsa ipsam obcaecati ipsum quidem ea explicabo, labore sint vitae impedit dolor aliquid libero at cumque nam sunt autem.</div>
                                <div class="col-3 bg-gradient-primary m-0 d-flex align-items-center" style="min-height: 100px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">{{notice_date[1]}}<br>{{notice_time[1]}}</span>
                                </div>
                                <div class="col-9 bg-primary m-0 py-3">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Nobis inventore magni sapiente cumque distinctio debitis similique provident nostrum voluptates? Nostrum dolore tempora enim esse beatae aspernatur excepturi cum accusantium ea.</div>
                                <div class="col-3 bg-gradient-warning m-0 d-flex align-items-center" style="min-height: 100px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">{{notice_date[2]}}<br>{{notice_time[2]}}</span>
                                </div>
                                <div class="col-9 bg-warning m-0 py-3">Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias nihil quod id rem sed atque sint ex nisi exercitationem iste, iusto ad molestias? Nam modi similique fugiat hic velit assumenda!</div>
                                <div class="col-3 bg-gradient-info m-0 d-flex align-items-center" style="min-height: 100px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">{{notice_date[3]}}<br>{{notice_time[3]}}</span>
                                </div>
                                <div class="col-9 bg-info m-0 py-3">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Aut iste nihil nobis assumenda veniam impedit voluptate, aliquam aspernatur quis et libero maiores, earum unde placeat, inventore eos dicta corporis doloribus!</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card m-2 p-0">
                            <div class="card-header m-0">
                                {{$t('attendance')}}
                            </div>
                            <div class="card-body row">
                                <div class="col-6 bg-gradient-success m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">23 | 23 | 0</span>
                                </div>
                                <div class="col-6 bg-success m-0 py-3">Management</div>
                                <div class="col-6 bg-gradient-primary m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">135 | 132 | 3</span>
                                </div>
                                <div class="col-6 bg-primary m-0 py-3">Assembly</div>
                                <div class="col-6 bg-gradient-warning m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">61 | 56 | 5</span>
                                </div>
                                <div class="col-6 bg-warning m-0 py-3">Injection</div>
                                <div class="col-6 bg-gradient-info m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">10 | 10 | 0</span>
                                </div>
                                <div class="col-6 bg-info m-0 py-3">Maintenance</div>
                                <div class="col-6 bg-gradient-success m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">4 | 4 | 0</span>
                                </div>
                                <div class="col-6 bg-success m-0 py-3">Warehouse</div>
                                <div class="col-6 bg-gradient-primary m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">5 | 4 | 1</span>
                                </div>
                                <div class="col-6 bg-primary m-0 py-3">Cleaning</div>
                                <div class="col-6 bg-gradient-warning m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">14 | 14 | 0</span>
                                </div>
                                <div class="col-6 bg-warning m-0 py-3">Quality Control</div>
                                <div class="col-6 bg-gradient-info m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">268 | 259 | 9</span>
                                </div>
                                <div class="col-6 bg-info m-0 py-3">Production</div>
                                <div class="col-6 bg-gradient-success m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">520 | 502 | 18</span>
                                </div>
                                <div class="col-6 bg-success m-0 py-3 font-weight-bold">Total</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card m-2 p-0">
                            <div class="card-header m-0">
                                {{$t('anniversary')}}
                            </div>
                            <div class="card-body row">
                                <div class="col-3 bg-gradient-success m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">
                                        <h4 v-if="days_remain[0] == 0">Today</h4>
                                        <h4 v-else>{{days_remain[0]}} days</h4><br>
                                        <h6>{{anniversary_date[0]}}</h6>
                                    </span>
                                </div>
                                <div class="col-9 bg-success m-0 py-3">
                                    <h6 class="font-weight-bold text-white">Birth Anniversary</h6>
                                    <h5>Mehadi Kubad</h5>
                                    <h6>AGM, Admin</h6>
                                </div>
                                <div class="col-3 bg-gradient-primary m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">
                                        <h4 v-if="days_remain[1] == 0">Today</h4>
                                        <h4 v-else>{{days_remain[1]}} days</h4><br>
                                        <h6>{{anniversary_date[1]}}</h6>
                                    </span>
                                </div>
                                <div class="col-9 bg-primary m-0 py-3">
                                    <h6 class="font-weight-bold text-white">Birth Anniversary</h6>
                                    <h5>Amir Khosru Mahmud</h5>
                                    <h6>Manager, Accounts</h6>
                                </div>
                                <div class="col-3 bg-gradient-warning m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">
                                        <h4 v-if="days_remain[2] == 0">Today</h4>
                                        <h4 v-else>{{days_remain[2]}} days</h4><br>
                                        <h6>{{anniversary_date[2]}}</h6>
                                    </span>
                                </div>
                                <div class="col-9 bg-warning m-0 py-3">
                                    <h6 class="font-weight-bold text-white">Birth Anniversary</h6>
                                    <h5>Musleh Uddin</h5>
                                    <h6>Manager, Production</h6>
                                </div>
                                <div class="col-3 bg-gradient-info m-0 d-flex align-items-center" style="min-height: 50px;">
                                    <span class="font-weight-bold m-auto p-auto text-white">
                                        <h4 v-if="days_remain[3] == 0">Today</h4>
                                        <h4 v-else>{{days_remain[3]}} days</h4><br>
                                        <h6>{{anniversary_date[3]}}</h6>
                                    </span>
                                </div>
                                <div class="col-9 bg-info m-0 py-3">
                                    <h6 class="font-weight-bold text-white">Work Anniversary</h6>
                                    <h5>Mehadi Kubad</h5>
                                    <h6>AGM, Admin</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card m-2 p-0">
                            <div class="card-header m-0">
                                {{$t('holiday')}}
                            </div>
                            <div class="card-body p-0">
                                <b-table striped hover :items="holiday" :fields="fields">
                                    <template v-slot:cell(index)="row">
                                        {{ row.index+1 }}
                                    </template>
                                </b-table>
                            </div>
                        </div>
                    </div>
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
            anniversary_date: [this.noticeDate(0), this.noticeDate(3), this.noticeDate(6), this.noticeDate(15)],
            notice_date: [this.noticeDate(0), this.noticeDate(3), this.noticeDate(6), this.noticeDate(15)],
            notice_time: ['10:00 am', '2:30 pm', '3:00 pm', '11:00 am'],
            days_remain: [this.dayRemain(0), this.dayRemain(3), this.dayRemain(6), this.dayRemain(15)],
            holiday: [],
        }
    },

    mounted() {
        fetch(`api/settings/roles`)
        .then(res => res.json())
        .then(res => {
            this.roles = res['allRoles'];
        })

        fetch(`api/holiday`)
        .then(res => res.json())
        .then(res => {
            this.holiday = res['remainHoliday']
        })
        .catch(err => {
            alert(err.response.data.message);
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

        noticeDate(str) {
            var date = new Date(),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2),
                dated = parseInt(day) + str

            if (dated > 28) {
                dated = dated - 28
                mnth = parseInt(mnth) + 1
                if (mnth > 12) {
                    mnth = mnth - 12
                    year++
                }

                mnth = ("0" + mnth).slice(-2)
                dated = ("0" + dated).slice(-2)
            }
            return [year, mnth, dated].join("-");
        },

        dayRemain(date) {
            var startDate = new Date();
            var endDate = new Date(this.noticeDate(date));
            var diff = endDate.getTime() - startDate.getTime();
            var day = Math.ceil(diff / 1000 / 60 / 60 / 24);
            return day
        },
    },

    computed: {
        ...mapGetters({
            module_no: 'role/module_no'
        }),

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'yearly_holiday', label : this.$t('date'), class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'event', label : this.$t('event'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },
    }
}
</script>

<style lang="scss" scoped>
.border-5{
    border-width: 3px !important;
}
</style>