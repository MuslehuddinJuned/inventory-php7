<template>
    <div class="container-fluid">
        <div class="card filterable" :class="noprint">
            <div class="card-header row m-0">
                <div class="col-md-6">
                    <h3 class="panel-title float-left">{{ $t('salary_sheet') }} <span v-if="savingDate">({{savingDate | dateParse('YYYY-MM-DD') | dateFormat('MMMM-YYYY') }})</span></h3>
                </div>
            </div>
            <div v-if="checkRoles('salary_sheet_Insert')" class="card-body my-3 text-center">
                <input type="date" v-model="savingDate" class="p-1" required>
                <button @click.prevent="save" class="mdb btn btn-outline-primary my-0 py-2" type="button" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon>{{ buttonTitle }}</button>
            </div>
            <div class="card-header">
                <h3 class="panel-title float-right">
                    <b-icon icon="circle-fill" animation="throb" class="text-success" :class="loading"></b-icon>
                    <fa v-if="checkRoles('salary_sheet_Update') && !dataEdit" @click="dataEdit = true" icon="edit" class="ml-2 pointer" fixed-width v-b-tooltip.hover :title="$t('edit')"/> 
                    <fa v-if="checkRoles('salary_sheet_Update') && dataEdit" @click="dataEdit = false" icon="save" class="ml-2 pointer text-success" fixed-width v-b-tooltip.hover :title="$t('save')"/>
                </h3>
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
                :items="salarySheet"
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
                <template v-slot:cell(in_time_1)="row">
                    <span v-if="!dataEdit" v-html="row.item.in_time_1"></span>
                    <input v-if="dataEdit" type="time" v-model="row.item.in_time_1"
                    @blur="updating(row.item.id, row.item.in_time_1, row.item.out_time_1, row.item.in_time_2, row.item.out_time_2, row.item.ot, row.item.ot_extra)">
                </template>
                <template v-slot:cell(out_time_1)="row">
                    <span v-if="!dataEdit" v-html="row.item.out_time_1"></span>
                    <input v-if="dataEdit" type="time" v-model="row.item.out_time_1"
                    @blur="updating(row.item.id, row.item.in_time_1, row.item.out_time_1, row.item.in_time_2, row.item.out_time_2, row.item.ot, row.item.ot_extra)">
                </template>
                <template v-slot:cell(in_time_2)="row">
                    <span v-if="!dataEdit" v-html="row.item.in_time_2"></span>
                    <input v-if="dataEdit" type="time" v-model="row.item.in_time_2"
                    @blur="updating(row.item.id, row.item.in_time_1, row.item.out_time_1, row.item.in_time_2, row.item.out_time_2, row.item.ot, row.item.ot_extra)">
                </template>
                <template v-slot:cell(out_time_2)="row">
                    <span v-if="!dataEdit" v-html="row.item.out_time_2"></span>
                    <input v-if="dataEdit" type="time" v-model="row.item.out_time_2"
                    @blur="updating(row.item.id, row.item.in_time_1, row.item.out_time_1, row.item.in_time_2, row.item.out_time_2, row.item.ot, row.item.ot_extra)">
                </template>
                <template v-slot:cell(total_hours)="row">
                    {{ total_hours(row.item.time, row.item.in_time_1, row.item.out_time_2) }}
                </template>
                <template v-slot:cell(ot)="row">
                    <span v-if="!dataEdit" v-html="row.item.ot"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.ot"
                    @keyup="updating(row.item.id, row.item.in_time_1, row.item.out_time_1, row.item.in_time_2, row.item.out_time_2, row.item.ot, row.item.ot_extra)" style="width : 30px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                </template>
                <template v-slot:cell(ot_extra)="row">
                    <span v-if="!dataEdit" v-html="row.item.ot_extra"></span>
                    <input v-if="dataEdit" type="text" v-model="row.item.ot_extra"
                    @keyup="updating(row.item.id, row.item.in_time_1, row.item.out_time_1, row.item.in_time_2, row.item.out_time_2, row.item.ot, row.item.ot_extra)" style="width : 30px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
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
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('salary_sheet') }
    },

    data() {
        return{
            salarySheet : [],
            dataEdit: false,
            roles: [],
            savingDate: null,
            buttonTitle : this.$t('generate_salary_sheet'),
            disable: false,

            noprint: '',

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
        // this.fetchData()
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

        save() {            

            if (this.savingDate) {  
                this.disable = !this.disable
                this.buttonTitle = this.$t('saving') 
                var date = new Date(this.savingDate),
                    year = date.getFullYear(),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2)
                    month = date.getMonth() + 1
                let start = year + '-' + mnth + '-01'
                let end = this.convertDate(new Date(year, mnth, 0))             
                axios.post(`api/salarysheet`, {
                    'start' : start,
                    'end' : end,
                    'date' : year + '-' + month
                })
                .then(({data}) =>{
                    this.errors = ''
                    this.salarySheet = data.salarySheet
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('generate_salary_sheet')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('generate_salary_sheet')
                })
            }
        },

        updating(id, in_time_1, out_time_1, in_time_2, out_time_2, ot, ot_extra) {
            this.buttonTitle = this.$t('saving')

            axios.patch(`api/attendance/${id}`, {
                'in_time_1' : in_time_1,
                'out_time_1' : out_time_1,
                'in_time_2' : in_time_2,
                'out_time_2' : out_time_2,
                'ot' : ot,
                'ot_extra' : ot_extra,
                'time' : in_time_1+' '+out_time_1+' '+in_time_2+' '+out_time_2
            })
            .then(({data}) => {
                this.errors = ''
                this.buttonTitle = this.$t('generate_salary_sheet')
            })
            .catch(err => {
                if(err.response.status == 422){
                    this.errors = err.response.data.errors
                } else alert(err.response.data.message) 
                this.buttonTitle = this.$t('generate_salary_sheet')
            });
        },

        destroy() {
            this.$toast.warning(this.$t('sure_to_delete'), this.attendance_date, {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/attendance/${this.attendance_date}`)
                        
                        .then(res => {
                            this.fetchData()
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
    },

    computed: {

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },

        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('generate_salary_sheet')
            return [
                // { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'ac_no', label : 'ID', sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'date', label : this.$t('date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // // { key: 'time', label : this.$t('time'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'in_time_1', label : this.$t('in'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'out_time_1', label : this.$t('out') + '(L)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'in_time_2', label : this.$t('in') + '(L)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'out_time_2', label : this.$t('out'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'total_hours', label : this.$t('total_hours'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'ot', label : 'OT', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                // { key: 'ot_extra', label : this.$t('OT (Extra)'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },
    }
}
</script>

<style lang="scss" scoped>

</style>