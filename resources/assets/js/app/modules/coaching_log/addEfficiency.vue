<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">

                <the-label label="Portfolio Name" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="efficiency_goal.description"
                            :options="description"
                            label="text"
                            input-id="portfolio_name"
                        />
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Priority" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <input type="text" id="priority" v-model="efficiency_goal.priority" class="form-control" label="text">
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="In Call (%)" :asterisk="true">
                    <validation-provider rules="required|numeric" v-slot="{ errors }">
                        <input type="text" id="in_call" v-model="efficiency_goal.in_call" class="form-control" label="text">
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Ready (%)" :asterisk="true">
                    <validation-provider rules="required|numeric" v-slot="{ errors }">
                        <input type="text" id="ready" v-model="efficiency_goal.ready" class="form-control" label="text">
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Wrap Up (%)" :asterisk="true">
                    <validation-provider rules="required|numeric" v-slot="{ errors }">
                        <input type="text" id="wrap_up" v-model="efficiency_goal.wrap_up" class="form-control" label="text">
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Not Ready (%)" :asterisk="true">
                    <validation-provider rules="required|numeric" v-slot="{ errors }">
                        <input type="text" id="not_ready" v-model="efficiency_goal.not_ready" class="form-control" label="text">
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <button-save dusk="add_efficiency" class="ladda-button btn btn-primary" />
            </div>
        </the-form>
    </ValidationObserver>
</template>
<script>
export default {
    props: {
        efficiency_goal_props: Object,
    },
    data: function () {
        return {
			efficiency_goal: {
                description: null,
                priority: '',
                in_call: '',
                ready: '',
                wrap_up: '',
                not_ready: '',
            },
            description: [],
        };
    },

    created(){
        this.eventRespondent();
        this.description = [];
        this.efficiency_goal.description = null;
        if(this.efficiency_goal_props.id){
            let portfolio_name = {
                text: this.efficiency_goal_props.portfolio_name,
                value: this.efficiency_goal_props.portfolio_id
            };
            this.efficiency_goal.description = portfolio_name;
            this.efficiency_goal.priority = this.efficiency_goal_props.priority;
            this.efficiency_goal.in_call = this.efficiency_goal_props.in_call;
            this.efficiency_goal.ready = this.efficiency_goal_props.ready;
            this.efficiency_goal.wrap_up = this.efficiency_goal_props.wrap_up;
            this.efficiency_goal.not_ready = this.efficiency_goal_props.not_ready;
        }
    },

    computed: { },

    methods: {
        eventUpdate() {
            let getAxios = '';
            if (this.efficiency_goal_props.id) {
                $('.ladda-button').attr("disabled", true);
                getAxios = this.$constants.Coaching_API.post('/efficiency_goal/'+this.efficiency_goal_props.id, {
                        params: {
                            profile_name: this.efficiency_goal.description,
                            priority: this.efficiency_goal.priority,
                            in_call: this.efficiency_goal.in_call,
                            ready: this.efficiency_goal.ready,
                            wrap_up: this.efficiency_goal.wrap_up,
                            not_ready: this.efficiency_goal.not_ready,
                        },
                });
                getAxios.then(response => {
                    $('.ladda-button').attr("disabled", false);
                    this.$bus.$emit('init_modal', false);
                    this.$swal({
                        html: 'Successfully edited',
                        type: "success",
                        confirmButtonText: 'OK',
                    });
                })
                .catch(error => {
                    this.globalErrorHandling(error);
                });
            } else {
                $('.ladda-button').attr("disabled", true);
                getAxios =  this.$constants.Coaching_API.post('/efficiency_goal', {
                        params: {
                            profile_name: this.efficiency_goal.description,
                            priority: this.efficiency_goal.priority,
                            in_call: this.efficiency_goal.in_call,
                            ready: this.efficiency_goal.ready,
                            wrap_up: this.efficiency_goal.wrap_up,
                            not_ready: this.efficiency_goal.not_ready,
                        },
                });
                getAxios.then(response => {
                    $('.ladda-button').attr("disabled", false);
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

        eventRespondent(){
            this.$constants.Coaching_API.get("/dropdown/portfolio")
                .then(response => {
                this.description = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        },

    },
}
</script>
