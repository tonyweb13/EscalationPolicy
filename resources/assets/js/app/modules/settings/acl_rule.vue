<template>
    <div class="wrapper wrapper-content animated fadeIn">
        <div class="text-right">
            <button type="button"  @click.prevent="addAclRules()" class="btn btn-info" style="margin-bottom:20px;">
                <i class="fa fa-plus"/> Create ACL Rule
            </button>
        </div>

        <v-client-table :data="data" :columns="columns" :options="options" >
            <template slot="actions" slot-scope="props">
                <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)">
                    <i class="fa fa-pencil"></i> Edit
                </button>
                <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)">
                    <i class="fa fa-remove"></i> Delete
                </button>
            </template>
        </v-client-table>
        <modal v-if="openModal" @close="openModal = false; eventModalClose()">
            <h3 slot="header">{{ headerName }}</h3>
            <settings-acl-modal :acl_rule_props="acl_rule" v-if="openAction == 'add'" slot="body" />
        </modal>
    </div>
</template>

<style scoped>

</style>

<script>
'use strict'

import SettingsAclModal from "./acl_modal.vue"

export default {
    name: "SettingsAcl",

    components: {
        SettingsAclModal,
    },

    props: {
        settingsProps: Object
    },

    data() {
        return {
            columns: [
                'actions',
                'rule_name',
                'rule_parent',
                'label',
                'description',
                'created_at',
                'updated_at',
            ],
            data:  [],
            acl_rule: {
                rule_name: '',
                rule_parent_id: '',
                rule_parent_name: '',
                label: '',
                description: '',
            },
            options: {
                headings: {
                    'rule_name': 'Rule Name',
                    'rule_parent': 'Parent Rule',
                    'label': 'Label',
                    'description': 'Description',
                    'created_at': 'Created At',
                    'updated_at': 'Updated At',
                },
                sortable: [
                    'rule_name',
                    'rule_parent',
                    'label',
                    'description',
                ],
                filterable: true,
                pagination: { chunk:10 }
            },
            openModal: false,
            openAction: "",
        }
    },

    created(){
        this.$bus.$on('aclSettings', this.getList);
        if (this.$route.hash == '#acl-rule') {
            this.getList();
        }
        if (this.$route.hash == '') {
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
            this.$constants.Settings_ACL_API.get("/acl_rule/list")
            .then(response => {
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

        addAclRules() {
            this.openModal = true;
            this.openAction = "add";
            this.headerName = "Add Acl Rule"
        },

        addEditRow: function (props_row) {
            if(props_row){
                this.headerName = "Edit Acl Rule";
                this.openAction = "add";
                this.openModal = true;
                this.acl_rule.id = props_row.id;
                this.acl_rule.rule_name = props_row.rule_name;
                this.acl_rule.rule_parent_id = props_row.rule_parent_id;
                this.acl_rule.label = props_row.label;
                this.acl_rule.description = props_row.description;
                this.acl_rule.rule_parent_name = props_row.rule_parent;
            }
        },

        deleteRow: function (props_row) {
            this.$swal({
                title: 'Are you sure you want to delete?',
                text: "Once deleted, you will not be able to revert this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
            })
                .then((result) => {
                    if (result.value) {
                        this.$constants.Settings_ACL_API.post("/acl_rule/delete_acl_rule/" + props_row.id)
                            .then(response => {
                                this.getList()
                                this.$success(props_row.rule_name + ' has been deleted!')
                            });
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$success(props_row.rule_name + ' has been cancelled!')
                    }
                })
        },

        initializeData() {
            this.$constants.Settings_ACL_API.get('/acl_rule/rule_parent')
                .then(response => {
                this.rule_parent = response.data
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
    },
}
</script>
