<template>
    <div class="modal-content">
        <div class="modal-body">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#tab-user_rule" aria-expanded="false">User Role</a>
                    </li>
                    <li><a data-toggle="tab" href="#tab-closed" aria-expanded="false">ACL Rules</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-user_rule" class="tab-pane active">
                        <settings-user-role :user="user_fields"/>
                    </div>
                    <div class="clearfix"></div>

                    <div id="tab-closed" class="tab-pane">
                        <settings-user-acl :user_fields="user_fields"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
'use strict'
import settingsUserRole from './settings_user_role.vue'
import settingsUserAcl from './settings_user_acl.vue'

export default {
    props: {
        acl_rule_props: {
            employee_no: '',
            last_name: '',
            first_name: '',
            role: {
                text: '',
                value: '',
            },
            vps_office_location: {
                text: '',
                value: '',
            }
        },
    },

    data: function () {
        return {
            user_fields: {
                first_name: '',
                last_name: '',
                employee_no: '',
                role_id: '',
                role: {},
                rules: {},
                vps_office_location: {},
            }
        }
    },

    methods: {
        saveAcl() {
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.post('/acl_rule/updateOrCreateUserRoleRules/'+this.acl_rule_props.id, {
                    params: {
                        employee_no: this.user_fields.employee_no,
                        rules: this.user_fields.rules,
                        role: this.user_fields.role,
                    },
            });
            getAxios.then(response => {
                this.$swal({
                    html: 'Successfully added',
                    type: "success",
                    showConfirmButton: false,
                    timer: 1000,
                });
                this.$bus.$emit('init_modal', false);
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
    },

    created(){
        if (this.acl_rule_props.id) {
            this.user_fields.id = this.acl_rule_props.id;
            this.user_fields.first_name = this.acl_rule_props.first_name
            this.user_fields.last_name = this.acl_rule_props.last_name
            this.user_fields.employee_no = this.acl_rule_props.employee_no
            this.user_fields.role = {
                text: this.acl_rule_props.role_name,
                value: this.acl_rule_props.role_id,
            }
            this.user_fields.vps_office_location = {
                text: this.acl_rule_props.office_loc_name,
                value: this.acl_rule_props.office_loc_id,
            }
            this.user_fields.rules = this.acl_rule_props.rules;
            console.log("this.user_fields==", this.user_fields);
        }
    },

    components: {
        settingsUserRole,
        settingsUserAcl,
    },
}
</script>
