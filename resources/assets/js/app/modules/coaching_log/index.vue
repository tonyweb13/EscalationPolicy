<template>
    <div class="row">
        <div class="ibox-content col-lg-12 p-md">
            <coaching-log-form />
        </div>
    </div>
</template>
<script>
    import coachingLogForm from '@/modules/coaching_log/coachingLogForm.vue'

    export default {
        components: {
            coachingLogForm,
		}
    }

</script>
