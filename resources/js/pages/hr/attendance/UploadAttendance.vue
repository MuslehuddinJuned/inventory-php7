<template>
    <div class="container">
        <div class="card filterable" :class="noprint">
            <div class="card-header row m-0">
                <div class="col-md-6">
                    <h3 class="panel-title float-left">{{ $t('upload_attendance') }}</h3>
                </div>                     
                <div class="col-md-6">
                    <div class="ml-md-auto m-sm-0 input-group col-md-12 col-lg-6 float-md-right">
                        <input type="date" v-model="attendance_date" class="form-control">
                        <div class="input-group-append">
                            <div @click="yearWiseDisplay(1)" class="input-group-text pointer"><b-icon icon="search"></b-icon></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body my-3">
                <form class="was-validated" >
                    <div class="input-group is-invalid">
                        <div class="custom-file">
                            <input type="file" @change="handleFileUpload" v-if="uploadReady" class="custom-file-input" id="validatedInputGroupCustomFile" required>
                            <label class="custom-file-label" for="validatedInputGroupCustomFile" :data-browse="$t('browse')">{{fileName}}</label>
                        </div>
                        <div class="input-group-append">
                            <button @click.prevent="save" class="btn btn-outline-secondary" type="button">{{$t('save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-header">
                <h3 class="panel-title float-left">
                    {{ $t('daily_attendance') }} ({{attendance_date | dateParse('YYYY-MM-DD') | dateFormat('DD-MMMM-YYYY')}})
                    <fa v-if="checkRoles('upload_attendance_Insert')" @click="editDetails" icon="edit" class="ml-2 pointer" fixed-width v-b-tooltip.hover :title="$t('edit')"/> 
                    <fa v-if="checkRoles('upload_attendance_Insert')" @click="destroy" icon="trash-alt" class="ml-2 pointer text-danger" fixed-width v-b-tooltip.hover :title="$t('delete')"/> 
                </h3>
            </div>
            <div class="card-body m-0 p-0">
                <div class="card-header d-flex align-items-center noprint">
                    <download-excel
                        id="tooltip-target-1"
                        class="btn btn-outline-default btn-sm mr-3"
                        :title="attendance_date"
                        :data="Usedleave"
                        :fields="json_fields"
                        worksheet="Daily Attendance"
                        name="Daily Attendance.xls">
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
                :items="Usedleave"
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
        return { title: this.$t('upload_attendance') }
    },

    data() {
        return{
            leaveList : [],
            Usedleave: [],
            Leave: [],
            uploadFile: null,
            fileName: this.$t('choose_file'),
            uploadReady: true,
            roles: [],
            attendance_date: this.convertDate(new Date()),
            reportEdit: false,
            buttonTitle : this.$t('save'),
            disable: false,
            json_fields: {
                'Material No': 'item_code',
                'Material': 'item',
                'Description': 'specification',
                'Grade': 'grade',
                'Stock': 'stock',
                'Unit': 'unit',
                'Weight': 'weight',
                'Total Weight': 'total_weight',
                'Unit Price': 'unit_price',
                'Total Price': 'total_price',
            },

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
        this.isBusy = true
        fetch(`api/leave`)
        .then(res => res.json())
        .then(res => {
            this.leaveList = res['LeaveList'];
            this.task = this.singleTask
            if (this.task.length < 1) {
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/usedleave/${this.year}`)
        .then(res => res.json())
        .then(res => {
            this.Usedleave = res['Usedleave']
            this.totalRows = this.Usedleave.length
            this.Leave = res['Leave']
            this.isBusy = false
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
            this.isBusy = true
            if(year > 1){ this.year = year }
            else { this.year = parseInt(this.year) + year }
            fetch(`api/usedleave/${this.year}`)
            .then(res => res.json())
            .then(res => {
                this.Usedleave = res['Usedleave']
                this.Leave = res['Leave']
                this.isBusy = false
            })
            this.task = this.singleTask
            if (this.task.length < 1) {
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
        },

        viewDetails(id) {
            this.taskHeadId = id
            this.noprint = 'noprint'
            this.taskDetailsId = null
            this.taskHead = this.taskHeadSingle

            fetch(`api/leave/${id}`)
            .then(res => res.json())
            .then(res => {
                this.taskDetailsAll = res['AllLeaves']
                this.personalLeave = this.taskDetailsSingle('read')
            })
            .catch(err => {
                alert(err.response.data.message);
            })

            this.$refs['dataView'].show()
        },

        addDetails() {
            this.reportEdit = !this.reportEdit            
            this.noprint = ''
        },

        editDetails(id) {
            this.taskDetailsId = id
            this.taskDetails = this.taskDetailsSingle('edit')
            if (this.taskDetails.length < 1) {
                this.taskDetails = [{'leave_type': null, 'reason': null, 'replacing_person': null, 'leave_start': this.convertDate(new Date()), 'leave_end': this.convertDate(new Date()), 'day_count': 1, 'employee_id': this.taskHeadId}]
            }
            this.$refs['dataEdit'].show()
        },
        
        taskDetailsSingle(view) {
            let array = []
            for (let i = 0; i < this.taskDetailsAll.length; i++) {
                if (view == 'edit') {
                    if (this.taskDetailsAll[i]['id'] == this.taskDetailsId) {
                        array[0] = this.taskDetailsAll[i]
                        break
                    }
                } else {
                    if (this.taskDetailsAll[i]['year'] == this.year) {
                        array[i] = this.taskDetailsAll[i]
                    }
                }                
            } return array
        },

        handleFileUpload(e) {
            let file = e.target.files[0];
            this.fileName = file.name
            let check = this.fileName.slice(this.fileName.length - 4)

            if (check == 'xlsx' || check == '.xls') {  
                var fileReader = new FileReader()
                fileReader.onload = (e) => {
                    this.uploadFile = e.target.result;
                }
                fileReader.readAsDataURL(e.target.files[0]);
            } else {
                this.uploadReady = false
                this.$nextTick(() => {
                    this.uploadReady = true
                })
                // this.$refs.fileInput.type='text';
                // this.$refs.fileInput.type='file';
                this.fileName = this.$t('choose_file')
                this.$toast.warning('.xlsx or .xls format only', this.$t('error_alert_title'), {
                    timeout: 10000,          
                    position: 'center',
                })
            }
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            let options = { headers: {'enctype': 'multipart/form-data'} };

            axios.post(`api/attendance`, {
                'name' : this.uploadFile
            }, options)
            .then(({data}) =>{
                this.errors = ''
                // this.leaveList.unshift(data.LeaveList)
                // this.task[0]['id'] = this.leaveList[0]['id']
                this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                this.disable = !this.disable
                this.buttonTitle = this.$t('save')
                this.reportEdit = !this.reportEdit
            })
            .catch(err => {
                if(err.response.status == 422){
                    this.errors = err.response.data.errors
                    this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                } else alert(err.response.data.message) 
                this.disable = !this.disable
                this.buttonTitle = this.$t('save')
            })
        },

        destroy() {
            let oldDayCount = this.taskDetails[0]['day_count']
            this.$toast.warning('Are you sure to DELETE this?', "Confirm", {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', (instance, toast) => {

                        axios.delete(`api/usedleave/${this.taskDetails[0]['id']}`)
                        .then(({data}) => {
                            this.yearWiseDisplay(this.year)
                            for (let i = 0; i < this.personalLeave.length; i++) {
                                if (this.personalLeave[i]['id'] == this.taskDetails[0]['id']) {
                                    this.personalLeave.splice(i, 1);
                                    break
                                }                                
                            }
                            let count = parseInt(this.taskHead[this.taskDetails[0]['leave_type']])
                            count -=  oldDayCount
                            this.taskHead[this.taskDetails[0]['leave_type']] = count
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
        },
    },

    computed: {
        singleTask() {
            let year = this.year
            return this.leaveList.filter(function (item) {
            return item['year'] == year
            })
        },

        taskHeadSingle() {
            let array = []
            for (let i = 0; i < this.Usedleave.length; i++) {
                if (this.Usedleave[i]['id'] == this.taskHeadId) {
                    array = this.Usedleave[i]
                    break
                }                
            } return array
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },

        reportClass(){
            return[ 
                this.reportEdit == true ? 'w-100' : 'border-0 bg-transparent rounded-0'
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
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'employee_id', label : this.$t('ID'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'casual_leave', label : this.$t('casual_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'sick_leave', label : this.$t('sick_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'annual_leave', label : this.$t('annual_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'maternity_leave', label : this.$t('maternity_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'paternity_leave', label : this.$t('paternity_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'compensatory_leave', label : this.$t('compensatory_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'unpaid_leave', label : this.$t('unpaid_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'half_leave', label : this.$t('half_leave'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },

        taskDetailsfieldsView() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leave_type', label : this.$t('leave_category'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'reason', label : this.$t('reason'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leave_start', label : this.$t('leave_start'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'leave_end', label : this.$t('leave_end'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'day_count', label : this.$t('days'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'created_at', label : this.$t('application_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },
    }
}
</script>

<style lang="scss" scoped>

</style>