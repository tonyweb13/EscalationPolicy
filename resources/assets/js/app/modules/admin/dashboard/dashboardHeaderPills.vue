<template>
    <div>
        <div class="widget style1" style="height: 42px;" :class="itemClass">
            <div class="row vertical-align">
                <div class="col-xs-6">
                    <p style="font-size:14px">{{ title  }}</p>
                </div>
                <div class="col-xs-6 text-right">
                    <div class="qa-loader" v-if="isLoading">
                        <loading-modal v-if="isLoading" />
                    </div>
                    <div style="font-size:25px;font-weight:500; margin-top: -8px;">{{ item ? item : 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.widget {
    padding: 12px !important;
}
</style>

<script>
"use strict"

export default {
    props: {
        title: null,
        itemClass: null,
        isLoading: false,
        item: null,
    },
}
</script>
