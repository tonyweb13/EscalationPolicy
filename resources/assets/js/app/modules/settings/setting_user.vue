<template>
    <div >
        <div class="row">
            <loading-modal v-if="isLoading" />

            <download-site-format
            v-if="role == 'HR Access' || role == 'HR Admin Access' || role == 'Super Admin Access'" />

            <mass-upload-customdb1-site-location
            v-if="role == 'HR Access' || role == 'HR Admin Access' || role == 'Super Admin Access'"/>

            <v-client-table :data="data" :columns="columns" :options="options" >
                <template slot="actions" slot-scope="props">
                    <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)">
                        <i class="fa fa-pencil"></i> Edit Acl Rule
                    </button>
                </template>
            </v-client-table>
            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <settings-user-acl-modal :acl_rule_props="acl_user_rule" v-if="openAction == 'add'" slot="body" />
            </modal>
        </div>
    </div>
</template>

<style scoped>

</style>

<script>
'use strict'

import SettingsUserAclModal from "./settings_user_modal.vue"
import massUploadVpsSiteLocation from './massUploadVpsSiteLocation.vue'
import downloadSiteFormat from './downloadSiteFormat.vue'

export default {
    name: "SettingsAcl",

    components: {
        SettingsUserAclModal,
        massUploadVpsSiteLocation,
        downloadSiteFormat
    },

    props: {
        settingsProps: Object
    },

    data() {
        return {
            all: [{value: null, title: 'All'}],
            isLoading: false,
            role: this.$ep.role,
            columns: [
                'actions',
                'employee_no',
                'first_name',
                'last_name',
                'department.name',
                'office_location.name',
                'designation.name',
                'roles.0.name',
            ],
            data:  [],
            acl_user_rule: {
                employee_no: '',
                last_name: '',
                first_name: '',
                office_loc_name: '',
                office_loc_id: '',
                role: {},
                rules: {},
            },
            options: {
                headings: {
                    'employee_no': 'Employee No',
                    'first_name': 'First Name',
                    'last_name': 'Last Name',
                    'department.name': 'Department',
                    'office_location.name': 'Office',
                    'designation.name': 'Position',
                    'roles.0.name': 'Role',
                },
                sortable: [
                    'employee_no',
                    'first_name',
                    'last_name',
                    'department.name',
                    'office_location.name',
                    'designation.name',
                    'status',
                    'roles.0.name',
                ],
                filterable: true,
                pagination: { chunk:10 }
            },
            openModal: false,
            openAction: "",
        }
    },

    created(){
        this.$bus.$on('userSettings', this.getList);
        if (this.$route.hash == '#user-settings') {
            this.getList();
        }
        this.$bus.$on('init_modal', (param) => {
            this.openModal = param;
            this.getList();
        });
    },

    methods: {
        getList: function() {
            this.isLoading = true
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.get("/acl_rule/user_list");
            getAxios.then(response => {
                this.data = response.data;
                this.isLoading = false
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        eventModalClose: function() {
            this.acl_rule = {};
        },

        addEditRow: function (props_row) {
            if (props_row) {
                console.log("props_row==", props_row);
                this.headerName = "Edit User Rule";
                this.openAction = "add";
                this.openModal = true;
                this.acl_user_rule.id = props_row.id;
                this.acl_user_rule.employee_no = props_row.employee_no;
                this.acl_user_rule.last_name = props_row.last_name;
                this.acl_user_rule.first_name = props_row.first_name;
                this.acl_user_rule.office_loc_name = props_row.office_location.name;
                this.acl_user_rule.office_loc_id = props_row.office_location.id;
                this.acl_user_rule.role_name = '';
                this.acl_user_rule.role_id = '';
                if (props_row.roles.length >= 1) {
                    this.acl_user_rule.role_name = props_row.roles[0].name;
                    this.acl_user_rule.role_id = props_row.roles[0].role_id;
                }
                console.log("this.acl_user_rule==", this.acl_user_rule);
            }
        },
    },
}
</script>
