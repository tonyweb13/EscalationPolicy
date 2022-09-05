<template>
    <form @submit.prevent="$emit('submitForm')" enctype="multipart/form-data" >
        <small v-show="asterisk" class="pull-right required_fields">
            <i> Complete the form by providing all the required fields with the information being asked.
                ( <span>*</span> )
            </i>
        </small>
        <slot name="form-body"/>
    </form>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {
        mixins: [ sharedInputMixin ],
    }
</script>
