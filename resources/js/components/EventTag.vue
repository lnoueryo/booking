<template>
    <div>
        <!-- <router-link
        :to="{name: 'detail', params: {id: booking.id}}"
        @mousemove="dragging($event)"
                :disabled="disabled"
        >
        <button
        :style="{width: width(booking, frameWidth),transform: '',opacity: 0.85}"
        @mousedown="dragStart($event);"
        @touchstart.prevent="touchStart($event);"
        @touchmove="touchMove($event)"
        @touchend="touchEnd({name: 'detail', params: {id: booking.id}})"
        @mousemove="dragging($event)"
        class="link"
        ref="booking"
        >{{booking.user.name}}</button>
        </router-link> -->
        <router-link
        @mousedown.native="dragStart($event);"
        @mousemove.native="dragging($event)"
        @touchstart.native.prevent="touchStart($event);"
        @touchmove.native="touchMove($event)"
        @touchend.native="touchEnd({name: 'detail', params: {id: booking.id}})"
        tag="button"
        :to="{name: 'detail', params: {id: booking.id}}"
        :disabled="disabled"
        >
        <span :disabled="disabled" @mousemove="dragging($event)" @mouseup="dragEnd" ref="booking" class="link" :style="{width: width(booking, frameWidth),transform: '',opacity: 0.85}">{{booking.user.name}}</span>
        </router-link>
    </div>
</template>

<script>
export default {
    props: ['index', 'booking', 'frameWidth', 'cells'],
    data() {
        return {
            calendar: [],
            bookings: [],
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
            touchJudge: '',
            dialog: false,
            dialogTitle: '',
            dialogMessage: '',
        }
    },
    methods: {
        changeTime(){
            const params = {from: this.changedBooking.newFrom, to: this.changedBooking.newTo}
            axios.put(`api/booking/${this.booking.id}`,params)
            .then((response)=>{
                this.bookings = this.bookings.filter((booking)=>{
                    return booking.id !== this.booking.id;
                })
                response.data.from = new Date(response.data.from);
                response.data.to = new Date(response.data.to);
                this.bookings.push(response.data);
                this.isBooking(this.dateIndex);
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
            this.touchJudge = setTimeout(function(){that.isMouse = true;that.$refs.booking[that.index].style.opacity=0.6},200)
        },
        dragStart(e){
            this.startX = e.clientX
            this.startY = e.clientY
            let that = this;
            this.touchJudge = setTimeout(function(){that.isMouse = true;that.$refs.booking.style.opacity = 0.6;that.disabled = true;},200)
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
                    this.touchEnd({name: 'detail', params: {id: this.booking.id}})
                }
            }
        },
        dragging(e){
            if (this.isMouse) {
                this.disabled = true;
                const movedDistanceX = e.clientX - this.startX
                const movedDistanceY = e.clientY - this.startY
                const booking = this.$refs.booking;
                this.distanceX = movedDistanceX;
                this.distanceY = movedDistanceY;
                if(this.isInsideFrame(booking)){
                    booking.style.transform = `translate(${this.currentPositionX + this.distanceX}px,${this.currentPositionY + this.distanceY}px)`;
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
                    this.dialogTitle = '予約変更'
                    this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    this.transform(booking, calendar, check)
                } else if(check.isCheck&&check.overBooking) {
                    this.dialogTitle = '<h3 style="color:red">ダブルブッキング！！</h3>'
                    this.dialogMessage = `以下のように変更すると予約が重なってしまいます。確認してください。<br><b>予約1</b>：${this.date(check.overBooking.from)}～${this.date((check.overBooking.to).getTime()+60*1000)}<br><b>予約2</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                    this.transform(booking, calendar, check)
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
            const booking = this.$refs.booking;
            // const check = this.check(Math.round((this.currentPositionX + this.distanceX)/63), Math.round((this.currentPositionY + this.distanceY)/46));
                this.dialogTitle = '予約変更'
                // this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
                this.transform(booking)
            // if (!check.isCheck&&!check.overBooking) {
            //     this.dialogTitle = '予約変更'
            //     this.dialogMessage = `以下のように変更しようとしています。確認してください。<br><b>変更前</b>：${this.date(check.from)}～${this.date(check.to+60*1000)}<br><b>変更後</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
            //     this.transform(booking, check)
            // } else if(check.isCheck&&check.overBooking) {
            //     this.dialogTitle = '<h3 style="color:red">ダブルブッキング！！</h3>'
            //     this.dialogMessage = `以下のように変更すると予約が重なってしまいます。確認してください。<br><b>予約1</b>：${this.date(check.overBooking.from)}～${this.date((check.overBooking.to)+60*1000)}<br><b>予約2</b>：${this.date(check.newFrom)}～${this.date(check.newTo+60*1000)}`
            //     this.transform(booking, check)
            // } else {
            //     booking.style.transform = `translate(0px,0px)`;
            // }
            booking.style.opacity = 0.9
            this.distanceX = 0;
            this.distanceY = 0;
            this.isMouse = false;
            // this.disabled = false;
        },
        transform(booking, check){
            this.currentPositionX  = Math.round((this.currentPositionX + this.distanceX)/63)*63;
            this.currentPositionY  = Math.round((this.currentPositionY + this.distanceY)/46)*46;
            booking.style.transform = `translate(${this.currentPositionX}px,${this.currentPositionY}px)`;
            this.dialog = true;
            // this.changedBooking = Object.assign(this.booking, check)
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
            const judgeLeft = this.cells.getBoundingClientRect().left-30 < booking.getBoundingClientRect().left;
            const judgeTop = this.cells.getBoundingClientRect().top-10 < booking.getBoundingClientRect().top;
            const judgeRight = this.cells.getBoundingClientRect().right+30 > booking.getBoundingClientRect().right;
            const judgeBottom = this.cells.getBoundingClientRect().bottom+10 > booking.getBoundingClientRect().bottom;
            return judgeRight && judgeLeft && judgeTop && judgeBottom ? true : false;
        },
        width(booking, frameWidth){
            const width = (frameWidth.width*(booking.duration/30)-10) + 'px';
            return width;
        },
        async makeCalendar(index){
            this.calendar = [];
            axios.get('api/time').then(response => {
                for (let i = 10*index; i < 10+10*index; i++) {
                    let dates = []
                    for (let j = 0; j < 19; j++) {
                        const today = new Date(response.data)
                        today.setMonth(today.getMonth())
                        today.setDate(today.getDate()+i)
                        today.setHours(10)
                        today.setMinutes(30*j)
                        today.setSeconds(0)
                        dates.push({date: today.getTime(), isBooking: false, x: 0, y: 0});
                    }
                    this.calendar.push(dates)
                }
            })
            .then(()=>{
                this.bookableDate((this.duration/30)-1);
            })
            .catch((error)=>{
                console.log(error)
                this.ready = false;
            })
        },
        // isBooking(){
        //     this.calendar.forEach((date, i) => {
        //         date.forEach((time, j) => {
        //             this.bookings.forEach((booking)=>{
        //                 if (this.floor(booking.from)==this.floor(time.date)) {
        //                     Object.assign(this.calendar[i][j],booking);
        //                     this.$set(this.calendar[i][j], 'isBooking', true);
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
                that.ready = false;
            },200)
        },
        makeTwoWeeks(index){
            let daysArray = [];
            axios.get('api/time').then(response => {
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