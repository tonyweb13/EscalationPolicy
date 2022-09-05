<template>
    <the-form :asterisk="true" @submitForm="eventUpdate">
        <div slot="form-body">
            <input type="hidden" v-model="cluster.hrsl_email_address" >
            <input type="hidden" v-model="cluster.hrbp_email_address" >
            <the-label label="HR Site Leader Location" :asterisk="true">
                <vSelect v-model="cluster.hrsl_office_location_id" :options="selectedSite" label="text" required/>
            </the-label>
            <the-label label="Select HR Site Leader" :asterisk="true">
                <vSelect v-model="cluster.hrsl_employee_no"
                :options="selectedHRSiteLeader"
                label="text"
                @input="eventEmail(cluster.hrsl_employee_no, 'hrsl')"
                required/>
            </the-label>
            <the-label label="Select HRBP" :asterisk="true">
                <vSelect
                v-model="cluster.hrbp_employee_no"
                :options="selectedHRBP"
                label="text"
                @input="eventEmail(cluster.hrbp_employee_no, 'hrbp')"
                required/>
            </the-label>
            <the-label label="Select Employee" :asterisk="true">
                <vSelect
                v-model="cluster.user_employee_no"
                :options="selectedUser"
                label="text"
                :disabled="disabledEmployeeAgent"
                required/>
            </the-label>
            <the-label label="Select Immediate Supervisor" :asterisk="true">
                <vSelect
                v-model="cluster.supervisor_employee_no"
                :options="selectedSupervisor"
                label="text"
                required/>
            </the-label>

            <the-label label="Select Immediate Manager">
                <vSelect
                v-model="cluster.manager_employee_no"
                :options="selectedManager"
                label="text"
                />
            </the-label>

            <button-save :disabled='!isComplete' />
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            clusterProps: Object,
        },
        data: function () {
            return {
                disabledEmployeeAgent: false,
                laddabtn : '',
                cluster: {
                    hrsl_employee_no: '',
                    hrbp_employee_no: '',
                    hrsl_office_location_id: '',
                    hrsl_email_address: '',
                    hrbp_email_address: '',
                    user_employee_no: '',
                    supervisor_employee_no: '',
                    manager_employee_no: '',
                },
                selectedUser:[],
                selectedSite: [],
                selectedHRSiteLeader: [],
                selectedHRBP: [],
                selectedSupervisor: [],
                selectedManager: [],
            };
        },
        created(){

            this.$constants.Admin_API.get("/hrbp/cluster/dropdown/site/location")
                .then(response => {
                this.selectedSite = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.$constants.Admin_API.get("/hrbp/cluster/dropdown/hr/site/leader")
                .then(response => {
                this.selectedHRSiteLeader = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            this.$constants.Admin_API.get("/hrbp/cluster/dropdown/hrbp")
                .then(response => {
                this.selectedHRBP = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

           this.$constants.Admin_API.get("/hrbp/cluster/dropdown/head")
                .then(response => {
                this.selectedSupervisor = response.data;
                this.selectedManager = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });

            if(this.clusterProps.cluster_id){

                /* Edit Cluster */
                this.disabledEmployeeAgent = true;

                if (this.clusterProps.employee_name == "") {

                    this.$constants.Admin_API.get("/hrbp/cluster/dropdown/not/in/usergroup")
                        .then(response => {
                        this.selectedUser = response.data;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }

                if (this.clusterProps.hrsl_office_location_id) {
                    this.cluster.hrsl_office_location_id = {
                        text: this.clusterProps.hrsl_office_location_name,
                        value: this.clusterProps.hrsl_office_location_id,
                        selected: "selected",
                    }
                }

                if (this.clusterProps.hrsl_employee_no) {
                    this.cluster.hrsl_employee_no = {
                        text: this.clusterProps.hrsl_name,
                        value: this.clusterProps.hrsl_employee_no,
                        selected: "selected",
                    }

                    this.cluster.hrsl_email_address = this.clusterProps.hrsl_email_address;
                }

                if (this.clusterProps.hrbp_employee_no) {
                    this.cluster.hrbp_employee_no = {
                        text: this.clusterProps.hrbp_name,
                        value: this.clusterProps.hrbp_employee_no,
                        selected: "selected",
                    }

                    this.cluster.hrbp_email_address = this.clusterProps.hrbp_email_address;
                }

                if (this.clusterProps.user_employee_no) {
                    this.cluster.user_employee_no = {
                        text: this.clusterProps.employee_name,
                        value: this.clusterProps.user_employee_no,
                        selected: "selected",
                    }
                }

                if (this.clusterProps.supervisor_employee_no) {
                    this.cluster.supervisor_employee_no = {
                        text: this.clusterProps.supervisor_name,
                        value: this.clusterProps.supervisor_employee_no,
                        selected: "selected",
                    }
                }

                if (this.clusterProps.manager_employee_no) {
                    this.cluster.manager_employee_no = {
                        text: this.clusterProps.manager_name,
                        value: this.clusterProps.manager_employee_no,
                        selected: "selected",
                    }
                }

            } else {

                /* Add Cluster */
                this.$constants.Admin_API.get("/hrbp/cluster/dropdown/not/in/cluster")
                    .then(response => {
                    this.selectedUser = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });

                this.disabledEmployeeAgent = false;
                this.cluster.hrsl_office_location_id = '';
                this.cluster.hrsl_employee_no = '';
                this.cluster.hrbp_employee_no = '';
                this.cluster.user_employee_no = '';
                this.cluster.supervisor_employee_no = '';
                this.cluster.manager_employee_no = '';
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.cluster.hrsl_office_location_id && this.cluster.hrsl_employee_no
                && this.cluster.hrbp_employee_no && this.cluster.user_employee_no
                && this.cluster.supervisor_employee_no;
            }
        },
        methods: {
            eventEmail(hr_emp_no, hr_position){

                this.$constants.Admin_API.get("/hrbp/cluster/get/email/" + hr_emp_no.value)
                 .then(response => {

                    if(hr_position == 'hrsl') {
                        this.cluster.hrsl_email_address = response.data.email_address;
                    }

                    if(hr_position == 'hrbp') {
                        this.cluster.hrbp_email_address = response.data.email_address;
                    }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventUpdate() {

                this.laddabtn.start();
                let cluster = this.cluster;

                let hrbp = '';
                let hrbp_email = '';

                if (cluster.hrbp_employee_no.value == '' || cluster.hrbp_employee_no.value == undefined) {

                    /* replace HRSL assign if HRBP empty */
                    hrbp = cluster.hrsl_employee_no.value;
                    hrbp_email = cluster.hrsl_email_address;

                } else {

                    hrbp = cluster.hrbp_employee_no.value;
                    hrbp_email = cluster.hrbp_email_address;
                }

                if (cluster.manager_employee_no == null || cluster.manager_employee_no == undefined
                || cluster.manager_employee_no == '') {
                    cluster.manager_employee_no = {
                        text: '',
                        value: '',
                    }
                }

                let getAxios = '';
                if (this.clusterProps.cluster_id) {

                    getAxios = this.$constants.Admin_API.get("/hrbp/cluster/"+this.clusterProps.cluster_id, {
                        params: {
                            hrsl_employee_no: cluster.hrsl_employee_no.value,
                            hrsl_email_address: cluster.hrsl_email_address,
                            hrbp_employee_no: hrbp,
                            hrbp_email_address: hrbp_email,
                            hrsl_office_location_id: cluster.hrsl_office_location_id.value,
                            user_employee_no: cluster.user_employee_no.value,
                            supervisor_employee_no: cluster.supervisor_employee_no.value,
                            manager_employee_no: cluster.manager_employee_no.value
                        }
                    });

                } else {

                    getAxios = this.$constants.Admin_API.get("/hrbp/cluster/create", {
                        params: {
                            hrsl_employee_no: cluster.hrsl_employee_no.value,
                            hrsl_email_address: cluster.hrsl_email_address,
                            hrbp_employee_no: hrbp,
                            hrbp_email_address: hrbp_email,
                            hrsl_office_location_id: cluster.hrsl_office_location_id.value,
                            user_employee_no: cluster.user_employee_no.value,
                            supervisor_employee_no: cluster.supervisor_employee_no.value,
                            manager_employee_no: cluster.manager_employee_no.value
                        }
                    });
                }

                getAxios.then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.cluster = response.data;
                    this.$success('Successfully Saved!')
                    this.$bus.$emit('updateList');
                    this.$bus.$emit('updateDashboard');
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
