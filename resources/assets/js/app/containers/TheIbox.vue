<template>
    <div>
        <div class="ibox-title">
            <h4>{{ title }}</h4>
            <span class="pull-right" style="margin-top: -25px;" v-html="asterisk"></span>
        </div>
        <div class="ibox-content" style="padding-bottom: 55px;">
            <slot/>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ibox',

        props: {
            title: {
                type: String,
                default: true,
            },
            asterisk: {
                type: String,
            },
        },
    }
</script>
