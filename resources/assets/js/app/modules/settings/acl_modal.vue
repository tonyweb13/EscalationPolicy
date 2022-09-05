<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <the-label label="Rule Name" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <input type="text" v-model="acl_rule.rule_name" class="form-control" label="text"/>
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Parent Rule" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="acl_rule.rule_parent"
                            :options="rule_parent"
                            label="text"
                        />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Label" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <input type="text" v-model="acl_rule.label" class="form-control" label="text"/>
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Description" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <input type="text" v-model="acl_rule.description" class="form-control" label="text"/>
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <button-save />
            </div>
        </the-form>
    </ValidationObserver>
</template>

<script>
'use strict'

export default {
    props: {
        acl_rule_props: {
            rule_name: '',
            rule_parent_id: '',
            rule_parent_name: '',
            label: '',
            description: '',
        },
    },

    data: function () {
        return {
            acl_rule: {
                rule_name: '',
                rule_parent_id: '',
                rule_parent_name: '',
                label: '',
                description: '',
                created_at: '',
                updated_at: '',
            },
            rule_parent: [],
        };
    },

    created(){
        this.eventAclRuleDropdown();
        if (this.acl_rule_props.id) {
            let acl_rule = {
                text: this.acl_rule_props.rule_parent_name,
                value: this.acl_rule_props.rule_parent_id,
            };
            this.acl_rule.rule_parent = this.acl_rule_props.rule_parent_name ? acl_rule  : '';
            this.acl_rule.rule_name = this.acl_rule_props.rule_name;
            this.acl_rule.rule_parent_id = this.acl_rule_props.rule_parent_id;
            this.acl_rule.label = this.acl_rule_props.label;
            this.acl_rule.description = this.acl_rule_props.description;
        }
    },

    computed: { },

    methods: {
        eventUpdate() {
            let getAxios = '';
            if (this.acl_rule_props.id) {
                getAxios = this.$constants.Settings_ACL_API.post('/acl_rule/create_acl_rule/'+this.acl_rule_props.id, {
                        params: {
                            rule_name: this.acl_rule.rule_name,
                            rule_parent: this.acl_rule.rule_parent,
                            label: this.acl_rule.label,
                            description: this.acl_rule.description,
                        },
                });
                getAxios.then(response => {
                    this.$bus.$emit('init_modal', false);
                    this.$swal({
                        html: 'Successfully added',
                        type: "success",
                        confirmButtonText: 'OK',
                    });
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            } else {
                getAxios =  this.$constants.Settings_ACL_API.post('/acl_rule/create_acl_rule', {
                    params: {
                        rule_name: this.acl_rule.rule_name,
                        rule_parent: this.acl_rule.rule_parent,
                        label: this.acl_rule.label,
                        description: this.acl_rule.description,
                    },
                });
                getAxios.then(response => {
                    this.$bus.$emit('init_modal', false);
                    this.$swal({
                        html: 'Successfully added',
                        type: "success",
                        confirmButtonText: 'OK',
                    });
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        },

        eventAclRuleDropdown(){
            this.$constants.Settings_ACL_API.get("/acl_rule/dropdown_rule_name")
            .then(response => {
                this.rule_parent = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

    },
}
</script>
