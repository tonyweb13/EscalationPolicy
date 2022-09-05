<template>
    <div class="row">
        <div class="ibox-content col-lg-12 p-md">
            <tab-group class="custom-tab tabs">
                <tab-element
                    v-for="(tab_title, index) in tab_titles"
                    v-if="isEnable(index)" :key="index" :name="tab_title"
                >
                <coachinglog-report v-if="tab_title === 'Coaching Log Report'"/>
                <irhistory-report v-if="tab_title === 'IR History Report'"/>
                <closed-da-tracker v-if="tab_title === 'Closed DA Tracker Report'" />
                <open-cases v-if="tab_title === 'Open Cases Report'" />
                </tab-element>
            </tab-group>
        </div>
    </div>
</template>
<script>
    import openCases from '@/modules/admin/settings/reports/opencases.vue'
    import closedDaTracker from '@/modules/admin/settings/reports/closeddatracker.vue'
    import irhistoryReport from '@/modules/admin/settings/reports/irhistoryreport.vue'
    import coachinglogReport from '@/modules/admin/settings/reports/coachinglogreport.vue'
    import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'

    const TAB_TITLES = {
        'open_cases_report': 'Open Cases Report',
        'closed_da_tracker_report': 'Closed DA Tracker Report',
        'ir_history_report': 'IR History Report',
        'coachinglog_report': 'Coaching Log Report',
    }
export default {
    components: {
        openCases,
        closedDaTracker,
        irhistoryReport,
        coachinglogReport,
        TabElement,
        TabGroup
    },
    created() {
        this.tab_titles = TAB_TITLES;
    },
    methods: {
        isEnable(item) {
            if (item === 'open_cases_report') {
                return {"is_enable":true}
            } else {
                return {"is_enable":false}
            }
        },
    }
}
</script>

