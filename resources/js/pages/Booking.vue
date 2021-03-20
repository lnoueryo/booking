<template>
    <!-- <div>
        <div class="d-flex text-center">
            <div style="width: 100%;">
                <transition name="fade">
                    <div class="d-flex text-center justify-content-start" style="justify-content: start;">
                        <div>
                            <div style="padding: 10px;">Date\Time</div>
                            <div v-for="(c, i) in calendar" :key="i" style="padding: 10px;">
                                <div v-if="days.length!=0">
                                    <div>{{ days[i].getMonth()+1}}/{{ days[i].getDate() }} {{ dayOfWeek(days[i].getDay()) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex text-center justify-content-start">
                            <div class="p-1" style="padding: 10px;height: 31px" v-for="(period , i) in periods" :key="i"><span>{{ period }}</span></div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </div> -->
  <v-row class="fill-height">
    <v-col>
      <v-sheet height="600">
        <v-calendar
          ref="calendar"
          v-model="value"
          color="primary"
          type="4day"
          :events="events"
          :event-color="getEventColor"
          :event-ripple="false"
          @change="getEvents"
          @mousedown:event="startDrag"
          @mousedown:time="startTime"
          @mousemove:time="mouseMove"
          @mouseup:time="endDrag"
          @mouseleave.native="cancelDrag"
        >
          <template v-slot:event="{ event, timed, eventSummary }">
            <div
              class="v-event-draggable"
              v-html="eventSummary()"
            ></div>
            <div
              v-if="timed"
              class="v-event-drag-bottom"
              @mousedown.stop="extendBottom(event)"
            ></div>
          </template>
        </v-calendar>
      </v-sheet>
    </v-col>
  </v-row>
</template>
<script>
// import axios from "axios";
// import firebase from '@/plugins/firebase.js'
// export default {
//     props: ['duration', 'price', 'title'],
//     data() {
//         return {
//             calendar: [],
//             bookings: [],
//             periods: ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30','19:00'],
//             days: this.makeTwoWeeks(0),
//             dateIndex: 0,
//             ready: false,
//         }
//     },
//     // watch: {
//     //     duration: {
//     //         handler(){
//     //             this.makeCalendar(this.dateIndex);
//     //         },
//     //         immediate: true
//     //     },
//     // },
//     created(){
//         const startDate = new Date();
//         startDate.setHours(0);
//         startDate.setMinutes(0);
//         startDate.setSeconds(0);
//         let endDate = new Date();
//         endDate.setDate(endDate.getDate()+29);
//         endDate.setHours(0);
//         endDate.setMinutes(0);
//         endDate.setSeconds(0);
//         const db = firebase.firestore();
//         db.collection('bookings').orderBy('from', 'asc').startAt(startDate).endAt(endDate).get().then((querySnapshot) =>{
//             querySnapshot.forEach(async(doc) => {
//                 this.bookings.push(doc.data());
//             });
//             this.makeCalendar(0)
//         });
//     },
//     methods: {
//         makeCalendar(index){
//             this.calendar = [];
//             axios.head(window.location.href).then(response => {
//                 for (let i = 10*index; i < 10+10*index; i++) {
//                     let dates = []
//                     for (let j = 0; j < 19; j++) {
//                         const today = new Date(response.headers.date)
//                         today.setMonth(today.getMonth())
//                         today.setDate(today.getDate()+i)
//                         today.setHours(10)
//                         today.setMinutes(30*j)
//                         today.setSeconds(0)
//                         dates.push({date: today, isBooking: false});
//                     }
//                     this.calendar.push(dates)
//                 }
//             })
//             .then(()=>{
//                 this.getBookings()
//             })
//             .then(()=>{
//                 this.bookableDate((this.duration/30)-1);
//             })
//             .catch((error)=>{
//                 console.log(error)
//                 this.ready = false;
//             })
//         },
//         getBookings(){
//             this.calendar.forEach((date, i) => {
//                 date.forEach((time, j) => {
//                     this.bookings.forEach(booking=>{
//                         if (this.floor(booking.from.toDate())<=this.floor(time.date)&&this.floor(time.date)<this.floor(booking.to.toDate())) {
//                             this.$set(this.calendar[i][j], 'isBooking', true)
//                         }
//                     })
//                 })
//             })
//         },
//         bookableDate(index){
//             this.calendar.forEach((date, i) => {
//                 date.forEach((time, j) => {
//                     this.bookings.forEach(booking=>{
//                         if (this.floor(booking.from.toDate())==this.floor(time.date)) {
//                             for (let k = 1; k < index+1; k++) {
//                                 if((j-k)!==-1){
//                                     this.$set(this.calendar[i][j-k], 'isBooking', true);
//                                 } else {
//                                     return;
//                                 }
//                             }
//                         }
//                     })
//                 })
//             })
//             this.calendar.forEach((date, i) => {
//                 date.forEach((time, j)=>{
//                     if(index!==0 && (date.length-index)<=j){
//                     this.$set(this.calendar[i][j], 'isBooking', true)
//                 }
//                 })
//             })
//             let that = this;
//             setTimeout(function(){
//                 that.ready = false;
//             },200)
//         },
//         floor(time){
//             return Math.floor(time.getTime()/1000)
//         },
//         makeTwoWeeks(index){
//             let daysArray = [];
//             axios.head(window.location.href).then(response => {
//                 for (let i = 10*index; i < 10+10*index; i++) {
//                     const days = new Date(response.headers.date);
//                     days.setMonth(days.getMonth())
//                     days.setDate(days.getDate()+i)
//                     daysArray.push(days);
//                 }
//             })
//             return daysArray;
//         },
//         nextWeek(){
//             this.ready = true;
//             this.calendar = [];
//             this.dateIndex+=1;
//             this.days = this.makeTwoWeeks(this.dateIndex);
//             this.makeCalendar(this.dateIndex)
//         },
//         previousWeek(){
//             this.ready = true;
//             this.calendar = [];
//             this.dateIndex-=1;
//             this.days = this.makeTwoWeeks(this.dateIndex);
//             this.makeCalendar(this.dateIndex)
//         },
//         dayOfWeek(num){
//             const dayOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
//             return dayOfWeek[num]
//         }
//     }
// }
</script>
<script>
  export default {
    data: () => ({
      value: '',
      events: [],
      colors: ['#2196F3', '#3F51B5', '#673AB7', '#00BCD4', '#4CAF50', '#FF9800', '#757575'],
      names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
      dragEvent: null,
      dragStart: null,
      createEvent: null,
      createStart: null,
      extendOriginal: null,
    }),
    methods: {
      startDrag ({ event, timed }) {
        if (event && timed) {
          this.dragEvent = event
          this.dragTime = null
          this.extendOriginal = null
        }
      },
      startTime (tms) {
        const mouse = this.toTime(tms)

        if (this.dragEvent && this.dragTime === null) {
          const start = this.dragEvent.start

          this.dragTime = mouse - start
        } else {
          this.createStart = this.roundTime(mouse)
          this.createEvent = {
            name: `Event #${this.events.length}`,
            color: this.rndElement(this.colors),
            start: this.createStart,
            end: this.createStart,
            timed: true,
          }

          this.events.push(this.createEvent)
        }
      },
      extendBottom (event) {
        this.createEvent = event
        this.createStart = event.start
        this.extendOriginal = event.end
      },
      mouseMove (tms) {
        const mouse = this.toTime(tms)

        if (this.dragEvent && this.dragTime !== null) {
          const start = this.dragEvent.start
          const end = this.dragEvent.end
          const duration = end - start
          const newStartTime = mouse - this.dragTime
          const newStart = this.roundTime(newStartTime)
          const newEnd = newStart + duration

          this.dragEvent.start = newStart
          this.dragEvent.end = newEnd
        } else if (this.createEvent && this.createStart !== null) {
          const mouseRounded = this.roundTime(mouse, false)
          const min = Math.min(mouseRounded, this.createStart)
          const max = Math.max(mouseRounded, this.createStart)

          this.createEvent.start = min
          this.createEvent.end = max
        }
      },
      endDrag () {
        this.dragTime = null
        this.dragEvent = null
        this.createEvent = null
        this.createStart = null
        this.extendOriginal = null
      },
      cancelDrag () {
        if (this.createEvent) {
          if (this.extendOriginal) {
            this.createEvent.end = this.extendOriginal
          } else {
            const i = this.events.indexOf(this.createEvent)
            if (i !== -1) {
              this.events.splice(i, 1)
            }
          }
        }

        this.createEvent = null
        this.createStart = null
        this.dragTime = null
        this.dragEvent = null
      },
      roundTime (time, down = true) {
        const roundTo = 15 // minutes
        const roundDownTime = roundTo * 60 * 1000

        return down
          ? time - time % roundDownTime
          : time + (roundDownTime - (time % roundDownTime))
      },
      toTime (tms) {
        return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
      },
      getEventColor (event) {
        const rgb = parseInt(event.color.substring(1), 16)
        const r = (rgb >> 16) & 0xFF
        const g = (rgb >> 8) & 0xFF
        const b = (rgb >> 0) & 0xFF

        return event === this.dragEvent
          ? `rgba(${r}, ${g}, ${b}, 0.7)`
          : event === this.createEvent
            ? `rgba(${r}, ${g}, ${b}, 0.7)`
            : event.color
      },
      getEvents ({ start, end }) {
        const events = []

        const min = new Date(`${start.date}T00:00:00`).getTime()
        const max = new Date(`${end.date}T23:59:59`).getTime()
        const days = (max - min) / 86400000
        const eventCount = this.rnd(days, days + 20)

        for (let i = 0; i < eventCount; i++) {
          const timed = this.rnd(0, 3) !== 0
          const firstTimestamp = this.rnd(min, max)
          const secondTimestamp = this.rnd(2, timed ? 8 : 288) * 900000
          const start = firstTimestamp - (firstTimestamp % 900000)
          const end = start + secondTimestamp

          events.push({
            name: this.rndElement(this.names),
            color: this.rndElement(this.colors),
            start,
            end,
            timed,
          })
        }

        this.events = events
      },
      rnd (a, b) {
        return Math.floor((b - a + 1) * Math.random()) + a
      },
      rndElement (arr) {
        return arr[this.rnd(0, arr.length - 1)]
      },
    },
  }
</script>
<style scoped lang="scss">
.v-event-draggable {
  padding-left: 6px;
}

.v-event-timed {
  user-select: none;
  -webkit-user-select: none;
}

.v-event-drag-bottom {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 4px;
  height: 4px;
  cursor: ns-resize;

  &::after {
    display: none;
    position: absolute;
    left: 50%;
    height: 4px;
    border-top: 1px solid white;
    border-bottom: 1px solid white;
    width: 16px;
    margin-left: -8px;
    opacity: 0.8;
    content: '';
  }

  &:hover::after {
    display: block;
  }
}
</style>