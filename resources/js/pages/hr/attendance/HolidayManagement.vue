<template>
    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h3 class="panel-title float-left">{{$t('holiday_management')}}</h3> 
            </div> 
            <div class="card-body row m-0 b-0">
                <div class="col-md-6"> 
                    <h4>{{$t('weekly_holidays')}} <fa v-if="checkRoles('holiday_management_Insert')" @click="weeksEdit" icon="edit" style="cursor: pointer;" fixed-width /></h4>
                    <b-form-select @change="weeksUpdate" v-model="WeeklyHoliday['weekly_holiday']" class="form-control col-12" :class="hideweek" :options="options"></b-form-select>
                    <span v-if="errors.weekly_holiday" class="error text-danger col-12" :class="hideweek"> {{errors.weekly_holiday[0]}} </span>
                    <ol class="mt-3">
                        <li v-for="(week, index) in WeeklyHoliday" :key="week.id">{{days[week.weekly_holiday]}}                        
                            <b-icon v-if="checkRoles('holiday_management_Delete')" @click="weeksDelete(week.id, index, 'weekly')" class="text-danger pointer" :class="hideweek" icon="x-circle-fill"></b-icon>
                        </li>
                    </ol>
                </div>
                <div class="col-md-6">                
                    <h4>{{$t('yearly_holidays')}} <fa v-if="checkRoles('holiday_management_Insert')" @click="yearlyEdit" icon="edit" style="cursor: pointer;" fixed-width /></h4>
                    <div class="input-group mb-2 col-md-12" :class="hideyearly">
                        <input type="date" v-model="WeeklyHoliday['yearly_holiday']" class="form-control col-md-12">
                        <div class="input-group-append">
                            <button class="input-group-text" @click="yearlyUpdate">{{$t('save')}}</button>
                        </div>                    
                        <span v-if="errors.yearly_holiday" class="error text-danger col-md-12"> {{errors.yearly_holiday[0]}} </span>
                    </div>
                    <div class="input-group mb-2 col-md-12">
                        <div class="input-group-prepend">
                            <div @click="yearWiseDisplay(-1)" class="input-group-text pointer"><b-icon icon="dash"></b-icon></div>
                        </div>
                        <input type="text" v-model="year" @change="yearWiseDisplay(year)" class="form-control text-center" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        <div class="input-group-append">
                            <div @click="yearWiseDisplay(1)" class="input-group-text pointer"><b-icon icon="plus"></b-icon></div>
                        </div>
                    </div> 
                    <div>
                        <ol class="">
                            <li v-for="(years, index) in YearlyHoliday" :key="years.id">
                                {{`${years.yearly_holiday}` | dateParse('YYYY-MM-DD') | dateFormat('MMMM-DD-YYYY')}}
                                <b-icon v-if="checkRoles('holiday_management_Delete')" @click="weeksDelete(years.id, index, 'yearly')" class="text-danger pointer" :class="hideyearly" icon="x-circle-fill"></b-icon>
                            </li>
                        </ol>
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
            return { title: this.$t('holiday_management') }
        },

        data() {
            var now = new Date()
            var currentYear = now.getFullYear()
            return {
                HolidayList : [],
                roles: [],
                HideWeeklyHoliday : true,              
                HideYearlylyHoliday : true,              
                errors : [],
                WeeklyHoliday : [], 
                YearlyHoliday : [], 
                options : [
                    { value: 6, text: this.$t('saturday') },
                    { value: 0, text: this.$t('sunday') },
                    { value: 1, text: this.$t('monday') },
                    { value: 2, text: this.$t('tuesday')},
                    { value: 3, text: this.$t('wednesday') },
                    { value: 4, text: this.$t('thursday') },
                    { value: 5, text: this.$t('friday') }
                ],
                days : [this.$t('sunday'), this.$t('monday'), this.$t('tuesday'), this.$t('wednesday'), this.$t('thursday'), this.$t('friday'), this.$t('saturday')],
                year : currentYear,

            
            }
        },

        mounted() {
            this.HideWeeklyHoliday = true
            this.HideYearlylyHoliday = true
            fetch(`api/holiday`)
            .then(res => res.json())
            .then(res => {
                this.HolidayList = res['HolidayList'];
                this.WeeklyHoliday = this.weekly
                this.YearlyHoliday = this.yearly
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
            checkRoles(role) {
                for (let i = 0; i < this.roles.length; i++) {
                    if (this.roles[i]['name'] == role) {
                        return true
                    }                
                } return false
            },

            yearWiseDisplay(year) {
                if(year > 1){ this.year = year }
                else { this.year = parseInt(this.year) + year }
                this.YearlyHoliday = this.yearly
            },

            yearlyUpdate() {
                this.year = this.WeeklyHoliday['yearly_holiday'].substring(0, this.WeeklyHoliday['yearly_holiday'].indexOf('-'))
                axios.post(`api/holiday`, {
                    'yearly_holiday' : this.WeeklyHoliday['yearly_holiday']
                })
                .then(({data}) =>{
                    this.$toast.success('Inserted Successfully', 'Success', {timeout: 3000, position: 'center'});
                    this.YearlyHoliday = this.yearly  
                    this.YearlyHoliday.unshift(data.NewWeeklyHoliday)
                    this.HolidayList.unshift(data.NewWeeklyHoliday)
                    this.errors = '';
                })
                .catch(err => {
                    if(err.response.status == 422){
                            this.errors = err.response.data.errors
                        } else {
                            alert(err.response.data.message);                       
                        }
                });
            },

            yearlyEdit() {
                this.HideYearlylyHoliday = !this.HideYearlylyHoliday
            },

            weeksEdit() {
                this.HideWeeklyHoliday = !this.HideWeeklyHoliday;
            },

            weeksUpdate() {
                axios.post(`api/holiday`, {
                    'weekly_holiday' : this.WeeklyHoliday['weekly_holiday']
                })
                .then(({data}) =>{
                    this.$toast.success('Inserted Successfully', 'Success', {timeout: 3000, position: 'center'});   
                    this.WeeklyHoliday.unshift(data.NewWeeklyHoliday);
                    this.errors = '';
                })
                .catch(err => {
                    if(err.response.status == 422){
                            this.errors = err.response.data.errors
                        } else {
                            alert(err.response.data.message);                       
                        }
                });                
            },

            weeksDelete(id, index, check) {
                this.$toast.warning('Are you sure to DELETE this?', "Confirm", {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', (instance, toast) => {

                        axios.delete(`api/holiday/${id}`)
                        .then(({data}) => {
                            if(check == 'weekly') { this.WeeklyHoliday.splice(index, 1) }
                            else { 
                                this.HolidayList = data['HolidayList'] 
                                this.YearlyHoliday.splice(index, 1)
                            }             
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
            hideweek() {
                return[ 
                    this.HideWeeklyHoliday == true ? 'd-none' : ''
                ]
            },

            hideyearly() {
                return [this.HideYearlylyHoliday == true ? 'd-none' : '']
            },

            weekly() {
                return this.HolidayList.filter(function (item){
                    return item['weekly_holiday'] != null
                })
            },

            yearly() {
                let check = this.year
                return this.HolidayList.filter(function (item){
                    return item['yearly_holiday'] != null && check == item['yearly_holiday'].substring(0, item['yearly_holiday'].indexOf('-'))
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    .cursor:hover {
        cursor: pointer;
        .click:hover{
            font-weight: bolder;
        }
    }
</style>