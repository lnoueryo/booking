<template>
    <div>
        <div class="d-flex text-center">
            <div class="whole-calendar" style="width: 100%;">
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
                        <div style="width:100%;min-width: 700px;">
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
        <v-dialog v-model="bookingDialog" persistent max-width="700px">
            <v-card>
                <!-- <v-card-title>
                    <span class="headline">User Profile</span>
                </v-card-title> -->
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6" md="6">
                                <v-menu v-model="menu3" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                    <v-text-field v-model="bookingDate" label="Picker without buttons" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                                    </template>
                                    <v-date-picker :max="maxDate" v-model="bookingDate" @input="menu3 = false"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="12" sm="6">
                            <v-menu ref="menu" v-model="menu2" :close-on-content-click="false" :nudge-right="40" :return-value.sync="time" transition="scale-transition" offset-y max-width="290px" min-width="290px">
                                <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="time" label="Picker in menu" prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs" v-on="on"></v-text-field>
                                </template>
                                <v-time-picker v-if="menu2" v-model="time" full-width @click:minute="$refs.menu.save(time)" :allowed-minutes="allowedMinutes" min="10:00" :max="max" format="24hr" scrollable>
                                <!-- <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="menu2 = false">
                                    Cancel
                                </v-btn>
                                <v-btn text color="primary" @click="$refs.dialog.save(time)">
                                    OK
                                </v-btn> -->
                                </v-time-picker>
                            </v-menu>
                            </v-col>
                            <v-col cols="12" sm="6" md="6">
                                <v-menu v-model="menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                    <v-text-field v-model="bookingDate" label="Picker without buttons" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                                    </template>
                                    <v-date-picker v-model="bookingDate" @input="menu = false"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="12" sm="6">
                            <v-dialog ref="dialog" v-model="modal2" :return-value.sync="time2" persistent width="290px">
                                <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="time2" label="Picker in dialog" prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs" v-on="on"></v-text-field>
                                </template>
                                <v-time-picker v-if="modal2" v-model="time2"  @click:minute="$refs.dialog.save(time2)" :allowed-minutes="allowedMinutes" full-width :min="min" max="19:30" format="24hr" scrollable>
                                </v-time-picker>
                            </v-dialog>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field single-line style="background-color: white" prepend-icon="mdi-bitcoin" label="料金" required v-model="booking.price" autocomplete="new-password"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field prepend-icon="mdi-timer-sand" label="合計時間" required v-model="booking.duration" disabled></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-autocomplete prepend-icon="mdi-content-cut" v-model="booking.plan" :items="shopPlans" label="プラン"></v-autocomplete>
                            </v-col>
                            <v-row class="px-3" v-if="booking.user">
                                <v-col cols="12" sm="10">
                                    <v-menu offset-y max-width="230">
                                        <template v-slot:activator="{ on }">
                                            <v-text-field prepend-icon="mdi-magnify" v-model="search" hint="名前、電話番号、Emailで検索可能です" label="Search" single-line v-on="on" autocomplete="new-password" @keyup="searchTimeOut"></v-text-field>
                                        </template>
                                        <v-list three-line v-if="userInfo.length > 0">
                                            <template v-for="(user, index) in userInfo">
                                                <v-list-item :key="index" @click="hello(user)">
                                                    <v-list-item-content style="align-items: start;">
                                                        <v-list-item-title style="-webkit-line-clamp: initial;" >{{ user.last_name }}{{ user.first_name}}<span v-if="user.guest">　(guest)</span></v-list-item-title>
                                                        <v-list-item-subtitle style="-webkit-line-clamp: initial;">{{ user.phone }}<br>{{ user.email }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </template>
                                        </v-list>
                                    </v-menu>
                                    <!-- <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line autocomplete="off" @keyup="searchTimeOut"></v-text-field> -->

                                </v-col>
                                <v-col cols="12" sm="2" class="d-flex align-items-center"><v-btn color="indigo" block class="white--text" @click="makeNewUser">新規作成</v-btn></v-col>
                                <v-col cols="12" v-if="booking.user.id==null"><h2>新規作成</h2></v-col>
                                <v-col cols="12" sm="3" md="3" class="d-flex">
                                    <v-icon left>mdi-checkbox-blank-circle</v-icon><v-text-field label="last name*" persistent-hint required v-model="booking.user.last_name" autocomplete="new-password" :disabled="booking.user.id!==null"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="3" md="3" class="d-flex">
                                    <v-icon left>mdi-checkbox-blank-circle-outline</v-icon><v-text-field label="first name*" required v-model="booking.user.first_name" autocomplete="new-password" :disabled="booking.user.id!==null"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="6" class="d-flex">
                                    <v-icon left>mdi-phone</v-icon><v-text-field label="Phone" persistent-hint required v-model="booking.user.phone" autocomplete="new-password" :disabled="booking.user.id!==null"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="6" class="d-flex">
                                    <v-icon left>mdi-email</v-icon><v-text-field label="Email*" required v-model="booking.user.email" autocomplete="new-password" :disabled="booking.user.id!==null"></v-text-field>
                                </v-col>
                            </v-row>
                            <v-col cols="12" sm="12" class="d-flex">
                                <v-icon left>mdi-message</v-icon><v-text-field label="memo" required v-model="booking.message" autocomplete="new-password"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                    <!-- <small>*indicates required field</small> -->
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
  <v-row>
    <!-- <v-col
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
    </v-col> -->
    <v-spacer></v-spacer>
    <!-- <v-col
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
    </v-col> -->
  </v-row>
    </div>
</template>
<script>
// import EventTag from '../components/EventTag'
// import router from '../routes';
import calendar from '../components/mixins/calendar'
export default {
    mixins: [calendar],
    props: ['duration', 'price', 'title'],
    data() {
        return {
            search: '',
            users: '',
            bookingDate: null,
            menu: false,
            menu2: false,
            menu3: false,
            modal: false,
            modal2: false,
            time: null,
            time2: null,
            calendar: [],
            bookings: [],
            booking: '',
            changedBooking: '',
            newBooking: {},
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
            windowTime: '',
            shopPlans: [],
            timeOut: '',
        }
    },
    computed:{
        windowSizeX(){
            return this.$store.state.windowSize.x;
        },
        max(){
            const bookingDate = new Date(`${this.bookingDate} ${this.time2}`);
            bookingDate.getTime()
            const minusOneMinute = bookingDate-60*1000;
            const newTime = new Date(minusOneMinute)
            const hour = newTime.getHours();
            const minute = newTime.getMinutes();
            return `${hour}:${minute}`
        },
        min(){
            const bookingDate = (new Date(`${this.bookingDate} ${this.time}`)).getTime();
            console.log(bookingDate)
            const plusOneMinute = bookingDate+30*60*1000;
            const newTime = new Date(plusOneMinute)
            const hour = newTime.getHours();
            const minute = newTime.getMinutes();
            return `${hour}:${minute}`
        },
        userInfo(){
            return this.users;
        }
    },
    watch: {
        windowSizeX:{
            handler(){
                if(this.ready){
                    clearInterval(this.windowTime);
                    this.resetBooking(this.dateIndex);
                    let that = this;
                    that.windowTime = setTimeout(function(){that.makeCalendar(that.dateIndex);},300);
                }
            },
            immediate: false
        },
        time: {
            handler(){
                const bookingDate = new Date(`${this.bookingDate} ${this.time}`);
                this.booking.from = bookingDate.getTime();
                if(this.booking.to){this.booking.duration = (this.booking.to-this.booking.from)/60/1000}
            },
            immediate: false
        },
        time2: {
            handler(){
                const bookingDate = new Date(`${this.bookingDate} ${this.time2}`);
                this.booking.to = bookingDate.getTime();
                this.booking.duration = (this.booking.to-this.booking.from)/60/1000;
            },
            immediate: false
        },
        bookingDate: {
            handler(){
                const bookingFromDate = new Date(`${this.bookingDate} ${this.time}`);
                const bookingToDate = new Date(`${this.bookingDate} ${this.time2}`);
                this.booking.from = bookingFromDate.getTime();
                this.booking.to = bookingToDate.getTime();
            },
            immediate: false
        },
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
        const {data, err} = await axios.get(`/api/${this.$route.params.sid}/shop-plan`)
        data.forEach((v)=>{
            this.shopPlans.push(v.title);
        })
    },
    beforeDestroy(){
        clearTimeout(this.touchJudge);
    },
    methods: {
        makeNewUser(){
            this.booking = Object.assign({}, this.booking, {user: {id: null,last_name: null, first_name: null,email: null,phone:null,method: false,message:null,memo:null}})
        },
        hello(user){
            this.booking = Object.assign({}, this.booking, {user: user})
            console.log(user)
        },
        searchTimeOut() {
            clearTimeout(this.timeOut);
            let that = this;
            this.timeOut = setTimeout(() => {
                that.searchUser();
            }, 400);
        },
        searchUser(){
            axios.get(`/api/${this.$route.params.sid}/user`, {
                params: {'cond_user': this.search},
            })
            .then(
                response =>{
                    console.log(response.data)
                    this.users = response.data
                },
            )
            .catch(function (error) {
                console.log(error);
            });
        },
        allowedMinutes: v => v == 0 || v == 30,
        resetBooking(index){
            for (let i = 10*index; i < 10+10*index; i++) {
                for (let j = 0; j < 19; j++) {
                    const newCell = {isBooking: false}
                    this.$set(this.calendar[i],j,{isBooking: false})
                }
            }
        },
        newBookingTime(){
            return this.booking.from-(this.num*(this.booking.duration-30)*60*1000)
        },
        async saveBookingDialog(){
            const params = this.booking;
            const response = await (axios.post(`/api/${this.$route.params.sid}/booking/create`, params));
            this.booking = this.bookings.find((booking)=>{
                return booking.id === response.data.id;
            })
            this.bookings = this.bookings.filter((booking)=>{
                return booking.id !== response.data.id;
            })
            this.checkBooking();
            this.bookings.push(response.data);
            this.isBooking();
            this.bookingDialog = false;
        },
        closeBookingDialog(){
            this.bookingDialog = false;
            this.checkBooking()
            this.bookings.splice(this.bookings.length-1, 1);
            this.isBooking(this.dateIndex);
        },
        touchMake(booking){
            this.booking = Object.assign(booking,{user: {id: null, last_name: null, first_name: null,email: null,phone:null,method: false,message:null,memo:null},isBooking: true,duration: 30,from:booking.date,to: null,shop_id: null,cancelled: false,message: null,plan: ''},{payment: {paid: false}});
            this.bookings.push(this.booking);
            this.isBooking();
            this.bookingDialog = true;
        },
        make(e,booking){
            this.num=0;
            this.booking = Object.assign(booking,{user: {id: null, last_name: null, first_name: null,email: null,phone:null,method: false,message:null,memo:null},isBooking: true,duration: 30,from:booking.date,to: null,shop_id: null,cancelled: false,message: null},{payment: {paid: false}});
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
            this.newBookingTimeFormat();
            this.newBookingDateFormat();
            this.bookingDialog = true;
            this.isMakeMouse = false;
            this.disabled = false;
        },
        newBookingTimeFormat(){
            let changeFromFormat;
            let changeToFormat;
            if (this.num==0) {
                const from = new Date(this.booking.from);
                const fromHours = from.getHours();
                const fromMinutes = from.getMinutes()==0?from.getMinutes()+'0':from.getMinutes();
                changeFromFormat = fromHours + ':' + fromMinutes;
                const to = new Date(this.booking.from+this.booking.duration*60*1000);
                const toHours = to.getHours();
                const toMinutes = to.getMinutes()==0?to.getMinutes()+'0':to.getMinutes();
                changeToFormat = toHours + ':' + toMinutes;
            } else {
                const from = new Date(this.booking.from-(this.booking.duration-30)*60*1000);
                const fromHours = from.getHours();
                const fromMinutes = from.getMinutes()==0?from.getMinutes()+'0':from.getMinutes();
                changeFromFormat = fromHours + ':' + fromMinutes;
                const to = new Date(this.booking.from);
                const toHours = to.getHours();
                const toMinutes = to.getMinutes()==0?to.getMinutes()+'0':to.getMinutes();
                changeToFormat = toHours + ':' + toMinutes;
            }
            this.time = changeFromFormat;
            this.time2 = changeToFormat;
        },
        newBookingDateFormat(){
            let from;
            if (this.num==0) {
                from = new Date(this.booking.from);
            } else {
                from = new Date(this.booking.from-(this.booking.duration-30)*60*1000);
            }
            const year = from.getFullYear();
            let month = from.getMonth()+1;
            if(month==13){
                month=1
            }
            if(month<10){
                month = '0'+ month
            }
            let date = from.getDate();
            if(date<10){
                date = '0' + date
            }
            this.bookingDate = `${year}-${month}-${date}`;
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
    .whole-calendar{
        overflow-x: scroll;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .whole-calendar::-webkit-scrollbar { 
        display:none;
    }
</style>