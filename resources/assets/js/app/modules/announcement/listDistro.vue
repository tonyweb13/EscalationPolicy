<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">
            <button-add v-if="rule.announcement.child_rules.distro.child_rules.add.is_enable"
            @add="addEditDistro()" thingToAdd="Distro" />

            <input-search  @search="eventSearch(searchKey)" >
            <input type="text"
                    class="form-control search-custom-input"
                    placeholder="Type Distro Name here..."
                    v-model="searchKey"
                    @change='eventSearch(searchKey)'
                >
            </input-search>

            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                    <div style="width: 60px;">
                        <button v-if="role == 'Super Admin Access' || role == 'HR Admin Access' || role == 'HR Access'"
                        class='btn btn-primary btn-xs' @click="addEditDistro(props.row)">
                            <i class="fa fa-pencil"/> {{ labels.editBtn }}
                        </button>
                    </div>
                </template>
            </v-client-table>

            <v-paginator style="margin-top: -50px; position: absolute;" :options="resource_options"
            :resource_url="resource_url" @update="updateResource" v-show="showPagination" />

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <add-edit-distro :distroProps="distro_data" slot="body" />
            </modal>
        </the-ibox>
    </div>
</template>
<script>
    import addEditDistro from '@/modules/announcement/addEditDistro.vue'

    export default {
        components: {
            addEditDistro
        },
        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            add: 'Distro',
                            edit: 'Edit Distro',
                            editBtn: 'Edit',
                        }
                    }
                },
            },
        data: function () {
            return {
                rule: this.$ep.rule,
                role: this.$ep.role,
                isLoading: false,
                columns: [
                    'actions',
                    'distro_name',
                ],
                rows:  [],
                options: {
                    headings: {
                        'distro_name': 'Distro Name',
                    },
                    sortable: [],
                    filterable: []
                },
                headerName: '',
                openModal: false,
                distro_data: {
                    distro_id: '',
                    distro_name: '',
                    distro_categories: '',
                    user_id: [],
                },
                resource_url: '/api/announcement/distro',
                resource_options: {
                    remote_data: 'data',
                    remote_current_page: 'current_page',
                    remote_last_page: 'last_page',
                    remote_next_page_url: 'next_page_url',
                    remote_prev_page_url: 'prev_page_url',
                    next_button_text: 'Go Next',
                    previous_button_text: 'Go Back'
                },
                searchKey: '',
                showPagination: true
            }
        },
        created() {
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods:{
            updateResource(data){
                this.isLoading = true
                this.rows = data
                this.isLoading = false
            },
            eventSearch: function (searchKey) {
                if (searchKey) {
                    this.isLoading = true
                    this.showPagination = false
                    this.$constants.Admin_API.get("/search/distro/"+searchKey)
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else {
                    location.reload();
                }
            },
            eventModalClose: function () {
                this.distro_data = {};
            },
            addEditDistro: function (props_row) {
                this.openModal = true;
                if (props_row) {
                    this.headerName = this.labels.edit;
                    this.distro_data.distro_id = props_row.id
                    this.distro_data.distro_name = props_row.distro_name;
                    this.distro_data.distro_categories = props_row.distro_categories;
                } else {
                    this.headerName = this.labels.add;
                    this.distro_data.distro_name = '';
                }
            },
        }
    }
</script>
