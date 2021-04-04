<template>
    <div>
        <v-stepper v-model="e1">
            <v-stepper-header>
            <v-stepper-step :complete="e1 > 1" step="1">
                PLAN
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="e1 > 2" step="2">
                DATE
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step step="3">
                CONFIRMATION
            </v-stepper-step>
            </v-stepper-header>

            <v-stepper-items>
            <v-stepper-content step="1">
                <v-row>
                    <v-col cols="12" md="4" v-for="(plan, i) in plans" :key="i">
                        <plan-card @click.native="selectPlan(plan)" v-bind="plan"></plan-card>
                    </v-col>
                </v-row>

                <!-- <v-btn color="primary" @click="e1 = 2">
                Continue
                </v-btn>

                <v-btn text>
                Cancel
                </v-btn> -->
            </v-stepper-content>

            <v-stepper-content step="2">
                <calendar v-bind="booking" @time="selectDate"></calendar>

                <!-- <v-btn color="primary" @click="e1 = 3">
                Continue
                </v-btn> -->

                <v-btn text @click="resetPlan">
                Cancel
                </v-btn>
            </v-stepper-content>

            <v-stepper-content step="3">
                <confirmation @back="resetBooking" v-bind="booking"></confirmation>

                <v-btn color="primary" @click="resetBooking">
                Continue
                </v-btn>

                <v-btn text @click="resetDate">
                Cancel
                </v-btn>
            </v-stepper-content>
            </v-stepper-items>
        </v-stepper>
    <v-snackbar v-model="bookingSnackbar" timeout="2000">
      予約が完了しました。
      <template v-slot:action="{ attrs }">
        <v-btn color="pink" text v-bind="attrs" @click="snackbar = false">
          閉じる
        </v-btn>
      </template>
    </v-snackbar>
    </div>
</template>
<script>
import Calendar from "../components/bookingpage/Calendar";
import PlanCard from '../components/bookingpage/PlanCard'
import Confirmation from '../components/bookingpage/Confirmation'
import indication from "../components/mixins/indication"
export default {
    mixins: [indication],
    components: {
        PlanCard,
        Confirmation,
        Calendar,
    },
    data: () => ({
        booking: {from: null, to: null, duration: null, price: null, title: null},
        loading: false,
        plans: [],
        bookingSnackbar: false
    }),
    computed: {
        duration(){
            return (this.plan.duration/30)-1;
        }
    },
    created(){
        this.getPlan();
    },
    methods: {
        async getPlan(){
            this.plans.splice(0, this.plans.length)
            const {data, err} = (await axios.get(`/api/${this.$route.params.sid}/shop-plan`))
            data.forEach(plan => {
            plan.types = JSON.parse(plan.types)
                this.plans.push(plan)
            });
            // this.$refs.planDialog.dialog = false;
        },
        selectPlan(plan){
            this.booking = Object.assign({}, this.booking, {duration: plan.duration, price: plan.price, title: plan.title})
            this.e1 = 2
        },
        selectDate(date){
            this.booking = Object.assign({}, this.booking, date)
            this.e1 = 3
        },
        resetPlan(){
            this.booking = Object.assign({}, this.booking, {duration: null, price: null, title: null})
            this.e1 = 1
        },
        resetDate(){
            this.booking = Object.assign({}, this.booking, {from: null, to: null})
            this.e1 = 2
        },
        resetBooking(){
            const params = {from: null, to: null, duration: null, price: null, title: null};
            this.booking = Object.assign({}, this.booking, params);
            this.e1 = 1;
            this.bookingSnackbar = true;
        }
    }
}
</script>
<style scoped>
    .fade-enter-active, .fade-leave-active {
        will-change: opacity;
        transition: opacity 1000ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0
    }
    @media screen and (max-width: 480px) {
        .v-stepper__content {
            padding: 5% 2%!important;
        }
    }
</style>