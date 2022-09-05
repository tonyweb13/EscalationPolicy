<template>
    <div slot="form-body">
        <the-label label="First Name" :asterisk="true">
            <input type="text" v-model="user.first_name" class="form-control" label="text" disabled="disabled"/>
        </the-label>

        <the-label label="Last Name" :asterisk="true">
            <input type="text" v-model="user.last_name" class="form-control" label="text" disabled="disabled"/>
        </the-label>

        <the-label label="Employee ID" :asterisk="true">
            <input type="number" v-model="user.employee_no" class="form-control" label="text" disabled="disabled" />
        </the-label>

        <the-label label="Site Location" :asterisk="true">
            <vSelect v-model="user.vps_office_location" :options="vps_site_loc" label="text" />
        </the-label>

        <the-label label="Role Name" :asterisk="true">
            <vSelect v-model="user.role" :options="roles_list" label="text" />
        </the-label>

        <button type="button" class="btn btn-primary" @click="saveAcl">
            SAVE
        </button>
    </div>
</template>

<script>
'use strict'

export default {
    name: "SettingsUserRoles",

    props: {
        user: {},
    },

    data() {
        return {
            users: {
                role: {
                    text: "",
                    value: ""
                }
            },
            roles_list: [],
            vps_site_loc: [],
        }
    },

    methods: {
        userRole(){
            this.$constants.Settings_ACL_API.get("/acl_rule/dropdown_role_name")
            .then(response => {
                this.roles_list = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        nullReturn(settings) {
            return {
                title: '',
                value: '',
            }
        },
        saveAcl() {
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.post('/acl_rule/updateOrCreateUserRoleRules/'+this.$props.user.id, {
                    params: {
                        employee_no: this.$props.user.employee_no,
                        rules: this.$props.user.rules,
                        role: this.$props.user.role,
                        vps_office_location: this.$props.user.vps_office_location,
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
        vpsSiteList(){
            this.$constants.Settings_ACL_API.get("/dropdown_vps_site_loc")
            .then(response => {
                this.vps_site_loc = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
    },
    mounted() {
        this.userRole();
        this.vpsSiteList();
    },
}
</script>
