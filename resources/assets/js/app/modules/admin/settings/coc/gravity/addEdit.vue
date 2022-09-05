<template>

    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <validation-provider rules="required" v-slot="{ errors }">
                    <input-text
                        v-model="gravity.fields"
                        label="Fields"
                        :asterisk="true"
                    />
                    <span class="style-red">{{ errors[0] }}</span>
                </validation-provider>
                <the-label label="Gravity" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect v-model="gravity.gravity" :options="selectedGravity" label="text" required/>
                        <span class="style-red">{{ errors[0] }}</span>
                    </validation-provider>
                </the-label>

                <input-textarea v-model="gravity.offense_description" label="IR Description"/>
                <button-save />
            </div>
        </the-form>
    </ValidationObserver>
</template>
<script>
    export default {
        props: {
            gravityProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                gravity: {
                    fields: '',
                    gravity: '',
                    offense_description: '',
                },
                selectedGravity:[],
            };
        },
        created(){
            this.eventGravity();

            if(this.gravityProps.grav_id){
                this.gravity.fields = this.gravityProps.grav_fields;
                this.gravity.gravity = this.gravityProps.grav_gravity;
                this.gravity.offense_description = this.gravityProps.grav_desc;
            } else {
                this.gravity.fields = null;
                this.gravity.gravity = null;
                this.gravity.offense_description = '';
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.gravity.fields && this.gravity.gravity;
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
            eventUpdate() {

                this.laddabtn.start();
                let gravity = this.gravity;
                let getAxios = '';

                if(this.gravityProps.grav_id){

                    getAxios = this.$constants.Coc_API.get("/gravity/"+this.gravityProps.grav_id, {
                        params: {
                            fields: gravity.fields,
                            gravity_id: gravity.gravity.value,
                            description: gravity.offense_description,
                        }
                    });

                } else {

                    getAxios = this.$constants.Coc_API.get("/gravity/create", {
                        params: {
                            fields: gravity.fields,
                            gravity_id: gravity.gravity.value,
                            description: gravity.offense_description,
                        }
                    });
                }

                getAxios.then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.gravity = response.data;
                    this.$success('Successfully Saved!')
                    this.$bus.$emit('updateList');
                    this.$bus.$emit('updateDashboard');
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
