<template>
    <div>
        <table class="table table-borderless">
            <tbody>
            <tr>
                <td colspan="2">
                    Creator : <strong>{{ compliance_view.creator }}</strong>
                </td>
            </tr>
            <tr>
                <td class="col-lg-6">
                    On : <strong>{{ compliance_view.created_at | formatDateCompleteMonth }}</strong>
                </td>
                <td class="col-lg-6" v-if="compliance_view.effective_date">
                    Effective : <strong>{{ compliance_view.effective_date | formatDateCompleteMonth }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td align="justify" colspan="2">
                    <div v-html="compliance_view.content" />
                </td>
            </tr>
            <tr v-if="compliance_view.attachments">
                <td v-if="compliance_view.attachments">
                    <div v-for="attach1 in compliance_view.attachments">
                        <strong v-if="attach1.attachments != 'null'">Download Attachment:</strong>
                        <ul v-for="attach2 in attach1.attachments.split(',')">
                            <li v-if="attach2.match(/announcement/g)">
                                <a :href="'/api/admin/incidentreport/download/attachment/'+ attach2 | urlReplace">
                                    {{ attach2 | urlReplace }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="table table-striped">
            <thead>
                <tr v-if="compliance_view.compliance">
                    <th style="background-color: lightgrey;" scope="col">Distro Name</th>
                    <th style="background-color: lightgrey;" scope="col">Employee Name</th>
                    <th style="background-color: lightgrey;" scope="col">Email Address</th>
                    <th style="background-color: lightgrey;" scope="col">Acknowledge At</th>
                </tr>
            </thead>
            <tbody>
                <tr scope="row" v-if="compliance_view.compliance" v-for="comply in compliance_view.compliance">
                    <td v-if="comply.distro">{{ comply.distro.distro_name }}</td><td v-else></td>
                    <td>{{ comply.recipient.first_name }} {{ comply.recipient.last_name }}</td>
                    <td>{{ comply.recipient.email_address }}</td>
                    <td>{{ comply.acknowledged_at }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    export default {
        props: {
            complianceProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                compliance_view: {
                    subject: '',
                    content: '',
                    compliance: [],
                    attachments: [],
                    created_at: '',
                    effective_date: '',
                    creator: '',
                },
            };
        },
        created(){
            this.compliance_view.subject = this.complianceProps.subject;
            this.compliance_view.content = this.complianceProps.content;
            this.compliance_view.compliance = this.complianceProps.compliance;
            this.compliance_view.attachments = this.complianceProps.attachments;
            this.compliance_view.created_at = this.complianceProps.created_at;
            this.compliance_view.effective_date = this.complianceProps.effective_date;
            this.compliance_view.creator = this.complianceProps.creator;
        },
    }
</script>
