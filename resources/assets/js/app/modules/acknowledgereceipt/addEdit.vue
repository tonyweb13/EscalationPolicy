<template>
        <the-form :asterisk="true" @submitForm="eventUpdate">
            <div slot="form-body">
                <div class="alert alert-danger" v-if="error_data">{{ error_data }}</div>

                <the-label label="Employee Name" :asterisk="true">
                    <vSelect 
                        v-model="acknowledge.employee" 
                        :options="selectedUser" 
                        @input="eventSupMan(acknowledge.employee.value)"
                        label="text" 
                        required
                        :disabled="disableEditEmp"
                    />    
                </the-label>
                <the-label v-if="acknowledge.supervisor" label="Supervisor:">
                    <div style="background: #eee; padding: 10px;" v-html="acknowledge.supervisor"/>
                </the-label>
                <the-label v-if="acknowledge.manager" label="Manager:">
                    <div style="background: #eee; padding: 10px;" v-html="acknowledge.manager"/>
                </the-label>
                <input-text
                    v-model="acknowledge.incentive"
                    label="Name of the Incentive"
                    :asterisk="true"
                    required
                />
                <input-text
                    v-model="acknowledge.prize"
                    label="Prize"
                />
                <the-label label="Date Received" :asterisk="true">
                    <datepicker
                            :input-class="'custom-datepicker'"
                            :format="'yyyy-MM-dd'"
                            :calendar-button="true"
                            :calendar-button-icon="'fa fa-calendar'"
                            :use-utc="true"
                            placeholder="YYYY-MM-DD"
                            v-model="acknowledge.date_received"
                        />
                </the-label>
                <the-label label="Item/s" :asterisk="true">
                    <input-textarea v-model="acknowledge.item" required />
                </the-label>
                <button-submit :disabled='!isComplete'/>
            </div>
        </the-form>
</template>
<script>
    export default {
        props: {
            acknowledgementProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
                disableEditEmp: false,
                selectedUser:[],
                acknowledge: {
                    employee: '',
                    supervisor: '',
                    supervisor_id: '',
                    manager: '',
                    manager_id: '',
                    incentive: '',
                    prize: '',
                    item: '',
                    date_received: '',
                },
                error_data: '',
            };
        },
        created(){
            this.eventUser();
            if(this.acknowledgementProps.acknowledge_id){
                this.disableEditEmp = true;
                this.acknowledge.incentive = this.acknowledgementProps.incentive;
                this.acknowledge.prize = this.acknowledgementProps.prize;
                this.acknowledge.item = this.acknowledgementProps.item;
                this.acknowledge.date_received = this.acknowledgementProps.date_received
                this.acknowledge.employee = {
                    value: this.acknowledgementProps.user_id,
                    text: this.acknowledgementProps.employee_name,
                };

            } else {
                this.disableEditEmp = false;
                this.acknowledge.employee = '';
                this.acknowledge.supervisor = '';
                this.acknowledge.supervisor_id = '';
                this.acknowledge.manager = '';
                this.acknowledge.manager_id = '';
                this.acknowledge.incentive = '';
                this.acknowledge.prize = '';
                this.acknowledge.item = '';
                this.acknowledge.date_received = '';
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.acknowledge.employee && this.acknowledge.incentive && this.acknowledge.item && this.acknowledge.date_received;
            }
        },
        methods: {
            eventUpdate() {
                this.laddabtn.start();
                let acknowledge = this.acknowledge;
                let getAxios = '';

                if(this.acknowledgementProps.acknowledge_id){
                    getAxios = this.$constants.Default_API.get("/acknowledgementreceipt/"+this.acknowledgementProps.acknowledge_id, {
                        params: {
                            user_id: acknowledge.employee.value,
                            supervisor_id: acknowledge.supervisor_id ? acknowledge.supervisor_id : null,
                            manager_id: acknowledge.manager_id ? acknowledge.supervisor_id : null,
                            incentive: acknowledge.incentive,
                            prize: acknowledge.prize,
                            item: acknowledge.item,
                            date_received: acknowledge.date_received,
                        }
                    });

                } else {
                    getAxios = this.$constants.Default_API.get("/acknowledgementreceipt/create", {
                        params: {
                            user_id: acknowledge.employee.value,
                            supervisor_id: acknowledge.supervisor_id,
                            manager_id: acknowledge.manager_id,
                            incentive: acknowledge.incentive,
                            prize: acknowledge.prize,
                            item: acknowledge.item,
                            date_received: acknowledge.date_received,
                        }
                    });
                }

                getAxios.then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.acknowledge = response.data;
                    this.$success('Successfully Saved!')
                    this.$bus.$emit('updateList');
                })
                .catch(error => {
                    this.error_data = error.response.data.message;

                    if (this.error_data) {
                        this.laddabtn.stop();
                    } else {
                        this.globalErrorHandling(error);
                    }
                });
            },
            eventUser(){
                this.$constants.Coaching_API.get("/dropdown/users_with_employee_no")
                    .then(response => {
                    this.selectedUser = response.data;
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            },
            eventSupMan(emp_val){
                if (emp_val) {
                    this.$constants.Default_API.get("/acknowledgementreceipt/supervisormanager/" + emp_val)
                        .then(response => {
                            this.acknowledge.supervisor = response.data.supervisor ? response.data.supervisor : ''
                            this.acknowledge.supervisor = response.data.supervisor ? response.data.supervisor : '' 
                            this.acknowledge.supervisor_id = response.data.supervisor_id ? response.data.supervisor_id : ''
                            this.acknowledge.manager = response.data.manager ? response.data.manager : ''
                            this.acknowledge.manager_id = response.data.manager_id ? response.data.manager_id : ''
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
                }
            },
        },
    }
</script>
