<template>
    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h3 class="panel-title float-left">{{ $t('holiday_management') }}</h3> 
                <div class="ml-auto">
                    <button v-if="checkRoles('holiday_management_Insert')" @click="editDetails(null)" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                </div>
            </div> 
            <div class="card-body row m-0 b-0">
                <div class="input-group mb-2 col-md-12">
                    <div class="input-group-prepend">
                        <div @click="yearWiseDisplay(-1)" class="input-group-text pointer"><b-icon icon="dash"></b-icon></div>
                    </div>
                    <input type="text" v-model="year" @change="yearWiseDisplay(year)" class="form-control text-center" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    <div class="input-group-append">
                        <div @click="yearWiseDisplay(1)" class="input-group-text pointer"><b-icon icon="plus"></b-icon></div>
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
                :items="YearlyHoliday"
                :fields="fields"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :filterIncludedFields="filterOn"
                :tbody-transition-props="transProps"
                @filtered="onFiltered"
                @row-clicked="(item) => editDetails(item.id)"
                class="table-transition"
                style="cursor : pointer"
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
                <template v-slot:cell(yearly_holiday)="row">
                    {{ row.item.yearly_holiday | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}}
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
        <!-- Start Edit Details Modal -->
        <b-modal ref="dataEdit" id="dataEdit" :title="$t('holiday_management')" no-close-on-backdrop>            
            <div class="modal-bodymb-2">
                <div>
                    <label class="col-form-label">{{ $t('date')}}</label>
                    <input type="date" class="form-control" v-model="task['yearly_holiday']">
                    <span v-if="errors.yearly_holiday" class="error text-danger"> {{$t('required_field') + ' ' + $t('unique')}} </span>
                </div>
                <div>
                    <label class="col-form-label">{{ $t('event')}}</label>
                    <input type="text" class="form-control" v-model="task['event']">
                </div>                             
            </div>
            <template v-slot:modal-footer>
                <button v-if="checkRoles('holiday_management_Delete') && taskId" @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                <button v-if="checkRoles('holiday_management_Insert')" @click="update" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End Edit Details Modal -->
    </div>
</template>

<script>
    export default {

        middleware: 'auth',

        metaInfo () {
            return { title: this.$t('holiday_management') }
        },

        data() {
            var now = new Date()
            var currentYear = now.getFullYear()
            return {
                HolidayList : [],
                YearlyHoliday: [],
                roles: [], 
                task: {'event': null, 'yearly_holiday': this.convertDate(new Date())},  
                taskId: null,     
                errors : [],
                year : currentYear,
                buttonTitle : this.$t('save'),
                disable: false, 
                
                transProps: {
                    // Transition name
                    name: 'flip-list'
                },
                totalRows: 1,
                currentPage: 1,
                perPage: 25,
                pageOptions: [10, 25, 50],
                filter: null,
                filterOn: [],
                isBusy: false,
            }
        },

        mounted() {
            this.isBusy = true
            fetch(`api/holiday`)
            .then(res => res.json())
            .then(res => {
                this.HolidayList = res['HolidayList'];
                this.YearlyHoliday = this.yearly
                this.totalRows = this.YearlyHoliday.length
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

            yearWiseDisplay(year) {
                if(year > 1){ this.year = year }
                else { this.year = parseInt(this.year) + year }
                this.YearlyHoliday = this.yearly
            },

            editDetails(id) {
                this.taskId = id
                
                if (!id) {
                   this.task = {'event': null, 'yearly_holiday': this.convertDate(new Date())}
                } else this.task = this.taskSingle

                this.$refs['dataEdit'].show()
            },

            update() {
                this.disable = !this.disable
                this.buttonTitle = this.$t('saving')
                this.year = this.task['yearly_holiday'].substring(0, this.task['yearly_holiday'].indexOf('-'))
                if (!this.taskId) {
                    axios.post(`api/holiday`, this.task)
                    .then(({data}) =>{
                        this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'});
                        this.HolidayList.unshift(data.NewWeeklyHoliday)
                        this.YearlyHoliday = this.yearly
                        this.totalRows = this.YearlyHoliday.length
                        this.errors = '';
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                        this.$refs['dataEdit'].hide()
                    })
                    .catch(err => {
                        if(err.response.status == 422){
                            this.errors = err.response.data.errors
                            this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                        } else alert(err.response.data.message);
                        
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                    });
                } else {
                    axios.patch(`api/holiday/${this.taskId}`, this.task)
                    .then(({data}) =>{
                        this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'});
                        this.errors = '';
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                        this.$refs['dataEdit'].hide()
                    })
                    .catch(err => {
                        if(err.response.status == 422){
                            this.errors = err.response.data.errors
                            this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                        } else alert(err.response.data.message);
                        
                        this.disable = !this.disable
                        this.buttonTitle = this.$t('save')
                    });
                }
            },

            destroy() {
                this.$toast.warning('Are you sure to DELETE this?', "Confirm", {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', (instance, toast) => {

                        axios.delete(`api/holiday/${this.taskId}`)
                        .then(({data}) => {
                            this.HolidayList = data['HolidayList']
                            this.YearlyHoliday = this.yearly
                            this.totalRows = this.YearlyHoliday.length  
                            this.$refs['dataEdit'].hide()
                        })
                        .catch(err => {
                            alert(err.response.data.message);                       
                        });

                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }, true],
                    ['<button>NO</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }],
                ]            
            });
            }

        },

        computed: {
            yearly() {
                let check = this.year
                return this.HolidayList.filter(function (item){
                    return item['yearly_holiday'].substring(0, item['yearly_holiday'].indexOf('-')) == check
                })
            },

            taskSingle() {
                let array = []
                for (let i = 0; i < this.YearlyHoliday.length; i++) {
                    if (this.YearlyHoliday[i]['id'] == this.taskId) {
                        array = this.YearlyHoliday[i]
                        break
                    }                    
                } return array
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
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'yearly_holiday', label : this.$t('date'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'event', label : this.$t('event'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },

            loading(){
                return[ 
                    this.buttonTitle == this.$t('saving') ? '' : 'd-none'
                ]
            },
        }
    }
</script>

<style lang="scss" scoped>
    // .cursor:hover {
    //     cursor: pointer;
    //     .click:hover{
    //         font-weight: bolder;
    //     }
    // }
</style>