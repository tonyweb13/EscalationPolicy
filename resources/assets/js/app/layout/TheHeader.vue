
<template>
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation">
            <div class="navbar-header">
                <img src="/img/arb.jpg" style="width: 100px; height: 40px; margin: 5px;">
                <img src="/img/fort-tech-logo.png" style="width: 120px; height: 40px; margin: 5px;">
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <img src="/img/favicon-32x32-ep.png">
                    <span class="m-r-sm text-muted welcome-message">Welcome to EP Version {{ version }}</span>
                </li>
                <li class="dropdown" v-if="countRespondent > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i>
                        <span class="label label-warning">
                            {{ countRespondent }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <!-- <template v-for="res in respondent">
                            <li>
                                <div class="dropdown-messages-box">
                                    <router-link to="/employee/incidentreport" class="pull-left">
                                        <img v-if="res.hrbp_user.profile_pic"
                                            alt="image"
                                            class="img-circle"
                                            :src="res.hrbp_user.profile_pic"
                                        >
                                        <img v-else
                                            alt="image"
                                            class="img-circle"
                                            src="/img/profile_small.jpg"
                                        >
                                    </router-link>
                                        <div class="media-body">
                                            You've Received a new message
                                            <br> from
                                            <strong>
                                                {{ res.hrbp_user.first_name }}
                                                {{ res.hrbp_user.last_name }}
                                            </strong><br>
                                            <small class="text-muted"><b>IR: #{{ res.ir_number }}</b></small> -
                                            <small class="text-muted">{{ res.created_at }}</small>
                                        </div>
                                </div>
                            </li>
                            <li class="divider"></li>
                        </template> -->
                        <li>
                            <div class="text-center link-block">
                                <router-link to="/incidentreport#in-progress">
                                    <i class="fa fa-envelope" style="margin-right:3px;"/>
                                    <strong>Read Incident Report In Progress</strong>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" v-if="countOnHold > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i>
                        <span class="label label-warning">
                            {{ countOnHold }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <div class="text-center link-block">
                                <router-link to="/incidentreport#on-hold">
                                    <i class="fa fa-envelope" style="margin-right:3px;"/>
                                    <strong>Read Incident Report On Hold</strong>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- <li class="dropdown" v-if="countNotesRegularUser > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-wechat"></i>
                        <span class="label label-warning">
                            {{ countNotesRegularUser }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <div class="text-center link-block">
                                <router-link to="/employee/incidentreport">
                                    <i class="fa fa-wechat" style="margin-right:3px;"/><strong>View HR Notes</strong>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </li> -->
                <li class="dropdown" v-if="countNotes > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-paperclip"></i>
                        <span class="label label-warning">
                            {{ countNotes }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages" >
                        <template v-for="notes_rep in notesReplies">
                        <li>
                            <div class="text-center link-block" style="margin-right: -50px; margin-left: -50px;">
                                <router-link v-if="notes_rep.status_id == 1" to="/incidentreport#new" exact>
                                    <i class="fa fa-paperclip" style="margin-right:3px;"/>
                                        <span>Noted by {{ notes_rep.complainant.notify_notes.noted_by.replace("_bp", "") | capitalize }}</span>
                                        <strong> IR:#{{ notes_rep.ir_number }} </strong>
                                        <i style="font-size:10px;"> (New) </i>
                                </router-link>
                                <router-link v-if="notes_rep.status_id == 2" to="/incidentreport#in-progress" exact>
                                    <i class="fa fa-paperclip" style="margin-right:3px;"/>
                                        <span>Noted by {{ notes_rep.complainant.notify_notes.noted_by.replace("_bp", "") | capitalize }}</span>
                                        <strong> IR:#{{ notes_rep.ir_number }} </strong>
                                        <i style="font-size:10px;"> (In Progress) </i>
                                </router-link>
                                <router-link v-if="notes_rep.status_id == 3" to="/incidentreport#on-hold" exact>
                                    <i class="fa fa-paperclip" style="margin-right:3px;"/>
                                        <span>Noted by {{ notes_rep.complainant.notify_notes.noted_by.replace("_bp", "")| capitalize }}</span>
                                        <strong> IR:#{{ notes_rep.ir_number }} </strong>
                                        <i style="font-size:10px;"> (On Hold) </i>
                                </router-link>
                            </div>
                        </li>
                        <li class="divider"></li>
                        </template>
                    </ul>
                </li>
                <li class="dropdown" v-if="countStatusNew > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-file-o"></i>
                        <span class="label label-warning">
                            {{ countStatusNew }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <div class="text-center link-block">
                                <router-link to="/incidentreport#new"  exact>
                                    <i class="fa fa-file-o" style="margin-right:3px;"/>
                                    <strong>
                                        View All New Incident Report
                                    </strong>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" v-if="countOnHoldRequest > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-file-text-o"></i>
                        <span class="label label-warning">
                            {{ countOnHoldRequest }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <div class="text-center link-block">
                                <router-link to="/incidentreport#on-hold" exact>
                                    <i class="fa fa-file-text-o" style="margin-right:3px;"/>
                                    <strong>
                                        View All On Hold Request
                                    </strong>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- <li class="dropdown" v-if="countNotesHr > '0'">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-wechat"></i>
                        <span class="label label-warning">
                            {{ countNotesHr }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <div class="text-center link-block">
                                <router-link to="/incidentreport">
                                    <i class="fa fa-wechat" style="margin-right:3px;"/><strong>View Notes</strong>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </li> -->
                <li>
                    <a href="/logout">
                        <i class="fa fa-sign-out"/> Log out
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
"use strict";

import {mapGetters} from 'vuex'

export default {
    name: 'TheFooter',

    data: function () {
            return {
            user: this.$ep.user,
            respondent: {},
            countRespondent: 0,
            countOnHold: 0,
            countOnHoldRequest: 0,
            countStatusNew: 0,
            countNotesRegularUser: 0,
            version: this.$ep.ep_version,
            status_url: ''
        }
    },
    mounted() {
        this.getNotes()
        window.Echo.channel("newNotes").listen("NotesCreated", e => {
            this.getNotes()
        });
    },
    computed: {
        ...mapGetters([
            'countNotes',
            'notesReplies',
        ])
    },
    created(){
        this.$bus.$on('updateNotes', () => {
            this.getNotes()
        });

        if (this.user.designation.name == 'HR Site Lead'
        || this.user.designation.name == 'HR Manager') {
            this.getCountOnHoldRequest();
        }
        if (this.user.designation.name == 'HR Site Lead'
        || this.user.designation.name == 'HR Business Partner'
        || this.user.designation.name == 'HR Manager') {
            this.getCountIrStatusNew();
            // this.getCountNotesHR();
            this.$bus.$on('changeIrStatusNotNew', () => {
                this.getCountIrStatusNew();
            });

        } else {
            this.getCountIRReply();
            this.getCountOnHoldReply();
            this.getCountNotesRegularUser();
            this.getNotifyRespondentReply();
            this.$bus.$on('changeRespondentCount', () => {
                this.getCountIRReply();
                this.getCountOnHoldReply();
                // this.getCountRespondentReply();
            });
        }
    },
    methods: {
        getCountRespondentReply() {
            this.$constants.Default_API.get("/respondent/email/count")
            .then(response => {
                this.countRespondent = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getCountIRReply() {
            this.$constants.Default_API.get("/respondent/email/countIR")
            .then(response => {
                this.countRespondent = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getCountOnHoldReply() {
            this.$constants.Default_API.get("/respondent/email/countOnHold")
            .then(response => {
                this.countOnHold = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getCountOnHoldRequest() {
            this.$constants.Incident_API.get("/onhold_request/count")
            .then(response => {
                this.countOnHoldRequest = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getCountNotesRegularUser() {
            this.$constants.Default_API.get("/notes/count/regularuser/notreply")
            .then(response => {
                this.countNotesRegularUser = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getNotifyRespondentReply() {
            this.$constants.Default_API.get("/respondent/email/notification")
            .then(response => {
                this.respondent = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getCountIrStatusNew() {
            this.$constants.Incident_API.get("/status/incident/count/1/new")
            .then(response => {
                this.countStatusNew = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        getNotes() {
            let loadRequired = {
                constants: this.$constants.Default_API,
                globalError: this.globalErrorHandling,
            }
            this.$store.dispatch('actionNotesReplied', loadRequired)
            this.$store.dispatch('actionCountNotes', loadRequired)
        }
    }
}
</script>
