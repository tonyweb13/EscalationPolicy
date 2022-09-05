<template>
    <button class="btn btn-default" @click="$emit('close')">
        <i class="fa fa-close"/> Close
    </button>
</template>

<script>
"use strict"

export default {
    name: 'ButtonClose',
}
</script>
