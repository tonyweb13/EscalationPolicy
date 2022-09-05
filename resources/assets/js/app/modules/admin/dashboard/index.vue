<template>
    <div>
    <dashboard-headers
            :data="dashboard_headers"
            :is-loading="has_pending_request"
        />
        <tab-group class="custom-tab tabs" v-if="role == 'Super Admin Access' || role == 'HR Admin Access' || role == 'HR Access'">
            <tab-element  v-for="(tab_title, index) in tab_titles" :key="index" :name="tab_title" >
                <dashboard-nte-da v-if="tab_title === 'NTE DA Dashboard'" />
                <video-tutorials v-if="tab_title === 'Video Tutorials'" />
            </tab-element>
        </tab-group>
        <tab-group class="custom-tab tabs" v-else>
            <tab-element  v-for="(tab_title, index) in tab_titles" :key="index" :name="tab_title" >
                <video-tutorials v-if="tab_title === 'Video Tutorials'" />
            </tab-element>
        </tab-group>
        <modal-no-close v-if="openModal">
            <osh-popup slot="body"/>
        </modal-no-close>
    </div>
</template>

<script>
"use strict"

import DashboardHeaders from './dashboardHeaderCount.vue'
import DashboardNteDa from './dashboardNteDa.vue'
import VideoTutorials from './videoTutorials.vue'
import OshPopup from '../../osh/popup.vue'

export default {
    components: {
        DashboardHeaders,
        DashboardNteDa,
        VideoTutorials,
        OshPopup
    },
    data() {
        return {
            role: this.$ep.role,
            has_pending_request: false,
            openModal: false,
            dashboard_headers: {
                open: null,
                closed: null,
                invalid: null,
                total: null,
            },
        }
    },
    created() {
        this.eventOshChecking();
        if (this.role == 'Super Admin Access' || this.role == 'HR Admin Access' || this.role == 'HR Access') {
            this.tab_titles = {
                'nte_da_dashboard': 'NTE DA Dashboard',
                'video_tutorials': 'Video Tutorials',
            }
        } else {
            this.tab_titles = {
                'video_tutorials': 'Video Tutorials',
            }
        }
        this.$bus.$on('updateDashboard', this.generateDashboardData);
        this.$bus.$on('init_modal', (param) => {
            this.openModal = param;
        });
    },
    mounted() {
        this.generateDashboardData()
    },
    methods: {
        generateDashboardHeaders() {
            this.has_pending_request = true
            this.$constants.Dashboard_API.post("/headers")
                .then(response => {
                    this.dashboard_headers = response.data;
                    this.has_pending_request = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
        },
        generateDashboardData() {
            this.dashboard_headers = {
                open: null,
                closed: null,
                invalid: null,
                total: null,
            }
            this.generateDashboardHeaders()
        },
        eventOshChecking() {
           this.$constants.Default_API.get("/osh/employee/checking")
           .then(response => {
                this.openModal = response.data;
                console.log("eventOshChecking==", this.openModal);
            })
           .catch(error => {
               this.globalErrorHandling(error);
            });
        }
    },
}
</script>
