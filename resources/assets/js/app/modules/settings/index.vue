<template>
	<div class="row">
		<div class="ibox-content col-lg-12 p-md">
			<!-- <div class="tabs-container">
		        <ul class="nav nav-tabs" id="tabs">
					<li class="active" v-if="this.rule.aclrules.is_enable">
					    <a
							data-toggle="tab"
							href="#tab-points-attendance"
							aria-expanded="true"
							@click="selectedTab(1)"
						>
					        ACL Rule
					    </a>
					</li>
					<li v-if="this.rule.user.is_enable">
					    <a
							data-toggle="tab"
							href="#tab-gravity-attendance"
							aria-expanded="false"
							@click="selectedTab(2)"
						>
					        Settings Rule
					    </a>
					</li>
		            <li>
						<a
							data-toggle="tab"
							href="#tab-cwd-policy"
							aria-expanded="false"
							@click="selectedTab(3)"
						>
							Offices
						</a>
					</li>
		        </ul>
			 	{{ rule }}
		        <div class="tab-content">
		            <div class="clearfix"></div>

		            <div id="tab-points-attendance" class="tab-pane active">
		                <acl-rules  :settingsProps ="settings(1)" />
		            </div>
					<div class="clearfix"></div>

		            <div id="tab-gravity-attendance" class="tab-pane">
						<settings-user  :settingsProps ="settings(2)" />
		            </div>
		            <div class="clearfix"></div>

		            <div id="tab-cwd-policy" class="tab-pane">
						<office-index :settingsProps ="settings(3)" />
		            </div>
		            <div class="clearfix"></div>
		        </div>
	    	</div> -->

			<tab-group class="custom-tab tabs">
	            <tab-element
	                v-for="(tab_title, index) in tab_titles"
	                v-if="isEnable(index)" :key="index" :name="tab_title"
	            >
					<acl-rules  :settingsProps ="settings(1)"
						v-if="tab_title === 'ACL Rule'"
					/>
					<settings-user  :settingsProps ="settings(2)"
						v-if="tab_title === 'User Settings'"
					/>
					<office-index :settingsProps ="settings(3)"
						v-if="tab_title === 'Office'"
					/>
	            </tab-element>
	        </tab-group>
		</div>
    </div>
</template>
<script>
	import settingsUser from '@/modules/settings/setting_user.vue'
	import aclRules from '@/modules/settings/acl_rule.vue'
	import officeIndex from '@/modules/settings/office/index.vue'
	import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'

    const TAB_TITLES = {
        'acl_rule': 'ACL Rule',
        'user_settings': 'User Settings',
        'office': 'Office',
    }

    export default {
		components: { settingsUser, aclRules, officeIndex, TabElement, TabGroup, },
        data: function () {
            return {
                rule: this.$ep.rule,
                role: this.$ep.role,
            	openModal: false,
            	headerName: 'Add Offense',
            }
        },
        watch: {
            '$route.hash'() {
                if (this.$route.hash == '#acl-rule') {
                    this.$bus.$emit('aclSettings');
                } else if (this.$route.hash == '#user-settings') {
                    this.$bus.$emit('userSettings');
                } else if (this.$route.hash == '#office') {
                    this.$bus.$emit('officeSettings');
                }
            }
        },
        created() {
			console.log('THIS.$ROUTE.HASH', this.$route)
			if (this.$route.hash == '#acl-rule') {
				this.$bus.$emit('aclSettings');
			} else if (this.$route.hash == '#user-settings') {
				this.$bus.$emit('userSettings');
			} else if (this.$route.hash == '#office') {
				this.$bus.$emit('officeSettings');
			} else if (this.$route.hash == ' ' || this.$route.hash == '') {
				console.log('asdasdasd');
				this.$bus.$emit('aclSettings');
			}
            this.tab_titles = TAB_TITLES;
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });
        },
        methods: {
			isEnable(item) {
                return true
            },
			selectedTab(settings_id){
				this.$bus.$emit('settingsListClick', settings_id);
			},

			settings: function (id) {

				return {
					settings_id: id,
				}
			},
        }
    }

</script>
