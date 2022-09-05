<template>
    <div class="footer">
        <div class="pull-right">
            <span>EP Version: {{ version }}</span>
        </div>
        <div>
            <span><strong>Copyright</strong> ARB Call Facilities &copy; 2019</span>
        </div>
    </div>
</template>

<script>
"use strict";

export default {
    name: 'TheFooter',
    data: function () {
            return {
            version: this.$ep.ep_version,
        }
    },
}
</script>
