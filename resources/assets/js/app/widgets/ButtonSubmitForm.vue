<template>
    <button type="button" :disabled='disabled' @click.prevent='$emit("click")' class="btn btn-primary">
        <i class="fa fa-check"/> <slot>Submit Form</slot>
    </button>
</template>

<script>
"use strict"

export default {
    name: 'ButtonSubmitForm',

    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
