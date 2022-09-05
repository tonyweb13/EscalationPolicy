<template>
    <transition name="modal">
        <div class="modal-mask" style="background-color: #333333f5;">
            <div class="modal-wrapper" style="width: 1120px; text-align: center;">
                <div class="modal-container">
                    <div class="modal-body">
                        <slot name="body" />
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
'use strict'

export default {
    name: 'Modal',
}
</script>
