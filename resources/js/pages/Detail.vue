<template>
    <div v-if="booking">
        <div class="d-flex" style="align-items:center;padding:1% 0;">
            <h2 style="margin:0 1%">{{booking.id}}</h2>
            <h2>{{user.name}}</h2>
            <div class="d-flex" style="justify-content:space-around;align-items:center;margin:0 3%;max-width: 600px;width:100%;">
                <h3>予約日<br>{{date(booking.from)}}</h3>
                <h3>作成日<br>{{date(booking.created_at)}}</h3>
            </div>
            <h3 style="margin:0 3%;">合計金額<br><div class="text-right"><span>{{price}}</span><span>円</span></div></h3>
            <v-col cols="2">
            <v-select v-model="select" :items="items" item-text="status" label="Select" persistent-hint return-object single-line></v-select>
            </v-col>
        </div>
        <div class="d-flex" style="justify-content: space-between;margin-bottom: 3%">
            <v-card style="max-width: 550px;width: 100%;padding:2%">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h2 style="margin:0 1%">プラン</h2>
                    <v-btn class="white--text" color="indigo">編集</v-btn>
                </div>
                <div class="d-flex" style="align-items:center;padding:1% 0;">
                    <h3 style="margin: 0 1%">タイトル:　</h3>
                    <div class="d-flex" v-for="(plan, i) in booking.plans" :key="i">
                        <h3 style="margin:0 1%">{{plan.title}}　</h3>
                    </div>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>開始時間:　</h3>
                    <h3 style="margin:0 1%">{{time(booking.from)}}</h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>終了時間:　</h3>
                    <h3 style="margin:0 1%">{{time(booking.to)}}</h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>料金:　</h3>
                    <h3 style="margin:0 1%">{{booking.price}}円</h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>支払い:　</h3>
                    <h3 style="margin:0 1%">現金</h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>所要時間:　</h3>
                    <h3 style="margin:0 1%">{{calculateHour(booking.duration)}}</h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>メッセージ:　</h3>
                    <h3 style="margin:0 1%">なし</h3>
                </div>
            </v-card>
            <v-card style="max-width: 550px;width: 100%;padding:2%">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h2 style="margin:0 1%">予約者情報</h2>
                    <v-btn class="white--text" color="indigo" @click="editUser=!editUser" v-if="editUser">編集</v-btn>
                    <v-btn class="white--text" color="red" @click="editUser=!editUser" v-if="!editUser">完了</v-btn>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>名前:　</h3>
                    <h3 style="width:90%"><textarea style="resize: none;margin:0 1%;width:100%" rows="1" :disabled="editUser">{{user.name}}</textarea></h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>電話番号:　</h3>
                    <h3 style="width:90%"><textarea style="resize: none;margin:0 1%;width:100%" rows="1" :disabled="editUser">なし</textarea></h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>メール:　</h3>
                    <h3 style="width:90%"><textarea style="resize: none;margin:0 1%;width:100%" rows="1" :disabled="editUser">{{user.email}}</textarea></h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>カード登録:　</h3>
                    <h3 style="width:90%"><textarea style="resize: none;margin:0 1%;width:100%" rows="1" :disabled="editUser">なし</textarea></h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>メッセージ:　</h3>
                    <h3 style="width:90%"><textarea style="resize: none;margin:0 1%;width:100%" rows="1" :disabled="editUser">なし</textarea></h3>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h3>メモ:　</h3>
                    <h3 style="width:90%"><textarea style="resize: none;margin:0 1%;width:100%" rows="1" :disabled="editUser">なし</textarea></h3>
                </div>
            </v-card>
        </div>
        <div class="d-flex" style="justify-content: space-between">
            <v-card style="max-width: 550px;width: 100%;padding:2%">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h2 style="margin:0 1%">メモ</h2>
                    <v-btn class="white--text" color="indigo" @click="editBookingMemo=!editBookingMemo" v-if="editBookingMemo">編集</v-btn>
                    <v-btn class="white--text" color="red" @click="editBookingMemo=!editBookingMemo" v-if="!editBookingMemo">完了</v-btn>
                </div>
                <h4><textarea style="resize: none;margin:0 1%;width:100%" :disabled="editBookingMemo">なし</textarea></h4>
            </v-card>
            <v-card style="max-width: 550px;width: 100%;padding:2%">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h2 style="margin:0 1%">更新情報</h2>
                    <v-btn class="white--text" color="indigo">編集</v-btn>
                </div>
                <h4><textarea style="resize: none;margin:0 1%;width:100%" disabled>なし</textarea></h4>
            </v-card>
        </div>
    </div>
</template>
<script>
export default {
    props: ['id'],
    data(){
        return {
            booking: '',
            select: { status: '未払い', isPaid: false},
            items: [
                { status: '未払い', isPaid: false },
                { status: '支払い済み', isPaid: true },
            ],
            editUser: true,
            editBookingMemo: true,
        }
    },
    computed: {
        price(){
            return !this.select.isPaid ? this.booking.price : 0;
        },
        user(){
            return this.booking.user;
        },
    },
    created(){
        this.hello()
    },
    methods: {
        date(time){
            let changedFormat = new Date(time);
            const day = ['日', '月', '火', '水', '木', '金', '土']
            changedFormat.setMonth(changedFormat.getMonth()+1)
            const month = changedFormat.getMonth(); 
            const date = changedFormat.getDate();
            const dayNum = changedFormat.getDay();
            const hours = changedFormat.getHours();
            let minutes;
            if (changedFormat.getMinutes()>=10) {
                minutes = changedFormat.getMinutes();
            } else {
                minutes = '0' + changedFormat.getMinutes()
            }
            let seconds;
            if (changedFormat.getSeconds()>=10) {
                seconds = changedFormat.getSeconds();
            } else {
                seconds = '0' + changedFormat.getSeconds()
            }
            const newFormat = `${month}月${date}日(${day[dayNum]}) ${hours}:${minutes}:${seconds}`
            return newFormat;
        },
        time(time){
            let changedFormat = new Date(time);
            let hours;
            if (changedFormat.getHours()>=10) {
                hours = changedFormat.getHours();
            } else {
                hours = '0' + changedFormat.getHours()
            }
            let minutes;
            if (changedFormat.getMinutes()>=10) {
                minutes = changedFormat.getMinutes();
            } else {
                minutes = '0' + changedFormat.getMinutes()
            }
            const newFormat = `${hours}:${minutes}`
            return newFormat;
        },
        calculateHour(duration){
            if (duration==30) {
                return duration+'分'
            } else if(duration%60){
                return Math.floor(duration/60)+'時間'+ 30 + '分';
            } else {
                return duration/60+'時間';
            }
        },
        hello(){
            axios.get(`../api/booking/${this.id}`)
            .then(response=>{
                this.booking = response.data
            })
        }
    },
}
</script>