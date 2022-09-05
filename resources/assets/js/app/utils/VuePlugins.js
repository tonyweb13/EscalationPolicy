'use strict'

import Vue from 'vue'
import vSelect from 'vue-select'
import { VueEditor } from 'vue2-editor'
import Datepicker from 'vuejs-datepicker'
import JsonExcel from 'vue-json-excel'
import VPaginator from 'vuejs-paginator'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'
import ApexCharts from 'vue-apexcharts'

let name_to_component = {
    vSelect,
    VueEditor,
    Datepicker,
    JsonExcel,
    VPaginator,
    ValidationObserver,
    ValidationProvider,
    TabGroup,
    TabElement,
    ApexCharts
}

Object.keys(name_to_component).forEach((name) => {
    Vue.component(name, name_to_component[name]);
});
