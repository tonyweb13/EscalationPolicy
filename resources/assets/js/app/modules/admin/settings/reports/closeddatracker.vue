<template>
    <div>
        <loading-modal-report v-if="isLoading" />
        <json-excel
            class   = "btn btn-danger"
            :fetch  = "fetchClosedDA"
            :fields = "json_fields"
            name    = "Closed DA Tracker Report.xls"
        >
        Export Closed DA Tracker Report
        </json-excel>
        <br><br>
        <the-ibox title="Export Report Closed DA Filter" style="height:370px; background:white;">
            <!-- Start Export Filter -->
            <div class="col-lg-6">
                <the-label label="Select Category" :asterisk="true">
                    <vSelect v-model="exportCategory" :options="selectedCategory"
                    @input="eventCategory" label="text" required/>
                </the-label>

                <the-label v-if="exportCategory.value && exportCategory.value == 'date_range'"
                label="Select Date Range" :asterisk="true">
                    <br>
                    <small>Date NTE Draft Start</small>
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'dd-MMM-yy'"
                        :calendar-button="true"
                        :calendar-button-icon="'fa fa-calendar'"
                        placeholder="day-month-year"
                        v-model="dateRangeStart"
                    />

                    <br>
                    <small>Date NTE Draft End</small>
                    <datepicker
                        :input-class="'custom-datepicker'"
                        :format="'dd-MMM-yy'"
                        :calendar-button="true"
                        :calendar-button-icon="'fa fa-calendar'"
                        placeholder="day-month-year"
                        v-model="dateRangeEnd"
                        @input="dateRangeValidation(dateRangeStart, dateRangeEnd)"
                    />
                </the-label>

                <the-label v-else-if="exportCategory.value && exportCategory.value != 'date_range'"
                label="Select Sub-category" :asterisk="true">
                    <vSelect v-model="exportSubcategory" :options="selectedSubcategory"
                    @input="eventFilter" label="text" required/>
                </the-label>

                <the-label v-show="requiredYear" label="Select Year" :asterisk="true">
                    <vSelect v-model="exportYear" :options="selectedYear"
                    @input="eventFilterWithYear" label="text" required/>
                </the-label>

                <!-- <json-excel
                    class   = "btn btn-primary"
                    :data   = "json_filter"
                    :fields = "json_fields"
                    :disabled="!isComplete"
                     name    = "Closed DA Tracker Report Filter.xls"
                > Export Filter </json-excel> -->
                <button @click="eventExportButton" v-show="buttonShow" style="border: 0;">
                <json-excel
                    class   = "btn btn-primary"
                    :data   = "json_filter"
                    :fields = "json_fields"
                    :disabled="!isComplete"
                     name    = "Closed DA Tracker Report Filter.xls"
                > Export Filter </json-excel>
                </button>
            </div>
            <!-- End Export Filter -->
        </the-ibox>
    </div>
</template>
<script>

export default {

    data: function () {
        return {
            isLoading: false,
            buttonShow: false,
            exportCategory: '',
            exportSubcategory: '',
            exportYear: '',
            dateRangeStart: '',
            dateRangeEnd: '',
            selectedCategory:[
                {value: 'site', text: 'Site'},
                {value: 'hrbp', text: 'Hrbp'},
                {value: 'immediate_supervisor', text: 'Supervisor'},
                {value: 'dm', text: 'Manager'},
                {value: 'coc_provision', text: 'Provision'},
                {value: 'description_of_the_offense', text: 'Offense'},
                {value: 'month_nte_draft', text: 'Month NTE Draft'},
                {value: 'quarter', text: 'Quarter'},
                {value: 'date_range', text: 'Date Range'},
            ],
            selectedSubcategory: [],
            selectedYear: [],
            json_fields: {
                'IR Number' : {
                    field: 'ir_number',
                    callback: (value) => {
                        return value ? `#${value}` : 'No IR Number';
                    }
                },
                'Employee Number' : 'employee_id',
                'Last Name' : 'last_name',
                'First Name' : 'first_name',
                'Position' : 'position',
                'Site' : 'site',
                'HRBP' : 'hrbp',
                'DM' : 'dm',
                'Immediate Supervisor' : 'immediate_supervisor',
                'Date of Offense' : 'date_of_offense',
                'NTE Request' : 'nte_request',
                'Date NTE Draft' : 'date_nte_draft',
                'COC Provision' : 'coc_provision',
                'Description of the Offense' : 'description_of_the_offense',
                'Gravity' : 'gravity',
                'NTE Date Served' : 'nte_date_served',
                'Date HR Received' : 'date_hr_received',
                'Date Admin Hearing' : 'date_admin_hearing',
                'DA Imposed' : 'da_imposed',
                'Date DA' : 'date_da',
                'Stage of the Case' : 'stage_of_the_case',
                'Notes for Case' : 'notes_for_case',
                'Case Status' : 'case_status',
                'Ageing' : 'ageing',
                'Days Exceeded TAT' : 'days_exceeded_tat',
                'TAT Status' : 'tat_status',
                'Year NTE Draft' : 'year_nte_draft',
                'Month NTE Draft' : 'month_nte_draft',
                'Week' : 'week',
                'Quarter' : 'quarter',
                'Rank' : 'rank',
            },
            json_data: [],
            json_filter: [],
            json_meta: [
                [
                    {
                        'key': 'charset',
                        'value': 'utf-8'
                    }
                ]
            ],
            requiredYear: false,
        }
    },
    computed: {
        isComplete () {
            if (this.exportCategory.value == 'date_range') {
                return this.exportCategory && this.dateRangeStart && this.dateRangeEnd;
            } else {
                return this.exportCategory.value && this.exportSubcategory.value;
            }
        }
    },
    methods: {
        async fetchClosedDA(){
            this.isLoading = true
            const response = await this.$constants.Admin_API.get("/closed/export/Da");
            console.log("json_fields closed da response==", response);
            this.isLoading = false
            return response.data;
        },
        eventCategory: function () {
            if (this.exportCategory.value) {
                this.exportSubcategory = ''
                this.selectedSubcategory = [];
                this.requiredYear = false;

                if (this.exportCategory.value == 'site') {
                    this.selectedSubcategory = this.$constants.Site_List;

                } else if (this.exportCategory.value == 'hrbp') {

                    this.$constants.Admin_API.get("/hrbp/cluster/dropdown/hrbpByName")
                        .then(response => {
                        this.isLoading = true
                        this.selectedSubcategory = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else if (this.exportCategory.value == 'immediate_supervisor') {

                    this.$constants.Admin_API.get("/closed/dropdown/supervisor")
                        .then(response => {
                        this.isLoading = true
                        this.selectedSubcategory = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else if (this.exportCategory.value == 'dm') {

                    this.$constants.Admin_API.get("/closed/dropdown/manager")
                        .then(response => {
                        this.isLoading = true
                        this.selectedSubcategory = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else if (this.exportCategory.value == 'coc_provision') {

                    this.$constants.Admin_API.get("/closed/dropdown/provision")
                        .then(response => {
                        this.isLoading = true
                        this.selectedSubcategory = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else if (this.exportCategory.value == 'description_of_the_offense') {

                    this.$constants.Admin_API.get("/closed/dropdown/offense")
                        .then(response => {
                        this.isLoading = true
                        this.selectedSubcategory = response.data;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });

                } else if (this.exportCategory.value == 'month_nte_draft') {
                    this.requiredYear = true;
                    this.selectedSubcategory = this.$constants.Month_List_Text;

                } else if (this.exportCategory.value == 'quarter') {
                    this.requiredYear = true;
                    this.selectedSubcategory = this.$constants.Quarter_List;
                }
            }
        },
        eventFilter: function () {
            this.buttonShow = false;
            if (this.exportCategory.value == 'month_nte_draft' || this.exportCategory.value == 'quarter') {

                this.selectedYear = this.$constants.Year_List;

            } else if (this.exportCategory.value && this.exportSubcategory.value) {

                this.$swal({
                    title: 'Are you sure you want to export data from '+ this.exportSubcategory.value +'?',
                    type: "warning",
                    confirmButtonClass: 'btn btn-success',
                }).then((result) => {
                    if (result.value) {
                        this.$constants.Admin_API.get("/closed/export/" + this.exportCategory.value
                        + "/" + this.exportSubcategory.value)
                        .then(response => {
                            this.isLoading = true
                            this.json_filter =  response.data;
                            this.buttonShow = true;
                            this.isLoading = false
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                    }
                })
            }
        },
        eventFilterWithYear: function () {
            if (this.exportCategory.value && this.exportSubcategory.value && this.exportYear.value) {
            this.buttonShow = false;
                this.$constants.Admin_API.get("/closed/export/"+this.exportCategory.value+"/"
                +this.exportSubcategory.value+"/"+this.exportYear.value)
                .then(response => {
                    this.isLoading = true
                    this.json_filter =  response.data;
                    this.buttonShow = true;
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        },
        updateIRNumber: function () {
            this.$constants.Admin_API.get("/closed/update/irnumber")
            .then(response => {
                this.$success("IR Number Updated!");
                response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        dateRangeValidation(start, end){
            this.buttonShow = false;
            if (end < start) {
                this.$swal({
                    text: "Date End not less than Date Start",
                    type: "error",
                    confirmButtonText: 'OK',
                }).then((result) => {
                    this.dateRangeStart = '';
                    this.dateRangeEnd = '';
                });

            } else {
                if (this.exportCategory.value == 'date_range' && start && end) {
                    this.$constants.Admin_API.get("/closed/date/range", {
                        params: {
                            start_range: start,
                            end_range: end,
                        }
                    }).then(response => {
                        this.isLoading = true
                        this.json_filter =  response.data;
                        this.buttonShow = true;
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            }
        },
        eventExportButton: function () {
            if (this.json_filter.length == 0) {
                this.$swal({
                    title: 'Sorry, No Data Found',
                    type: "warning",
                    confirmButtonClass: 'btn btn-success',
                })
            }
            this.buttonShow = false;
        },
    }
}
</script>

