<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12">
            <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('personnel_file') }}</h3> 
                </div>
                <div class="col-md-4 card-body noprint"><b-form-select v-model="DepartmentName" :options="DepartmentList" value-field="department" text-field="department"></b-form-select></div>
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
                    :items="employeeListByDept"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    @row-clicked="(item) => viewDetails(item.id)"
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
                    <template v-slot:cell(employee_image)="row">
                        <a :href="'/images/employee/' + row.item.employee_image"><b-img :src="'/images/employee/' + row.item.employee_image" style="height: 50px; max-width: 150px;" alt=""></b-img></a>
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

        <!-- Start Edit Details Modal -->
        <b-modal ref="dataEdit" id="dataEdit" size="lg" :title="$t('personnel_file')" no-close-on-backdrop>            
            <div class="modal-body row m-0 p-0">
                <div class="form-row col-md-12">                          
                    <div class="form-group col-md-6">
                        <label for="date" class="col-form-label">{{$t('date')}}</label>
                        <input v-model="task['date']" type="date" class="form-control" id="date" name="date">
                    </div>                      
                    <div class="form-group col-md-6">                        
                        <label for="activity" class="col-form-label">{{$t('activity')}}</label>
                        <input type="text" class="form-control" id="activity" name="Name" v-model="task['activity']">
                    </div>                      
                    <div class="form-group col-md-6">                        
                        <label for="amount" class="col-form-label">{{$t('amount')}}</label>
                        <input type="text" class="form-control" id="amount" name="Name" v-model="task['amount']" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="file_link" class="col-form-label">{{$t('file_link')}}</label>
                        <input type="file_link" class="form-control" id="file_link" name="file_link" v-model="task['file_link']">
                    </div>     
                    <div class="form-group col-md-12">
                        <label for="remarks" class="col-form-label">{{$t('remarks')}}</label>
                        <input type="text" class="form-control" id="remarks" name="remarks" v-model="task['remarks']">
                    </div>
                </div>
            </div>                        
            <template v-slot:modal-footer="">
                <div class="col-md-12">
                    <div class="col-md-5 float-left">
                        <button v-if="checkRoles('personnel_file_Delete') && taskId" @click="destroy" class="mdb btn btn-outline-danger float-left">{{ $t('delete') }}</button>
                    </div>
                    <div class="col-md-7 float-right">
                        <button @click="$refs['dataEdit'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right" data-dismiss="modal">{{$t('Close')}}</button>
                        <button v-if="checkRoles('personnel_file_Insert')" @click="save" class="mdb btn btn-outline-default float-right" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                    </div>
                </div>
            </template>
        </b-modal>        
        <!-- End Edit Details Modal --> 

        <!-- Start view Details Modal -->
        <b-modal class="b-0" ref="dataView" id="dataView" size="xl" :title="$t('personnel_file')" no-close-on-backdrop>
            <div class="modal-body row m-0 p-0">
                <div class="col-md-3 text-center m-0">
                    <img style="width: 100%; " :src="'/images/employee/' + singlEmployee['employee_image']" alt="Picture not found">
                </div>
                <div class="col-md-9 m-0">
                    <div class="col-md-12 p-0">
                        <h2>{{singlEmployee['name']}}</h2>
                    </div>
                    <div class="col-md-12 mt-2 p-0">
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">ID</p>
                            </div>
                            <div class="col-md-8 bg-light">
                                <p class="my-auto">{{singlEmployee['employee_id']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('designation')}}</p>
                            </div>
                            <div class="col-md-8 bg-info">
                                <p class="my-auto text-white">{{singlEmployee['designation']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 my-auto bg-light">
                                <p class="my-auto font-weight-bold">{{$t('section')}}</p>
                            </div>
                            <div class="col-md-8 bg-light">
                                <p class="my-auto">{{singlEmployee['section']}}</p>
                            </div>
                        </div>
                        <div class="row m-0 p-0 col-md-12">
                            <div class="col-md-4 bg-info my-auto">
                                <p class="my-auto text-white font-weight-bold">{{$t('department')}}</p>
                            </div>
                            <div class="col-md-8 bg-info">
                                <p class="my-auto text-white">{{singlEmployee['department']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 m-0 p-0 mt-3">
                    <b-table show-empty small striped hover stacked="md" primary-key="id" :items="personnelList" :fields="personnelFields"
                    @row-clicked="(item) => editDetails(item.id)"
                    class="table-transition"
                    style="cursor : pointer">                                   
                        <template v-slot:cell(index)="row">
                            {{ row.index+1 }}
                        </template>
                        <template v-slot:cell(file_link)="row">
                            <a v-if="row.item.file_link" :href="row.item.file_link">File</a>
                        </template>
                    </b-table>
                </div> 
            </div>
            <template v-slot:modal-header="">
                <div class="d-flex align-items-center col-12 m-0 p-0">
                    <h3 class="panel-title float-left">{{ $t('personnel_file') }}</h3>                     
                    <div class="ml-auto">
                        <button v-if="checkRoles('personnel_file_Insert')" @click="addDetails" class="mdb btn btn-outline-info" v-b-modal.dataEdit>{{ $t('InsertNew') }}</button>
                    </div>
                </div>
            </template>
            <template v-slot:modal-footer="">
                <button @click="$refs['dataView'].hide()" type="button" class="mdb btn btn-outline-mdb-color float-right" data-dismiss="modal">{{$t('Close')}}</button>
            </template>
        </b-modal>
        <!-- End view Details Modal -->
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('personnel_file') }
    },

    data() {
        return{
            employeeList : [],
            singlEmployee : {},
            personnelList: [],
            errors : [],
            task: {'date': this.convertDate(new Date()), 'activity': null, 'amount': null, 'remarks': null, 'file_link': null, 'employee_id': null},
            taskId: null,
            employeeId: null,
            title: '',
            buttonTitle : this.$t('save'),
            disable: false,
            noprint: 'noprint',
            DepartmentList: [],
            DepartmentName: 'Management',

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

        fetch(`api/employee`)
        .then(res => res.json())
        .then(res => {
            this.employeeList = res['EmployeeList'];
            this.isBusy = false
        })
        .catch(err => {
            alert(err.response.data.message);
        })

        fetch(`api/salarysheet`)
        .then(res => res.json())
        .then(res => {
            this.DepartmentList = res['Department'];
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

        viewDetails(id) {
            this.employeeId = id
            console.log(id)
            fetch(`api/personnel/${id}`)
            .then(res => res.json())
            .then(res => {
                this.personnelList = res['Personnel'];
            })
            .catch(err => {
                alert(err.response.data.message);
            })
            this.noprint = 'noprint'
            this.singlEmployee = this.singlEmployeeMethod
            this.$refs['dataView'].show()
        },

        addDetails() {
            this.taskId = null
            this.task = {'date': this.convertDate(new Date()), 'activity': null, 'amount': null, 'remarks': null, 'file_link': null, 'employee_id': this.employeeId}
        },

        editDetails(id) {
            if (this.checkRoles('personnel_file_Update')) {                
                this.taskId = id
                for (let i = 0; i < this.personnelList.length; i++) {
                    if (this.personnelList[i]['id'] == id) {
                        this.task = this.personnelList[i]
                        break
                    }                
                }
                this.$refs['dataEdit'].show()
            }
        },

        convertDate(str) {
            var date = new Date(str),
                year = date.getFullYear(),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2)
            return [year, mnth, day].join("-");
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')

            if(this.taskId == null){
                axios.post(`api/personnel`, this.task)
                .then(({data}) =>{
                    this.errors = ''
                    this.personnelList.unshift(data.Personnel)
                    this.taskId = this.personnelList[0]['id']
                    this.totalRows = this.personnelList.length
                    this.$toast.success(this.$t('success_message_add'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.$refs['dataEdit'].hide()
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
            } else {
                axios.patch(`api/personnel/${this.taskId}`, this.task)
                .then(({data}) => {
                    this.errors = ''
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.$refs['dataEdit'].hide()
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                    } else alert(err.response.data.message) 
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }
        },

        destroy() {
            this.$toast.warning(this.$t('sure_to_delete'), this.attendance_date, {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {
                        axios.delete(`api/personnel/${this.taskId}`)                        
                        .then(res => {
                            for (let i = this.personnelList.length -1; i >= 0; i--) {
                                if(this.personnelList[i]['id'] == this.taskId){
                                    this.personnelList.splice(i, 1);                           
                                    break
                                }   
                            }
                            this.$refs['dataEdit'].hide()
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
        roles() {
            return JSON.parse(localStorage.getItem("roles"))
        },
        
        singlEmployeeMethod() {
            let array = []
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['id'] == this.employeeId) {
                    array = this.employeeList[i]
                    break
                }                
            }
            return array
        },

        employeeListByDept() {
            let array = [], k=0
            for (let i = 0; i < this.employeeList.length; i++) {
                if (this.employeeList[i]['department'] == this.DepartmentName) {
                    array[k++] = this.employeeList[i]
                }                
            }

            this.totalRows = array.length
            return array
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
                { key: 'employee_id', label : 'ID', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'employee_image', label : this.$t('image'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'first_name', label : this.$t('name'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'designation', label : this.$t('designation'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'department', label : this.$t('department'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'start_date', label : this.$t('joining_date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'service_length', label : this.$t('service_length'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
            ]
        },

        personnelFields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'date', label : this.$t('date'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'activity', label : this.$t('activity'), sortable: true, class: 'text-center align-middle', tdClass: 'p-0', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'amount', label : this.$t('amount'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'file_link', label : this.$t('file_link'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'remarks', label : this.$t('remarks'), sortable: true, class: 'text-center align-middle', thClass: 'border-top border-dark font-weight-bold'},
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

</style>