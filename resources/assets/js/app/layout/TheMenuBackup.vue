<template>
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><img alt="image" class="img-circle" :src="imageMini"></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold">{{ user.first_name }} {{ user.last_name }} </strong>
                                </span>
                                <span class="text-muted text-xs block">
                                    {{ user.designation.name }}
                                    <b class="caret"/>
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>
                                <a href="/logout">
                                    <i class="fa fa-sign-out"/> Log out
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- TODO: will replace ACL rules here  -->
                <!-- start Menu -->
                <li>
                    <router-link to="/">
                        <i class="fa fa-dashboard"/>
                        <span class="nav-label">Dashboard</span>
                    </router-link>
                </li>

                <!-- HR Admin Menu -->
                <template v-if="user.designation.name == 'HR Analyst'
                        || user.designation.name == 'Sr. Programmer'">
                    <li>
                        <router-link to="/settings/coc/offense">
                            <i class="fa fa-eyedropper"></i> Code of Conduct
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/settings/behavioral/action">
                            <i class="fa fa-newspaper-o"></i> Behavioral Action
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/settings/levels/gravity">
                            <i class="fa fa-newspaper-o"></i> Levels of Gravity
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/settings/incident/report/resolution">
                            <i class="fa fa-pencil-square-o"></i> Case Closure
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/incidentreport">
                            <i class="fa fa-newspaper-o"></i>
                            <span class="nav-label"> Incident Report</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/irhistory">
                            <i class="fa fa-flask"></i>
                            <span class="nav-label"> Incident Report History</span>
                        </router-link>
                    </li>
                    <!-- <li>
                        <router-link to="/progression">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> Progression Offense</span>
                        </router-link>
                    </li> -->
                    <li>
                        <router-link to="/hrbp/cluster">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> HRBP Cluster</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/settings/reports">
                            <i class="fa fa-cloud-download"></i> Reports
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/settings/audit/logs">
                            <i class="fa fa-cloud-download"></i> Audit Logs
                        </router-link>
                    </li>
                </template>

                <!-- HR Access Menu -->
                <template v-else-if="user.designation.name == 'HR Manager'
                    || user.designation.name == 'HR Recuitment Manager'
                    || user.designation.name == 'HR Business Partner'
                    || user.designation.name == 'HR Site Lead'">
                    <li>
                        <router-link to="/employee/coc">
                            <i class="fa fa-eyedropper"></i> Code of Conduct
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/employee/behavioral/action">
                            <i class="fa fa-newspaper-o"></i> Behavioral Action
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/employee/levels/gravity">
                            <i class="fa fa-newspaper-o"></i> Levels of Gravity
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/employee/irresolution">
                            <i class="fa fa-pencil-square-o"></i> Case Closure
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/incidentreport">
                            <i class="fa fa-newspaper-o"></i>
                            <span class="nav-label"> Incident Report</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/irhistory">
                            <i class="fa fa-flask"></i>
                            <span class="nav-label"> Incident Report History</span>
                        </router-link>
                    </li>
                    <!-- <li>
                        <router-link to="/progression">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> Progression Offense</span>
                        </router-link>
                    </li> -->
                    <!-- hrbp melodie access only-->
                    <li v-if="user.employee_no == 20183690">
                        <router-link to="/hrbp/cluster">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> HRBP Cluster</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/settings/reports">
                            <i class="fa fa-cloud-download"></i> Reports
                        </router-link>
                    </li>
                </template>

                <!-- Regular Users Menu -->
                <template v-else>
                    <li>
                        <router-link to="/employee/coc">
                            <i class="fa fa-eyedropper"></i> Code of Conduct
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/employee/behavioral/action">
                            <i class="fa fa-newspaper-o"></i> Behavioral Action
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/employee/levels/gravity">
                            <i class="fa fa-newspaper-o"></i> Levels of Gravity
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/employee/incidentreport">
                            <i class="fa fa-chain"></i>
                            <span class="nav-label"> Incident Report</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/irhistory">
                            <i class="fa fa-flask"></i>
                            <span class="nav-label"> Incident Report History</span>
                        </router-link>
                    </li>
                    <!-- <li>
                        <router-link to="/progression">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> Progression Offense</span>
                        </router-link>
                    </li> -->
                    <template v-for="accs in access" v-if="user.designation.name == accs.designation.name">
                        <!-- <li>
                            <router-link to="/head/approval">
                                <i class="fa fa-arrow-circle-o-up"></i>
                                <span class="nav-label"> IR Approval</span>
                            </router-link>
                        </li> -->
                        <li>
                            <router-link to="/employee/coachinglog">
                                <i class="fa fa-arrow-circle-o-up"></i>
                                <span class="nav-label"> Coaching Logs</span>
                            </router-link>
                        </li>
                    </template>
                    <!-- ghiniel access only-->
                    <li v-if="user.employee_no == 20183338">
                        <router-link to="/employee/coachinglog">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> Coaching Logs</span>
                        </router-link>
                    </li>

                    <li >
                        <router-link to="/settings/acl_rule">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label"> ACL Rule</span>
                        </router-link>
                    </li>
                    <li >
                        <router-link to="/settings/settings_user">
                            <i class="fa fa-arrow-circle-o-up"></i>
                            <span class="nav-label">Settings User</span>
                        </router-link>
                    </li>
                </template>
                <!-- end Menu -->
            </ul>
        </div>
    </nav>
</template>

<script>
"use strict";

export default {
    name: 'TheMenu',

    data: function () {
            return {
            user: this.$ep.user,
            imageMini: '/img/profile_small.jpg',
            access: ''
        }
    },

    created(){
        this.$constants.Default_API.get("/user/supervisor/manager/access")
        .then(response => {
            this.access = response.data;
            console.log(this.access);
        })
        .catch(error => {
            this.globalErrorHandling(error);
        });
    },

    mounted(){
        $('#side-menu').metisMenu()

        if(this.user.profile_pic){
            this.imageMini = 'https://vpssystem.s3.ap-southeast-2.amazonaws.com/customdb1/images/profile/'+this.user.profile_pic;
        }
    },

}
</script>
