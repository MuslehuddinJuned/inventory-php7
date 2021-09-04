<template>
    <div class="container justify-content-center">
       <div class="col-md-12" :class="noprint">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <div class="form-group row mx-2">
                        <label for="AttendanceDate" class="col-form-label"><h3>Attendance of</h3></label>
                        <div >
                            <input v-model="AttendanceDate" id="AttendanceDate" class="form-control-lg border-0 bg-transparent rounded-0" type="date">
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
                    :items="TodayAttendance"
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
                    <template v-slot:cell(in_time)="row">
                        <span v-if="row.item.in_time">{{row.item.in_time}}</span>
                        <span v-else-if="row.item.others"></span>
                        <span v-else><b-form-checkbox @change="save('In', row.item.id)" switch> </b-form-checkbox></span>
                    </template>
                    <template v-slot:cell(out_time)="row">
                        <span v-if="row.item.out_time">{{row.item.out_time}}</span>
                        <span v-else-if="row.item.others"></span>
                        <span v-else><b-form-checkbox @change="save('Out', row.item.id)" switch> </b-form-checkbox></span>
                    </template>
                    <template v-slot:cell(others)="row">
                        <span v-if="row.item.in_time"></span>
                        <span v-else-if="row.item.others">{{row.item.others}}</span>
                        <span v-else>
                            <b-form-group>
                                <b-form-radio-group>
                                    <b-form-radio value="Outdoor" @click="save('Outdoor', row.item.id)">Outdoor</b-form-radio>
                                    <b-form-radio value="Holiday" @click="save('Holiday', row.item.id)">Holiday</b-form-radio>
                                    <b-form-radio value="Leave" @click="save('Leave', row.item.id)">Leave</b-form-radio>
                                    <b-form-radio value="Others" @click="save('Others', row.item.id)">Others</b-form-radio>
                                </b-form-radio-group>
                            </b-form-group>
                        </span>
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
    // middleware: 'auth',

    metaInfo () {
        return { title: this.$t('attendance') }
    },

    data() {
        return{
            AttendanceDate: new Date(),
            TodayAttendance: [],
            roles: [],
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
            noprint: 'noprint',
        }
    },

    mounted() {
        this.AttendanceDate = this.convertDate(this.AttendanceDate)
        this.fetchData()
        

        // fetch(`api/settings/roles`)
        // .then(res => res.json())
        // .then(res => {
        //     this.roles = res['allRoles'];
        // })
    },

    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },

        // checkRoles(role) {
        //     for (let i = 0; i < this.roles.length; i++) {
        //         if (this.roles[i]['name'] == role) {
        //             return true
        //         }                
        //     } return false
        // },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        fetchData() {
            this.isBusy = true
            fetch(`api/m_attendance/${this.AttendanceDate}`)
            .then( res => res.json())
            .then(res => {
                this.TodayAttendance = res['attendance'];
                console.log(this.TodayAttendance)
                this.isBusy = false;                
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        save(value, id) {
            this.isBusy = true
            if (value == 'Others') {
                
            } 
            axios.post(`api/m_attendance`, {'others' : value, 'in_time' : null, 'out_time' : null, 'employee_id' : id, 'att_date' : this.AttendanceDate})
            .then(({data}) =>{
                this.fetchData()
            })
            .catch(err => {
                alert(err.response.data.message)                                        
            })
            
        }
    },

    computed: {
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
                { key: 'index', label : '#', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'name', label : this.$t('name'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time', label : 'In', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time', label : 'Out', class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'others', label : this.$t('others'), class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },
    }
}
</script>

<style>

</style>