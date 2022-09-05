<template>
    <div class="form-group">
        <div v-show="asterisk" class="asterisk_label">*</div>
        <label class="m-l-xs">{{ label }}</label>
        <input class="form-control" type="number" :value="value" @input="onInput"
        :required="required" :disabled="disabled" :placeholder="placeholder" @paste.prevent
        pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==11) return false;" />
    </div>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {
        name: 'InputNumber',

        mixins: [ sharedInputMixin ],
    }
</script>
