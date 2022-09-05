<template>
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#tab-stage-one" aria-expanded="true">Stage One Form</a>
            </li>
            <li><a data-toggle="tab" href="#tab-stage-two-to-five" aria-expanded="false">Stage Two to Five Form</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-stage-one" class="tab-pane active">
                <add-monthly-stage-one-form :employee_no="employee_no" />
            </div>
            <div class="clearfix"></div>

            <div id="tab-stage-two-to-five" class="tab-pane">
                <add-monthly-stage-two-to-five-form :employee_no="employee_no" />
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>
<script>
    import addMonthlyStageOneForm from '@/modules/coaching_log/addMonthlyStageOneForm.vue'
    import addMonthlyStageTwoToFiveForm from '@/modules/coaching_log/addMonthlyStageTwoToFiveForm.vue'
    export default {
    components: { addMonthlyStageOneForm, addMonthlyStageTwoToFiveForm },
    props: {
        employee_no: String,
    },
}
</script>
