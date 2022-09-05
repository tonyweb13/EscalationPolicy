<template>
    <button class="ladda-button btn btn-primary" data-style="slide-right"
    data-color="mint" type="submit" :disabled="disabled">
        <i :class="icon"/> {{ name }}
    </button>
</template>

<script>
"use strict"

export default {
    name: 'ButtonCustom',

    props: ['disabled', 'name', 'icon']
}
</script>
