<template>
    <div class="container-fluid justify-content-center">
        <div class="col-md-12" :class="noprint">
           <div class="card filterable">
                <div class="card-header d-flex align-items-center">
                    <h3 class="panel-title float-left">{{ $t('hourly_production')}}</h3>
                </div> 
                <div class="card-body">
                    <div class="col-md-6 float-left">
                        <select class="form-control" v-model="department">
                            <option value="assembly">{{ $t('assembly') }}</option>
                            <option value="polish">{{ $t('polish') }}</option>
                            <option value="wash">{{ $t('wash') }}</option>
                            <option value="injection">{{ $t('injection') }}</option>
                            <option value="cutting">{{ $t('cutting') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 float-left input-group">
                        <input type="date" class="form-control" v-model="prodDate">
                        <div @click="searchByDate" class="input-group-append input-group-text pointer"><b-icon icon="search"></b-icon></div>
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
                        <button class="mdb btn btn-outline-default"><b-icon icon="circle-fill" animation="throb" :class="loading"></b-icon> {{ buttonTitle }}</button>                     
                    </div>
                    <b-table id="table-transition" primary-key="id" :busy="isBusy" show-empty small striped hover responsive
                    :items="ProductionByDeparment"
                    :fields="fields"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :tbody-transition-props="transProps"
                    @filtered="onFiltered"
                    class="table-transition table-bordered"
                    style="cursor : pointer"
                    >
                    <template v-slot:table-busy>
                        <div class="text-center align-middle text-success my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>{{$t('loading')}}</strong>
                        </div>
                    </template>
                    <template v-slot:cell(action)="row">
                        <a @click="addRow" class="btn btn-sm text-black-50"><fa icon="plus" fixed-width /></a>
                        <a @click="destroy_d(row.item.id, row.index)" class="btn btn-sm text-black-50"><fa icon="trash-alt" fixed-width /></a>
                    </template>
                    <template v-slot:cell(line)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.line" style="min-width: 100px;" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(section)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.section" style="min-width: 100px;" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(leader)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.leader" style="min-width: 100px;" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(polist_id)="row">
                        <div style="min-width: 150px;">
                            <model-select :options="PoList" v-model="row.item.polist_id" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0"></model-select>
                            <b-icon icon="arrow-repeat" @click="save(row.item, 'refresh')" class="pointer"></b-icon>
                        </div>
                    </template>
                    <template v-slot:cell(item)="row">
                        <textarea @keyup="lazySaving(row.item)" type="text" v-model="row.item.item" style="min-width: 100px;" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0"></textarea>
                    </template>
                    <template v-slot:cell(quantity)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.quantity"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(hourly_target)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.hourly_target" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(manpower)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.manpower" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0">
                    </template>
                    <template v-slot:cell(complete)="row">
                        {{row.item.complete  = parseInt(complete_method(row.item.polist_id) || 0) + parseInt(row.item.qty_1 || 0)+ parseInt(row.item.qty_2 || 0)+ parseInt(row.item.qty_3 || 0)+ parseInt(row.item.qty_4 || 0)+ parseInt(row.item.qty_5 || 0)+ parseInt(row.item.qty_6 || 0)+ parseInt(row.item.qty_7 || 0)+ parseInt(row.item.qty_8 || 0)+ parseInt(row.item.qty_9 || 0)+ parseInt(row.item.qty_10 || 0)+ parseInt(row.item.qty_11 || 0)+ parseInt(row.item.qty_12 || 0)+ parseInt(row.item.qty_13 || 0)+ parseInt(row.item.qty_14 || 0)+ parseInt(row.item.qty_15 || 0)+ parseInt(row.item.qty_16 || 0)+ parseInt(row.item.qty_17 || 0)+ parseInt(row.item.qty_18 || 0)+ parseInt(row.item.qty_19 || 0)+ parseInt(row.item.qty_20 || 0)+ parseInt(row.item.qty_21 || 0)+ parseInt(row.item.qty_22 || 0)+ parseInt(row.item.qty_23 || 0)+ parseInt(row.item.qty_24 || 0)}}
                    </template>
                    <template v-slot:cell(balance)="row">
                        {{row.item.balance = row.item.quantity - row.item.complete}}
                    </template>
                    <template v-slot:cell(qty_8)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_8" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_8" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_8" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_9)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_9" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_9" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_9" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_10)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_11)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_11" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_11" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_11" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_12)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_12" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_12" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_12" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_13)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_13" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_13" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_13" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_14)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_14" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_14" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_14" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_15)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_16)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_16" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_16" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_16" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_17)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_17" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_17" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_17" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_18)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_18" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_18" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_18" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_19)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_19" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_19" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_19" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_20)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_20" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_20" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_20" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_21)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_21" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_21" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_21" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_22)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_22" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_22" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_22" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_23)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_23" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_23" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_23" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_24)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_24" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_24" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_24" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_1)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_2)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_2" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_2" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_2" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_3)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_4)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_4" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_4" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_4" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_5)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_5" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_5" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_5" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_6)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_6" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_6" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_6" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(qty_7)="row">
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.qty_7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0" placeholder="Qty">
                        <!-- <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.remain_7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="Remain"> -->
                        <input @keyup="lazySaving(row.item)" type="text" v-model="row.item.ng_7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control text-center row-fluid m-0 p-0 border-top border-right-0 border-left-0 border-bottom-0 border-dark bg-transparent rounded-0" placeholder="NG">
                    </template>
                    <template v-slot:cell(total)="row">
                        <span class="border-bottom border-dark col-12">{{parseInt(row.item.qty_1 || 0)+ parseInt(row.item.qty_2 || 0)+ parseInt(row.item.qty_3 || 0)+ parseInt(row.item.qty_4 || 0)+ parseInt(row.item.qty_5 || 0)+ parseInt(row.item.qty_6 || 0)+ parseInt(row.item.qty_7 || 0)+ parseInt(row.item.qty_8 || 0)+ parseInt(row.item.qty_9 || 0)+ parseInt(row.item.qty_10 || 0)+ parseInt(row.item.qty_11 || 0)+ parseInt(row.item.qty_12 || 0)+ parseInt(row.item.qty_13 || 0)+ parseInt(row.item.qty_14 || 0)+ parseInt(row.item.qty_15 || 0)+ parseInt(row.item.qty_16 || 0)+ parseInt(row.item.qty_17 || 0)+ parseInt(row.item.qty_18 || 0)+ parseInt(row.item.qty_19 || 0)+ parseInt(row.item.qty_20 || 0)+ parseInt(row.item.qty_21 || 0)+ parseInt(row.item.qty_22 || 0)+ parseInt(row.item.qty_23 || 0)+ parseInt(row.item.qty_24 || 0)}}</span>
                        <span class="col-12">{{parseInt(row.item.ng_1 || 0)+ parseInt(row.item.ng_2 || 0)+ parseInt(row.item.ng_3 || 0)+ parseInt(row.item.ng_4 || 0)+ parseInt(row.item.ng_5 || 0)+ parseInt(row.item.ng_6 || 0)+ parseInt(row.item.ng_7 || 0)+ parseInt(row.item.ng_8 || 0)+ parseInt(row.item.ng_9 || 0)+ parseInt(row.item.ng_10 || 0)+ parseInt(row.item.ng_11 || 0)+ parseInt(row.item.ng_12 || 0)+ parseInt(row.item.ng_13 || 0)+ parseInt(row.item.ng_14 || 0)+ parseInt(row.item.ng_15 || 0)+ parseInt(row.item.ng_16 || 0)+ parseInt(row.item.ng_17 || 0)+ parseInt(row.item.ng_18 || 0)+ parseInt(row.item.ng_19 || 0)+ parseInt(row.item.ng_20 || 0)+ parseInt(row.item.ng_21 || 0)+ parseInt(row.item.ng_22 || 0)+ parseInt(row.item.ng_23 || 0)+ parseInt(row.item.ng_24 || 0)}}</span>
                    </template>
                    <template v-slot:cell(remarks)="row">
                        <textarea @keyup="lazySaving(row.item)" type="text" v-model="row.item.product_code" class="form-control text-center row-fluid p-0 m-0 border-0 bg-transparent rounded-0"></textarea>
                    </template>
                    </b-table>
                </div>
            </div>
        </div> 
    </div>
</template>

<script>
import { ModelSelect } from 'vue-search-select';
export default {
    middleware: 'auth',

    metaInfo () {
        return { title: this.$t('hourly_production') }
    },

    data() {
        return{
            PoList : [],
            Production: [],
            ProductionByDeparment: [],
            roles: [],
            department: 'assembly',
            prodDate: this.convertDate(new Date()),
            noprint : '',
            disable: false,
            waiting: false,
            buttonTitle : this.$t('save'),

            transProps: {
                // Transition name
                name: 'flip-list'
            },
            filter: null,
            filterOn: [],
            isBusy: false,
        }
    },

    mounted() {
        this.fetchData()

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

        fetchData() {
            this.isBusy = true
            fetch(`api/prodhourly/${this.prodDate}`)
            .then(res => res.json())
            .then(res => {
                this.PoList = res['PoList']
                this.Production = res['production']
                this.ProductionByDeparment = this.ProductionByDeparmentMethod
                this.isBusy = false
            })
            .catch(err => {
                alert(err.response.data.message);
            })
        },

        searchByDate() {
            this.fetchData()
        },

        complete_method(id) {
            if (id) {
                for (let i = 0; i < this.PoList.length; i++) {
                    if (this.PoList[i]['department'] == this.department && this.PoList[i]['value'] == id) {
                        return this.PoList[i]['total_prod']
                    }
                    
                }
            }
        },

        addRow() {            
            this.ProductionByDeparment.push({'id': null, 'line': null, 'section': null, 'department': this.department, 'po_no': null, 'product_code': null, 'quantity': null, 'remarks': null, 'prod_date': this.prodDate, 
            'qty_1': null, 'ng_1': null, 'qty_2': null, 'ng_2': null, 'qty_3': null, 'ng_3': null, 'qty_4': null, 'ng_4': null, 'qty_5': null, 'ng_5': null, 'qty_6': null, 
            'ng_6': null, 'qty_7': null, 'ng_7': null, '': null, 'ng_8': null, '': null, 'ng_9': null, '': null, 'ng_10': null, 'qty_11': null, 'ng_11': null, 'qty_12': null, 
            'ng_12': null, '': null, 'ng_13': null, 'qty_14': null, 'ng_14': null, 'qty_15': null, 'ng_15': null, 'qty_16': null, 'ng_16': null, 'qty_17': null, 'ng_17': null, 
            'qty_18': null, 'ng_18': null, 'qty_19': null, 'ng_19': null, 'qty_20': null, 'ng_20': null, 'qty_21': null, 'ng_21': null, 'qty_22': null, 'ng_22': null, 'qty_23': null, 
            'ng_23': null, 'qty_24': null, 'ng_24': null, 'polist_id': null, 'producthead_id': null})
        },

        lazySaving(value, index) {     
            this.disable = !this.disable;
            this.buttonTitle = this.$t('saving')
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                this.save(value, index);
            }, 500);
        },

        save(value, index) {
            if (this.wating) return
            if(!value['id']){
                this.waiting = true
                axios.post(`api/prodhourly`, value)
                .then(({data}) =>{
                    if(index == 'refresh' && value['polist_id']) this.fetchData()
                    value['id'] = data.id
                    this.waiting = false
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                        this.$toast.error(this.$t('required_field'), this.$t('error'), {timeout: 3000, position: 'center'})
                    }
                    this.waiting = false
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                    alert(err.response.data.message)                      
                })
            } else {
                axios.patch(`api/prodhourly/${value['id']}`, value)
                .then(res => {
                    if(index == 'refresh' && value['polist_id']) this.fetchData()
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                })
                .catch(err => {
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors
                    }
                    this.disable = !this.disable
                    this.buttonTitle = this.$t('save')
                });
            }
        },

        destroy_d(id, index){
            this.$toast.warning(this.$t('sure_to_delete'), this.$t('confirm'), {
                timeout: 20000,           
                position: 'center',
                buttons: [
                    ['<button><b>' + this.$t('ok') +'</b></button>', (instance, toast) => {                        
                        if(id){
                            axios.delete(`api/prodhourly/${id}`)                        
                            .then(res => {
                                this.ProductionByDeparment.splice(index, 1)                                
                            })
                            .catch(err => {
                                alert(err.response.data.message);                       
                            });
                        } else {
                            this.ProductionByDeparment.splice(index, 1)
                        }

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
        TypetoSearch() {
            const lang = this.$i18n.locale
            if (!lang) { return '' }
            return this.$t('TypetoSearch')
        },

        ProductionByDeparmentMethod() {
            let array = [], k=0

            if (this.Production.length == 0) 
            return [{'id': null, 'line': null, 'section': null, 'department': this.department, 'po_no': null, 'product_code': null, 'quantity': null, 'remarks': null, 'prod_date': this.prodDate, 
            'qty_1': null, 'ng_1': null, 'qty_2': null, 'ng_2': null, 'qty_3': null, 'ng_3': null, 'qty_4': null, 'ng_4': null, 'qty_5': null, 'ng_5': null, 'qty_6': null, 
            'ng_6': null, 'qty_7': null, 'ng_7': null, '': null, 'ng_8': null, '': null, 'ng_9': null, '': null, 'ng_10': null, 'qty_11': null, 'ng_11': null, 'qty_12': null, 
            'ng_12': null, '': null, 'ng_13': null, 'qty_14': null, 'ng_14': null, 'qty_15': null, 'ng_15': null, 'qty_16': null, 'ng_16': null, 'qty_17': null, 'ng_17': null, 
            'qty_18': null, 'ng_18': null, 'qty_19': null, 'ng_19': null, 'qty_20': null, 'ng_20': null, 'qty_21': null, 'ng_21': null, 'qty_22': null, 'ng_22': null, 'qty_23': null, 
            'ng_23': null, 'qty_24': null, 'ng_24': null, 'polist_id': null, 'producthead_id': null}]

            for (let i = 0; i < this.Production.length; i++) {
                if (this.Production[i]['department'] == this.department) {
                    array[k++] = this.Production[i]
                }                
            }
            return array 
        },

        Total() {
            let t = 0
            for (let i = 0; i < this.productionByStore.length; i++) {
                t += (parseFloat(this.productionByStore[i]['prod_qty']) || 0)                
            } return t
        },

        fields() {
            const lang = this.$i18n.locale
            if (!lang) { return [] }
            this.buttonTitle = this.$t('save')
    
            return [
                { key: 'action', label : '#', class: 'text-center align-middle', thClass: 'bg-white border-top border-dark font-weight-bold'},
                { key: 'line', label : this.$t('line'), class: 'text-center align-middle', tdClass: 'p-0', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'section', label : this.$t('section'), class: 'text-center align-middle', tdClass: 'p-0', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'leader', label : this.$t('leader'), class: 'text-center align-middle', tdClass: 'p-0', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'polist_id', label : 'PO No', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'item', label : this.$t('style') + ' ' + this.$t('code'), stickyColumn: true, class: 'bg-white text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'manpower', label : this.$t('manpower'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'hourly_target', label : this.$t('hourly_target'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'quantity', label : this.$t('quantity'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'complete', label : this.$t('complete'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'balance', label : this.$t('balance'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold' },
                { key: 'qty_8', label : '8-9 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_9', label : '9-10 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_10', label : '10-11 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_11', label : '11-12 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_12', label : '12-1 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_13', label : '1-2 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_14', label : '2-3 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_15', label : '3-4 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_16', label : '4-5 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_17', label : '5-6 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_18', label : '6-7 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_19', label : '7-8 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_20', label : '8-9 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_21', label : '9-10 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_22', label : '10-11 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_23', label : '11-12 pm', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_24', label : '12-1 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_1', label : '1-2 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_2', label : '2-3 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_3', label : '3-4 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_4', label : '4-5 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_5', label : '5-6 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_6', label : '6-7 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'qty_7', label : '7-8 am', class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'total', label : this.$t('total'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
                { key: 'remarks', label : this.$t('remarks'), class: 'text-center align-middle', thClass: 'text-nowrap border-top border-dark font-weight-bold'},
            ]
        },

        loading(){
            return[ 
                this.buttonTitle == this.$t('saving') ? '' : 'd-none'
            ]
        },
    },

    components: { ModelSelect }
}
</script>

<style>

</style>