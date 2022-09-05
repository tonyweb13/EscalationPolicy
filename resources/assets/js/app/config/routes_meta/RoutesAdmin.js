'use strict'

import dashboard from '@/modules/admin/dashboard'
import incidentreport from '@/modules/admin/incidentreport'
import irhistory from '@/modules/irhistory'
import progressionoffense from '@/modules/admin/progressionoffense'
import hrbpcluster from '@/modules/admin/hrbpcluster'
import aclrule from '@/modules/settings/acl_rule'
import settingsuser from '@/modules/settings/setting_user'
import approval from '@/modules/admin/approval'


export default [
    {
        path: '/',
        component: dashboard,
        meta: {
            label: 'Dashboard',
            icon: 'fa fa-dashboard',
        },
    },
    {
        path: '/incidentreport',
        component: incidentreport,
        meta: {
            label: 'Incident Report',
            icon:'fa fa-flip-vertical',
        },
    },
    {
        path: '/irhistory',
        component: irhistory,
        meta: {
            label: 'Incident Report History',
            icon: 'fa fa-flip-vertical',
        },
    },
    {
        path: '/progression',
        component: progressionoffense,
        meta: {
            label: 'Progression Offense',
            icon: 'fa fa-flip-vertical',
        },
    },
    {
        path: '/hrbp/cluster',
        component: hrbpcluster,
        meta: {
            label: 'HRBP Cluster',
            icon: 'fa fa-flip-horizontal',
        },
    },
    {
        path: '/settings/acl_rule',
        component: aclrule,
        meta: {
            label: 'ACL Rules',
            icon: 'fa fa-flip-horizontal',
        },
    },
    {
        path: '/settings/settings_user',
        component: settingsuser,
        meta: {
            label: 'Settings User',
            icon: 'fa fa-flip-horizontal',
        },
    },
    {
        path: '/head/approval',
        component: approval,
        meta: {
            label: 'Approval',
            icon: 'fa fa-thumbs-o-up',
        },
    },
]
