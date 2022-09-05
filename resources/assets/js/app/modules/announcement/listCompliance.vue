<template>
    <div>
        <loading-modal v-if="isLoading" />
        <the-ibox title="">

            <input-search  @search="eventSearch(searchKey)" >
            <input type="text"
                    class="form-control search-custom-input"
                    placeholder="Type Subject here..."
                    v-model="searchKey"
                    @change='eventSearch(searchKey)'
                >
            </input-search>

        <v-client-table :data="rows" :columns="columns" :options="options">
                <template slot="actions" slot-scope="props">
                    <div>
                        <button class='btn btn-warning btn-xs' @click="viewCompliance(props.row)"
                        v-if="rule.announcement.child_rules.compliance.child_rules.view.is_enable">
                            <i class="fa fa-eye"/> {{ labels.viewBtn }}
                        </button>
                        <button class='btn btn-success btn-xs'
                        @click="downloadCompliance(props.row.compliance, props.row.subject, props.row.created_at)"
                        v-if="rule.announcement.child_rules.compliance.child_rules.download.is_enable">
                            <i class="fa fa-download"/> {{ labels.downloadBtn }}
                        </button>
                    </div>
                </template>
                <template slot="creator" slot-scope="props">
                    <span v-if="props.row.sender" >
                        {{ props.row.sender.first_name }}
                        {{ props.row.sender.last_name }}
                    </span>
                </template>
                <template slot="date_created" slot-scope="props">
                    <span v-if="props.row.created_at" >
                        {{ props.row.created_at | formatDateCompleteMonth }}
                    </span>
                </template>
                <template slot="effective_date" slot-scope="props">
                    <span v-if="props.row.effective_date" >
                        {{ props.row.effective_date | formatDateCompleteMonth }}
                    </span>
                </template>
        </v-client-table>

            <v-paginator style="margin-top: -50px; position: absolute;" :options="resource_options"
            :resource_url="resource_url" @update="updateResource" v-show="showPagination" />

            <modal v-if="openModal" @close="openModal = false; eventModalClose()">
                <h3 slot="header">{{ headerName }}</h3>
                <view-compliance :complianceProps="compliance_data" slot="body" />
            </modal>
        </the-ibox>
    </div>
</template>
<script>
    import viewCompliance from '@/modules/announcement/viewCompliance.vue'

    export default {
        components: {
            viewCompliance
        },
        props:
            {
                labels: {
                    type: Object,
                    default() {
                        return {
                            view: 'View Compliance',
                            viewBtn: 'View',
                            downloadBtn: 'Download'
                        }
                    }
                },
            },
        data: function () {
            return {
                rule: this.$ep.rule,
                isLoading: false,
                columns: [
                    'actions',
                    'subject',
                    'creator',
                    'date_created',
                    'effective_date',
                    'acknowledge_percentage'
                ],
                rows:  [],
                options: {
                    headings: {
                        'acknowledge_percentage': 'Compliance Rate'
                    },
                    sortable: [],
                    filterable: []
                },
                headerName: '',
                openModal: false,
                compliance_data: {
                    subject: '',
                    content: '',
                    created_at: '',
                    compliance_rate: '',
                    compliance: [],
                    attachments: [],
                    effective_date: '',
                    creator: '',
                },
                json_fields: [],
                resource_url: '/api/announcement/compliance',
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
                    this.$constants.Admin_API.get("/search/compliance/"+searchKey)
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
                this.compliance_data = {};
            },
            viewCompliance: function (props_row) {
                this.openModal = true;
                if (props_row && props_row.compliance && props_row.sender) {
                    this.headerName = this.labels.view;
                    this.headerName = props_row.subject;
                    this.compliance_data.content = props_row.content;
                    this.compliance_data.compliance = props_row.compliance
                    this.compliance_data.attachments = props_row.compliance[0].announce_attached
                    this.compliance_data.created_at = props_row.created_at
                    this.compliance_data.effective_date = props_row.effective_date
                    this.compliance_data.creator = props_row.sender.first_name
                    + " " + props_row.sender.last_name
                }
            },
            downloadCompliance: function (row_compliance, subject, created_at) {
                let csvContent = "data:text/csv;charset=utf-8,";
                let compliances = [];

                for (let i = 0; i < row_compliance.length; i++) {
                    if (row_compliance[i].recipient && row_compliance[i].recipient.designation
                    && row_compliance[i].recipient.office_location){
                        compliances =
                        Object.values([
                            row_compliance[i].recipient.employee_no,
                            row_compliance[i].recipient.first_name + ' ' +
                            row_compliance[i].recipient.last_name,
                            row_compliance[i].recipient.designation.name,
                            row_compliance[i].recipient.office_location.name,
                            row_compliance[i].recipient.email_address,
                            row_compliance[i].acknowledged_at
                        ])

                        this.json_fields.push(compliances);
                    }
                }

                csvContent += [
                    Object.values(["Employee Number", "Employee Name", "Designation", "Site",
                    "Email Address", "Acknowledged At"]),
                    ...this.json_fields.map(item => Object.values(item))
                ]
                .join("\n")
                .replace(/(^\[)|(\]$)/gm, "");

                const data = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", data);
                link.setAttribute("download", "compliance_" + subject + "_" + created_at+".csv");
                link.click();
            },
        }
    }
</script>
