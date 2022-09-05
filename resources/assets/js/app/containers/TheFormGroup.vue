<template>
    <div class="form-group">
        <label style="margin-left: 10px;">{{ label }}</label>
        <div class="control has-icon has-icon-right">
            <slot/>
        </div>
    </div>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {
        mixins: [ sharedInputMixin ],
    }
</script>
