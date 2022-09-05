<template>
    <div class="row">
        <!-- <div class="ibox-content col-lg-12 p-md">
            <div class="tabs-container">
                <ul class="nav nav-tabs">

                    <li class="active" v-if="this.rule.announcement.is_enable">
                        <a data-toggle="tab" href="#tab-announcement" aria-expanded="true">
                            Announcement
                        </a>
                    </li>

                    <li v-if="this.rule.announcement.child_rules.distro.is_enable">
                        <a data-toggle="tab" href="#tab-distro" aria-expanded="false" @click="eventDistro()">
                            Distro
                        </a>
                    </li>

                    <li v-if="this.rule.announcement.child_rules.compliance.is_enable">
                        <a data-toggle="tab" href="#tab-compliance" aria-expanded="false" @click="eventCompliance()">
                            Compliance
                        </a>
                    </li>

                    <li v-if="rule.announcement.child_rules.compose_mail.is_enable">
                        <a data-toggle="tab" href="#tab-sent" aria-expanded="false"  @click="eventSent()">
                            Sent
                        </a>
                    </li>

                </ul>
                <div class="tab-content">

                    <div id="tab-announcement" class="tab-pane active">
                        <announcement-list/>
                    </div>
                    <div class="clearfix"></div>

                    <div id="tab-distro" class="tab-pane">
                        <distro-list/>
                    </div>
                    <div class="clearfix"></div>

                    <div id="tab-compliance" class="tab-pane">
                        <compliance-list/>
                    </div>
                    <div class="clearfix"></div>

                    <div id="tab-sent" class="tab-pane">
                        <sent-list/>
                    </div>
                    <div class="clearfix"></div>

                </div>
            </div>
        </div> -->
        <tab-group class="custom-tab tabs">
            <tab-element
                v-for="(tab_title, index) in tab_titles"
                v-if="isEnable(index)" :key="index" :name="tab_title"
            >
                <announcement-list
                    v-if="tab_title === 'Announcement'"
                />
                <distro-list
                    v-if="tab_title === 'Distro List'"
                />
                <compliance-list
                    v-if="tab_title === 'Compliance List'"
                />
                <sent-list
                    v-if="tab_title === 'Sent List'"
                />
            </tab-element>
        </tab-group>
    </div>
</template>
<script>
    import announcementList from '@/modules/announcement/listAnnouncement.vue'
    import distroList from '@/modules/announcement/listDistro.vue'
    import complianceList from '@/modules/announcement/listCompliance.vue'
    import sentList from '@/modules/announcement/listSent.vue'
    import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'
    import { mapGetters } from "vuex";

    const TAB_TITLES = {
        'announcement': 'Announcement',
        'distro_list': 'Distro List',
        'compliance_list': 'Compliance List',
        'sent_list': 'Sent List',
    }

    export default {
        data: function () {
            return {
                rule: this.$ep.rule,
                role: this.$ep.role,
            }
        },
        components: {
            announcementList,
            distroList,
            complianceList,
            sentList,
            TabElement,
            TabGroup,
        },
        mounted() {
            window.Echo.channel("newAnnouncement").listen("AnnouncementCreated", e => {
                let loadRequired = {
                    constants: this.$constants.Default_API,
                    setLoading: this.setLoading,
                    globalError: this.globalErrorHandling
                }
                this.$store.dispatch('fetchAnnounces', loadRequired)
            });
        },
        watch: {
            '$route.hash'() {
                if (this.$route.hash == '#announcement') {
                    this.$bus.$emit('newList');
                } else if (this.$route.hash == '#in-progress') {
                    this.$bus.$emit('inprogressList');
                } else if (this.$route.hash == '#distro-list') {
                    this.$bus.$emit('distroList');
                } else if (this.$route.hash == '#compliance-list') {
                    this.$bus.$emit('complianceList');
                } else if (this.$route.hash == '#sent-list') {
                    this.$bus.$emit('sentList');
                }
            }
        },
        created(){
            if (this.$route.hash == '#in-progress') {
                this.$bus.$emit('inprogressList');
            } else if (this.$route.hash == '#distro-list') {
                this.$bus.$emit('distroList');
            } else if (this.$route.hash == '#compliance-list') {
                this.$bus.$emit('complianceList');
            } else if (this.$route.hash == '#sent-list') {
                this.$bus.$emit('sentList');
            }
            this.tab_titles = TAB_TITLES;
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        computed: {

            ...mapGetters([
                'rows',
                'sentrows'
            ])
        },
        methods: {
            isEnable(item) {
                if (item === 'new') {
                    return this.rule['incident_report']['child_rules'][item]['is_enable']
                } else {
                    return true
                }
            },
            eventDistro(){
				this.$bus.$emit('distroList');
            },
            eventCompliance(){
				this.$bus.$emit('complianceList');
            },
            eventSent(){
				this.$bus.$emit('sentList');
            },
        }
    }
</script>
