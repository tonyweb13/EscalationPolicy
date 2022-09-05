<template>
    <transition name="modal" >
        <div class="modal-mask">
            <div class="modal-wrapper" style="width:85%;" >
                <div class="modal-container" >
                    <div class="modal-header">
                        <span class="col-md-10">
                        <slot name="header"/>
                        </span>
                        <span class="col-md-2 pull-right">
                            <button class="pull-right btn btn-default btn-xs" alt="Close" @click="$emit('close')">
                                <i class="fa fa-close"/>
                            </button>
                        </span>
                    </div>
                    <div class="modal-body">
                        <slot name="body" />
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<style scoped>
    .modal-footer .btn .btn {
        margin-bottom: 5px !important;
    }
</style>

<script>
'use strict'

export default {
    name: 'Modal',
}
</script>
