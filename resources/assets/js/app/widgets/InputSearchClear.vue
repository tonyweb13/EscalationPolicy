<template>
    <div style="overflow: hidden; margin-bottom:10px;">
                <div class="col-lg-4">
                <slot/>
                <i style="float: left;
                    padding: 6px;
                    border: none;
                    margin-top: -35px;
                    font-size: 17px;
                    margin-left: -20px;"
                    class="fa fa-search"
                    aria-hidden="true">
                </i>
                </div>
                <div class="col-lg-2">
                <button style="border: none;
                    margin-left: -15px;"
                    class="btn btn-success"
                    @click='$emit("search")' >
                    Search
                </button>
                <button type="button" class="btn btn-default" :disabled="disabled"
                @click='$emit("clear")'> Clear </button>
                </div>
                <div class="col-lg-6"></div>
            </div>
</template>
<script>
    import sharedInputMixin from '@/mixins/SharedInputMixins'

    export default {
        name: 'InputSearchClear',

        mixins: [ sharedInputMixin ],
    }
</script>
