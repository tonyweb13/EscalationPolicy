<template>
    <div class="checkbox checkbox-success checkbox-inline">
        <input
            type="checkbox"
            :name="name"
            :id="id"
            :checked="checked"
            :disabled="disabled"
            @change="onCheck"
        >
        <label :for="id"><b> {{ label }} </b></label>
    </div>
</template>

<script>

    export default {
        props: {
            id: {
                type: String,
                required: false,
            },
            name: {
                type: String,
                required: false,
            },
            label: {
                type: String,
                required: true,
            },
            checked: {
                type: Boolean,
                required: true,
            },
            disabled: {
                type: Boolean,
                required: false,
            },
        },
        methods: {
            onCheck: function (event) {
                this.$emit('change', event.target.checked);
            },
        },
    }
</script>
