<template>
    <tab-group class="custom-tab tabs" v-if="role == 'Super Admin Access' || role == 'HR Admin Access'
    || role == 'HR Access'">
            <tab-element  v-for="(tab_title, index) in tab_titles" :key="index" :name="tab_title" >
                <osh-video v-if="tab_title === 'Occupational Safety and Health Video'" />
                <osh-list v-if="tab_title === 'OSH List'" />
            </tab-element>
        </tab-group>
        <tab-group class="custom-tab tabs" v-else>
            <tab-element  v-for="(tab_title, index) in tab_titles" :key="index" :name="tab_title" >
                <osh-video v-if="tab_title === 'Occupational Safety and Health Video'" />
            </tab-element>
        </tab-group>
</template>
<script>
"use strict"

import OshVideo from './oshVideo.vue'
import OshList from './oshList.vue'

export default {
    components: {
        OshVideo,
        OshList
    },
    data() {
        return {
            role: this.$ep.role,
        }
    },
    created() {
        if (this.role == 'Super Admin Access' || this.role == 'HR Admin Access' || this.role == 'HR Access') {
            this.tab_titles = {
                'osh_video': 'Occupational Safety and Health Video',
                'osh_list': 'OSH List',
            }
        } else {
            this.tab_titles = {
                'osh_video': 'Occupational Safety and Health Video',
            }
        }
    },
}
</script>
