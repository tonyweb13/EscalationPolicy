<template>
    <div class="row">
        <div class="ibox-content col-lg-12 p-md">
            <table class="col-lg-12">
                <tr>
                    <td>
                        <input-search  @search="eventSearch(searchKey)" >
                        <input type="text"
                            class="form-control search-custom-input"
                            placeholder="Search offense, category, gravity here..."
                            v-model="searchKey"
                            @change='eventSearch(searchKey)'>
                        </input-search>
                    </td>
                    <td>
                    <div class="pull-right">
                        <a  href="/api/admin/settings/coc/download" v-if="rule.code_of_conduct.child_rules.attendance.child_rules.export.is_enable">
                            <button type="button" class="btn btn-success">
                                <i class="fa fa-download"></i> Export COC
                            </button>
                        </a>
                    </div>
                    </td>
                </tr>
            </table>
            <hr style="margin-top: 28px;"/>

            <!-- Start Searching -->
            <div v-if="showSearchAll">
                <div style="margin-left: 25px;"><i>Search found in word "<b>{{ searchKey }}</b>"</i></div>
                <v-client-table :data="rows" :columns="columns" :options="options">
                    <template slot="actions" slot-scope="props" >
                    <div>
                        <button
                            class='btn btn-primary btn-xs'
                            @click="editRow(props.row)"
                            v-if="rule.code_of_conduct.child_rules.attendance.child_rules.edit.is_enable "
                        >
                            <i class="fa fa-pencil"></i> {{ labels.editBtn }}</button>
                        <button
                            class='btn btn-danger btn-xs'
                            @click="deleteRow(props.row)"
                            v-if="rule.code_of_conduct.child_rules.attendance.child_rules.archive.is_enable"
                        >
                            <i class="fa fa-remove"></i> {{ labels.deleteBtn }}
                        </button>
                    </div>
                    </template>
                        <template slot="category" slot-scope="props">
                            <div style="width: 150px;" v-html="props.row.category.name"/>
                        </template>
                        <template slot="gravity" slot-scope="props">
                            <div style="width: 80px;">{{ props.row.gravity.gravity }}</div>
                        </template>
                        <template slot="prescriptive" slot-scope="props">
                            <div style="width: 100px;">{{ props.row.gravity.prescriptive_period }}</div>
                        </template>
                        <template slot="description" slot-scope="props">
                            <div v-html="props.row.description"/>
                        </template>
                        <template slot="first_instance" slot-scope="props">
                        <div style="width: auto;" v-html="(props.row.instance && props.row.instance.first_instance) ? props.row.instance.first_instance.name : ''"/>
                    </template>
                    <template slot="second_instance" slot-scope="props">
                    <div style="width: auto;" v-html="(props.row.instance && props.row.instance.second_instance) ? props.row.instance.second_instance.name : ''"/>
                    </template>
                    <template slot="third_instance" slot-scope="props">
                    	<div style="width: auto;" v-html="(props.row.instance && props.row.instance.third_instance) ? props.row.instance.third_instance.name : ''"/>
                    </template>
                    <template slot="fourth_instance" slot-scope="props">
                    	<div style="width: auto;" v-html="(props.row.instance && props.row.instance.fourth_instance) ? props.row.instance.fourth_instance.name : ''"/>
                    </template>
                    <template slot="fifth_instance" slot-scope="props">
                    	<div style="width: auto;" v-html="(props.row.instance && props.row.instance.fifth_instance) ? props.row.instance.fifth_instance.name : ''"/>
                    </template>
                    <template slot="sixth_instance" slot-scope="props">
                    	<div style="width: auto;" v-html="(props.row.instance && props.row.instance.sixth_instance) ? props.row.instance.sixth_instance.name : ''"/>
                    </template>
                    <template slot="seventh_instance" slot-scope="props">
                    	<div style="width: auto;" v-html="(props.row.instance && props.row.instance.seventh_instance) ? props.row.instance.seventh_instance.name : ''"/>
                    </template>
                </v-client-table>
            </div>
            <!-- End Searching -->

            <tab-group class="custom-tab tabs" v-if="showCOCTabs">
                <tab-element
                    v-for="(tab_title, index) in tab_titles"
                    :key="index" :name="tab_title"
                >
                    <category-attendance-list
                        :categoriesProps = "category(
                            1,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Attendance'"
                    />
                    <category-cwd-list
                        :categoriesProps = "category(
                            2,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'CWD Policy'"
                    />
                    <category-negligence-list
                        :categoriesProps = "category(
                            3,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Negligence'"
                    />
                    <category-general-house-rules-list
                        :categoriesProps = "category(
                            4,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'General House Rules'"
                    />
                    <category-fraud-list
                        :categoriesProps = "category(
                            5,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Fraud'"
                    />
                    <category-misconduct-list
                        :categoriesProps = "category(
                            6,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Misconduct'"
                    />
                    <category-poor-work-ethics-list
                        :categoriesProps = "category(
                            7,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Poor Work Ethics'"
                    />
                    <category-information-list
                        :categoriesProps = "category(
                            8,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Information Security'"
                    />
                    <category-zero-list
                        :categoriesProps = "category(
                            9,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Zero Tolerance Policy'"
                    />
                    <category-compliance-list
                        :categoriesProps = "category(
                            10,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Compliance Breach'"
                    />
                    <category-general-company-list
                        :categoriesProps = "category(
                            11,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'General Company Rules and Regulations'"
                    />
                    <category-negligence-pip-list
                        :categoriesProps = "category(
                            12,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Negligence PIP'"
                    />
                    <category-lms-list
                        :categoriesProps = "category(
                            13,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'LMS Guidelines'"
                    />
                    <category-corrective-list
                        :categoriesProps = "category(
                            14,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Corrective Policy'"
                    />
                    <category-hard-return-list
                        :categoriesProps = "category(
                            16,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Hard Return'"
                    />
                    <category-warranted-escalation-list
                        :categoriesProps = "category(
                            17,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Warranted Escalation'"
                    />
                    <category-attendance-supervisor-list
                        :categoriesProps = "category(
                            18,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Attendance Supervisor And Manager'"
                    />
                    <category-attendance-trainer-list
                        :categoriesProps = "category(
                            19,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Attendance Trainer'"
                    />
                    <category-credit-repair-offer-list
                        :categoriesProps = "category(
                            20,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Credit Repair Offer'"
                    />
                    <category-credit-repair-offer-sup-list
                        :categoriesProps = "category(
                            21,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Credit Repair Offer Supervisor'"
                    />
                    <category-pre-underwriting-list
                        :categoriesProps = "category(
                            22,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Pre-Underwriting'"
                    />

                    <category-operations-system-list
                        :categoriesProps = "category(
                            23,
                            rulesList(index)
                        )"
                        v-if="tab_title === 'Operations Processes System and Guidelines'"
                    />
                </tab-element>
            </tab-group>

            <modal v-if="openModal" @close="openModal = false">
                <h3 slot="header">{{ headerName }}</h3>
                <search-edit v-if="search_all_data" :searchAllProps="search_all_data" slot="body" />
                <add-edit v-else :offenseProps="offense_data" slot="body" />
            </modal>
        </div>
    </div>
</template>
<script>
    import categoryAttendanceList from '@/modules/admin/settings/coc/offense/attendanceList.vue'
    import categoryCwdList from '@/modules/admin/settings/coc/offense/cwdList.vue'
    import categoryNegligenceList from '@/modules/admin/settings/coc/offense/negligenceList.vue'
    import categoryGeneralHouseRulesList from '@/modules/admin/settings/coc/offense/generalHouseRulesList.vue'
    import categoryFraudList from '@/modules/admin/settings/coc/offense/fraudList.vue'
    import categoryMisconductList from '@/modules/admin/settings/coc/offense/misconductList.vue'
    import categoryPoorWorkEthicsList from '@/modules/admin/settings/coc/offense/poorWorkEthicsList.vue'
    import categoryInformationList from '@/modules/admin/settings/coc/offense/informationList.vue'
    import categoryZeroList from '@/modules/admin/settings/coc/offense/zeroList.vue'
    import categoryComplianceList from '@/modules/admin/settings/coc/offense/complianceList.vue'
    import categoryGeneralCompanyList from '@/modules/admin/settings/coc/offense/generalCompanyList.vue'
    import categoryNegligencePipList from '@/modules/admin/settings/coc/offense/negligencePipList.vue'
    import categoryLmsList from '@/modules/admin/settings/coc/offense/lmsList.vue'
    import categoryCorrectiveList from '@/modules/admin/settings/coc/offense/correctiveList.vue'
    import categoryHardReturnList from '@/modules/admin/settings/coc/offense/hardReturnList.vue'
    import categoryWarrantedEscalationList from '@/modules/admin/settings/coc/offense/warrantedEscalationList.vue'
    import categoryAttendanceSupervisorList from '@/modules/admin/settings/coc/offense/attendanceSupervisorList.vue'
    import categoryAttendanceTrainerList from '@/modules/admin/settings/coc/offense/attendanceTrainerList.vue'
    import categoryCreditRepairOfferList from '@/modules/admin/settings/coc/offense/creditRepairOfferList.vue'
    import categoryCreditRepairOfferSupList from '@/modules/admin/settings/coc/offense/creditRepairOfferSupList.vue'
    import categoryPreUnderwritingList from '@/modules/admin/settings/coc/offense/preUnderwritingList.vue'
    import categoryOperationsSystemList from '@/modules/admin/settings/coc/offense/operationsSystemList.vue'
    import addEdit from '@/modules/admin/settings/coc/offense/addEdit.vue'
    import searchEdit from '@/modules/admin/settings/coc/offense/searchEdit.vue'
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
        'general_company_rules_and_regulations': 'General Company Rules and Regulations',
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
            categoryAttendanceList,
            categoryCwdList,
            categoryNegligenceList,
            categoryGeneralHouseRulesList,
            categoryFraudList,
            categoryMisconductList,
            categoryPoorWorkEthicsList,
            categoryInformationList,
            categoryZeroList,
            categoryComplianceList,
            categoryGeneralCompanyList,
            categoryNegligencePipList,
            categoryLmsList,
            categoryCorrectiveList,
            categoryHardReturnList,
            categoryWarrantedEscalationList,
            categoryAttendanceSupervisorList,
            categoryAttendanceTrainerList,
            categoryCreditRepairOfferList,
            categoryCreditRepairOfferSupList,
            categoryPreUnderwritingList,
            categoryOperationsSystemList,
            addEdit,
            searchEdit,
            TabElement,
            TabGroup
        },
        props: {
            labels: {
                type: Object,
                    default() {
                        return {
                            add: 'Add Offense',
                            edit: 'Edit Offense',
                            editBtn: 'Edit',
                            deleteBtn: 'Archive'
                    }
                }
            },
        },
        data: function () {
            return {
                rule: this.$ep.rule,
                openModal: false,
                headerName: 'Add Offense',
                category_props: {
                    category_id: '',
                    category_rule: '',
                },
                searchKey: '',
                showCOCTabs: true,
                showSearchAll: false,
                columns: this.columns,
                rows:  [],
                options: {
                     headings: {
                        'prescriptive': 'Prescriptive Period',
                    },
                    sortable: [],
                    filterable: false,
                },
                search_all_data: {
                    off_description: '',
                    off_offense: '',
                    off_gravity: '',
                    off_prescriptive_period: '',
                    category_id: '',
                    category_name: '',
                    first_instance: '',
                    second_instance: '',
                    third_instance: '',
                    fourth_instance: '',
                    fifth_instance: '',
                    sixth_instance: '',
                    seventh_instance: '',
                },
                categoryTitle: '',
                categoryName: '',
                categoryID: '',
            }
        },
        watch: {
            '$route.hash'() {
                if (this.$route.hash == '#attendance') {
                    this.$bus.$emit('categoryList1');
                } else if (this.$route.hash == '#cwd-policy') {
                    this.$bus.$emit('categoryList2');
                } else if (this.$route.hash == '#negligence') {
                    this.$bus.$emit('categoryList3');
                } else if (this.$route.hash == '#general-house-rules') {
                    this.$bus.$emit('categoryList4');
                } else if (this.$route.hash == '#fraud') {
                    this.$bus.$emit('categoryList5');
                } else if (this.$route.hash == '#misconduct') {
                    this.$bus.$emit('categoryList6');
                } else if (this.$route.hash == '#poor-work-ethics') {
                    this.$bus.$emit('categoryList7');
                } else if (this.$route.hash == '#information-security') {
                    this.$bus.$emit('categoryList8');
                } else if (this.$route.hash == '#zero-tolerance-policy') {
                    this.$bus.$emit('categoryList9');
                } else if (this.$route.hash == '#compliance-breach') {
                    this.$bus.$emit('categoryList10');
                } else if (this.$route.hash == '#general-company-rules-and-regulations') {
                    this.$bus.$emit('categoryList11');
                } else if (this.$route.hash == '#negligence-pip') {
                    this.$bus.$emit('categoryList12');
                } else if (this.$route.hash == '#lms-guidelines') {
                    this.$bus.$emit('categoryList13');
                } else if (this.$route.hash == '#corrective-policy') {
                    this.$bus.$emit('categoryList14');
                } else if (this.$route.hash == '#hard-return') { /* Note: Ignore  categoryList15 for Attendance Points*/
                    this.$bus.$emit('categoryList16');
                } else if (this.$route.hash == '#warranted-escalation') {
                    this.$bus.$emit('categoryList17');
                } else if (this.$route.hash == '#attendance-supervisor-and-manager') {
                    this.$bus.$emit('categoryList18');
                } else if (this.$route.hash == '#attendance-trainer') {
                    this.$bus.$emit('categoryList19');
                } else if (this.$route.hash == '#credit-repair-offer') {
                    this.$bus.$emit('categoryList20');
                } else if (this.$route.hash == '#credit-repair-offer-supervisor') {
                    this.$bus.$emit('categoryList21');
                } else if (this.$route.hash == '#pre-underwriting') {
                    this.$bus.$emit('categoryList22');
                } else if (this.$route.hash == '#operations-processes-system-and-guidelines') {
                    this.$bus.$emit('categoryList23');
                }
            },
        },
        created() {
            this.tab_titles = TAB_TITLES;
            this.$bus.$on('init_modal', (param) => {
                this.openModal = param;
            });

        this.columns = (this.rule.code_of_conduct.child_rules.attendance.child_rules.edit.is_enable
        || this.rule.code_of_conduct.child_rules.attendance.child_rules.archive.is_enable) ?
                [
                    'actions',
                    'category',
                    'offense',
                    'gravity',
                    'prescriptive',
                    'description',
                    'first_instance',
                    'second_instance',
                    'third_instance',
                    'fourth_instance',
                    'fifth_instance',
                    'sixth_instance',
                    'seventh_instance'
                ] :
                [
                    'category',
                    'offense',
                    'gravity',
                    'prescriptive',
                    'description',
                    'first_instance',
                    'second_instance',
                    'third_instance',
                    'fourth_instance',
                    'fifth_instance',
                    'sixth_instance',
                    'seventh_instance'
                ];
        },
        methods: {
            rulesList(item) {
                return this.rule['code_of_conduct']['child_rules'][item]['child_rules']
            },
            addEditRow: function () {
                this.openModal = true;
                this.offense_data.off_description = '';
                this.offense_data.off_offense = '';
                this.offense_data.off_gravity = '';
                this.offense_data.prescriptive_period = '';
            },
            category: function (id, rule) {
                return {
                    category_id: id,
                    category_rule: rule,
                }
            },
            eventSearch: function (keySearchFound) {
                if (keySearchFound) {
                    this.isLoading = true
                    this.searchKey = keySearchFound
                    this.$constants.Admin_API.get("/search/coc/"+keySearchFound)
                    .then(response => {
                        this.rows = response.data;
                        this.showCOCTabs = false,
                        this.showSearchAll = true,
                        this.isLoading = false
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                } else {
                    location.reload();
                }
            },
            editRow: function (props_row) {
                this.openModal = true;
                if(props_row){
                    this.headerName = "Search All Edit Form";
                    this.search_all_data.off_id = props_row.id;
                    this.search_all_data.category_name = props_row.category.name;
                    this.search_all_data.category_id = props_row.category_id;
                    this.search_all_data.off_description = props_row.description;
                    this.search_all_data.off_offense = props_row.offense;
                    this.search_all_data.off_gravity = props_row.gravity.gravity + " - " +
                                                    props_row.gravity.prescriptive_period;
                    if (props_row.instance) {
                        this.search_all_data.first_instance = props_row.instance.first_instance_coc_id
                        this.search_all_data.second_instance = props_row.instance.second_instance_coc_id
                        this.search_all_data.third_instance = props_row.instance.third_instance_coc_id
                        this.search_all_data.fourth_instance = props_row.instance.fourth_instance_coc_id
                        this.search_all_data.fifth_instance = props_row.instance.fifth_instance_coc_id
                        this.search_all_data.sixth_instance = props_row.instance.sixth_instance_coc_id
                        this.search_all_data.seventh_instance = props_row.instance.seventh_instance_coc_id
                    }
                }
            },
            deleteRow: function (props_row) {
                this.$swal({
                    html: 'Are you sure you want to delete this offense: <br> <b>' + props_row.offense + '</b> ?',
                    text: "Once deleted, you will not be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                })
                    .then((result) => {
                        if (result.value) {
                            this.$constants.Coc_API.delete("/offense/" + props_row.id)
                                .then(response => {
                                    response.data;
                                    location.reload();
                                })
                                .catch(error => {
                                    this.globalErrorHandling(error);
                                });
                            this.$success('Offense: ' + props_row.offense + ' has been deleted!')
                        } else if (result.dismiss === this.$swal.DismissReason.cancel) {
                            this.$failure('Offense: ' + props_row.offense +' has been cancelled')
                        }
                    })
            },
        }
    }

</script>
