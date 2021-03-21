<template>
    <div>
        <div class="d-flex text-center">
            <div style="width: 100%;">
                <transition name="fade">
                    <div ref="calendar" class="text-center d-flex">
                        <div>
                            <div style="border: 1px solid #e0e0e0;color: #000;">Date\Time</div>
                            <div v-for="(c, i) in calendar" :key="i">
                                <div v-if="days.length!=0">
                                    <div style="padding: 10px 0;height: 46px;border-left: 1px solid #e0e0e0;border-right: 1px solid #e0e0e0;border-bottom: 1px solid #e0e0e0;color: #000;">{{ days[i].getMonth()+1}}/{{ days[i].getDate() }} {{ dayOfWeek(days[i].getDay()) }}</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex text-center">
                                <div class="period" v-for="(period , i) in periods" :key="i"><span>{{ period }}</span></div>
                            </div>
                            <div ref="cells" style="position:relative">
                            <div style="position:absolute;left:0;right:0;top:0;bottom:0;z-index:0" @mousemove="dragging($event)" @mouseleave="dragEnd" @mouseup="dragEnd" @touchmove="touchMove($event)" @touchend="dragEnd" :style="{display: 'block', zIndex: disabled?2:0}"></div>
                                <div v-for="(c, i) in calendar" :key="i" class="d-flex">
                                    <div class="frame" ref="frame" v-for="(time, j) in c" :key="j">
                                        <router-link
                                        @mousedown.native="dragStart($event);index1=j;index2=i;booking=time"
                                        @touchstart.native.prevent="touchStart($event);index1=j;index2=i;booking=time"
                                        @touchmove.native="touchMove($event)"
                                        @touchend.native="touchEnd({name: 'detail', params: {id: time.id}})"
                                        tag="button" class="link" ref="booking"
                                        :to="{name: 'detail', params: {id: time.id}}"
                                        :style="{width: width(time, j+i*19),transform: ''}"
                                        :disabled="disabled"
                                        v-if="time.isBooking"
                                        >
                                        <span>{{time.user.name}}</span>
                                        </router-link>
                                        <div @mousemove="dragging($event)" @touchmove="touchMove($event)" ref="dev" style="padding: 10px 0" v-else><b>&nbsp;&nbsp;</b></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <v-btn @click="previousWeek">back</v-btn>
                <v-btn @click="nextWeek">next</v-btn>
            </div>
        </div>
        <v-dialog
            v-model="dialog"
            max-width="400"
            >
            <v-card>
                <v-card-title class="headline" v-html="dialogTitle">
                </v-card-title>

                <v-card-text v-html="dialogMessage">
                </v-card-text>

                <v-card-actions>
                <!-- <v-spacer></v-spacer> -->

                <v-btn color="indigo darken-1" text @click="changeTime">
                    はい
                </v-btn>
                <v-btn color="red darken-1" text @click="undo">
                    いいえ
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
export default {
    props: ['duration', 'price', 'title'],
    data() {
        return {
            calendar: [],
            bookings: [],
            booking: '',
            changedBooking: '',
            periods: ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30','19:00'],
            days: this.makeTwoWeeks(0),
            dateIndex: 0,
            ready: false,
            startX: 0,
            startY: 0,
            isMouse: false,
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
        }
    },
    async created(){
        const startDate = new Date();
        startDate.setHours(0);
        startDate.setMinutes(0);
        startDate.setSeconds(0);
        let endDate = new Date();
        endDate.setDate(endDate.getDate()+29);
        endDate.setHours(0);
        endDate.setMinutes(0);
        endDate.setSeconds(0);
        const response = await (axios.get('https://lnoueryo98.sakura.ne.jp/seasons/api/booking'));
        const bookings = response.data;
        for (let i = 0; i < bookings.length; i++) {
            bookings[i].from = await this.sqlToJsDate(bookings[i].from)
            bookings[i].to = await this.sqlToJsDate(bookings[i].to)
            await this.bookings.push(bookings[i]);
        }
        this.makeCalendar(0);
    },
    methods: {
        changeTime(){
            const params = {from: new Date(this.changedBooking.newFrom), to: new Date(this.changedBooking.newTo)}
            console.log(params)
            axios.post(`https://lnoueryo98.sakura.ne.jp/seasons/api/booking/update/${this.booking.id}`,params)
            .then((response)=>{
                this.bookings = this.bookings.filter((booking)=>{
                    return booking.id !== this.booking.id;
                })
                response.data.from = new Date(response.data.from);
                response.data.to = new Date(response.data.to);
                this.bookings.push(response.data);
                this.makeCalendar(this.dateIndex);
                this.dialog = false
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
                const cells = this.$refs.cells;
                this.distanceX = movedDistanceX;
                this.distanceY = movedDistanceY;
                const judgeLeft = cells.getBoundingClientRect().left-30 < booking.getBoundingClientRect().left;
                const judgeTop = cells.getBoundingClientRect().top-10 < booking.getBoundingClientRect().top;
                const judgeRight = cells.getBoundingClientRect().right+30 > booking.getBoundingClientRect().right;
                const judgeBottom = cells.getBoundingClientRect().bottom+10 > booking.getBoundingClientRect().bottom;
                if(judgeRight && judgeLeft && judgeTop && judgeBottom){
                    booking.style.transform = `translate(${calendar.x + this.distanceX}px,${calendar.y + this.distanceY}px)`;
                } else {
                    this.distanceX = 0;
                    this.distanceY = 0;
                    booking.style.transform = `translate(${0}px,${0}px)`;
                    this.touchEnd()
                }
            }
        },
        // touchMove(e){
        //     if (this.isMouse) {
        //         let calendar = this.calendar[this.index2][this.index1]
        //         this.disabled = true;
        //         const movedDistanceX = e.touches[0].clientX - this.startX
        //         const movedDistanceY = e.touches[0].clientY - this.startY
        //         // const distance = this.currentPosition+movedDistance
        //         this.distanceX = movedDistanceX;
        //         this.distanceY = movedDistanceY;
        //         // if(170>distance && distance > -((this.styles.length-3)*340)-170){
        //         //     }
        //         const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
        //         booking.style.transform = `translate(${calendar.x + this.distanceX}px,${calendar.y + this.distanceY}px)`
        //     }
        // },
        dragging(e){
            if (this.isMouse) {
                let calendar = this.calendar[this.index2][this.index1]
                this.disabled = true;
                const movedDistanceX = e.clientX - this.startX
                const movedDistanceY = e.clientY - this.startY
                const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
                const cells = this.$refs.cells;
                this.distanceX = movedDistanceX;
                this.distanceY = movedDistanceY;
                const judgeLeft = cells.getBoundingClientRect().left-30 < booking.getBoundingClientRect().left;
                const judgeTop = cells.getBoundingClientRect().top-10 < booking.getBoundingClientRect().top;
                const judgeRight = cells.getBoundingClientRect().right+30 > booking.getBoundingClientRect().right;
                const judgeBottom = cells.getBoundingClientRect().bottom+10 > booking.getBoundingClientRect().bottom;
                if(judgeRight && judgeLeft && judgeTop && judgeBottom){
                    booking.style.transform = `translate(${calendar.x + this.distanceX}px,${calendar.y + this.distanceY}px)`;
                } else {
                    this.distanceX = 0;
                    this.distanceY = 0;
                    booking.style.transform = `translate(${0}px,${0}px)`;
                    this.dragEnd()
                }
            }
        },
        touchEnd(root){
            if (this.isMouse) {
                const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
                const cells = this.$refs.cells;
                let calendar = this.calendar[this.index2][this.index1]
                const check = this.check(Math.round((calendar.x + this.distanceX)/63), Math.round((calendar.y + this.distanceY)/46));
                this.disabled = false;
                this.isMouse = false;
                if (!check.isCheck&&!check.overBooking) {
                    calendar.x  = Math.round((calendar.x + this.distanceX)/63)*63;
                    calendar.y  = Math.round((calendar.y + this.distanceY)/46)*46;
                    booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
                    this.dialogTitle = '予約変更'
                    this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    this.dialog = true;
                    this.changedBooking = Object.assign(this.booking, check)
                } else if(check.isCheck&&check.overBooking) {
                    calendar.x  = Math.round((calendar.x + this.distanceX)/63)*63;
                    calendar.y  = Math.round((calendar.y + this.distanceY)/46)*46;
                    booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
                    this.dialogTitle = '<h3 style="color:red">ダブルブッキング！！</h3>'
                    this.dialogMessage = `以下のように変更すると予約が重なってしまいます。確認してください。<br><b>予約1</b>：${this.date(check.overBooking.from)}～${this.date((check.overBooking.to).getTime()+60*1000)}<br><b>予約2</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    this.dialog = true;
                    this.changedBooking = Object.assign(booking, check)
                } else {
                    booking.style.transform = `translate(0px,0px)`;
                }
                booking.style.opacity = 1
                this.distanceX = 0;
                this.distanceY = 0;
            } else {
                clearTimeout(this.touchJudge)
                this.$router.push(root)
            }
        },
        dragEnd(){
            const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
            const cells = this.$refs.cells;
            let calendar = this.calendar[this.index2][this.index1]
            const check = this.check(Math.round((calendar.x + this.distanceX)/63), Math.round((calendar.y + this.distanceY)/46));
            this.disabled = false;
            this.isMouse = false;
            if (!check.isCheck&&!check.overBooking) {
                calendar.x  = Math.round((calendar.x + this.distanceX)/63)*63;
                calendar.y  = Math.round((calendar.y + this.distanceY)/46)*46;
                booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
                this.dialogTitle = '予約変更'
                this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                this.dialog = true;
                this.changedBooking = Object.assign(this.booking, check)
            } else if(check.isCheck&&check.overBooking) {
                calendar.x  = Math.round((calendar.x + this.distanceX)/63)*63;
                calendar.y  = Math.round((calendar.y + this.distanceY)/46)*46;
                booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
                this.dialogTitle = '<h3 style="color:red">ダブルブッキング！！</h3>'
                this.dialogMessage = `以下のように変更すると予約が重なってしまいます。確認してください。<br><b>予約1</b>：${this.date(check.overBooking.from)}～${this.date((check.overBooking.to).getTime()+60*1000)}<br><b>予約2</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                this.dialog = true;
                this.changedBooking = Object.assign(booking, check)
            } else {
                booking.style.transform = `translate(0px,0px)`;
            }
            booking.style.opacity = 1
            this.distanceX = 0;
            this.distanceY = 0;
        },
        // dragEnd(){
        //     this.disabled = false;
        //     this.isMouse = false;
        //     const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
        //     const cells = this.$refs.cells;
        //     let calendar = this.calendar[this.index2][this.index1]
        //     if((cells.getBoundingClientRect().x-20)<booking.getBoundingClientRect().x){
        //         // calendar.x = calendar.x + this.distanceX;
        //         // calendar.y = calendar.y + this.distanceY;
        //         // console.log(Math.round((calendar.x + this.distanceX)/63))
        //         calendar.x  = Math.round((calendar.x + this.distanceX)/63)*63;
        //         calendar.y  = Math.round((calendar.y + this.distanceY)/46)*46;
        //         booking.style.transform = `translate(${calendar.x}px,${calendar.y}px)`;
        //         booking.style.opacity = 1
        //         this.distanceX = 0;
        //         this.distanceY = 0;
        //         this.check(Math.round((calendar.x + this.distanceX)/63), Math.round((calendar.y + this.distanceY)/46));
        //         // this.$router.go({path: this.$router.currentRoute.path, force: true})
        //     } else {
        //         this.distanceX = 0;
        //         this.distanceY = 0;
        //         calendar.x  = 0;
        //         calendar.y  = 0;
        //         booking.style.transform = `translate(${0}px,${0}px)`;
        //     }
        // },
        check(x, y){
            const from = this.floor(this.booking.from);
            const to = this.floor(this.booking.to);
            const newDate = y*24*60*60
            const newFrom = from + x*30*60 + newDate;
            const newTo = to + x*30*60 + newDate;
            const condition3 = from !== newFrom;
            const condition4 = (new Date(newFrom*1000)).getHours()>=10;
            if (condition3 && condition4) {
                let bookings = this.bookings.filter((v)=>{
                    return v.id !== this.booking.id
                })
                const isCheck = bookings.find((booking)=>{
                    const condition1 = newFrom <= this.floor(booking.to) && newTo >= this.floor(booking.to);
                    const condition2 = this.floor(booking.from) < newTo && this.floor(booking.to) >= newTo;
                    return condition1 || condition2;
                })
                return {isCheck: isCheck ? true : false, from: from*1000, to: to*1000, newFrom: newFrom*1000, newTo:newTo*1000,overBooking: isCheck ? isCheck : ''}
            } else {
                return {isCheck: true, from: from*1000, to: to*1000, newFrom: newFrom*1000, newTo:newTo*1000}
            }
        },
        floor(time){
            return Math.floor(time.getTime()/1000)
        },
        width(booking, index){
            const width = (this.$refs.frame[index].getBoundingClientRect().width*(booking.duration/30)-10) + 'px';
            return width
        },
        sqlToJsDate(sqlDate){
            var sqlDateArr1 = sqlDate.split("-");
            var sYear = sqlDateArr1[0];
            var sMonth = (Number(sqlDateArr1[1]) - 1).toString();
            var sqlDateArr2 = sqlDateArr1[2].split(" ");
            var sDay = sqlDateArr2[0];
            var sqlDateArr3 = sqlDateArr2[1].split(":");
            var sHour = sqlDateArr3[0];
            var sMinute = sqlDateArr3[1];
            var sqlDateArr4 = sqlDateArr3[2].split(".");
            var sSecond = sqlDateArr4[0];
            return new Date(sYear,sMonth,sDay,sHour,sMinute,sSecond);
        },
        async makeCalendar(index){
            this.calendar = [];
            axios.get('https://lnoueryo98.sakura.ne.jp/seasons/api/time').then(response => {
                for (let i = 10*index; i < 10+10*index; i++) {
                    let dates = []
                    for (let j = 0; j < 19; j++) {
                        const today = new Date(response.data)
                        today.setMonth(today.getMonth())
                        today.setDate(today.getDate()+i)
                        today.setHours(10)
                        today.setMinutes(30*j)
                        today.setSeconds(0)
                        dates.push({date: today, isBooking: false, x: 0, y: 0});
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
                    this.bookings.forEach((booking)=>{
                        if (this.floor(booking.from)==this.floor(time.date)) {
                            Object.assign(this.calendar[i][j],booking);
                            this.$set(this.calendar[i][j], 'isBooking', true);
                        }
                    })
                })
            })
        },
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
                that.ready = false;
            },200)
        },
        floor(time){
            return Math.floor(time.getTime()/1000)
        },
        makeTwoWeeks(index){
            let daysArray = [];
            axios.get('https://lnoueryo98.sakura.ne.jp/seasons/api/time').then(response => {
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
            this.ready = true;
            this.calendar = [];
            this.dateIndex+=1;
            this.days = this.makeTwoWeeks(this.dateIndex);
            this.makeCalendar(this.dateIndex)
        },
        previousWeek(){
            this.ready = true;
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
        background-color: #757575;
        overflow:hidden;
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
        width:63px;
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