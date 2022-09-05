<template>
    <div class="row">
        <br>
        <button v-if="!Object.keys(rules).length" type="button" class="btn btn-primary" @click="applyRules">
            Apply Rules
        </button>
        <br>
        <div v-for="(rule, index_rule) in rules">
            <div class="col-lg-2">
                <div class="switch">
                    <div class="onoffswitch">
                        <input
                            type="checkbox"
                            :checked="rule.is_enable"
                            v-model="rule.is_enable"
                            class="onoffswitch-checkbox"
                            :id="`${index_rule}`"
                        >
                        <label
                            class="onoffswitch-label"
                            :for="`${index_rule}`"
                            :id="`${index_rule}_label`"
                        >
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div>
                <span v-if="rule.child_rules !== undefined" class="acllabels">
                    <a :href="`#c_${index_rule}`" data-toggle="collapse">
                        {{ index_rule }}
                    </a>
                </span>
                <span class="acllabels" v-else>{{ index_rule }}</span>
                {{ showDescription(index_rule)}}
            </div>
            <div class="row">
                <div class="col-lg-12 collapse" :class="{in:rule.is_enable}" :id="`c_${index_rule}`">
                    <div
                        v-if="rule.child_rules !== undefined"
                        v-for="(child, index_child) in rule.child_rules"
                    >
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input
                                            type="checkbox"
                                            :checked="child.is_enable"
                                            v-model="child.is_enable"
                                            class="onoffswitch-checkbox"
                                            :id="`${index_rule}_${index_child}`"
                                        >
                                        <label
                                            class="onoffswitch-label"
                                            :for="`${index_rule}_${index_child}`"
                                            :id="`${index_rule}_${index_child}_label`"
                                        >
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span v-if="child.child_rules !== undefined" class="acllabels">
                                    <a :href="`#c_${index_rule}_${index_child}`" data-toggle="collapse">
                                        {{ index_child }}
                                    </a>
                                </span>
                                <span class="acllabels" v-else>{{ index_child }}</span>
                                {{ showDescription(index_child)}}
                            </div>
                            <div class="row">
                                <div
                                    class="col-lg-12 collapse"
                                    :class="{in:child.is_enable}"
                                    :id="`c_${index_rule}_${index_child}`"
                                >
                                    <div
                                        v-if="child.child_rules !== undefined"
                                        v-for="(desc, index_desc) in child.child_rules"
                                    >
                                        <div class="col-lg-12">
                                            <div class="col-lg-2">
                                                <div class="switch">
                                                    <div class="onoffswitch">
                                                        <input
                                                            type="checkbox"
                                                            :checked="desc.is_enable"
                                                            v-model="desc.is_enable"
                                                            class="onoffswitch-checkbox"
                                                            :id="`${index_child}_${index_desc}`"
                                                        >
                                                        <label
                                                            class="onoffswitch-label"
                                                            :for="`${index_child}_${index_desc}`"
                                                            :id="`${index_child}_${index_desc}_label`"
                                                        >
                                                            <span class="onoffswitch-inner"></span>
                                                            <span class="onoffswitch-switch"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <span v-if="desc.child_rules !== undefined" class="acllabels">
                                                    <a
                                                        :href="`#c_${index_rule}_${index_child}_${index_desc}`"
                                                        data-toggle="collapse"
                                                    >
                                                        {{ index_desc }}
                                                    </a>
                                                </span>
                                                <span class="acllabels" v-else>{{ index_desc }}</span>
                                                {{ showDescription(index_desc)}}
                                            </div>
                                            <div class="row">
                                                <div
                                                    class="col-lg-12 collapse"
                                                    :class="{in:desc.is_enable}"
                                                    :id="`c_${index_rule}_${index_child}_${index_desc}`"
                                                >
                                                    <div
                                                        v-if="desc.child_rules !== undefined"
                                                        v-for="(desc_1, index_desc_1) in desc.child_rules"
                                                    >
                                                        <div class="col-lg-12">
                                                            <div class="col-lg-2">
                                                                <div class="switch">
                                                                    <div class="onoffswitch">
                                                                        <input
                                                                            type="checkbox"
                                                                            :checked="desc_1.is_enable"
                                                                            v-model="desc_1.is_enable"
                                                                            class="onoffswitch-checkbox"
                                                                            :id="`${index_desc}_${index_desc_1}`"
                                                                        >
                                                                        <label
                                                                            :id="`${index_desc}_${index_desc_1}_label`"
                                                                            :for="`${index_desc}_${index_desc_1}`"
                                                                            class="onoffswitch-label"
                                                                        >
                                                                            <span class="onoffswitch-inner"/>
                                                                            <span class="onoffswitch-switch"/>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <span class="acllabels">{{ index_desc_1 }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-field">
            <button v-if="Object.keys(rules).length" type="button" class="btn btn-primary" @click="saveAcl">
                SAVE
            </button>
            <button v-if="Object.keys(rules).length" type="button" class="btn btn-danger" @click="removeRule">
                Remove Rules
            </button>
        </div>
    </div>
</template>

<style>
.acllabels {
    text-transform: uppercase;
    font-weight: bold;
}
</style>

<script>
'use strict'

export default {
    name: "SettingsAclRules",

    props: {
        user_fields: {},
    },

    data() {
        return {
            acl: [],
            rules: [],
            selected_rule_parent_name: '',
        }
    },

    watch: {
        user_fields() {
            this.rules = this.user_fields.rules
        }
    },

    methods: {
        showDescription(index) {
            let rules = this.acl.find(rule => rule.rule_name === index)

            return (rules !== undefined) ? `( ${rules.description} )` : ''
        },

        applyRules() {
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.post('acl_rule/apply/' + this.$props.user_fields.employee_no);
            getAxios.then(response => {
                this.$swal({
                    html: 'Successfully added',
                    type: "success",
                    showConfirmButton: false,
                    timer: 1000,
                });
                this.$bus.$emit('init_modal', false);
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        removeRule() {
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.post('/acl_rule/remove/'+this.$props.user_fields.employee_no);
            getAxios.then(response => {
                this.$swal({
                    html: 'Successfully Remove',
                    type: "success",
                    showConfirmButton: false,
                    timer: 1000,
                });
                this.$bus.$emit('init_modal', false);
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        saveAcl() {
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.post('/acl_rule/updateOrCreateUserRoleRules/'+this.$props.user_fields.id, {
                    params: {
                        employee_no: this.$props.user_fields.employee_no,
                        rules: this.rules,
                        role: this.$props.user_fields.role,
                    },
            });
            getAxios.then(response => {
                this.$swal({
                    html: 'Successfully added',
                    type: "success",
                    showConfirmButton: false,
                    timer: 1000,
                });
                this.$bus.$emit('init_modal', false);
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

        getRules() {
            let getAxios = '';
            getAxios = this.$constants.Settings_ACL_API.get('/acl_rule/rules/'+this.$props.user_fields.id);
            getAxios.then(response => {
                this.rules = response.data;
                this.isLoading = false
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
    },

    mounted() {
        this.getRules();
        this.$constants.Settings_ACL_API.get('/acl_rule/user_acl/')
        .then(response => {
            this.acl = result
        })
        .catch(error => {
            this.globalErrorHandling(error);
        });
    }
}
</script>
