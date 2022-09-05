<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                    <the-label label="Provision" :asterisk="true">
                        <vSelect
                            v-model="offense.category"
                            :disabled="true"
                            label="text"
                        />
                    </the-label>

                    <validation-provider rules="required" v-slot="{ errors }">
                        <input-textarea v-model="offense.offense" label="Offense" :asterisk="true"/>
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>

                    <the-label label="Gravity" :asterisk="true">
                        <validation-provider rules="required" v-slot="{ errors }">
                            <vSelect
                                v-model="offense.gravity"
                                :options="selectedGravity"
                                label="text"
                                :class="{'is-invalid': !!errors.length}"
                            />
                            <span class="style-red">{{ errors[0] }}</span>
                        </validation-provider>
                    </the-label>

                    <the-label label="IR Description">
                        <vue-editor v-model="offense.description" :editorToolbar="customToolbar"/>
                    </the-label>

                    <validation-provider rules="required" v-slot="{ errors }">
                        <the-label label="First Instance" :asterisk="true">
                            <vSelect
                                v-model="offense.first_instance"
                                :options="selectedIRR"
                                label="text"
                            />
                        </the-label>
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>

                    <the-label label="Second Instance" :asterisk="false">
                        <vSelect
                            v-model="offense.second_instance"
                            :options="selectedIRR"
                            label="text"
                        />
                    </the-label>

                    <the-label label="Third Instance" :asterisk="false">
                        <vSelect
                            v-model="offense.third_instance"
                            :options="selectedIRR"
                            label="text"
                        />
                    </the-label>

                    <the-label label="Fourth Instance" :asterisk="false">
                        <vSelect
                            v-model="offense.fourth_instance"
                            :options="selectedIRR"
                            label="text"
                        />
                    </the-label>

                    <the-label label="Fifth Instance" :asterisk="false">
                        <vSelect
                            v-model="offense.fifth_instance"
                            :options="selectedIRR"
                            label="text"
                        />
                    </the-label>

                    <the-label label="Sixth Instance" :asterisk="false">
                        <vSelect
                            v-model="offense.sixth_instance"
                            :options="selectedIRR"
                            label="text"
                        />
                    </the-label>

                    <the-label label="Seventh Instance" :asterisk="false">
                        <vSelect
                            v-model="offense.seventh_instance"
                            :options="selectedIRR"
                            label="text"
                        />
                    </the-label>
                <button-save />
            </div>
        </the-form>
    </ValidationObserver>
</template>
<script>
    export default {
        props: {
            searchAllProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                offense: {
                    offense: '',
                    category: '',
                    description: '',
                    gravity: '',
                    first_instance: '',
                    second_instance: '',
                    third_instance: '',
                    fourth_instance: '',
                    fifth_instance: '',
                    sixth_instance: '',
                    seventh_instance: '',
                },
                category: {
                    text: '',
                    value: '',
                    selected: '',
                },
                offenseCategory: false,
                selectedIRR: [],
                selectedGravity: this.eventGravity(),
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
            };
        },
        created(){
            this.offense.category = {
                text: this.searchAllProps.category_name,
                value: this.searchAllProps.category_id,
                selected: "selected",
            }
            this.eventInstance()
            if(this.searchAllProps.off_id){
                this.offense.description = this.searchAllProps.off_description;
                this.offense.offense = this.searchAllProps.off_offense;
                this.offense.gravity = this.searchAllProps.off_gravity;
            } else {
                this.offense.offense = null;
                this.offense.gravity = null;
                this.offense.description = '';
                this.offense.first_instance = null;
                this.offense.second_instance = '';
                this.offense.third_instance = '';
                this.offense.fourth_instance = '';
                this.offense.fifth_instance = '';
                this.offense.sixth_instance = '';
                this.offense.seventh_instance = '';
            }
            if (this.searchAllProps.category_id == 16) {
                this.offense.category = {
                    text: "Hard Return",
                    value: 16,
                    selected: "selected",
                }
            }
            if (this.searchAllProps.category_id == 17) {
                this.offense.category = {
                    text: "Warranted Escalation",
                    value: 17,
                    selected: "selected",
                }
            }
            if(this.searchAllProps.first_instance){

            } else {
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.offense.category && this.offense.offense && this.offense.gravity && this.offense.first_instance;
            }
        },
        methods: {
            eventGravity(){
                this.$constants.Coc_API.get("/dropdown/gravity")
                    .then(response => {
                        this.selectedGravity = response.data;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            eventInstance(){
                this.$constants.Coc_API.get("/dropdown/irr")
                    .then(response => {
                    this.selectedIRR = response.data;
                    for (var i = 0; i < this.selectedIRR.length; i++) {
                        if(this.searchAllProps){
                            if (this.selectedIRR[i].value == this.searchAllProps.first_instance) {
                                this.offense.first_instance = this.selectedIRR[i]
                            }
                            if (this.selectedIRR[i].value == this.searchAllProps.second_instance) {
                                this.offense.second_instance = this.selectedIRR[i]
                            }
                            if (this.selectedIRR[i].value == this.searchAllProps.third_instance) {
                                this.offense.third_instance = this.selectedIRR[i]
                            }
                            if (this.selectedIRR[i].value == this.searchAllProps.fourth_instance) {
                                this.offense.fourth_instance = this.selectedIRR[i]
                            }
                            if (this.selectedIRR[i].value == this.searchAllProps.fifth_instance) {
                                this.offense.fifth_instance = this.selectedIRR[i]
                            }
                            if (this.selectedIRR[i].value == this.searchAllProps.sixth_instance) {
                                this.offense.sixth_instance = this.selectedIRR[i]
                            }
                            if (this.selectedIRR[i].value == this.searchAllProps.seventh_instance) {
                                this.offense.seventh_instance = this.selectedIRR[i]
                            }
                        } else {
                            this.offense.first_instance = '';
                            this.offense.second_instance = '';
                            this.offense.third_instance = '';
                            this.offense.fourth_instance = '';
                            this.offense.fifth_instance = '';
                            this.offense.sixth_instance = '';
                            this.offense.seventh_instance = '';
                        }
                    }
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventUpdate() {
                // this.$validator.validateAll().then((result) => {
                //     if (result) {
                        this.laddabtn.start();
                        let offense = this.offense;
                        let getAxios = '';

                        if(this.searchAllProps.off_id){
                            getAxios = this.$constants.Coc_API.get("/offense/"+this.searchAllProps.off_id, {
                                params: {
                                    offense: offense.offense,
                                    category_id: offense.category.value,
                                    gravity_id: offense.gravity.value,
                                    description: offense.description,
                                    first_instance_coc_id: offense.first_instance ? offense.first_instance.value : '',
                                    second_instance_coc_id: offense.second_instance ? offense.second_instance.value : '',
                                    third_instance_coc_id: offense.third_instance ? offense.third_instance.value : '',
                                    fourth_instance_coc_id: offense.fourth_instance ? offense.fourth_instance.value : '',
                                    fifth_instance_coc_id: offense.fifth_instance ? offense.fifth_instance.value : '',
                                    sixth_instance_coc_id: offense.sixth_instance ? offense.sixth_instance.value : '',
                                    seventh_instance_coc_id: offense.seventh_instance ? offense.seventh_instance.value : '',
                                }
                            });

                        } else {

                            getAxios = this.$constants.Coc_API.get("/offense/create", {
                                params: {
                                    offense: offense.offense,
                                    category_id: offense.category.value,
                                    gravity_id: offense.gravity.value,
                                    description: offense.description,
                                    first_instance_coc_id: offense.first_instance ? offense.first_instance.value : '',
                                    second_instance_coc_id: offense.second_instance ? offense.second_instance.value : '',
                                    third_instance_coc_id: offense.third_instance ? offense.third_instance.value : '',
                                    fourth_instance_coc_id: offense.fourth_instance ? offense.fourth_instance.value : '',
                                    fifth_instance_coc_id: offense.fifth_instance ? offense.fifth_instance.value : '',
                                    sixth_instance_coc_id: offense.sixth_instance ? offense.sixth_instance.value : '',
                                    seventh_instance_coc_id: offense.seventh_instance ? offense.seventh_instance.value : '',
                                }
                            });
                        }

                        getAxios.then(response => {
                            this.laddabtn.stop();
                            this.$bus.$emit('init_modal', false);
                            this.offense = response.data;
                            this.$success('Successfully Saved!')
                            this.$bus.$emit('updateList');
                            this.$bus.$emit('updateDashboard');
                            location.reload();
                        })
                        .catch(error => {
                            this.globalErrorHandling(error);
                        });
                //     }
                // });
            },
        },
    }
</script>
