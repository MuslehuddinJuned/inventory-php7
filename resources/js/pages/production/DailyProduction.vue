<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('daily_production')}}</h3>
                </div> 
                <div class="card-body">
                    <div class="col-md-6 float-left">
                        <select class="form-control" v-model="department">
                            <option>{{ $t('assembly') }}</option>
                            <option>{{ $t('polish') }}</option>
                            <option>{{ $t('wash') }}</option>
                            <option>{{ $t('injection') }}</option>
                            <option>{{ $t('cutting') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 float-left input-group">
                        <input type="date" class="form-control" v-model="searchDate">
                        <div @click="searchByDate" class="input-group-append input-group-text pointer"><b-icon icon="search"></b-icon></div>
                    </div>
                    
                </div>
                <div class="card-body m-0 p-0">
                    <div class="card-header d-flex align-items-center noprint">
                        <download-excel
                            id="tooltip-target-1"
                            class="btn btn-outline-default btn-sm mr-3"
                            :title="'Production Report: ' + searchDate"
                            :data="productionByDept"
                            :fields="json_fields"
                            worksheet="Daily Production"
                            name="Daily Production.xls">
                            <b-icon icon="file-earmark-spreadsheet-fill"></b-icon>
                        </download-excel>
                        <b-tooltip target="tooltip-target-1" triggers="hover">
                            Save this table to Excel
                        </b-tooltip>
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
                    </div>
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover stacked="md"
                    :items="productionByDept"
                    :fields="fields"
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
                    <template v-slot:cell(achievement)="row">
                        {{(row.item.achievement || 0).toFixed(0)}}
                    </template>
                    <template slot="bottom-row">
                        <td class="text-white bg-info font-weight-bold text-center" colspan="12">Total</td>
                        <td class="text-white bg-info font-weight-bold text-center">{{Total}}</td>
                        <td class="text-white bg-info font-weight-bold text-center" colspan="2"></td>
                    </template>
                    </b-table>

                    <div v-if="productionByDept.length > 0" class="card-body" id="chart">
                        <apexchart type="bar" height="350" :options="chartOptions" :series="series"></apexchart>
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
            Production: [],
            department: this.$t('assembly'),
            // productionByDept: [],
            searchDate: this.convertDate(new Date()),
            noprint : '',

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            filter: null,
            filterOn: [],
            isBusy: false,

            json_fields: {
                'Line': 'line',
                'Section': 'section',
                'Item': 'item',
                'ETD': 'etd',
                'Quantity': 'quantity',
                'Balance': 'balance',
                'Manpower': 'manpower',
                'Hourly Target': 'hourly_target',
                'Working Hour': 'work_hour',
                'Total Target': 'total_target',
                'Achieve': 'daily_prod',
                'Achievement (%)': 'achievement',
                'Leader': 'leader',
                'Remarks': 'remarks',
            },

            //for chart
            dailyProduction : [],
            dataLabelX : [],
            series: [],
            chartOptions: {
                chart: { height: 350, type: 'bar', zoom: { enabled: true } },
                plotOptions: {
                    bar: { 
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) { return val + "%"; },
                    offsetY: -20,
                    style: { fontSize: '12px', colors: ["#304758"] }
                },
                
                xaxis: {
                    categories: [],
                    position: 'bottom',
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    crosshairs: {
                        fill: {
                            type: 'gradient',
                            gradient: {
                                colorFrom: '#D8E3F0',
                                colorTo: '#BED1E6',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            }
                        }
                    },
                    tooltip: { enabled: true, }
                },
                yaxis: {
                    axisBorder: { show: true }, axisTicks: { show: true, },
                    labels: { show: true, formatter: function (val) { return val + "%";} }                
                },
            },

            // end chart
        }
    },

    mounted() {
        this.isBusy = true
        fetch(`api/prodstore/${this.searchDate}`)
        .then(res => res.json())
        .then(res => {
            this.Production = res['Production']
            this.getGraph()
            this.isBusy = false
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
            fetch(`api/prodstore/${this.searchDate}`)
            .then(res => res.json())
            .then(res => {
                this.Production = res['Production']
                this.getGraph()
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        getGraph() {
            for (let i = 0; i < this.productionByDept.length; i++) {
                this.dailyProduction[i] = (this.productionByDept[i]['achievement'] || 0).toFixed(0)
                this.dataLabelX[i] = this.productionByDept[i]['line'] + '>> ' + this.productionByDept[i]['item']
            }
            if (this.productionByDept.length == 0) {
                this.dailyProduction = []
            }

            this.series=[{
                name: this.$t('daily_production'),
                data: this.dailyProduction
            }]
            this.chartOptions = {'xaxis' : {'categories': this.dataLabelX}}
        },
    },

    computed: {
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        productionByDept() {
            let array = [], k=0
            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['department'] == this.department) {
                    array[k++] = this.Production[i]
                }                
            }
            return array 
        },

        Total() {
            let t = 0, a= 0
            for (let i = 0; i < this.productionByDept.length; i++) {
                t += (parseFloat(this.productionByDept[i]['total_target']) || 0)                
                a += (parseFloat(this.productionByDept[i]['daily_prod']) || 0)                
            } 
            return (a*100/t).toFixed(0)
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                // { key: 'product_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'line', label : this.$t('line'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'section', label : this.$t('section'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold' },
                { key: 'item', label : this.$t('style') + ' ' + this.$t('code'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'etd', label : 'ETD', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'quantity', label : this.$t('quantity'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'balance', label : this.$t('balance'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'manpower', label : this.$t('manpower'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'hourly_target', label : this.$t('hourly_target'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'work_hour', label : this.$t('work_hour'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_target', label : this.$t('total_target'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'daily_prod', label : this.$t('achieve'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'achievement', label : this.$t('achievement'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leader', label : this.$t('leader'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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