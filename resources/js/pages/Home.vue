<template>
    <div>
        <div class="d-flex text-center">
            <div style="width: 100%;">
                <transition name="calendar" mode="out-in">
                    <div ref="calendar" class="text-center d-flex" v-show="ready" style="height: 520px;width:100%;">
                        <div>
                            <div style="border: 1px solid #e0e0e0;color: #000;">Date\Time</div>
                            <div v-for="(c, i) in calendar" :key="i">
                                <div v-if="days.length!=0">
                                    <div style="padding: 10px 0;height: 46px;border-left: 1px solid #e0e0e0;border-right: 1px solid #e0e0e0;border-bottom: 1px solid #e0e0e0;color: #000;">{{ days[i].getMonth()+1}}/{{ days[i].getDate() }} {{ dayOfWeek(days[i].getDay()) }}</div>
                                </div>
                            </div>
                        </div>
                        <div style="width:100%;">
                            <div class="d-flex text-center">
                                <div class="period" v-for="(period , i) in periods" :key="i"><span>{{ period }}</span></div>
                            </div>
                            <div ref="cells" style="position:relative">
                            <div style="position:absolute;left:0;right:0;top:0;bottom:0;z-index:0" @mousemove="dragging($event)" @mouseleave="dragEnd" @mouseup="dragEnd" @touchmove="touchMove($event)" @touchend="dragEnd" :style="{display: 'block', zIndex: disabled?2:0}"></div>
                                <div v-for="(c, i) in calendar" :key="i" class="d-flex">
                                    <div class="frame" ref="frame" v-for="(time, j) in c" :key="j">
                                        <!-- <div v-if="time.isBooking" class="link"><router-link
                                        @mousedown.native="dragStart($event);index1=j;index2=i;booking=time"
                                        @touchstart.native.prevent="touchStart($event);index1=j;index2=i;booking=time"
                                        @touchmove.native="touchMove($event)"
                                        @touchend.native="touchEnd({name: 'detail', params: {id: time.id}})"
                                        tag="button" ref="booking"
                                        :to="{name: 'detail', params: {id: time.id}}"
                                        :style="{width: width(time, j+i*19),transform: '',backgroundColor: time.status==false?'#4C64D3':'#757575',borderRadius: '5px'}"
                                        :disabled="disabled"
                                        >
                                        <span>{{time.user.last_name}} {{time.user.first_name}}</span>
                                        </router-link><v-badge color="red darken-1" offset-x="7" offset-y="-11" dot content overlap style="z-index:10" v-if="time.payment.paid"></v-badge></div> -->
                                        <router-link
                                        @mousedown.native="dragStart($event);index1=j;index2=i;booking=time"
                                        @touchstart.native.prevent="touchStart($event);index1=j;index2=i;booking=time"
                                        @touchmove.native="touchMove($event)"
                                        @touchend.native="touchEnd({name: 'detail', params: {sid: time.shop_id, id: time.id}})"
                                        tag="button" class="link" ref="booking"
                                        :to="{name: 'detail', params: {sid: time.shop_id, id: time.id}}"
                                        :style="{width: width(time, j+i*19),transform: '',backgroundColor: time.status==false?'#4C64D3':'#757575'}"
                                        :disabled="disabled"
                                        v-if="time.isBooking&&(new Date()).getTime()<time.from"
                                        ><div style="position: absolute;right:-3px;top:-3px;width: 8px;height: 8px;border-radius:50%;background-color:red;" v-if="!time.payment.paid"></div>
                                        <span>{{time.user.last_name}} {{time.user.first_name}}</span>
                                        </router-link>

                                        <router-link
                                        tag="button" class="link" ref="booking"
                                        :to="{name: 'detail', params: {sid: time.shop_id, id: time.id}}"
                                        :style="{width: width(time, j+i*19),transform: '',backgroundColor: '#75757550'}"
                                        v-else-if="time.isBooking&&(new Date()).getTime()>time.from"
                                        ><div style="position: absolute;right:-3px;top:-3px;width: 8px;height: 8px;border-radius:50%;background-color:red;" v-if="!time.payment.paid"></div>
                                        <span>{{time.user.last_name}} {{time.user.first_name}}</span>
                                        </router-link>
                                        <div @mousedown.left="make($event, time);index1=j;index2=i;" @click="touchMake(time);index1=j;index2=i;booking=time" @mousemove="dragging($event)" @touchmove="touchMove($event)" ref="dev" style="width: 100%;height: 100%;" v-else></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <v-btn @click="previousWeek" :disabled="!ready">back</v-btn>
                <v-btn @click="nextWeek" :disabled="!ready">next</v-btn>
            </div>
        </div>
        <v-dialog v-model="dialog" max-width="400">
            <v-card>
                <v-card-title class="headline" v-html="dialogTitle">
                </v-card-title>
                <v-card-text v-html="dialogMessage">
                </v-card-text>
                <v-card-actions>
                <v-btn color="indigo darken-1" text @click="changeTime">
                    はい
                </v-btn>
                <v-btn color="red darken-1" text @click="undo">
                    いいえ
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="bookingDialog" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">User Profile</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col ols="12" sm="4">
                                <v-text-field label="開始時間" required :value="newBookingDate(newBookingTime())"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-text-field label="終了時間" required :value="newBookingDate(newBookingTime()+booking.duration*60*1000-1)"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-text-field label="合計時間" required :value="booking.duration"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" md="6">
                                <v-text-field label="first name*" required></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" md="6">
                                <v-text-field label="last name*" hint="example of persistent helper text" persistent-hint required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field label="Email*" required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field label="Password*" type="password" required></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                    <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeBookingDialog">
                        Close
                    </v-btn>
                    <v-btn color="blue darken-1" text @click="saveBookingDialog">
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
  <!-- <v-row>
    <v-col
      cols="11"
      sm="5"
    >
      <v-menu
        ref="menu"
        v-model="menu2"
        :close-on-content-click="false"
        :nudge-right="40"
        :return-value.sync="time"
        transition="scale-transition"
        offset-y
        max-width="290px"
        min-width="290px"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="time"
            label="Picker in menu"
            prepend-icon="mdi-clock-time-four-outline"
            readonly
            v-bind="attrs"
            v-on="on"
          ></v-text-field>
        </template>
        <v-time-picker
          v-if="menu2"
          v-model="start"
          full-width
          @click:minute="$refs.menu.save(start)"
        　min="10:00"
          max="19:00"
          format="24hr"
          scrollable
        ></v-time-picker>
      </v-menu>
    </v-col>
    <v-spacer></v-spacer>
    <v-col
      cols="11"
      sm="5"
    >
      <v-dialog
        ref="dialog"
        v-model="modal2"
        :return-value.sync="time"
        persistent
        width="290px"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="end"
            label="Picker in dialog"
            prepend-icon="mdi-clock-time-four-outline"
            readonly
            v-bind="attrs"
            v-on="on"
          ></v-text-field>
        </template>
        <v-time-picker
          v-if="modal2"
          v-model="end"
          full-width
        　min="10:00"
          max="19:00"
          format="24hr"
          scrollable
        >
          <v-spacer></v-spacer>
          <v-btn
            text
            color="primary"
            @click="modal2 = false"
          >
            Cancel
          </v-btn>
          <v-btn
            text
            color="primary"
            @click="$refs.dialog.save(end)"
          >
            OK
          </v-btn>
        </v-time-picker>
      </v-dialog>
    </v-col>
  </v-row> -->
    </div>
</template>
<script>
// import EventTag from '../components/EventTag'
// import router from '../routes';
export default {
    props: ['duration', 'price', 'title'],
    data() {
        return {
            start: null,
            end: null,
            menu2: false,
            modal2: false,
            calendar: [],
            bookings: [],
            booking: '',
            changedBooking: '',
            newBooking: {},
            periods: ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30','19:00'],
            days: this.makeTwoWeeks(0),
            dateIndex: 0,
            ready: false,
            startX: 0,
            startY: 0,
            isMouse: false,
            isMakeMouse: false,
            disabled: false,
            currentPositionX: 0,
            currentPositionY: 0,
            distanceX: 0,
            distanceY: 0,
            bookingIndex: '',
            index1: 0,
            index2: 0,
            touchJudge: '',
            dialog: false,
            dialogTitle: '',
            dialogMessage: '',
            bookingDialog: false,
            num: 0,
            windowTime: ''
        }
    },
    computed:{
        windowSizeX(){
            return this.$store.state.windowSize.x;
        },
    },
    watch: {
        windowSizeX:{
            handler(v){
                if(this.ready){
                    clearInterval(this.windowTime);
                    this.resetBooking(this.dateIndex);
                    let that = this;
                    that.windowTime = setTimeout(function(){that.makeCalendar(that.dateIndex);},300);
                }
            },
            immediate: false
        }
    },
    async created(){
        const response = await (axios.get(`/api/${this.$route.params.sid}/booking`));
        const bookings = response.data;
        for (let i = 0; i < bookings.length; i++) {
            await this.bookings.push(bookings[i]);
        }
        this.makeCalendar(0);
        Echo.private('new-booking')
        .listen('NewBooking', response => {
            if(response.booking.shop_id == this.$route.params.sid){
                this.bookings.push(response.booking);
                this.isBooking(this.dateIndex);
                const fullName = response.booking.user.last_name + '　' + response.booking.user.first_name
                Push.create('新しい予約が入りました',{
                    body: fullName+'様',
                    icon: '/images/logo/logo01.png',
                    timeout: 30000,
                    onClick: function () {
                        window.focus();
                        this.close();
                    }
                })
            }
        });
        Echo.private('change-booking')
        .listen('ChangeBooking',response => {
            if(response.booking.shop_id == this.$route.params.sid){
                if (this.changedBooking.newFrom !== response.booking.from) {
                    this.booking = this.bookings.find((booking)=>{
                        return booking.id === response.booking.id;
                    })
                    this.bookings = this.bookings.filter((booking)=>{
                        return booking.id !== response.booking.id;
                    })
                    this.checkBooking();
                    this.bookings.push(response.booking);
                    this.isBooking();
                    const fullName = response.booking.user.last_name + '　' + response.booking.user.first_name
                    Push.create('予約変更がありました',{
                        body: fullName+'様',
                        icon: '/images/logo/logo01.png',
                        timeout: 30000,
                        onClick: function () {
                            window.focus();
                            this.close();
                        }
                    })
                }
            }
        });
    },
    beforeDestroy(){
        clearTimeout(this.touchJudge);
    },
    methods: {
        resetBooking(index){
            for (let i = 10*index; i < 10+10*index; i++) {
                for (let j = 0; j < 19; j++) {
                    const newCell = {isBooking: false}
                    this.$set(this.calendar[i],j,{isBooking: false})
                }
            }
        },
        newBookingDate(time){
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
            let seconds;
            if (changedFormat.getSeconds()>=10) {
                seconds = changedFormat.getSeconds();
            } else {
                seconds = '0' + changedFormat.getSeconds()
            }
            const newFormat = `${month}月${date}日(${day[dayNum]}) ${hours}:${minutes}:${seconds}`
            return newFormat;
        },
        newBookingTime(){
            return this.booking.from-(this.num*(this.booking.duration-30)*60*1000)
        },
        saveBookingDialog(){
            this.bookingDialog = false;
        },
        closeBookingDialog(){
            this.bookingDialog = false;
            this.checkBooking()
            this.bookings.splice(this.bookings.length-1, 1);
            this.isBooking(this.dateIndex);
        },
        touchMake(booking){
            this.booking = Object.assign(booking,{user: {last_name: 'new', first_name: ''},isBooking: true,duration: 30,from:booking.date});
            this.bookings.push(this.booking);
            this.isBooking();
            this.bookingDialog = true;
        },
        make(e,booking){
            this.num=0;
            this.booking = Object.assign(booking,{user: {last_name: 'new', first_name: ''},isBooking: true,duration: 30,from:booking.date},{payment: {paid: false}});
            this.bookings.push(this.booking);
            this.isBooking();
            this.isMakeMouse = true;
            this.disabled = true;
            this.startX = e.clientX
            this.startY = e.clientY
        },
        moveBooking(e){
            const movedDistanceX = e.clientX - this.startX
            const booking = this.bookings[this.bookings.length-1];
            this.distanceX = movedDistanceX;
            if((30+(Math.round((this.distanceX)/this.$refs.frame[0].getBoundingClientRect().width))*30) !== booking.duration){
                const frame = this.$refs.frame[this.index1 + this.index2*19];
                let duration = 30+(Math.round((this.distanceX)/this.$refs.frame[0].getBoundingClientRect().width))*30;
                if (duration>1) {
                    frame.children[0].style.width = (frame.getBoundingClientRect().width*Math.sqrt(Math.pow(duration, 2))/30-10) + 'px';
                    this.$set(booking, 'duration', duration);
                    if(this.num==1){
                        frame.children[0].style.transform = `translate(0px,0)`
                        this.num=0
                    }
                    } else {
                        this.num=1
                        duration-=60
                    frame.children[0].style.width = (frame.getBoundingClientRect().width*Math.sqrt(Math.pow(duration, 2))/30-10) + 'px';
                    this.$set(booking, 'duration', Math.sqrt(Math.pow(duration, 2)));
                    if(duration!==-60){
                        duration+=30
                        frame.children[0].style.transform = `translate(${frame.getBoundingClientRect().width*duration/30}px,0)`
                    } else {
                        duration+=30
                        frame.children[0].style.transform = `translate(${frame.getBoundingClientRect().width*duration/30}px,0)`
                    }
                }
            }
        },
        endMoveBooking(){
            this.bookingDialog = true;
            this.isMakeMouse = false;
            this.disabled = false;
        },
        changeTime(){
            const params = {from: this.changedBooking.newFrom, to: this.changedBooking.newTo}
            axios.put(`/api/${this.$route.params.sid}/booking/${this.booking.id}`,params)
            .then((response)=>{
                this.bookings = this.bookings.filter((booking)=>{
                    return booking.id !== this.booking.id;
                })
                this.checkBooking();
                this.bookings.push(response.data);
                this.isBooking();
                this.dialog = false;
                this.changedBooking = {};
                // console.log(this.bookings[index])
                // this.bookings.splice(index, 1)
            })
        },
        checkBooking(){
            this.calendar.forEach((date, i) => {
                date.forEach((time, j) => {
                    if (this.floor(this.booking.from)==this.floor(time.date)) {
                        const calendar = this.calendar[i][j];
                        const newCell = {date: calendar.date, isBooking: false, x: 0, y: 0}
                        // this.calendar[i].splice(j, 1, newCell)
                        this.$set(this.calendar[i],j,newCell)
                    }
                })
            })
        },
        undo(){
            let calendar = this.calendar[this.index2][this.index1]
            const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
            calendar.x  = 0;
            calendar.y  = 0;
            booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
            this.dialog = false
        },
        touchStart(e){
            this.startX = e.touches[0].clientX;
            this.startY = e.touches[0].clientY;
            let that = this;
            this.touchJudge = setTimeout(function(){that.isMouse = true;that.$refs.frame[that.index1+that.index2*19].children[0].style.opacity=0.6},200)
        },
        dragStart(e){
            this.startX = e.clientX
            this.startY = e.clientY
            let that = this;
            this.touchJudge = setTimeout(function(){that.isMouse = true;that.$refs.frame[that.index1+that.index2*19].children[0].style.opacity=0.6},200)
        },
        touchMove(e){
            if(this.isMouse){
                let calendar = this.calendar[this.index2][this.index1]
                this.disabled = true;
                const movedDistanceX = e.touches[0].clientX - this.startX
                const movedDistanceY = e.touches[0].clientY - this.startY
                const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
                this.distanceX = movedDistanceX;
                this.distanceY = movedDistanceY;
                if(this.isInsideFrame(booking)){
                    booking.style.transform = `translate(${calendar.x + this.distanceX}px,${calendar.y + this.distanceY}px)`;
                } else {
                    this.distanceX = 0;
                    this.distanceY = 0;
                    booking.style.transform = `translate(${0}px,${0}px)`;
                    this.touchEnd({name: 'detail', params: {sid: this.$route.params.sid, id: this.booking.id}})
                }
            }
        },
        dragging(e){
            if (this.isMouse) {
                this.moveTag(e)
            }
            if (this.isMakeMouse) {
                this.moveBooking(e)
            }
        },
        moveTag(e){
            let calendar = this.calendar[this.index2][this.index1]
            this.disabled = true;
            const movedDistanceX = e.clientX - this.startX
            const movedDistanceY = e.clientY - this.startY
            const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
            this.distanceX = movedDistanceX;
            this.distanceY = movedDistanceY;
            if(this.isInsideFrame(booking)){
                booking.style.transform = `translate(${calendar.x + this.distanceX}px,${calendar.y + this.distanceY}px)`;
            } else {
                this.distanceX = 0;
                this.distanceY = 0;
                booking.style.transform = `translate(${0}px,${0}px)`;
                this.dragEnd()
            }
        },
        touchEnd(root){
            if (this.isMouse) {
                const width = this.$refs.frame[0].getBoundingClientRect().width
                const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
                const cells = this.$refs.cells;
                let calendar = this.calendar[this.index2][this.index1]
                const check = this.check(Math.round((calendar.x + this.distanceX)/width), Math.round((calendar.y + this.distanceY)/46));
                this.disabled = false;
                this.isMouse = false;
                if (!check.isCheck&&!check.overBooking) {
                    this.dialogTitle = '予約変更'
                    this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    this.transform(booking, calendar, check)
                } else if(check.isCheck&&check.overBooking) {
                    //overbooking用
                    // this.dialogTitle = '<h3 style="color:red">ダブルブッキング！！</h3>'
                    // this.dialogMessage = `以下のように変更すると予約が重なってしまいます。確認してください。<br><b>予約1</b>：${this.date(check.overBooking.from)}～${this.date((check.overBooking.to).getTime()+60*1000)}<br><b>予約2</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    // this.transform(booking, calendar, check)
                    booking.style.transform = `translate(0px,0px)`;
                } else {
                    booking.style.transform = `translate(0px,0px)`;
                }
                booking.style.opacity = 0.9
                this.distanceX = 0;
                this.distanceY = 0;
            } else {
                clearTimeout(this.touchJudge)
                this.$router.push(root)
            }
        },
        dragEnd(){
            if (this.isMouse) {
                this.endMoveTag()
            }
            if (this.isMakeMouse) {
                this.endMoveBooking()
            }
        },
        endMoveTag(){
            const width = this.$refs.frame[0].getBoundingClientRect().width
            const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
            let calendar = this.calendar[this.index2][this.index1]
            const check = this.check(Math.round((calendar.x + this.distanceX)/width), Math.round((calendar.y + this.distanceY)/46));
            this.disabled = false;
            this.isMouse = false;
            if (!check.isCheck&&!check.overBooking) {
                this.dialogTitle = '予約変更'
                this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                this.transform(booking, calendar, check)
            } else if(check.isCheck&&check.overBooking) {
                    //overbooking用
                    // this.dialogTitle = '<h3 style="color:red">ダブルブッキング！！</h3>'
                    // this.dialogMessage = `以下のように変更すると予約が重なってしまいます。確認してください。<br><b>予約1</b>：${this.date(check.overBooking.from)}～${this.date((check.overBooking.to).getTime()+60*1000)}<br><b>予約2</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    // this.transform(booking, calendar, check)
                booking.style.transform = `translate(0px,0px)`;
            } else {
                booking.style.transform = `translate(0px,0px)`;
            }
            booking.style.opacity = 0.9
            this.distanceX = 0;
            this.distanceY = 0;
        },
        transform(booking, calendar, check){
            const width = this.$refs.frame[0].getBoundingClientRect().width
            calendar.x  = Math.round((calendar.x + this.distanceX)/width)*width;
            calendar.y  = Math.round((calendar.y + this.distanceY)/46)*46;
            booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
            this.dialog = true;
            this.changedBooking = Object.assign(this.booking, check)
        },
        check(x, y){
            const from = this.booking.from;
            const to = this.booking.to;
            const newDate = y*24*60*60*1000
            const newFrom = from + x*30*60*1000 + newDate;
            const newTo = to + x*30*60*1000 + newDate;
            const condition3 = from !== newFrom;
            const condition4 = (new Date(newFrom)).getHours()>=10;
            if (condition3 && condition4) {
                let bookings = this.bookings.filter((v)=>{
                    return v.id !== this.booking.id
                })
                const isCheck = bookings.find((booking)=>{
                    const condition1 = this.floor(newFrom) <= this.floor(booking.to) && this.floor(newTo) >= this.floor(booking.to);
                    const condition2 = this.floor(booking.from) < this.floor(newTo) && this.floor(booking.to) >= this.floor(newTo);
                    return condition1 || condition2;
                })
                return {isCheck: isCheck ? true : false, from: from, to: to, newFrom: newFrom, newTo:newTo, overBooking: isCheck ? isCheck : ''}
            } else {
                return {isCheck: true, from: from, to: to, newFrom: newFrom, newTo:newTo}
            }
        },
        floor(time){
            return Math.floor((time)/1000)
        },
        isInsideFrame(booking){
            const cells = this.$refs.cells;
            const judgeLeft = cells.getBoundingClientRect().left-30 < booking.getBoundingClientRect().left;
            const judgeTop = cells.getBoundingClientRect().top-10 < booking.getBoundingClientRect().top;
            const judgeRight = cells.getBoundingClientRect().right+30 > booking.getBoundingClientRect().right;
            const judgeBottom = cells.getBoundingClientRect().bottom+10 > booking.getBoundingClientRect().bottom;
            return judgeRight && judgeLeft && judgeTop && judgeBottom ? true : false;
        },
        width(booking, index){
            // const width = booking.duration/30*100 + '%';
            // const ration = this.$refs.frame[0].getBoundingClientRect().width/(this.$refs.frame[0].getBoundingClientRect().width-1)-0.004;
            // console.log(ration)
            // const width = ration*booking.duration/30*100 + '%';

            const width = (this.$refs.frame[0].getBoundingClientRect().width*(booking.duration/30)-6) + 'px';
            return width
        },
        async makeCalendar(index){
            this.calendar = [];
            axios.get('/api/time').then(response => {
                for (let i = 10*index; i < 10+10*index; i++) {
                    let dates = []
                    for (let j = 0; j < 19; j++) {
                        const today = new Date(response.data)
                        today.setMonth(today.getMonth())
                        today.setDate(today.getDate()+i)
                        today.setHours(10)
                        today.setMinutes(30*j)
                        today.setSeconds(0)
                        today.setMilliseconds(0)
                        dates.push({date: today.getTime(), isBooking: false, x: 0, y: 0});
                    }
                    this.calendar.push(dates)
                }
            })
            .then(()=>{
                this.isBooking()
            })
            .then(()=>{
                this.bookableDate((this.duration/30)-1);
            })
            .catch((error)=>{
                console.log(error)
                this.ready = false;
            })
        },
        isBooking(){
            this.calendar.forEach((date, i) => {
                date.forEach((time, j) => {
                    this.bookings.forEach((booking, k)=>{
                        if (this.floor(booking.from)==this.floor(time.date)) {
                            this.$set(this.bookings[k], 'from', time.date);
                            this.$set(this.calendar[i][j], 'isBooking', true);
                            Object.assign(this.calendar[i][j],booking);
                        }
                    })
                })
            })
        },
        // isBooking(){ //overbooking用
        //     this.calendar.forEach((date, i) => {
        //         date.forEach((time, j) => {
        //             this.bookings.forEach((booking)=>{
        //                 if (this.floor(booking.from)==this.floor(time.date)) {
        //                     Object.assign(this.calendar[i][j],booking);
        //                     this.$set(this.calendar[i][j], 'isBooking', true);
        //                     let clazz = Vue.extend(EventTag)
        //                     let instance = new clazz({
        //                         router,
        //                         propsData: {
        //                             index: i+j*19,
        //                             booking: booking,
        //                             frameWidth: this.$refs.frame[i+j*19].getBoundingClientRect(),
        //                             cells: this.$refs.cells
        //                         }
        //                     }).$mount()
        //                     this.$refs.frame[i+j*19].appendChild(instance.$el)
        //                 }
        //             })
        //         })
        //     })
        // },
        bookableDate(index){
            this.calendar.forEach((date, i) => {
                date.forEach((time, j) => {
                    this.bookings.forEach(booking=>{
                        if (this.floor(booking.from)==this.floor(time.date)) {
                            for (let k = 1; k < index+1; k++) {
                                if((j-k)!==-1){
                                    this.$set(this.calendar[i][j-k], 'isBooking', true);
                                } else {
                                    return;
                                }
                            }
                        }
                    })
                })
            })
            this.calendar.forEach((date, i) => {
                date.forEach((time, j)=>{
                    if(index!==0 && (date.length-index)<=j){
                        this.$set(this.calendar[i][j], 'isBooking', true)
                    }
                })
            })
            let that = this;
            setTimeout(function(){
                that.ready = true;
            },200)
        },
        makeTwoWeeks(index){
            let daysArray = [];
            axios.get('/api/time').then(response => {
                for (let i = 10*index; i < 10+10*index; i++) {
                    const days = new Date(response.data);
                    days.setMonth(days.getMonth())
                    days.setDate(days.getDate()+i)
                    daysArray.push(days);
                }
            })
            return daysArray;
        },
        nextWeek(){
            this.ready = false;
            this.calendar = [];
            this.dateIndex+=1;
            this.days = this.makeTwoWeeks(this.dateIndex);
            this.makeCalendar(this.dateIndex)
        },
        previousWeek(){
            this.ready = false;
            this.calendar = [];
            this.dateIndex-=1;
            this.days = this.makeTwoWeeks(this.dateIndex);
            this.makeCalendar(this.dateIndex)
        },
        dayOfWeek(num){
            const dayOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
            return dayOfWeek[num]
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
    }
}
</script>

<style scoped>
    .link{
        position: absolute;
        /* background-color: #757575; */
        /* overflow:hidden; */
        left:5%;
        top:25%;
        z-index:1;
        height: 25px;
        border-radius:5px;
        color:white;
        text-decoration:none;
        transition: opacity 0.7s;
    }
    .period{
        border-bottom: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        border-top: 1px solid #e0e0e0;
        max-width:63px;
        width: 100%;
        overflow: hidden;
    }
    .frame{
        border-right: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
        color: #000;
        position: relative;
        width:100%;
        height: 46px;
    }
</style>