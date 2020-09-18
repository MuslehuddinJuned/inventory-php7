<template>
    <div class="container">
        <div class="card filterable">
            <div class="card-header d-flex align-items-center">
                <h3 class="panel-title float-left">
                    {{ $t('leave_management') }}
                    <fa v-if="checkRoles('leave_management_Insert')" @click="editDetails" icon="edit" class="ml-2 pointer" fixed-width />                    
                </h3>                     
                <div class="ml-auto input-group col-md-2">
                    <div class="input-group-prepend">
                        <div @click="yearWiseDisplay(-1)" class="input-group-text pointer"><b-icon icon="dash"></b-icon></div>
                    </div>
                    <input type="text" v-model="year" @change="yearWiseDisplay(year)" class="form-control text-center" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    <div class="input-group-append">
                        <div @click="yearWiseDisplay(1)" class="input-group-text pointer"><b-icon icon="plus"></b-icon></div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row m-0 p-0">
                    <div class="col-md-4 my-1">
                        {{$t('casual_leave')}}:
                        <input type="text" v-model="task[0]['casual_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('sick_leave')}}:
                        <input type="text" v-model="task[0]['sick_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('annual_leave')}}:
                        <input type="text" v-model="task[0]['annual_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('maternity_leave')}}:
                        <input type="text" v-model="task[0]['maternity_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('paternity_leave')}}:
                        <input type="text" v-model="task[0]['paternity_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                    <div class="col-md-4 my-1">
                        {{$t('half_leave')}}:
                        <input type="text" v-model="task[0]['half_leave']" :class="reportClass" class="ml-1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>
                </div>
                <div class="col-12 mt-5 text-center">
                    <button v-if="checkRoles('leave_management_Insert') && reportEdit" @click="save" class="mdb btn btn-outline-default" :disabled="disable"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
                    <button v-if="reportEdit" @click="editDetails" type="button" class="mdb btn btn-outline-mdb-color">{{$t('Close')}}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('leave_management') }
    },

    data() {
        return{
            leaveList : [],
            task: [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}],
            roles: [],
            year: new Date().getFullYear(),
            reportEdit: false,
            buttonTitle : this.$t('save'),
            disable: false,

            noprint: 'noprint',

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
            this.task = this.singleTask
            if (this.task.length < 1) {
                this.task = [{'casual_leave' : 0, 'sick_leave': 0, 'annual_leave': 0, 'maternity_leave': 0, 'paternity_leave': 0, 'half_leave': 0}]
            }
        },

        editDetails() {
            this.reportEdit = !this.reportEdit
        },

        save() {
            this.disable = !this.disable
            this.buttonTitle = this.$t('saving')
            this.task[0]['year'] = this.year

            if(this.task[0]['id'] == null){
                axios.post(`api/leave`, this.task[0])
                .then(({data}) =>{
                    this.errors = ''
                    this.leaveList.unshift(data.LeaveList)
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
            } else {
                axios.patch(`api/leave/${this.task[0]['id']}`, this.task[0])
                .then(({data}) => {
                    this.errors = ''
                    this.$toast.success(this.$t('success_message_update'), this.$t('success'), {timeout: 3000, position: 'center'})
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    this.reportEdit = !this.reportEdit
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
    },

    computed: {
        singleTask() {
            let year = this.year
            return this.leaveList.filter(function (item) {
            return item['year'] == year
            })
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },

        reportClass(){
            return[ 
                this.reportEdit == true ? '' : 'border-0 bg-transparent rounded-0'
            ]
        },
    }
}
</script>

<style lang="scss" scoped>

</style>