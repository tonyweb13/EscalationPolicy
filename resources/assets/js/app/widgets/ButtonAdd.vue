<template>
    <div class="pull-right" style="margin-top: -55px;">
        <button type="button" :disabled='disabled' @click.prevent='$emit("add")'
        class="btn btn-info">
            <i class="fa fa-plus"/> Add {{ thingToAdd }}
        </button>
    </div>
</template>

<script>
"use strict"

export default {
    name: 'ButtonAdd',

    props: {
        thingToAdd: {
            type: String,
            required: true,
        },

        disabled: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
