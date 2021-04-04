<template>
    <div class="wrapper">
        <div style="background-color: #f8f8f8;padding: 4%;border-radius: 5px">
            <div class="py-4 px-2 d-flex flex-wrap justify-content-between">
                <h2 class="my-2">確認画面</h2>
                <div class="d-flex justify-content-between align-items-center my-2 total">
                    <h4 class="pr-4" style="margin:0">合計金額: {{price}}円</h4>
                    <v-btn class="white--text px-4 block" color="indigo" @click="booking">予約する</v-btn>
                </div>
            </div>
            <v-card class="d-flex justify-content-center" style="padding:5%">
                <v-row>
                    <v-col cols="12" sm="12" md="6">
                        <v-col cols="12">
                            <h4>予約情報</h4>
                            <hr style="border:0;border-top:2px dashed rgb(179 154 154);">
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">開始時間</h5>
                            <span class="sub">{{ changeToTimeStamp(from) }}</span>
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">終了時間</h5>
                            <span class="sub">{{ changeToTimeStamp(to) }}</span>
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">所要時間</h5>
                            <span class="sub">{{ calculateHour(duration) }}</span>
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">プラン名</h5>
                            <span class="sub">{{ title }}</span>
                        </v-col>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                        <v-col cols="12">
                            <h4>会員情報</h4>
                            <hr style="border:0;border-top:2px dashed rgb(179 154 154);">
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">名前</h5>
                            <span class="sub">{{ fullName }}</span>
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">メールアドレス</h5>
                            <span class="sub">{{ user.email }}</span>
                        </v-col>
                        <v-col cols="12">
                            <h5 class="title">電話番号</h5>
                            <span class="sub">{{ user.phone }}</span>
                        </v-col>
                        <!-- <v-col cols="12">
                            <h5>合計料金</h5>
                            <span>{{ user.phone }}</span>
                        </v-col> -->
                    </v-col>
                    <v-col cols="12">
                        <v-col cols="12">
                            <h5 class="title">要望</h5>
                            <v-textarea auto-grow outlined prepend-inner-icon="mdi-comment" background-color="grey lighten-4"></v-textarea>
                        </v-col>
                    </v-col>
                </v-row>
            </v-card>
        </div>
    </div>
</template>
<script>
import indication from "../mixins/indication"
export default {
    props: ['duration', 'price', 'title', 'from', 'to'],
    mixins: [indication],
    computed: {
        fullName(){
            return this.$store.getters.user.last_name + '　' + this.$store.getters.user.first_name
        },
        user(){
            return this.$store.getters.user;
        }
    },
    methods: {
        changeToTimeStamp(time){
            let changedFormat = new Date(time*1);
            const day = ['日', '月', '火', '水', '木', '金', '土']
            changedFormat.setMonth(changedFormat.getMonth()+1)
            const month = changedFormat.getMonth()===0 ? 12 : changedFormat.getMonth();
            const date = changedFormat.getDate();
            const dayNum = changedFormat.getDay();
            const hours = changedFormat.getHours();
            let minutes;
            if (changedFormat.getMinutes()>=10) {
                minutes = changedFormat.getMinutes();
            } else {
                minutes = '0' + changedFormat.getMinutes()
            }
            const newFormat = `${month}月${date}日(${day[dayNum]}) ${hours}:${minutes}`
            return newFormat;
        },
        booking(){
            this.$emit('back');
        }
    }
}
</script>

<style scoped>
    .is-disabled {
        pointer-events: none;
        opacity: .5;
        color: red;
        font-weight: bold;
    }
    .fade-enter-active, .fade-leave-active {
        will-change: opacity;
        transition: opacity 1000ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        opacity: 1;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0
    }
    .title {
        font-size: 21px;
    }
    .sub {
        font-size: 16px;
    }
    .wrapper {
        padding: 3%;
    }
    @media screen and (max-width: 767px) {
        .total {
            width: 100%;
        }
    }
    @media screen and (max-width: 480px) {
        .total {
            width: 100%;
            flex-wrap: wrap;
        }
        .block {
            width: 100%;
            margin-top: 8%;
        }
        .wrapper {
            padding: 0;
        }
    }
</style>
