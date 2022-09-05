<template>
    <div class="form-group">
        <div v-show="asterisk" class="asterisk_label">*</div>
        <label class="m-l-xs">{{ label }}</label>
        <slot/>
    </div>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {
        mixins: [ sharedInputMixin ],
    }
</script>
