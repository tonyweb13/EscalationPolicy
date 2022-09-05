<template>
    <ValidationObserver v-slot="{ handleSubmit }">
        <the-form :asterisk="true" @submitForm="handleSubmit(eventUpdate)">
            <div slot="form-body">
                <the-label label="Provision" :asterisk="true">
                    <vSelect
                        v-model="category"
                        label="text"
                        :disabled="true"
                    />
                </the-label>
                <validation-provider rules="required" v-slot="{ errors }">
                    <input-textarea v-model="attendance.infraction" label="Type of Infraction" :asterisk="true"/>
                    <span class="style-red">{{ errors[0] }}</span><br v-if="errors[0]"/><br v-if="errors[0]"/>
                </validation-provider>

                <the-label label="Attendance Points" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vSelect
                            v-model="attendance.attendancepoint"
                            :options="selectedAttendance"
                            label="text"
                            name="attendancepoint"
                        />
                        <span class="style-red">{{ errors[0] }}</span>
                    </validation-provider>
                </the-label>

                <the-label label="Definition" :asterisk="true">
                    <validation-provider rules="required" v-slot="{ errors }">
                        <vue-editor v-model="attendance.definition" :editorToolbar="customToolbar"/>
                        <span class="style-red">{{ errors[0] }}</span>
                    </validation-provider>
                </the-label>

                <button-save />
            </div>
        </the-form>

    </ValidationObserver>
</template>
<script>
    export default {
        props: {
            attendanceProps: Object,
        },
        data: function () {
            return {
                laddabtn : '',
                asterisk: true,
                category: {
                    text: "Attendance with Points",
                    value: 15,
                    selected: "selected",
                },
                attendance: {
                    infraction: '',
                    attendancepoint: '',
                    definition: '',
                },
                selectedAttendance: this.eventAttendance(),
                customToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': '' },{ 'align': 'center' },{ 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                ],
            };
        },
        created(){
            if(this.attendanceProps.att_id){
                this.attendance.infraction = this.attendanceProps.att_infraction;
                this.attendance.category = this.attendanceProps.att_category;
                this.attendance.attendancepoint = this.attendanceProps.att_attendancepoint;
                this.attendance.definition = this.attendanceProps.att_definition;
            } else {
                this.attendance.attendancepoint = null;
                this.attendance.infraction = null;
                this.attendance.definition = null;
            }
        },
        mounted() {
            this.laddabtn = this.$ladda.create(document.querySelector('.ladda-button'));
        },
        computed: {
            isComplete () {
                return this.attendance.infraction && this.category
                && this.attendance.attendancepoint && this.attendance.definition;
            }
        },
        methods: {
            eventAttendance(){
                this.$constants.Coc_API.get("/dropdown/attendance/points")
                    .then(response => {
                        this.selectedAttendance = response.data;
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
            eventUpdate() {
                this.laddabtn.start();
                let attendance = this.attendance;
                let getAxios = '';


                if(this.attendanceProps.att_id){
                    getAxios = this.$constants.Coc_API.get("/attendance/points/system/"+this.attendanceProps.att_id, {
                        params: {
                            type_infraction: attendance.infraction,
                            category_id: this.category.value,
                            attendancepoint_id: attendance.attendancepoint.value,
                            definition: attendance.definition,
                        }
                    });

                } else {
                    getAxios = this.$constants.Coc_API.get("/attendance/points/system/create", {
                        params: {
                            type_infraction: attendance.infraction,
                            category_id: this.category.value,
                            attendancepoint_id: attendance.attendancepoint.value,
                            definition: attendance.definition,
                        }
                    });
                }

                getAxios.then(response => {
                    this.laddabtn.stop();
                    this.$bus.$emit('init_modal', false);
                    this.attendance = response.data;
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
