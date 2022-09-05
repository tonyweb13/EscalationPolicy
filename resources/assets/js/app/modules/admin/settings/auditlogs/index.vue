<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">
            <input-search  @search="eventSearch(searchKey)" >
            <input type="text"
                    class="form-control search-custom-input"
                    placeholder="Type Old Values or New Values here..."
                    v-model="searchKey"
                    @change='eventSearch(searchKey)'
                >
            </input-search>
            <!-- commented: too many data to download -->
            <!-- <div class="pull-right" style="margin-top: -55px;">
                <json-excel
                    class   = "btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    name    = "audit logs.xls"
                >
                <i class="fa fa-download"></i>
                Export Audit Logs
                </json-excel>
            </div> -->
            <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="audit_user" slot-scope="props">
                    {{ props.row.audit_user.first_name }}
                    {{ props.row.audit_user.last_name }}
                </template>
                <template slot="auditable_type" slot-scope="props">
                    {{ props.row.auditable_type | stringReplace }}
                </template>
                <template slot="old_values" slot-scope="props">
                    <div style="width:350px;">
                        <small v-for="oldVal in props.row.old_values">
                            {{ oldVal | stringReplace }}
                        </small>
                    </div>
                </template>
                <template slot="new_values" slot-scope="props" >
                    <div style="width:350px;">
                        <small v-for="newVal in props.row.new_values">
                            {{ newVal | stringReplace }}
                        </small>
                    </div>
                </template>
            </v-client-table>

            <v-paginator style="margin-top: -50px; position: absolute;" :options="resource_options"
            :resource_url="resource_url" @update="updateResource" v-show="showPagination"/>

        </the-ibox>
    </div>
</template>
<script>
export default {
    data: function () {
        return {
            isLoading: false,
            columns: [
                'audit_user',
                'event',
                'auditable_id',
                'auditable_type',
                'old_values',
                'new_values',
                'created_at'
            ],
            rows:  [],
            options: {
                sortable: [
                    'user_id',
                    'event',
                    'auditable_id',
                    'auditable_type',
                    'created_at'
                ],
                filterable: false,
            },
            resource_url: '/api/admin/audit/logs',
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
            // json_fields: {
            //     'Audit User': {
            //         field: 'audit_user',
            //         callback: (value) => {
            //             if (value.first_name || value.last_name) {
            //                 return value.first_name + " " + value.last_name;
            //             } else {
            //                 return 'N/A'
            //             }
            //         }
            //     },
            //     'Event': 'event',
            //     'Auditable ID': 'auditable_id',
            //     'Auditable Type': {
            //         field: 'auditable_type',
            //         callback: (value) => {
            //               if (value) {
            //                 return String(value).replace(/app|models|admin|settings|coc|\\/gi, '');
            //             }
            //         }
            //     },
            //     'Old Values': {
            //         field: 'old_values',
            //         callback: (value) => {
            //               if (value) {
            //                 return String(value).replace(/{|}|\[\]/gi, '');
            //             }
            //         }
            //     },
            //     'New Values': {
            //         field: 'new_values',
            //         callback: (value) => {
            //               if (value) {
            //                 return String(value).replace(/{|}|\[\]/gi, '');
            //             }
            //         }
            //     },
            //     'Created': 'created_at'
            // },
            // json_data: [],
            // json_meta: [
            //     [
            //         {
            //             'key': 'charset',
            //             'value': 'utf-8'
            //         }
            //     ]
            // ],
        }
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
                    this.$constants.Admin_API.get("/search/auditlogs/"+searchKey)
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
    }
}
</script>
