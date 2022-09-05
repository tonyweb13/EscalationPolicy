<template>
    <div class="row">
        <div class="ibox-content col-lg-12 p-md">
            <tab-group class="custom-tab tabs">
                <tab-element
                    v-for="(tab_title, index) in tab_titles"
                    v-if="isEnable(index)" :key="index" :name="tab_title"
                >
                    <gravity-list
                        v-if="tab_title === 'Levels of Gravity'"
                        :complainantProps="complainant_data"
                    />
                    <yearly-total-infraction
                        v-if="tab_title === 'Yearly Total Infraction'"
                        :complainantProps="complainant_data"
                    />
                    <monthly-total-infraction
                        v-if="tab_title === 'Monthly Total Infraction'"
                        :complainantProps="complainant_data"
                    />
                </tab-element>
            </tab-group>
        </div>
    </div>
</template>
<script>
    import gravityList from '@/modules/admin/settings/coc/gravity/index.vue'
    import yearlyTotalInfraction from '@/modules/admin/settings/coc/gravity/yearlyTotalInfraction.vue'
    import monthlyTotalInfraction from '@/modules/admin/settings/coc/gravity/monthlyTotalInfraction.vue'
    import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'

    const TAB_TITLES = {
        'levels_of_gravity': 'Levels of Gravity',
        'yearly_total_infraction': 'Yearly Total Infraction',
        'monthly_total_infraction': 'Monthly Total Infraction',
    }

    export default {
        data: function () {
            return {
                rules: this.$ep.rule,
                complainant_data: {
                    list: true
                },
                openModal: false,
                openAction: "",
            }
        },

        components: {
            gravityList,
            yearlyTotalInfraction,
            TabElement,
            TabGroup,
            monthlyTotalInfraction
		},

        watch: {
            '$route.hash'() {
                if (this.$route.hash == '#yearly-total-infraction') {
                    this.$bus.$emit('yearlyTableClick');
                } else if (this.$route.hash == '#monthly-total-infraction') {
                    this.$bus.$emit('monthlyTableClick');
                } else if (this.$route.hash == '#levels-of-gravity') {
                    this.$bus.$emit('levelsOfGravity');
                }
            }
        },

        created() {
            this.tab_titles = TAB_TITLES;
        },

        methods: {
            isEnable(item) {
                if (item === 'levels_of_gravity') {
                    return this.rules[item]['is_enable']
                } else {
                    return this.rules['levels_of_gravity']['child_rules'][item]['is_enable']
                }

            },

			selectedTab(id){
                if (id == 'yearly_total_infraction') {
                    this.$bus.$emit('yearlyTableClick');
                } else if (id == 'monthly_total_infraction') {
                    this.$bus.$emit('monthlyTableClick');
                }
			},
        }
    }

</script>
