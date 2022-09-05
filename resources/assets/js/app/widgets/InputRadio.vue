<template>
    <div class="radio radio-success radio-inline">
        <input
            type="radio"
            :name="name"
            :id="id"
            :checked="checked"
        >
        <label :for="id"><b> {{ label }} </b></label>
    </div>
</template>

<script>

    export default {
        props: {
            id: {
                type: String,
                default: false,
            },
            name: {
                type: String,
                required: true,
            },
            label: {
                type: String,
                required: true,
            },
            checked: {
                type: String,
                required: false,
            },
        },
        methods: {
            onRadio: function (event) {
                console.log("event radio==");
                console.log(event.target.value);
                this.$emit('input', event.target.value);
            },
        },
    }
</script>
