<template>
    <div v-if="booking">
        <div class="d-flex" style="flex-wrap:wrap;align-items:center;padding:1% 0;justify-content: space-between;width: 100%">
            <div class="d-flex" style="align-items:center;margin:auto">
                <h4 style="margin:0 1%;">予約番号<br><div style="font-size: 10px;">{{booking.id}}</div></h4>
                <h4 style="min-width:90px;margin:0 5px;">予約者名<br>{{user.last_name}} {{user.first_name}}</h4>
                <div class="d-flex" style="justify-content:space-around;align-items:center;margin:0 3%;max-width: 400px;width:100%;">
                    <h4>予約日<br>{{date(booking.from)}}</h4>
                    <h4>作成日<br>{{date(booking.created_at)}}</h4>
                </div>
            </div>
            <div class="d-flex" style="align-items:center;margin:auto">
                <h4 style="margin:0 3%;min-width:80px;">合計金額<br><div class="text-right"><span>{{price}}</span><span>円</span></div></h4>
                <v-col cols="6">
                <v-select v-model="select" :items="items" item-text="status" label="Select" persistent-hint return-object single-line @input="hello"></v-select>
                </v-col>
                <v-btn class="white--text" color="pink" @click="cancelDialog=true">削除</v-btn>
            </div>
        </div>
        <div class="d-flex" style="justify-content: space-between;margin-bottom: 3%;flex-wrap:wrap;">
            <v-card style="max-width: 550px;width: 50%;min-width:360px;padding:2%;margin:10px auto;">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h3 style="margin:0 1%">プラン</h3>
                    <v-btn class="white--text" color="indigo">編集</v-btn>
                </div>
                <div class="d-flex" style="align-items:center;padding:1% 0;">
                    <h4 style="margin: 0 1%">タイトル:　</h4>
                    <div class="d-flex" v-for="(plan, i) in booking.plans" :key="i">
                        <h4 style="margin:0 1%">{{plan.title}}　</h4>
                    </div>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>開始時間:　</h4>
                    <h4 style="margin:0 1%">{{time(booking.from)}}</h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>終了時間:　</h4>
                    <h4 style="margin:0 1%">{{time(booking.to)}}</h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>料金:　</h4>
                    <h4 style="margin:0 1%">{{booking.payment.price}}円</h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>支払い:　</h4>
                    <h4 style="margin:0 1%">{{method}}</h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>所要時間:　</h4>
                    <h4 style="margin:0 1%">{{calculateHour(booking.duration)}}</h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>メッセージ:　</h4>
                    <h4 style="margin:0 1%">{{isNull(booking.memo)}}</h4>
                </div>
            </v-card>
            <v-card style="max-width: 550px;width: 50%;min-width:360px;padding:2%;margin:10px auto;">
                <div class="d-flex" style="align-items:center;justify-content:space-between;">
                    <h3 style="margin:0 1%">予約者情報</h3>
                    <v-btn class="white--text" color="indigo" @click="editUser=!editUser" v-if="editUser">編集</v-btn>
                    <v-btn class="white--text" color="red" @click="editUser=!editUser" v-if="!editUser">完了</v-btn>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>名前:　</h4>
                    <h4 style="width:85%"><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" rows="1" :disabled="editUser">{{user.last_name}} {{user.first_name}}</textarea></h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>電話番号:　</h4>
                    <h4 style="width:85%"><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" rows="1" :disabled="editUser">{{user.phone}}</textarea></h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>メール:　</h4>
                    <h4 style="width:85%"><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" rows="1" :disabled="editUser">{{user.email}}</textarea></h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>カード登録:　</h4>
                    <h4 style="width:85%"><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" rows="1" :disabled="editUser">{{register}}</textarea></h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>メッセージ:　</h4>
                    <h4 style="width:85%"><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" rows="1" :disabled="editUser">{{isNull(user.message)}}</textarea></h4>
                </div>
                <div class="d-flex" style="margin: 0 1%;padding:1% 0;">
                    <h4>メモ:　</h4>
                    <h4 style="width:85%"><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" rows="1" :disabled="editUser">{{isNull(user.memo)}}</textarea></h4>
                </div>
            </v-card>
        </div>
        <div class="d-flex" style="justify-content: space-between;flex-wrap:wrap">
            <v-card style="max-width: 550px;width: 50%;min-width:360px;padding:2%;margin:10px auto">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h3 style="margin:0 1%">メモ</h3>
                    <v-btn class="white--text" color="indigo" @click="editBookingMemo=!editBookingMemo" v-if="editBookingMemo">編集</v-btn>
                    <v-btn class="white--text" color="red" @click="editBookingMemo=!editBookingMemo" v-if="!editBookingMemo">完了</v-btn>
                </div>
                <h4><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" :disabled="editBookingMemo">なし</textarea></h4>
            </v-card>
            <v-card style="max-width: 550px;width: 50%;min-width:360px;padding:2%;margin:10px auto">
                <div class="d-flex" style="align-items:center;justify-content:space-between">
                    <h3 style="margin:0 1%">更新情報</h3>
                    <v-btn class="white--text" color="indigo">編集</v-btn>
                </div>
                <h4><textarea style="resize: none;margin:0 1%;width:100%;overflow: hidden;" disabled>なし</textarea></h4>
            </v-card>
        </div>
        <v-dialog v-model="cancelDialog" max-width="400">
            <v-card>
                <v-card-title class="headline">
                    予約削除
                </v-card-title>
                <v-card-text>
                    こちらの予約を削除しますか？<br><b>{{booking.id}}</b>：{{user.last_name}} {{user.first_name}}<br><b>予約日</b>{{date(booking.from)}}
                </v-card-text>
                <v-card-actions>
                <v-btn color="indigo darken-1" text @click="cancelBooking">
                    はい
                </v-btn>
                <v-btn color="red darken-1" text @click="cancelDialog=false">
                    いいえ
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
export default {
    props: ['sid','id'],
    data(){
        return {
            booking: '',
            select: { status: '未払い', isPaid: false },
            items: [
                { status: '未払い', isPaid: false },
                { status: '支払い済み', isPaid: true },
            ],
            editUser: true,
            editBookingMemo: true,
            cancelDialog: false,
        }
    },
    computed: {
        price(){
            return !this.select.isPaid ? this.booking.payment.price : 0;
        },
        user(){
            return this.booking.user;
        },
        method(){
            const method = this.booking.payment.method;
            return method==='cash' ? '現金' : 'カード'
        },
        register(){
            const method = this.user.method;
            return method  ? 'あり' : 'なし'
        },
    },
    async created(){
        const {data, err} = (await axios.get(`/api/booking/${this.$route.params.sid}/${this.$route.params.id}`));
        if (data) {
            this.booking = data;
            if (!data.status) {
                axios.put(`/api/booking/${this.$route.params.id}`,{status: true})
            }
            const paid = data.payment.paid;
            if(paid){
                this.select = { status: '支払い済み', isPaid: true};
            } else {
                this.select ={ status: '未払い', isPaid: false};
            }
        }   else {
            this.$router.back()
        }

    },
    methods: {
        async hello(){
            const {data, err} = await (axios.put(`/api/payment/${this.booking.id}`,{paid: this.select.isPaid}));
            if(data){
                this.booking = Object.assign({}, this.booking, {payment: data});
            }
        },
        isNull(str){
            return str?str:'なし'
        },
        cancelBooking(){
            axios.put(`/api/booking/${this.$route.params.sid}/${this.$route.params.id}`)
            .then(response=>{
                this.booking = response.data;
                // if (!response.data.isRead) {
                //     axios.put(`api/booking/${this.id}`,{cancelled: true})
                // }
            })
        },
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
            let minutes;
            changedFormat.setMinutes(Math.round(changedFormat.getMinutes()/10)*10)
            minutes = changedFormat.getMinutes();
            if (changedFormat.getMinutes()<10) {
                minutes = '0' + changedFormat.getMinutes()
            }
            let hours;
            if (changedFormat.getHours()>=10) {
                hours = changedFormat.getHours();
            } else {
                hours = '0' + changedFormat.getHours()
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
    },
}
</script>

<style>
    h4 {
        font-weight: bold
    }
</style>