<template>
    <div>
        <img v-if="showTemplate1" :src="'/uploads/egreetings/template1/' 
        + egreetings.employee_number + '/' + egreetings.created_at.slice(0, 10) + '/' 
        + egreetings.template1" style="width: 100%; height: 100%;">
        <div v-if="showTemplate1" class="emp_name" > {{ egreetings.employee_name }} </div>
        <div v-if="showTemplate1" class="click_here" @click="next()"> 
            Click here to open your birthday card 
        </div>

        <transition name="bounce">
            <img v-if="showTemplate2" :src="'/uploads/egreetings/template2/' 
            + egreetings.employee_number + '/' + egreetings.created_at.slice(0, 10) + '/' 
            + egreetings.template2" style="width: 100%; height: 100%;">
        </transition>

        <!-- <transition name="slide-fade">
            <div style="text-align:center">
                <img v-if="showTemplate2" :src="'/uploads/egreetings/profile_pic/' 
                + egreetings.employee_number + '/' + egreetings.created_at.slice(0, 10) + '/' 
                + egreetings.profile_pic" class="prof_pic">
            </div>
        </transition> -->
    </div>
</template>
<style scoped>
.emp_name {
    text-align: center;
    color: white;
    font-size: xx-large;
    font-weight: bold;
    font-family: "Agency FB";
    margin-top: -90px;
    margin-bottom: 10px;
}
/* .prof_pic {
    width: 120px; 
    height: 120px; 
    position: absolute;
    margin-top: -170px;
    margin-left: -55px;
} */
.click_here {
    text-align: center; 
    cursor: pointer; 
    color: yellow;
}
.bounce-enter-active {
  animation: bounce-in .8s;
}
.bounce-leave-active {
  animation: bounce-in .8s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to {
  transform: translateX(10px);
  opacity: 0;
}
</style>
<script>
    export default {
        props: {
            eGreetingsProps: Object,
        },
        data: function () {
            return {
                showTemplate1: true,
                showTemplate2: false,
                egreetings: {
                    template1: '',
                    template2: '',
                    // profile_pic: '',
                    employee_number: '',
                    employee_name: '',
                    email_address: '',
                    birthday: '',
                    created_at: '',
                    greeting_id: '',
                },
            };
        },
        created(){
            if (this.eGreetingsProps.greeting_id) {
                this.egreetings.template1 = this.eGreetingsProps.template1;
                this.egreetings.template2 = this.eGreetingsProps.template2;
                // this.egreetings.profile_pic = this.eGreetingsProps.profile_pic;
                this.egreetings.employee_number = this.eGreetingsProps.employee_number;
                this.egreetings.employee_name = this.eGreetingsProps.employee_name;
                this.egreetings.email_address = this.eGreetingsProps.email_address;
                this.egreetings.birthday = this.eGreetingsProps.birthday;
                this.egreetings.created_at = this.eGreetingsProps.created_at;
                this.egreetings.greeting_id = this.eGreetingsProps.greeting_id;
            }
        },
        methods:{
            next: function () {
                this.showTemplate2 = true
                this.showTemplate1 = false
            }
        }
    }
</script>
