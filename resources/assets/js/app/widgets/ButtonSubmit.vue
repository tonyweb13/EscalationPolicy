<template>
    <button class="ladda-button btn btn-primary" data-style="slide-right"
    data-color="mint" type="submit" :disabled="disabled">
        <i class="fa fa-save"/> Submit
    </button>
</template>

<script>
"use strict"

export default {
    name: 'ButtonSubmit',

    props: ['disabled']
}
</script>
