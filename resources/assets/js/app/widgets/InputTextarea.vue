<template>
    <div class="form-group">
        <div v-show="asterisk" class="asterisk_label">*</div>
        <label class="m-l-xs">{{ label }}</label>
        <textarea class="form-control" :value="value" @input="onInput" rows="8"
        :required="required" :disabled="disabled" :placeholder="placeholder" >
        </textarea>
    </div>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {

        mixins: [ sharedInputMixin ],
    }
</script>
