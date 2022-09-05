<template>
    <div class="row">
        <loading-modal v-if="isLoading" />
        <div class="ibox-content col-lg-12 p-md">
            <tab-group class="custom-tab tabs">
                <tab-element
                    v-for="(tab_title, index) in tab_titles"
                    v-if="isEnable(index)" :key="index" :name="tab_title"
                >
                    <attendance-list :categoriesProps =  "1"
                        v-if="tab_title === 'Attendance'"
                    />
                    <cwd-list :categoriesProps = "2"
                        v-if="tab_title === 'CWD Policy'"
                    />
                    <negligence-list :categoriesProps = "3"
                        v-if="tab_title === 'Negligence'"
                    />
                    <general-house-list :categoriesProps = "4"
                        v-if="tab_title === 'General House Rules'"
                    />
                    <fraud-list :categoriesProps = "5"
                       v-if="tab_title === 'Fraud'"
                    />
                    <misconduct-list :categoriesProps = "6"
                        v-if="tab_title === 'Misconduct'"
                    />
                    <poor-list :categoriesProps = "7"
                        v-if="tab_title === 'Poor Work Ethics'"
                    />
                    <information-list :categoriesProps = "8"
                    	v-if="tab_title === 'Information Security'"
                    />
                    <zero-list :categoriesProps = "9"
                        v-if="tab_title === 'Zero Tolerance Policy'"
                    />
                    <compliance-list :categoriesProps = "10"
                        v-if="tab_title === 'Compliance Breach'"
                    />
                    <general-company-list :categoriesProps = "11"
                        v-if="tab_title === 'General Company Rules and Regulations'"
                    />
                    <negligence-pip-list :categoriesProps = "12"
                        v-if="tab_title === 'Negligence PIP'"
                    />
                    <lms-list :categoriesProps = "13"
                        v-if="tab_title === 'LMS Guidelines'"
                    />
                    <corrective-list :categoriesProps = "14"
                        v-if="tab_title === 'Corrective Policy'"
                    />
                    <hard-list :categoriesProps = "16"
                        v-if="tab_title === 'Hard Return'"
                    />
                    <warranted-list :categoriesProps = "17"
                        v-if="tab_title === 'Warranted Escalation'"
                    />
                    <attendance-supervisor-list :categoriesProps = "18"
                        v-if="tab_title === 'Attendance Supervisor And Manager'"
                    />
                    <attendance-trainer-list :categoriesProps = "19"
                        v-if="tab_title === 'Attendance Trainer'"
                    />
                    <credit-repair-offer-list :categoriesProps = "20"
                        v-if="tab_title === 'Credit Repair Offer'"
                    />
                    <credit-repair-offer-sup-list :categoriesProps = "21"
                        v-if="tab_title === 'Credit Repair Offer Supervisor'"
                    />
                    <pre-underwriting-list :categoriesProps = "22"
                        v-if="tab_title === 'Pre-Underwriting'"
                    />
                    <operations-system-list :categoriesProps = "23"
                        v-if="tab_title === 'Operations Processes System and Guidelines'"
                    />
                </tab-element>
            </tab-group>
        </div>
    </div>
</template>
<script>
    import attendanceList from '@/modules/admin/progressionoffense/attendanceList.vue'
    import cwdList from '@/modules/admin/progressionoffense/cwdList.vue'
    import negligenceList from '@/modules/admin/progressionoffense/negligenceList.vue'
    import generalHouseList from '@/modules/admin/progressionoffense/generalHouseList.vue'
    import fraudList from '@/modules/admin/progressionoffense/fraudList.vue'
    import misconductList from '@/modules/admin/progressionoffense/misconductList.vue'
    import poorList from '@/modules/admin/progressionoffense/poorList.vue'
    import informationList from '@/modules/admin/progressionoffense/informationList.vue'
    import zeroList from '@/modules/admin/progressionoffense/zeroList.vue'
    import complianceList from '@/modules/admin/progressionoffense/complianceList.vue'
    import generalCompanyList from '@/modules/admin/progressionoffense/generalCompanyList.vue'
    import negligencePipList from '@/modules/admin/progressionoffense/negligencePipList.vue'
    import lmsList from '@/modules/admin/progressionoffense/lmsList.vue'
    import correctiveList from '@/modules/admin/progressionoffense/correctiveList.vue'
    import hardList from '@/modules/admin/progressionoffense/hardList.vue'
    import warrantedList from '@/modules/admin/progressionoffense/warrantedList.vue'
    import attendanceSupervisorList from '@/modules/admin/progressionoffense/attendanceSupervisorList.vue'
    import attendanceTrainerList from '@/modules/admin/progressionoffense/attendanceTrainerList.vue'
    import creditRepairOfferList from '@/modules/admin/progressionoffense/creditRepairOfferList.vue'
    import creditRepairOfferSupList from '@/modules/admin/progressionoffense/creditRepairOfferSupList.vue'
    import preUnderwritingList from '@/modules/admin/progressionoffense/preUnderwritingList.vue'
    import operationsSystemList from '@/modules/admin/progressionoffense/operationsSystemList.vue'
    import {Tabs as TabGroup, Tab as TabElement} from 'vue-tabs-component-dsandber'

    const TAB_TITLES = {
        'attendance': 'Attendance',
        'cwd_policy': 'CWD Policy',
        'negligence': 'Negligence',
        'general_house_rules': 'General House Rules',
        'fraud': 'Fraud',
        'misconduct': 'Misconduct',
        'poor_work_ethics': 'Poor Work Ethics',
        'information_security': 'Information Security',
        'zero_tolerance_policy': 'Zero Tolerance Policy',
        'compliance_breach': 'Compliance Breach',
        'general_company_rules': 'General Company Rules and Regulations',
        'negligence_pip': 'Negligence PIP',
        'lms_guidelines': 'LMS Guidelines',
        'corrective_policy': 'Corrective Policy',
        'hard_return': 'Hard Return',
        'warranted_escalation': 'Warranted Escalation',
        'attendance_supervisor_and_manager': 'Attendance Supervisor And Manager',
        'attendance_trainer': 'Attendance Trainer',
        'credit_repair_offer': 'Credit Repair Offer',
        'credit_repair_offer_supervisor': 'Credit Repair Offer Supervisor',
        'pre_underwriting': 'Pre-Underwriting',
        'operations_processes_system_and_guidelines': 'Operations Processes System and Guidelines',
    }
    export default {
        components: {
            attendanceList,
            cwdList,
            negligenceList,
            generalHouseList,
            fraudList,
            misconductList,
            poorList,
            informationList,
            zeroList,
            complianceList,
            generalCompanyList,
            negligencePipList,
            lmsList,
            correctiveList,
            hardList,
            warrantedList,
            attendanceSupervisorList,
            attendanceTrainerList,
            creditRepairOfferList,
            creditRepairOfferSupList,
            preUnderwritingList,
            operationsSystemList,
            TabElement,
            TabGroup
        },
        data: function () {
            return {
                rule: this.$ep.rule,
                role: this.$ep.role,
                openModal: false,
                headerName: 'Add Offense',
                isLoading: false,
            }
        },

        watch: {
            '$route.hash'() {
                if (this.$route.hash == '#attendance') {
                    this.$bus.$emit('attendance');
                } else if (this.$route.hash == '#cwd-policy') {
                    this.$bus.$emit('cwd');
                } else if (this.$route.hash == '#negligence') {
                    this.$bus.$emit('negligence');
                } else if (this.$route.hash == '#general-house-rules') {
                    this.$bus.$emit('generalHouse');
                } else if (this.$route.hash == '#fraud') {
                    this.$bus.$emit('fraud');
                } else if (this.$route.hash == '#misconduct') {
                    this.$bus.$emit('misconduct');
                } else if (this.$route.hash == '#poor-work-ethics') {
                    this.$bus.$emit('poor');
                } else if (this.$route.hash == '#information-security') {
                    this.$bus.$emit('information');
                } else if (this.$route.hash == '#zero-tolerance-policy') {
                    this.$bus.$emit('zeroTolerance');
                } else if (this.$route.hash == '#compliance-breach') {
                    this.$bus.$emit('compliance');
                } else if (this.$route.hash == '#general-company-rules-and-regulations') {
                    this.$bus.$emit('generalCompany');
                } else if (this.$route.hash == '#negligence-pip') {
                    this.$bus.$emit('negligencePip');
                } else if (this.$route.hash == '#lms-guidelines') {
                    this.$bus.$emit('lmsGuidlines');
                } else if (this.$route.hash == '#corrective-policy') {
                    this.$bus.$emit('corrective');
                } else if (this.$route.hash == '#hard-return') {
                    this.$bus.$emit('hardReturn');
                } else if (this.$route.hash == '#warranted-escalation') {
                    this.$bus.$emit('warrantedEscalation');
                } else if (this.$route.hash == '#attendance-supervisor-and-manager') {
                    this.$bus.$emit('attendanceSupervisorAndManager');
                } else if (this.$route.hash == '#attendance-trainer') {
                    this.$bus.$emit('attendanceTrainer');
                } else if (this.$route.hash == '#credit-repair-offer') {
                    this.$bus.$emit('creditRepairOffer');
                } else if (this.$route.hash == '#credit-repair-offer-supervisor') {
                    this.$bus.$emit('creditRepairOfferSupervisor');
                } else if (this.$route.hash == '#pre-underwriting') {
                    this.$bus.$emit('preUnderwriting');
                } else if (this.$route.hash == '#operations-processes-system-and-guidelines') {
                    this.$bus.$emit('operationsProcessesSystemAndGuidelines');
                }
            }
        },
        created() {
            this.refreshPeriod();
            if (this.$route.hash == '#attendance') {
                this.$bus.$emit('attendance');
            } else if (this.$route.hash == '#cwd-policy') {
                this.$bus.$emit('cwd');
            } else if (this.$route.hash == '#negligence') {
                this.$bus.$emit('negligence');
            } else if (this.$route.hash == '#general-house-rules') {
                this.$bus.$emit('generalHouse');
            } else if (this.$route.hash == '#fraud') {
                this.$bus.$emit('fraud');
            } else if (this.$route.hash == '#misconduct') {
                this.$bus.$emit('misconduct');
            } else if (this.$route.hash == '#poor-work-ethics') {
                this.$bus.$emit('poor');
            } else if (this.$route.hash == '#information-security') {
                this.$bus.$emit('information');
            } else if (this.$route.hash == '#zero-tolerance-policy') {
                this.$bus.$emit('zeroTolerance');
            } else if (this.$route.hash == '#compliance-breach') {
                this.$bus.$emit('compliance');
            } else if (this.$route.hash == '#general-company-rules-and-regulations') {
                this.$bus.$emit('generalCompany');
            } else if (this.$route.hash == '#negligence-pip') {
                this.$bus.$emit('negligencePip');
            } else if (this.$route.hash == '#lms-guidelines') {
                this.$bus.$emit('lmsGuidlines');
            } else if (this.$route.hash == '#corrective-policy') {
                this.$bus.$emit('corrective');
            } else if (this.$route.hash == '#hard-return') {
                this.$bus.$emit('hardReturn');
            } else if (this.$route.hash == '#warranted-escalation') {
                this.$bus.$emit('warrantedEscalation');
            } else if (this.$route.hash == '#attendance-supervisor-and-manager') {
                this.$bus.$emit('attendanceSupervisorAndManager');
            } else if (this.$route.hash == '#attendance-trainer') {
                this.$bus.$emit('attendanceTrainer');
            } else if (this.$route.hash == '#credit-repair-offer') {
                this.$bus.$emit('creditRepairOffer');
            } else if (this.$route.hash == '#credit-repair-offer-supervisor') {
                this.$bus.$emit('creditRepairOfferSupervisor');
            } else if (this.$route.hash == '#pre-underwriting') {
                this.$bus.$emit('preUnderwriting');
            } else if (this.$route.hash == '#operations-processes-system-and-guidelines') {
                    this.$bus.$emit('operationsProcessesSystemAndGuidelines');
            } else if (this.$route.hash == ' ') {
                this.$bus.$emit('attendance');
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
            refreshPeriod: function() {
                this.isLoading = true
                this.$constants.Progression_API.get("/refresh/period")
                .then(response => {
                    response.data;
                    this.$success('Refresh Period Running Background Success!');
                    this.isLoading = false
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        }
    }

</script>
