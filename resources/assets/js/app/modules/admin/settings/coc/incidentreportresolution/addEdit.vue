<template>
    <the-form :asterisk="true" @submitForm="eventUpdate">
        <div slot="form-body">
                <input-text
                    v-model="corrective.name"
                    label="Name"
                    :asterisk="true"
                />
                <span class="style-red" v-if="error_data">{{ error_data }}</span>
                <br><br>
            <button-save />
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            correctiveProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                error_data: '',
                corrective: {
                    name: '',
                },
            };
        },
        created(){
            if(this.correctiveProps.cor_id){
                this.corrective.name = this.correctiveProps.cor_corrective;
            } else {
                this.corrective.name = '';
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.corrective.name;
            },
        },
        methods: {
            eventUpdate() {
                this.laddabtn.start();
                let corrective = this.corrective;
                let getAxios = '';

                if(this.correctiveProps.cor_id){

                            getAxios = this.$constants.Coc_API.get("/incidentreportresolution/"+this.correctiveProps.cor_id, {
                                params: {
                                    name: corrective.name,
                                }
                            });

                } else {
                    getAxios = this.$constants.Coc_API.get("/incidentreportresolution/create", {
                        params: {
                            name: corrective.name,
                        }
                    });
                }

                getAxios.then(response => {
                            this.corrective = response.data;
                            this.$bus.$emit('init_modal', false);
                            this.laddabtn.stop();
                            this.$success('Successfully Saved!')
                            this.$bus.$emit('updateList');
                            this.$bus.$emit('updateDashboard');
                })
                .catch(error => {
                    /* using ValidatorController */
                    this.error_data = String(error.response.data.message).replace(/"name"|:|\\|\[|\]|\"|{|}/gi, '');

                    if (this.error_data) {
                        this.laddabtn.stop();
                    } else {
                        this.globalErrorHandling(error);
                    }
                });
            },
        },
    }
</script>
