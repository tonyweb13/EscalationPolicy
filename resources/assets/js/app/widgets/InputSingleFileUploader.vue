<template>
    <div class="form-group">
        <div v-show="asterisk" class="asterisk_label">*</div>
        <label class="m-l-xs">{{ label }}</label>
        <div>
            <label for="file-upload" class="btn btn-success custom-single-file-button">
                <i class="fa fa-upload m-r-xs" />
                Upload
            </label>
            <input id="file-upload"
                type="file"
                class="form-control"
                @change="onChange"
            />
        </div>
    </div>
</template>
<script>
export default {
    props: {
        asterisk: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: true,
        },
    },
    methods: {
        onChange: function (event) {
            this.$emit('change', event);
        },
    },
}
</script>
