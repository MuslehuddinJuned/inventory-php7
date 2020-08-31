<template>
    <div class="container">
        <div class="card filterable">
            <div class="card-header">
                <h3 class="panel-title float-left">{{ $t('user_management') }}</h3>
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
                :items="userList"
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
                <template v-slot:cell(action)="row">
                    <a @click="editDetails(row.item.id, row.item.name)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
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
        <b-modal ref="dataEdit" id="dataEdit" size="xl" :title="title" no-close-on-backdrop ok-only>            
            <div class="modal-body row m-0 p-0">                
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
                    :items="roles"
                    :fields="fieldsRole"
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
                    <template v-slot:cell(view)="row">
                        <b-form-checkbox @change="save(row.item.view['value'], row.item.view['id'])" v-model="row.item.view['value']" switch> </b-form-checkbox>
                    </template>
                    <template v-slot:cell(insert)="row">
                        <b-form-checkbox @change="save(row.item.insert['value'], row.item.insert['id'])" v-model="row.item.insert['value']" switch></b-form-checkbox>
                    </template>
                    <template v-slot:cell(update)="row">
                        <b-form-checkbox @change="save(row.item.update['value'], row.item.update['id'])" v-model="row.item.update['value']" switch> </b-form-checkbox>
                    </template>
                    <template v-slot:cell(delete)="row">
                        <b-form-checkbox @change="save(row.item.delete['value'], row.item.delete['id'])" v-model="row.item.delete['value']" switch> </b-form-checkbox>
                    </template>
                    <template v-slot:cell(role)="row">
                        <a @click="editDetails(row.item.id, row.item.name)" class="btn btn-sm text-black-50" v-b-modal.dataEdit><fa icon="edit" fixed-width /></a>
                    </template>
                    </b-table>
                    
                    <div class="col-12 mx-auto p-0 noprint">
                        <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows_Role"
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
            <template v-slot:modal-footer="">
                <button @click="hideModal" class="mdb btn btn-outline-default"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>
            </template>
        </b-modal>
        
        <!-- End Edit Details Modal -->
    </div>
</template>

<script>
export default {
    data() {
        return{
            userList : [],
            roles: [
                {name: this.$t('user_management'), view:{value: false, id: 1}, insert:{value: false, id: 0}, update:{value: false, id: 0}, delete:{value: false, id: 0}},
                {name: this.$t('InventoryItem'), view:{value: false, id: 2}, insert:{value: false, id: 3}, update:{value: false, id: 4}, delete:{value: false, id: 5}},
                {name: this.$t('ItemReceive'), view:{value: false, id: 6}, insert:{value: false, id: 7}, update:{value: false, id: 8}, delete:{value: false, id: 9}},
                {name: this.$t('ItemIssue'), view:{value: false, id: 10}, insert:{value: false, id: 11}, update:{value: false, id: 12}, delete:{value: false, id: 13}},
                {name: this.$t('requisition'), view:{value: false, id: 14}, insert:{value: false, id: 15}, update:{value: false, id: 16}, delete:{value: false, id: 17}},
                {name: this.$t('product_details'), view:{value: false, id: 18}, insert:{value: false, id: 19}, update:{value: false, id: 20}, delete:{value: false, id: 21}},
                {name: this.$t('po_list'), view:{value: false, id: 22}, insert:{value: false, id: 23}, update:{value: false, id: 25}, delete:{value: false, id: 25}}
            ],
            errors : [],
            noprint: 'noprint',
            buttonTitle : this.$t('close'),
            title: '',


            transProps: {
                // Transition name
                name: 'flip-list'
            },
            totalRows: 1,
            totalRows_Role: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [10, 25, 50],
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        this.isBusy = true
        
        fetch(`api/settings/profile`)
        .then(res => res.json())
        .then(res => {  
            this.userList = res['users'];
            this.totalRows = this.userList.length;            
            this.isBusy = false;                
        })
        .catch(err => {
            alert(err.response.data.message);
        })
    },

    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },

        editDetails(id, name) {
            this.title = name
            this.taskId = id
            fetch(`api/settings/profile/${id}`)
            .then(res => res.json())
            .then(res => {  
                this.roles = res['roles'];
                this.totalRows_Role = this.roles.length;            
                this.isBusy = false;                
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        saved(val, id) {
            console.log(val)
        },

        save(val, id) {
            this.buttonTitle = this.$t('saving')

            axios.post(`api/settings/profile`, {
                check: val,
                user_id: this.taskId,
                role_id: id
            })
            .then(({data}) =>{
                this.buttonTitle = this.$t('close')
            })
            .catch(err => {
                this.buttonTitle = this.$t('close')
                alert(err.response.data.message)                      
            })
            
        },

        showModal() {
            this.$refs['dataEdit'].show()
        },

        hideModal() {
            this.$refs['dataEdit'].hide()
        },
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
        this.buttonTitle = this.$t('close')

            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'name', label : this.$t('name'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'email', label : this.$t('email'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'action', label: this.$t('Action'),  class: 'text-center', thClass: 'border-top border-dark font-weight-bold'}
            ]            
        },

        fieldsRole() {
        const lang = this.$i18n.locale
        if (!lang) { return [] }
        this.buttonTitle = this.$t('close')

            return [
                { key: 'index', label : '#', sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'name', label : this.$t('roles'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'view', label : this.$t('view'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'insert', label : this.$t('insert'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'update', label : this.$t('update'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
                { key: 'delete', label : this.$t('delete'), sortable: true, class: 'text-center', thClass: 'border-top border-dark font-weight-bold'},
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