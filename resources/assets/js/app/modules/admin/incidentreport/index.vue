<template>
    <div class="row">
        <div class="ibox-content col-lg-12 p-md">
            <button type="button" @click.prevent="addComplainant()" class="btn btn-info" style="margin-bottom:20px;"
            v-if="rule.incident_report.child_rules.create_incident_report.is_enable">
                <i class="fa fa-plus"/> Create Incident Report
            </button>
            <!-- <div class="tabs-container">
                <ul class="nav nav-tabs">

                    <template v-if="this.$ep.role == 'Regular User Access'
                    || this.$ep.role == 'Regular Supervisor Access'">

                        <li class="active" v-if="rule.incident_report.child_rules.in_progress.is_enable">
                            <a data-toggle="tab" href="#tab-inprogress" aria-expanded="false">
                                In Progress
                            </a>
                        </li>

                    </template>

                    <template v-else>

                        <li class="active" v-if="rule.incident_report.child_rules.new.is_enable">
                            <a data-toggle="tab" href="#tab-new" aria-expanded="true">New</a>
                        </li>

                        <li v-if="rule.incident_report.child_rules.in_progress.is_enable">
                            <a data-toggle="tab" href="#tab-inprogress" aria-expanded="false" @click="eventInprogress()">
                                In Progress
                            </a>
                        </li>

                    </template>

                    <li v-if="rule.incident_report.child_rules.on_hold.is_enable">
                        <a data-toggle="tab" href="#tab-onhold" aria-expanded="false" @click="eventOnhold()">On Hold</a>
                    </li>

					<li>
                        <a data-toggle="tab" href="#tab-invite" aria-expanded="false" @click="eventInvite()">Invite</a>
                    </li>

                    <li v-if="rule.incident_report.child_rules.closed.is_enable">
                        <a data-toggle="tab" href="#tab-closed" aria-expanded="false" @click="eventClosed()">Closed</a>
                    </li>

                </ul>
                <div class="tab-content">

                    <template v-if="this.$ep.role == 'Regular User Access'
                    || this.$ep.role == 'Regular Supervisor Access'">

                        <div id="tab-inprogress" class="tab-pane active"
                        v-if="rule.incident_report.child_rules.in_progress.is_enable">
                            <inprogress-list :incidentProps = '2' />
                        </div>
                        <div class="clearfix"></div>

                    </template>

                    <template v-else>

                        <div id="tab-new" class="tab-pane active"
                        v-if="rule.incident_report.child_rules.new.is_enable">
                            <new-list :incidentProps = '1' />
                        </div>
                        <div class="clearfix"></div>

                        <div id="tab-inprogress" class="tab-pane"
                        v-if="rule.incident_report.child_rules.in_progress.is_enable">
                            <inprogress-list :incidentProps = '2' />
                        </div>
                        <div class="clearfix"></div>

                    </template>

                    <div id="tab-onhold" class="tab-pane"
                    v-if="rule.incident_report.child_rules.on_hold.is_enable">
                        <onhold-list :incidentProps = '3' />
                    </div>
                    <div class="clearfix"></div>

                    <div id="tab-invite" class="tab-pane"><invite/></div>
					<div class="clearfix"></div>

                    <div id="tab-closed" class="tab-pane" v-if="rule.incident_report.child_rules.closed.is_enable">
                        <closed/>
                    </div>
                    <div class="clearfix"></div>

                </div> -->
                <!-- For Applying danTabs -->
                <tab-group class="custom-tab tabs">
                    <tab-element
                        v-for="(tab_title, index) in tab_titles"
                        v-if="isEnable(index)" :key="index" :name="tab_title"
                    >
                        <new-list
                            v-if="tab_title === 'New'"
                        />
                        <inprogress-list
                            v-if="tab_title === 'In Progress'"
                        />
                        <onhold-list
                            v-if="tab_title === 'On Hold'"
                        />
                        <invite v-if="tab_title === 'Invite'" />
                        <closed
                            v-if="tab_title === 'Closed'"
                        />
                    </tab-element>
                </tab-group>
            </div>

            <modal v-if="openModal" @close="openModal = false">
                <add-form v-if="openAction == 'add'" slot="body" />
                <view-form v-else :complainantProps="complainant_data" slot="body" />
            </modal>
        </div>
    </div>
</template>
<script>
    // import incidentList from '@/modules/admin/incidentreport/list.vue'
    import newList from '@/modules/admin/incidentreport/listNew.vue'
    import inprogressList from '@/modules/admin/incidentreport/listInprogress.vue'
    import onholdList from '@/modules/admin/incidentreport/listOnhold.vue'
    import viewForm from '@/modules/admin/incidentreport/viewForm.vue'
    import addForm from '@/modules/complainant_respondent/addForm.vue'
    import closed from '@/modules/admin/incidentreport/closed.vue'
    import invite from '@/modules/complainant_respondent/invite.vue'
    import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'

    const TAB_TITLES = {
        'new': 'New',
        'in_progress': 'In Progress',
        'on_hold': 'On Hold',
        'invite': 'Invite',
        'closed': 'Closed',
    }

    export default {
        data: function () {
            return {
                rule: this.$ep.rule,
                role: this.$ep.role,
                openModal: false,
                openAction: "",
            }
        },
        components: {
            // incidentList,
            addForm,
            viewForm,
            newList,
            inprogressList,
            onholdList,
            invite,
            closed,
            TabElement,
            TabGroup,
		},
        watch: {
            '$route.hash'() {
                if (this.$route.hash == '#new') {
                    this.$bus.$emit('newList');
                } else if (this.$route.hash == '#in-progress') {
                    this.$bus.$emit('inprogressList');
                } else if (this.$route.hash == '#on-hold') {
                    this.$bus.$emit('onholdList');
                } else if (this.$route.hash == '#invite') {
                    this.$bus.$emit('inviteList');
                } else if (this.$route.hash == '#closed') {
                    this.$bus.$emit('closedList');
                } else if (this.$route.hash == '') {
                    if (this.rule.incident_report.child_rules.new.is_enable) {
                        this.$bus.$emit('newList');
                    } else if (this.rule.incident_report.child_rules.in_progress.is_enable) {
                        this.$bus.$emit('inprogressList');
                    }
                }
            }
        },
        created(){
            if (this.rule.incident_report.child_rules.new.is_enable) {
                this.$bus.$emit('newList');
            } else if (this.rule.incident_report.child_rules.in_progress.is_enable) {
                this.$bus.$emit('inprogressList');
            } else if (this.rule.incident_report.child_rules.on_hold.is_enable) {
                this.$bus.$emit('onholdList');
            }
            this.tab_titles = TAB_TITLES;
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods: {
            isEnable(item) {
                if (item === 'new') {
                    return this.rule['incident_report']['child_rules'][item]['is_enable']
                } else {
                    return true
                }
            },

			addComplainant: function () {
                this.openModal = true;
                this.openAction = "add";
            },
            // eventInprogress(){
			// 	this.$bus.$emit('inprogressList');
            // },
            // eventOnhold(){
			// 	this.$bus.$emit('onholdList');
            // },
            // eventInvite(){
			// 	this.$bus.$emit('inviteList');
            // },
            // eventClosed(){
			// 	this.$bus.$emit('closedList');
            // },
		}
    }

</script>
