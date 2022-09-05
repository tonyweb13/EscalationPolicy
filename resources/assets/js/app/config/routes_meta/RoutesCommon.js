'use strict'

import irhistory from '@/modules/irhistory'
import announcement from '@/modules/announcement'
import acknowledgereceipt from '@/modules/acknowledgereceipt'
import dashboard from '@/modules/admin/dashboard'
import gravity from '@/modules/admin/settings/coc/gravity/indexGravity'
import incidentreport from '@/modules/admin/incidentreport'
import offense from '@/modules/admin/settings/coc/offense'
import behaviorial from '@/modules/admin/settings/coc/behavioralaction/index.vue'
import incidentreportresolution from '@/modules/admin/settings/coc/incidentreportresolution'
import coachinglog from '@/modules/coaching_log/coachingLogForm.vue'
import hrbpcluster from '@/modules/admin/hrbpcluster'
import reports from '@/modules/admin/settings/reports'
import auditlogs from '@/modules/admin/settings/auditlogs'
import progressionoffense from '@/modules/admin/progressionoffense'
import osh from '@/modules/osh'
import egreetings from '@/modules/egreetings'


let rule = window.EP.settings.rule

function isEnabled(rules) {
    return rules.is_enable
}

export default [
    {
        path: '/',
        component: dashboard,
        meta: {
            rules: "dashboard",
            label: 'Dashboard',
            icon: 'fa fa-dashboard',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.dashboard)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/egreetings',
        component: egreetings,
        meta: {
            rules: "e_greetings",
            label: 'E-Greetings',
            icon: 'fa fa-envelope-o',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled({"is_enable":true})) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/osh',
        component: osh,
        meta: {
            rules: "osh_module",
            label: 'OSH Module',
            icon: 'fa fa-wrench',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled({"is_enable":true})) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/acknowledgereceipt',
        component: acknowledgereceipt,
        meta: {
            rules: "acknowledge_receipt",
            label: 'Acknowledge Receipt',
            icon: 'fa fa-money',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.acknowledge_receipt)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/announcement',
        component: announcement,
        meta: {
            rules: "announcement",
            label: 'Announcement',
            icon: 'fa fa-microphone',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.announcement)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/codeofconduct',
        component: offense,
        meta: {
            rules: "code_of_conduct",
            label: 'Code of Conduct',
            icon: 'fa fa-eyedropper',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.code_of_conduct)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/behaviorialaction',
        component: behaviorial,
        meta: {
            rules: "behavioral_action",
            label: 'Behaviorial Action',
            icon: 'fa fa-newspaper-o',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.behavioral_action)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/levelsgravity',
        component: gravity,
        meta: {
            rules: "levels_of_gravity",
            label: 'Levels of Gravity',
            icon: 'fa fa-newspaper-o',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.levels_of_gravity)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/caseclosure',
        component: incidentreportresolution,
        meta: {
            rules: "case_closure",
            label: 'Case Closure',
            icon:'fa fa-paragraph',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.case_closure)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/incidentreport',
        component: incidentreport,
        meta: {
            rules: "incident_report",
            label: 'Incident Report',
            icon:'fa fa-chain',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.incident_report)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/irhistory',
        component: irhistory,
        meta: {
            rules: "incident_report_history",
            label: 'Incident Report History',
            icon: 'fa fa-flask',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.incident_report_history)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/hrbpcluster',
        component: hrbpcluster,
        meta: {
            rules: "hrbp_cluster",
            label: 'HRBP Cluster',
            icon: 'fa fa-paint-brush',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.hrbp_cluster)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/progression',
        component: progressionoffense,
        meta: {
            rules: "progression_offense",
            label: 'Progression Offense',
            icon: 'fa fa-institution',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.progression_offense)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/employee/coachinglog',
        component: coachinglog,
        meta: {
            rules: "coaching_logs",
            label: 'Coaching Log',
            icon: 'fa fa-paragraph',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.coaching_logs)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/reports',
        component: reports,
        meta: {
            rules: "reports",
            label: 'Reports',
            icon: 'fa fa-cloud-download',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.reports)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/auditlogs',
        component: auditlogs,
        meta: {
            rules: "audit_logs",
            label: 'Audit Logs',
            icon: 'fa fa-cloud-download',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.audit_logs)) {
                next()
            } else {
                next('/')
            }
        },
    }
]
