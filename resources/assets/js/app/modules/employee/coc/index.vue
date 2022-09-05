<template>
	<div class="row">
		<div class="ibox-content col-lg-12 p-md m-b-sm">
			<!-- <div class="tabs-container">
		        <ul class="nav nav-tabs">
		            <li class="active">
						<a data-toggle="tab" href="#tab-attendance" aria-expanded="true">
                    		Attendance with Points
						</a>
					</li>
					<li>
						<a data-toggle="tab" href="#tab-attendance-gravity" aria-expanded="false">
                    		Attendance with Gravity
						</a>
					</li>
		            <li><a data-toggle="tab" href="#tab-cwd-policy" aria-expanded="false">CWD Policy</a></li>
		            <li><a data-toggle="tab" href="#tab-negligence" aria-expanded="false">Negligence</a></li>
		            <li><a data-toggle="tab" href="#tab-general-house" aria-expanded="false">
						General House Rules
					</a></li>
		            <li><a data-toggle="tab" href="#tab-fraud" aria-expanded="false">Fraud</a></li>
					<li><a data-toggle="tab" href="#tab-misconduct" aria-expanded="false">Misconduct</a></li>
					<li><a data-toggle="tab" href="#tab-poor-work-ethics" aria-expanded="false">
						Poor Work Ethics
					</a></li>
					<li><a data-toggle="tab" href="#tab-information-security" aria-expanded="false">
						Information Security
					</a></li>
					<li><a data-toggle="tab" href="#tab-zero-tolerance-policy" aria-expanded="false">
						Zero Tolerance Policy
					</a></li>
					<li><a data-toggle="tab" href="#tab-compliance-breach" aria-expanded="false">
						Compliance Breach</a></li>
					<li><a data-toggle="tab" href="#tab-general-company" aria-expanded="false">
						General Company Rules and Regulations
					</a></li>
					<li><a data-toggle="tab" href="#tab-pip" aria-expanded="false">Negligence PIP</a></li>
					<li><a data-toggle="tab" href="#tab-lms" aria-expanded="false">LMS Guidelines</a></li>
					<li><a data-toggle="tab" href="#tab-corrective" aria-expanded="false">Corrective Policy</a></li>
		        </ul>
		        <div class="tab-content">
		            <div class="clearfix"></div>

		            <div id="tab-attendance" class="tab-pane active">
		                <category-attendance :categoriesProps = "15" />
		            </div>
					<div class="clearfix"></div>

		            <div id="tab-attendance-gravity" class="tab-pane active">
		                <category-list :categoriesProps = "1" />
		            </div>
		            <div class="clearfix"></div>

		            <div id="tab-cwd-policy" class="tab-pane">
		                <category-list :categoriesProps = "2" />
		            </div>
		            <div class="clearfix"></div>

		            <div id="tab-negligence" class="tab-pane">
		                <category-list :categoriesProps = "3" />
		            </div>
		            <div class="clearfix"></div>

		            <div id="tab-general-house" class="tab-pane">
		                <category-list :categoriesProps = "4" />
		            </div>
		            <div class="clearfix"></div>

		            <div id="tab-fraud" class="tab-pane">
		               <category-list :categoriesProps = "5" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-misconduct" class="tab-pane">
		                <category-list :categoriesProps = "6" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-poor-work-ethics" class="tab-pane">
		                <category-list :categoriesProps = "7" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-information-security" class="tab-pane">
		                <category-list :categoriesProps = "8" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-zero-tolerance-policy" class="tab-pane">
		                <category-list :categoriesProps = "9" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-compliance-breach" class="tab-pane">
		                <category-list :categoriesProps = "10" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-general-company" class="tab-pane">
		                <category-list :categoriesProps = "11" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-pip" class="tab-pane">
		                <category-list :categoriesProps = "12" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-lms" class="tab-pane">
		                <category-list :categoriesProps = "13" />
		            </div>
		            <div class="clearfix"></div>

					<div id="tab-corrective" class="tab-pane">
		                <category-list :categoriesProps = "14" />
		            </div>
		            <div class="clearfix"></div>
		        </div>
	    	</div> -->
			<tab-group class="custom-tab tabs">
				<tab-element
					v-for="(tab_title, index) in tab_titles"
					v-if="isEnable(index)" :key="index" :name="tab_title"
				>
					<category-attendance
						v-if="tab_title === 'Attendance with Points'"
						:categoriesProps = "15"
					/>
					<category-list
						v-if="tab_title === 'Attendance with Gravity'"
						:categoriesProps = "1"
					/>
					<category-list
						v-if="tab_title === 'CWD Policy'"
						:categoriesProps = "2"
					/>
					<category-list
						v-if="tab_title === 'Negligence'"
						:categoriesProps = "3"
					/>
					<category-list
						v-if="tab_title === 'General House Rules'"
						:categoriesProps = "4"
					/>
				   	<category-list
						v-if="tab_title === 'Fraud'"
						:categoriesProps = "5"
					/>
					<category-list
						v-if="tab_title === 'Misconduct'"
						:categoriesProps = "6"
					/>
					<category-list
						v-if="tab_title === 'Poor Work Ethics'"
						:categoriesProps = "7"
					/>
					<category-list
						v-if="tab_title === 'Information Security'"
						:categoriesProps = "8"
					/>
					<category-list
						v-if="tab_title === 'Zero Tolerance Policy'"
						:categoriesProps = "9"
					/>
					<category-list
						v-if="tab_title === 'Compliance Breach'"
						:categoriesProps = "10"
					/>
					<category-list
						v-if="tab_title === 'General Company Rules and Regulations'"
						:categoriesProps = "11"
					/>
					<category-list
						v-if="tab_title === 'Negligence PIP'"
						:categoriesProps = "12"
					/>
					<category-list
						v-if="tab_title === 'LMS Guidelines'"
						:categoriesProps = "13"
					/>
					<category-list
						v-if="tab_title === 'Corrective Policy'"
						:categoriesProps = "14"
					/>
					<category-list
						v-if="tab_title === 'Hard Return'"
						:categoriesProps = "16"
					/>
					<category-list
						v-if="tab_title === 'Warranted Escalation'"
						:categoriesProps = "17"
					/>
				</tab-element>
			</tab-group>
		</div>
    </div>
</template>
<script>
	import categoryList from '@/modules/employee/coc/category.vue'
	import categoryAttendance from '@/modules/employee/coc/categoryAttendance.vue'
	import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'

	const TAB_TITLES = {
		'attendance_with_points': 'Attendance with Points',
		'attendance_with_gravity': 'Attendance with Gravity',
		'cwd_policy': 'CWD Policy',
		'negligence': 'Negligence',
		'general_house_rules': 'General House Rules',
		'fraud': 'Fraud',
		'misconduct': 'Misconduct',
		'poor_work_ethics': 'Poor Work Ethics',
		'information_security': 'Information Security',
		'zero_tolerance_policy': 'Zero Tolerance Policy',
		'compliance_breach': 'Compliance Breach',
		'general_company_rules_and_regulations': 'General Company Rules and Regulations',
		'negligence_pip': 'Negligence PIP',
		'lms_guidelines': 'LMS Guidelines',
		'corrective_policy': 'Corrective Policy',
		'hard_return': 'Hard Return',
		'warranted_escalation': 'Warranted Escalation',
	}

    export default {
		components: { categoryList, categoryAttendance, TabElement, TabGroup },
    }

	created(){
		this.tab_titles = TAB_TITLES;
	},

</script>
