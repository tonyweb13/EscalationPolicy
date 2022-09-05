<template>
    <div class="wrapper wrapper-content animated fadeIn">
        <div class="text-right">
            <button type="button"  @click.prevent="addOffice()" class="btn btn-info" style="margin-bottom:20px;">
                <i class="fa fa-plus"/> Create Office
            </button>
        </div>

        <v-client-table :data="data" :columns="columns" :options="options" >
            <template slot="actions" slot-scope="props">
                <button class='btn btn-primary btn-xs' @click="addEditRow(props.row)">
                    <i class="fa fa-pencil"></i> Edit
                </button>
                <!-- <button class='btn btn-danger btn-xs' @click="deleteRow(props.row)">
                    <i class="fa fa-remove"></i> Delete
                </button> -->
            </template>
        </v-client-table>
        <modal v-if="openModal" @close="openModal = false; eventModalClose()">
            <h3 slot="header">{{ headerName }}</h3>
            <settings-office-modal :office_props="office" v-if="openAction == 'add'" slot="body" />
        </modal>
    </div>
</template>

<style scoped>

</style>

<script>
'use strict'

import SettingsOfficeModal from "./addEdit.vue"

export default {
    name: "SettingsAcl",

    components: {
        SettingsOfficeModal,
    },

    props: {
        settingsProps: Object
    },

    data() {
        return {
            columns: [
                'actions',
                'site',
                'complete_address',
            ],
            data:  [],
            office: {
                site: '',
                complete_address: '',
            },
            options: {
                headings: {
                    'site': 'Office',
                    'complete_address': 'Complete Address',
                },
                sortable: [
                    'site',
                    'complete_address',
                ],
                filterable: true,
                pagination: { chunk:10 }
            },
            openModal: false,
            openAction: "",
        }
    },

    created(){
        this.$bus.$on('officeSettings', this.getList);
        if (this.$route.hash == '#office') {
            this.getList();
        }
        this.$bus.$on('init_modal', (param) => {
            this.openModal = param;
            this.office = {}
            this.getList();
        });
    },

    methods: {
        getList: function() {
            this.isLoading = true
            this.$constants.Settings_ACL_API.get("/office/location")
            .then(response => {
                this.data = response.data;
                this.isLoading = false
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        eventModalClose: function() {
            this.office = {};
        },

        addOffice() {
            this.openModal = true;
            this.openAction = "add";
            this.headerName = "Add Office"
        },

        addEditRow: function (props_row) {
            if(props_row){
                this.headerName = "Edit Office";
                this.openAction = "add";
                this.openModal = true;
                this.office.id = props_row.id;
                this.office.site = props_row.site;
                this.office.complete_address = props_row.complete_address;
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
                        this.$constants.Settings_ACL_API.delete("/office/location/" + props_row.id)
                            .then(response => {
                                this.getList()
                                this.$success(props_row.site + ' has been deleted!')
                            });
                    } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                        this.$success(props_row.site + ' has been cancelled!')
                    }
                })
        },
    },
}
</script>
