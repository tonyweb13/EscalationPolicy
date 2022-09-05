"use strict"

import 'jquery'
import 'bootstrap'
import 'metismenu'
import 'toastr/build/toastr.min.css'
import 'ladda/dist/ladda-themeless.min.css'
import '@/style/CustomStyle.css'

import Vue from 'vue'
import App from '@/app.vue'
import VueRouter from 'vue-router'
import Routes from '@/config/Routes'
import swal from 'sweetalert2'
import moment from 'moment'
import * as ladda from 'ladda'
import * as constants from '@/config/Constants'
import { success, failure } from '@/utils/ToastrMessages'
import { ServerTable, ClientTable, Event } from 'vue-tables-2'
import sharedCatchError from '@/mixins/SharedCatchError'
import Echo from 'laravel-echo'
import VueResource from 'vue-resource'
import { extend } from 'vee-validate';
import { required, regex, numeric } from 'vee-validate/dist/rules';

import '@/utils/VuePlugins'
import '@/containers/Containers'
import '@/widgets/Widgets'
import store from '@/store'

$.ajaxSetup({
    headers: {'X-CSRF-Token': EP.csrf_token}
});

let settings = window.EP.settings

Vue.prototype.$ep = settings
Vue.prototype.$constants = constants
Vue.prototype.$ladda = ladda
Vue.prototype.$bus = new Vue()
Vue.prototype.$swal = swal
Vue.prototype.$success = success
Vue.prototype.$failure = failure
Vue.config.productionTip = false

Vue.use(VueRouter)
Vue.use(ClientTable)
Vue.use(VueResource)
Vue.mixin(sharedCatchError)

Vue.filter('formatDate', function(value) {
  if (value) {
    return moment(String(value)).format('YYYY-MM-DD')
  }
})

Vue.filter('formatDateCompleteMonth', function(value) {
  if (value) {
    return moment(String(value)).format('MMMM DD, YYYY')
  }
})

Vue.filter('stringReplace', function (value) {
  if (value) {
    return String(value).replace(/app|models|admin|settings|coc|\\|{|}/gi, '');
  }
})

Vue.filter('urlReplace', function (value) {
  if (value) {
    return String(value).replace(/\\|\[|\]|"/gi, '');
  }
})

Vue.filter('capitalize', function (value) {
  if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
})

window.Pusher = require('pusher-js');

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  encrypted: false
});

extend('required', {
  ...required,
  message: 'This field is required'
});

extend("numeric", {
  ...numeric,
  message: 'This field is numeric/numbers/integers only'
});

extend('daterange', {
    params: ['target'],
        validate(value, { target }) {
            let start = new Date(target);
            let end = new Date(value);
        return start <= end;
    },
    message: 'Date of Incident is not valid range'
});

export const router = new VueRouter({
    routes: Routes,
    mode: 'history',
    scrollBehavior: function(to, from, savedPosition) {
        if (to.hash) {
            if (from.path == to.path && savedPosition == null) {
                location.reload();
            }
            return {selector: to.hash}
        } else {
            return { x: 0, y: 0 }
        }
    },
})

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#ep-app')
