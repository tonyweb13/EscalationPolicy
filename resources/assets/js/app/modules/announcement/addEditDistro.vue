<template>
    <the-form :asterisk="true" @submitForm="eventUpdate">
        <div slot="form-body">
            <input-text v-model="distro.distro_name" label="Distro Name" :asterisk="true"/>

            <the-label label="Employee Name and Email Address" :asterisk="true">
                <br>
                <small style="background-color: yellow;">
                    <b>NOTE:</b> <i>Employee not searchable here 
                    if no email address provided in VPS credentials, <br>
                    if employee already resign or inactive please remove here</i>
                </small><br><br>
                <vSelect
                    multiple
                    v-model="distro.user_id"
                    :options="selectedUsersEmail"
                    label="text"
                />
            </the-label>

            <button-save :disabled='!isComplete' />
        </div>
    </the-form>
</template>
<script>
    export default {
        props: {
            distroProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                distro: {
                    distro_name: '',
                    user_id: [],
                    user_multiple: [],
                },
                selectedUsersEmail: [],
            };
        },
        created(){
            if (this.distroProps.distro_id) {
                this.distro.distro_name = this.distroProps.distro_name;
                if (this.distroProps.distro_categories) {

                    for(let r=0; r<this.distroProps.distro_categories.length; r++){
                        if (this.distroProps.distro_categories[r].distro_user) {
                            this.distro.user_id[r] = {
                                text: this.distroProps.distro_categories[r].distro_user.first_name
                                + " " + this.distroProps.distro_categories[r].distro_user.last_name
                                + " - " + this.distroProps.distro_categories[r].distro_user.email_address,
                                value: this.distroProps.distro_categories[r].distro_user.id,
                                selected: "selected"
                            };
                        }
                    }
                }
            }
            
            this.$constants.Default_API.get("/dropdown/users/email")
                .then(response => {
                this.selectedUsersEmail = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                if (this.distro.user_id.length > 0) {
                    return this.distro.distro_name && this.distro.user_id;
                }
            }
        },
        methods: {
            eventUpdate() {
                for(let r=0; r<this.distro.user_id.length; r++){
                    this.distro.user_multiple.push(this.distro.user_id[r].value);
                }

                this.laddabtn.start();
                let distro = this.distro;
                let getAxios = '';

                if (this.distroProps.distro_id) {
                    getAxios = this.$constants.Default_API.get("/announcement/distro/"+this.distroProps.distro_id, {
                        params: {
                            distro_name: distro.distro_name,
                            user_id: distro.user_multiple,
                        }
                    });
                } else {

                    getAxios = this.$constants.Default_API.get("/announcement/distro/create", {
                        params: {
                            distro_name: distro.distro_name,
                            user_id: distro.user_multiple,
                        }
                    });
                }
                
                getAxios.then(response => {
                    this.laddabtn.stop();
                    this.distro = response.data;
                    console.log("this.distro==", this.distro)
                    this.$bus.$emit('init_modal', false);
                    this.$success('Successfully Send!');
                    location.reload();
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
        },
    }
</script>
