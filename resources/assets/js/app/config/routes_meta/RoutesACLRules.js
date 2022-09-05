'use strict'

import aclrule from '@/modules/settings/acl_rule'
import role from '@/modules/settings/setting_user'

let rule = window.EP.settings.rule

function isEnabled(rules) {
    return rules.is_enable
}

export default [
    {
        path: '/settings/acl_rule',
        component: aclrule,
        meta: {
            rules: "aclrules",
            label: 'ACL Rules',
            icon: 'fa fa-eyedropper',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.aclrules)) {
                next()
            } else {
                next('/')
            }
        },
    },
    {
        path: '/settings/settings_user',
        component: role,
        meta: {
            rules: "user",
            label: 'User Settings',
            icon: 'fa fa-dashboard',
        },
        beforeEnter: (to, from, next) => {
            if (isEnabled(rule.user)) {
                next()
            } else {
                next('/')
            }
        },
    },
]
