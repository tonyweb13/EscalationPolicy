<template>
    <div>
        <div >
            <table class="table borderless">
                <tbody>
                    <tr >
                        <td width="50%">
                            <strong>Employee Name :</strong>
                            <span>
                                {{close_view_data.first_name}} {{close_view_data.last_name}}
                            </span>
                        </td>
                        <td width="50%" >
                            <strong>Employee ID :</strong>
                            <span>{{close_view_data.employee_id}}</span>
                        </td>
                    </tr>
                    <tr >
                        <td width="50%">
                            <strong>Position :</strong>
                            <span>
                                {{close_view_data.position}}
                            </span>
                        </td>
                        <td width="50%" >
                            <strong>Site :</strong>
                            <span>{{close_view_data.site}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <strong>HRBP Name : </strong>
                            <span>{{close_view_data.hrbp}}</span>
                        </td>
                        <td width="50%">
                            <strong>Deputy Manager : </strong>
                            <span>{{close_view_data.dm}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <strong>Immediate Supervisor : </strong>
                            <span>{{close_view_data.immediate_supervisor}}</span>
                        </td>
                        <td width="50%">
                            <strong>Date of offense : </strong>
                            <span>{{close_view_data.date_of_offense}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" >
                            <strong>NTE Request : </strong>
                            <span>{{close_view_data.nte_request}}</span>
                        </td>
                        <td width="50%">
                            <strong>Date NTE Draft : </strong>
                            <span>{{close_view_data.date_nte_draft}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" >
                            <strong>Coc Provision : </strong>
                            <span>{{close_view_data.coc_provision}}</span>
                        </td>
                        <td width="50%">
                            <strong>Gravity :</strong>
                            <span>{{close_view_data.gravity}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" width="100%" >
                            <strong>Description Of Offense :</strong>
                            <span>{{close_view_data.description_of_the_offense}}</span>
                            <strong style="color:red;" v-if="close_view_data.offense_exist">
                                {{ close_view_data.offense_exist }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <strong>NTE Served To Employee : </strong>
                            <span>{{close_view_data.nte_date_served}}</span>
                        </td>
                        <td width="50%" >
                            <strong>Date HR Received : </strong>
                            <span>{{close_view_data.date_hr_received}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <strong>Date of Admin Hearing : </strong>
                            <span>{{close_view_data.date_admin_hearing}}</span>
                        </td>
                        <td width="50%" v-if="role != 'Regular User Access'
                        && designation.toLowerCase().indexOf('supervisor') < 0
                        && designation.toLowerCase().indexOf('team') < 0">
                            <strong>DA imposed : </strong>
                            <span>{{close_view_data.da_imposed}}</span>
                            <strong style="color:red;" v-if="close_view_data.irr_exist">
                                {{ close_view_data.irr_exist }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" >
                            <strong>Stage of Case : </strong>
                            <span>{{close_view_data.stage_of_the_case}}</span>
                        </td>
                        <td width="50%">
                            <strong>Notes For The Case : </strong>
                            <span>
                                {{ stripScripts( close_view_data.notes_for_case ) }}
                            </span>
                        </td>
                    </tr>
                    <tr >
                        <td width="50%" >
                            <strong>Closed Stage :</strong>
                            <span>{{close_view_data.case_status}}</span>
                        </td>
                        <td width="50%" >
                            <strong>Ageing :</strong>
                            <span>{{close_view_data.ageing}}</span>
                        </td>
                    </tr>
                    <tr >
                        <td width="50%" >
                            <strong>Days Exceeded TAT :</strong>
                            <span>{{close_view_data.days_exceeded_tat}}</span>
                        </td>
                        <td width="50%" >
                            <strong>TAT Status :</strong>
                            <span>{{close_view_data.tat_status}}</span>
                        </td>
                    </tr>
                    <tr >
                        <td width="50%" >
                            <strong>Year NTE Draft :</strong>
                            <span>{{close_view_data.year_nte_draft}}</span>
                        </td>
                        <td width="50%" >
                            <strong>Month NTE Draft :</strong>
                            <span>{{close_view_data.month_nte_draft}}</span>
                        </td>
                    </tr>
                    <tr >
                        <td width="50%" >
                            <strong>Week :</strong>
                            <span>{{close_view_data.week}}</span>
                        </td>
                        <td width="50%" >
                            <strong>Quarter :</strong>
                            <span>{{close_view_data.quarter}}</span>
                        </td>
                    </tr>
                    <tr >
                        <td width="50%" >
                            <strong>Position :</strong>
                            <span>{{close_view_data.position}}</span>
                        </td>
                        <td width="50%" >
                            <strong>Rank :</strong>
                            <span>{{close_view_data.rank}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        closedProps: Object,
    },
    data: function () {
        return {
            role: this.$ep.role,
            designation: this.$ep.user.designation.name,
            close_view_data: {
                ageing: '',
                case_status: '',
                coc_provision: '',
                da_imposed: '',
                date_admin_hearing: '',
                date_da: '',
                date_hr_received: '',
                date_nte_draft: '',
                date_of_offense: '',
                days_exceeded_tat: '',
                description_of_the_offense: '',
                dm: '',
                employee_id: '',
                first_name: '',
                gravity: '',
                hrbp: '',
                id: '',
                immediate_supervisor: '',
                last_name: '',
                month_nte_draft: '',
                notes_for_case: '',
                nte_date_served: '',
                nte_request: '',
                position: '',
                quarter: '',
                rank: '',
                site: '',
                stage_of_the_case: '',
                tat_status: '',
                week: '',
                year_nte_draft: '',
            },
        };
    },
    created(){
        if(this.closedProps.id){
            this.close_view_data.ageing = this.closedProps.ageing;
            this.close_view_data.case_status = this.closedProps.case_status;
            this.close_view_data.coc_provision = this.closedProps.coc_provision;
            this.close_view_data.da_imposed = this.closedProps.da_imposed;
            this.close_view_data.irr_exist = this.closedProps.irr_exist;
            this.close_view_data.offense_exist = this.closedProps.offense_exist;
            this.close_view_data.date_admin_hearing = this.closedProps.date_admin_hearing;
            this.close_view_data.date_da = this.closedProps.date_da;
            this.close_view_data.date_hr_received = this.closedProps.date_hr_received;
            this.close_view_data.date_nte_draft = this.closedProps.date_nte_draft;
            this.close_view_data.date_of_offense = this.closedProps.date_of_offense;
            this.close_view_data.days_exceeded_tat = this.closedProps.days_exceeded_tat;
            this.close_view_data.description_of_the_offense = this.closedProps.description_of_the_offense;
            this.close_view_data.dm = this.closedProps.dm;
            this.close_view_data.employee_id = this.closedProps.employee_id;
            this.close_view_data.first_name = this.closedProps.first_name;
            this.close_view_data.gravity = this.closedProps.gravity;
            this.close_view_data.hrbp = this.closedProps.hrbp;
            this.close_view_data.id = this.closedProps.id;
            this.close_view_data.immediate_supervisor = this.closedProps.immediate_supervisor;
            this.close_view_data.last_name = this.closedProps.last_name;
            this.close_view_data.month_nte_draft = this.closedProps.month_nte_draft;
            this.close_view_data.notes_for_case = this.closedProps.notes_for_case;
            this.close_view_data.nte_date_served = this.closedProps.nte_date_served;
            this.close_view_data.nte_request = this.closedProps.nte_request;
            this.close_view_data.position = this.closedProps.position;
            this.close_view_data.quarter = this.closedProps.quarter;
            this.close_view_data.rank = this.closedProps.rank;
            this.close_view_data.site = this.closedProps.site;
            this.close_view_data.stage_of_the_case = this.closedProps.stage_of_the_case;
            this.close_view_data.tat_status = this.closedProps.tat_status;
            this.close_view_data.week = this.closedProps.week;
            this.close_view_data.year_nte_draft = this.closedProps.year_nte_draft;
        }
    },
    methods: {
        stripScripts: function (html) {
            let temporalDivElement = document.createElement("div");
            // Set the HTML content with the providen
            temporalDivElement.innerHTML = html;
            // Retrieve the text property of the element (cross-browser support)
            return temporalDivElement.textContent || temporalDivElement.innerText || "";
        }
    },
}
</script>
