<template>
    <div class="container-fluid">
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
                            <label class="custom-file-label" for="validatedInputGroupCustomFile" :data-browse="$t('browse')"> {{fileName}}</label>
                        </div>
                        <div class="input-group-append">
                            <button @click.prevent="save" class="btn btn-outline-secondary" type="button" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon>{{ buttonTitle }}</button>
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
                :items="attendanceList"
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
                    {{ in_time_1(row.item.time, row.item.in_time_1) }}
                </template>
                <template v-slot:cell(out_time_1)="row">
                    {{ out_time_1(row.item.time, row.item.out_time_1) }}
                </template>
                <template v-slot:cell(in_time_2)="row">
                    {{ in_time_2(row.item.time, row.item.in_time_2) }}
                </template>
                <template v-slot:cell(out_time_2)="row">
                    {{ out_time_2(row.item.time, row.item.out_time_2) }}
                </template>
                <template v-slot:cell(total_hours)="row">
                    {{ total_hours(row.item.in_time_1, row.item.out_time_2) }}
                </template>
                <template v-slot:cell(ot)="row">
                    {{ ot(row.item.in_time_1, row.item.out_time_2) }}
                </template>
                <template v-slot:cell(ot_extra)="row">
                    {{ ot_extra(row.item.in_time_1, row.item.out_time_2) }}
                </template>
                
            <!-- 'ac_no', 'name', 'department', 'date', 'time', 'in_time_1', 'in_time_2', 'out_time_1', 'out_time_2', 'ot', 'ot_extra' -->
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
            attendanceList : [],
            Usedleave: [],
            Leave: [],
            uploadFile: null,
            fileName: this.$t('choose_file'),
            uploadReady: true,
            roles: [],
            attendance_date: this.convertDate(new Date('2020-09-05')),
            buttonTitle : this.$t('save'),
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
        this.isBusy = true
        fetch(`api/attendance/${this.attendance_date}`)
        .then(res => res.json())
        .then(res => {
            this.attendanceList = res['Attendance']
            this.totalRows = this.attendanceList.length
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

        editDetails(id) {
            this.taskDetailsId = id
            this.taskDetails = this.taskDetailsSingle('edit')
            if (this.taskDetails.length < 1) {
                this.taskDetails = [{'leave_type': null, 'reason': null, 'replacing_person': null, 'leave_start': this.convertDate(new Date()), 'leave_end': this.convertDate(new Date()), 'day_count': 1, 'employee_id': this.taskHeadId}]
            }
            this.$refs['dataEdit'].show()
        },
        
        in_time_1(time, time1) {
            if (time.length > 0) return time1
            return '00:00'
        },

        out_time_1(time, time1) {
            if (time.length > 12) return time1
            return '00:00'
        },
        
        in_time_2(time, time1) {
            if (time.length > 18) return time1
            return '00:00'
        },
        
        out_time_2(time, time1) {
            if (time.length > 6) return time1
            return '00:00'
        },

        total_hours(start, end) {
            if (start.length < 2) return '0:00'

            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            diff -= hours * 1000 * 60 * 60;
            var minutes = Math.floor(diff / 1000 / 60);

            // If using time pickers with 24 hours format, add the below line get exact hours
            if (hours < 0)
            hours = hours + 24;

            return (hours <= 9 ? "0" : "") + hours + ":" + (minutes <= 9 ? "0" : "") + minutes;
        },

        ot(start, end) {
            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            if (hours < 0) hours = hours + 24;
            if ((hours - 9) > 0 && (hours - 9)< 3) return hours - 9 + ':00'
            else if ((hours - 9) > 2) return '2:00'
            return '0:00'
        },

        ot_extra(start, end) {
            start = start.split(":");
            end = end.split(":");
            var startDate = new Date(0, 0, 0, start[0], start[1], 0);
            var endDate = new Date(0, 0, 0, end[0], end[1], 0);
            var diff = endDate.getTime() - startDate.getTime();
            var hours = Math.floor(diff / 1000 / 60 / 60);
            if (hours < 0) hours = hours + 24;
            if ((hours - 9) > 2) return hours - 11 + ':00'
            return '0:00'
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

            if (this.uploadFile) {                
                axios.post(`api/attendance`, {
                    'uploadFile' : this.uploadFile
                }, options)
                .then(({data}) =>{
                    this.errors = ''
                    this.isBusy = true
                    // this.leaveList.unshift(data.LeaveList)
                    // this.task[0]['id'] = this.leaveList[0]['id']
                    this.attendanceList = data.attendance
                    this.totalRows = this.attendanceList.length
                    console.log(this.attendanceList)
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.isBusy = false
                    this.uploadReady = false
                    this.$nextTick(() => {
                        this.uploadReady = true
                    })
                    this.fileName = this.$t('choose_file')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
            }
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
                { key: 'ac_no', label : this.$t('ID'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'date', label : this.$t('date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'time', label : this.$t('time'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time_1', label : this.$t('in'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time_1', label : this.$t('out') + '(L)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'in_time_2', label : this.$t('in') + '(L)', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'out_time_2', label : this.$t('out'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'total_hours', label : this.$t('total_hours'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'ot', label : this.$t('OT'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'ot_extra', label : this.$t('OT (Extra)'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]            
        },
    }
}
</script>

<style lang="scss" scoped>

</style>