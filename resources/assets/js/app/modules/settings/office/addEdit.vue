<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <the-label label="Site" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <input type="text" v-model="office.site" class="form-control" label="text"/>
                        <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                    </validation-provider>
                </the-label>

                <the-label label="Complete Address" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <input type="text" v-model="office.complete_address" class="form-control" label="text"/>
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
        office_props: {
            site: '',
            complete_address: '',
        },
    },

    data: function () {
        return {
            office: {
                site: '',
                complete_address: '',
            },
        };
    },

    created(){
        if (this.office_props.id) {
            this.office.site = this.office_props.site;
            this.office.complete_address = this.office_props.complete_address;
        }
    },

    computed: { },

    methods: {
        eventUpdate() {
            let getAxios = '';
            if (this.office_props.id) {
                getAxios = this.$constants.Settings_ACL_API.get('/office/location/'+this.office_props.id, {
                    params: {
                        site: this.office.site,
                        complete_address: this.office.complete_address
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
                getAxios =  this.$constants.Settings_ACL_API.get('/office/location/create', {
                    params: {
                        site: this.office.site,
                        complete_address: this.office.complete_address
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
    },
}
</script>
