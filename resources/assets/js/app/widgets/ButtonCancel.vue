<template>
    <button type="button" @click.prevent='$emit("click")' class="btn btn-danger">
        <i class="fa fa-ban"/> Cancel
    </button>
</template>

<script>
"use strict"

export default {
    name: 'ButtonCancel',
}
</script>
