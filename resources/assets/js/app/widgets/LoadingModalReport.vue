<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="loader"></div>
            </div>
            <div class="alert alert-warning warning-modal" role="alert">
                <label class="fa fa-warning"></label><br>
                <h3>Please wait and do not refresh while processing your request, <br>
                it takes time to extract your report.<br>
                Thank You!
                </h3>
            </div>
        </div>
    </transition>
</template>
<style scoped>
    .loader {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #1ab394; /* Green */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: loading-modal-spin 2s linear infinite;
        display: block;
        margin: auto;
    }

    @keyframes loading-modal-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .warning-modal {
        text-align: center;
        vertical-align: middle;
        font-weight: 900;
        height: 120px;
        float: left;
        position: absolute;
        padding: 10px;
    }
</style>