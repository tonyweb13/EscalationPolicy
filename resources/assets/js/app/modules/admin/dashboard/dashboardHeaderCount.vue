<template>
    <div class="row">
        <div class="col-lg-3">
            <dashboard-header-pills
                item-class="label-success"
                title="Daily Open"
                :item="data.open"
                :is-loading="isLoading"
            />
        </div>
        <div class="col-lg-3">
            <dashboard-header-pills
                item-class="lazur-bg"
                title="Daily Closed"
                :item="data.close"
                :is-loading="isLoading"
            />
        </div>
        <div class="col-lg-3">
            <dashboard-header-pills
                item-class="red-bg"
                title="Daily Invalid"
                :item="data.invalid"
                :is-loading="isLoading"
            />
        </div>
        <div class="col-lg-3">
            <dashboard-header-pills
                item-class="yellow-bg"
                title=" Daily Total IR"
                :item="data.total"
                :is-loading="isLoading"
            />
        </div>
    </div>
</template>

<script>
"use strict"

import DashboardHeaderPills from './dashboardHeaderPills.vue'

export default {
    components: {DashboardHeaderPills},

    props: {
        data: {},
        isLoading: false,
    },

    methods: {
        roundDecimal(value) {
            let round
            if (this.isLoading) {
                round = ''
            } else {
                round = (value) ? value.toFixed(2) + '%' : 0
            }

            return round
        }
    },
}
</script>
