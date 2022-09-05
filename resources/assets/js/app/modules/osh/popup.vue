<template>
    <div>
        <!-- <iframe src="https://drive.google.com/file/d/1tBxt1VEqvYHykN_jcgfZyCEHGetXAzZE/preview"
        frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen
        allow="autoplay; accelerometer; encrypted-media; gyroscope; picture-in-picture"
        width="900" height="680">
        </iframe> -->
        <video width="1400" height="900" autoplay>
            <source src="/videos/OSH_MODULE_EDITED.mp4" type="video/mp4">
        </video>
        <div class="hide-controls"></div>
        <div v-if="timerCount <= 0">
            <button class="btn btn-primary btn-lg" @click="eventAcknowledge()">
            <i class="fa fa-check"></i> Finish</button>
        </div>
        <div v-else>
            <h2 style="display: inline;">Please watch video and wait until</h2>
            <h1 style="display: inline;">{{ timerCount }} seconds </h1>
            <h2 style="display: inline;">to proceed</h2>
        </div>
    </div>
</template>
<style>
.hide-controls {
    width: 960px;
    height: 70px;
    margin-top: -60px;
    background-color: white;
    position: relative;
}
</style>
<script>
    export default {
        data() {
            return {
                openModal: true,
                timerCount: 443
            }
        },
        watch: {
            timerCount: {
                handler(value) {
                    if (value > 0) {
                        setTimeout(() => {
                            this.timerCount--;
                        }, 1000);
                    }
                },
                immediate: true
            }
        },
        methods: {
            eventAcknowledge() {
            this.$constants.Default_API.get("/osh/save/finish")
                .then(response => {
                    response.data
                    this.$bus.$emit('init_modal', false);
                    this.$success('Kindly check acknowledgement sent to your email address, Thanks')
                }).catch(error => {
                    this.globalErrorHandling(error);
                });
            }
        },
    }
</script>
