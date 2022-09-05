<template>
    <div class="form-group">
        <div v-show="asterisk" class="asterisk_label">*</div>
        <label class="m-l-xs">{{ label }}</label>
        <input class="form-control" type="text" :value="value" @input="onInput"
        :required="required" :disabled="disabled" :placeholder="placeholder" />
    </div>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {
        name: 'InputText',

        mixins: [ sharedInputMixin ],
    }
</script>
