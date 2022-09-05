'use strict'

import RouteSettings from '@/modules/settings/index'

let role = window.EP.settings.role
let designation = window.EP.settings.user.designation.name

export default [
    {
        path: '/settings',
        component: RouteSettings,
        meta: {
            rules: 'settings',
            label: (designation.indexOf('IT Help') >= 0 || role == 'Super Admin Access'
            || role == 'HR Admin Access') ? 'Settings' : '',
            title: 'Settings',
            breadcrumb: 'Settings',
            icon: 'fa fa-cog',
            global_menu : false,
        },
        beforeEnter: (to, from, next) => {
            if (designation.indexOf('IT Help') >= 0 || role == 'Super Admin Access'
            || role == 'HR Admin Access') {
                next()
            } else {
                next('/')
            }
        },
    },
]
