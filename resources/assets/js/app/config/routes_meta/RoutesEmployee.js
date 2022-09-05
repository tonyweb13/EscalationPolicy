'use strict'

import dashboard from '@/modules/employee/dashboard'
import complainant from '@/modules/complainant_respondent'
import coc from '@/modules/employee/coc'
import behavioral from '@/modules/employee/coc/behavioralAction.vue'
import offense from '@/modules/employee/coc/gravityOffense.vue'
import coachinglog from '@/modules/coaching_log'
import irhistory from '@/modules/irhistory'
import irresolution from '@/modules/employee/coc/IrResolution.vue'

export default [
    {
        path: '/',
        component: dashboard,
        meta: {
            label: 'Dashboard',
            icon:'fa fa-dashboard',
        },
    },
    {
        path: '/employee/coc',
        component: coc,
        meta: {
            label: 'Code of Conduct',
            icon: 'fa fa-paragraph',
        },
    },
    {
        path: '/employee/behavioral/action',
        component: behavioral,
        meta: {
            label: 'Behavioral Action Table',
            icon: 'fa fa-paragraph',
        },
    },
    {
        path: '/employee/levels/gravity',
        component: offense,
        meta: {
            label: 'Levels of Gravity',
            icon: 'fa fa-paragraph',
        },
    },
    {
        path: '/employee/irresolution',
        component: irresolution,
        meta: {
            label: 'Case Closure',
            icon: 'fa fa-paragraph',
        },
    },
    {
        path: '/employee/incidentreport',
        component: complainant,
        meta: {
            label: 'Incident Report',
            icon: 'fa fa-paragraph',
        },
    },
    {
        path: '/employee/coachinglog',
        component: coachinglog,
        meta: {
            label: 'Coaching Log',
            icon: 'fa fa-paragraph',
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
]
