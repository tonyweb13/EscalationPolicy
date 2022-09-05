<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <the-label label="Gravity" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect v-model="gravity.gravity" :options="selectedGravity" label="text" required/>
                        <span class="style-red">{{ errors[0] }}</span>
                    </validation-provider>
                </the-label>
                <the-label label="1st Occurrence" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect v-model="gravity.first_irr" :options="selectedOccurrence" label="text" required/>
                        <span class="style-red">{{ errors[0] }}</span>
                    </validation-provider>
                </the-label>
                <the-label label="2nd Occurrence">
                    <vSelect v-model="gravity.second_irr" :options="selectedOccurrence" label="text"/>
                </the-label>
                <the-label label="3rd Occurrence">
                    <vSelect v-model="gravity.third_irr" :options="selectedOccurrence" label="text"/>
                </the-label>
                <the-label label="4th Occurrence">
                    <vSelect v-model="gravity.fourth_irr" :options="selectedOccurrence" label="text"/>
                </the-label>
                <the-label label="5th Occurrence">
                    <vSelect v-model="gravity.fifth_irr" :options="selectedOccurrence" label="text"/>
                </the-label>
                <the-label label="6th Occurrence">
                    <vSelect v-model="gravity.sixth_irr" :options="selectedOccurrence" label="text"/>
                </the-label>
                <the-label label="7th Occurrence">
                    <vSelect v-model="gravity.seventh_irr" :options="selectedOccurrence" label="text"/>
                </the-label>
                <button-save  />
            </div>
        </the-form>
    </ValidationObserver>
</template>
<script>
    export default {
        props: {
            gravityOccurrenceProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                gravity: {
                    gravity: '',
                    first_irr: '',
                    second_irr: '',
                    third_irr: '',
                    fourth_irr: '',
                    fifth_irr: '',
                    sixth_irr: '',
                    seventh_irr: '',
                },
                selectedGravity:[],
                selectedOccurrence:[],
            };
        },
        created(){
            this.eventGravity();
            this.eventOccurrence();

            if(this.gravityOccurrenceProps.occur_id){
                this.gravity.gravity = this.gravityOccurrenceProps.occur_gravity;
                this.gravity.first_irr = this.gravityOccurrenceProps.occur_first_irr;
                this.gravity.second_irr = this.gravityOccurrenceProps.occur_second_irr;
                this.gravity.third_irr = this.gravityOccurrenceProps.occur_third_irr
                this.gravity.fourth_irr = this.gravityOccurrenceProps.occur_fourth_irr;
                this.gravity.fifth_irr = this.gravityOccurrenceProps.occur_fifth_irr;
                this.gravity.sixth_irr = this.gravityOccurrenceProps.occur_sixth_irr;
                this.gravity.seventh_irr = this.gravityOccurrenceProps.occur_seventh_irr;
            } else {
                this.gravity.gravity = null;
                this.gravity.first_irr = null;
                this.gravity.second_irr = '';
                this.gravity.third_irr = '';
                this.gravity.fourth_irr = '';
                this.gravity.fifth_irr = '';
                this.gravity.sixth_irr = '';
                this.gravity.seventh_irr = '';
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.gravity.gravity && this.gravity.first_irr;
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
            eventOccurrence(){
                this.$constants.Coc_API.get("/dropdown/irr")
                    .then(response => {
                        this.selectedOccurrence = response.data;
                        console.log(this.selectedOccurrence);
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            eventUpdate() {
                this.laddabtn.start();
                let gravity = this.gravity;
                let getAxios = '';
                if(this.gravityOccurrenceProps.occur_id){

                    getAxios = this.$constants.Coc_API.get("/behavioralaction/"+this.gravityOccurrenceProps.occur_id, {
                        params: {
                            gravity_id: gravity.gravity.value,
                            first_irr: gravity.first_irr.value,
                            second_irr: gravity.second_irr.value,
                            third_irr: gravity.third_irr.value,
                            fourth_irr: gravity.fourth_irr.value,
                            fifth_irr: gravity.fifth_irr.value,
                            sixth_irr: gravity.sixth_irr.value,
                            seventh_irr: gravity.seventh_irr.value,
                        }
                    });

                } else {

                    getAxios = this.$constants.Coc_API.get("/behavioralaction/create", {
                        params: {
                            gravity_id: gravity.gravity.value,
                            first_irr: gravity.first_irr.value,
                            second_irr: gravity.second_irr.value,
                            third_irr: gravity.third_irr.value,
                            fourth_irr: gravity.fourth_irr.value,
                            fifth_irr: gravity.fifth_irr.value,
                            sixth_irr: gravity.sixth_irr.value,
                            seventh_irr: gravity.seventh_irr.value,
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
