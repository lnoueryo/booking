<template>
    <div>
        <div class="d-flex text-center">
            <div style="width: 100%;">
                <transition name="fade">
                    <div class="text-center d-flex" style="position:relative">
                        <div style="position:absolute;left:0;right:0;top:0;bottom:0;z-index:0" @mouseleave="dragEnd" @mousemove="dragging($event)" @mouseup="dragEnd" :style="{display: 'block', zIndex: disabled?2:0}"></div>
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
                            <div ref="cells">
                                <div v-for="(c, i) in calendar" :key="i" class="d-flex">
                                    <div class="frame" ref="frame" v-for="(time, j) in c" :key="j">
                                        <router-link v-if="time.isBooking" ref="booking" @mousedown.native="dragStart($event);index1=j;index2=i;" @mousemove.native="dragging($event)" tag="button" class="link" :to="{name: 'detail', params: {id: time.id}}"  :style="{width: width(time, j+i*19),transform: ''}" :disabled="disabled"><span>{{time.user.name}}</span></router-link>
                                        <div @mousemove="dragging($event)" ref="dev" style="padding: 10px 0" v-else><b>&nbsp;&nbsp;</b></div>
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
    </div>
</template>
<script>
export default {
    props: ['duration', 'price', 'title'],
    data() {
        return {
            calendar: [],
            bookings: [],
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
        }
    },
    computed: {
        // translateX() {
        //     return `translateX(${this.currentPosition+this.distance}px)`
        // }
    },
    // watch: {
    //     duration: {
    //         handler(){
    //             this.makeCalendar(this.dateIndex);
    //         },
    //         immediate: true
    //     },
    // },
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
        // translate() {
        //     return `translate(${this.currentPositionX+this.distanceX}px,${this.currentPositionY+this.distanceY}px)`
        // },
        dragStart(e){
            this.startX = e.clientX
            this.startY = e.clientY
            this.isMouse = true;
        },
        dragging(e){
            if (this.isMouse) {
                let calendar = this.calendar[this.index2][this.index1]
                this.disabled = true;
                const movedDistanceX = e.clientX - this.startX
                const movedDistanceY = e.clientY - this.startY
                // const distance = this.currentPosition+movedDistance
                this.distanceX = movedDistanceX;
                this.distanceY = movedDistanceY;
                // if(170>distance && distance > -((this.styles.length-3)*340)-170){
                //     }
                const booking = this.$refs.frame[this.index1+this.index2*19].children[0];
                booking.style.transform = `translate(${calendar.x + this.distanceX}px,${calendar.y + this.distanceY}px)`
                // console.log(this.$refs.frame[this.index1+this.index2*19].children[0].style)
            }
        },
        dragEnd(){
            this.disabled = false;
            this.isMouse = false;
            let calendar = this.calendar[this.index2][this.index1]
            calendar.x = calendar.x + this.distanceX;
            calendar.y = calendar.y + this.distanceY;
            // this.currentPosition = Math.round((this.currentPosition + this.distance)/340)*340;
            this.distanceX = 0;
            this.distanceY = 0;
        },
        // dragEnd(){
        //     this.disabled = false;
        //     this.isMouse = false;
        //     this.currentPositionX = this.currentPositionX + this.distanceX;
        //     this.currentPositionY = this.currentPositionY + this.distanceY;
        //     // this.currentPosition = Math.round((this.currentPosition + this.distance)/340)*340;
        //     this.distanceX = 0;
        //     this.distanceY = 0;
        // },
        width(booking, index){
            const width = (this.$refs.frame[index].getBoundingClientRect().width*(booking.duration/30)-10) + 'px';
            return width
        },
        sqlToJsDate(sqlDate){
            //sqlDate in SQL DATETIME format ("yyyy-mm-dd hh:mm:ss.ms")
            var sqlDateArr1 = sqlDate.split("-");
            //format of sqlDateArr1[] = ['yyyy','mm','dd hh:mm:ms']
            var sYear = sqlDateArr1[0];
            var sMonth = (Number(sqlDateArr1[1]) - 1).toString();
            var sqlDateArr2 = sqlDateArr1[2].split(" ");
            //format of sqlDateArr2[] = ['dd', 'hh:mm:ss.ms']
            var sDay = sqlDateArr2[0];
            var sqlDateArr3 = sqlDateArr2[1].split(":");
            //format of sqlDateArr3[] = ['hh','mm','ss.ms']
            var sHour = sqlDateArr3[0];
            var sMinute = sqlDateArr3[1];
            var sqlDateArr4 = sqlDateArr3[2].split(".");
            //format of sqlDateArr4[] = ['ss','ms']
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
                console.log(3)
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
        }
    }
}
</script>

<style scoped>
    .link{
        position: absolute;
        background-color: #757575;
        overflow:hidden;
        left:5px;
        top:25%;
        z-index:1;
        height: 25px;
        border-radius:5px;
        color:white;
        text-decoration:none
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
        max-width:63px;
        width:100%;
        height: 46px;
    }
</style>