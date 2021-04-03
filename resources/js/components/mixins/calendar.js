export default {
    data() {
        return {
            periods: ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30','19:00'],
        }
    },
    computed:{
        maxDate(){
            const today = new Date();
            const year = today.getFullYear();
            let month = today.getMonth()+1;
            if(month==13){
                month=1
            }
            if(month<10){
                month = '0'+ month
            }
            today.setDate(today.getDate()+20);
            let date = today.getDate();
            if(date<10){
                date = '0' + date
            }
            console.log(`${year}-${month}-${date}`)
            return `${year}-${month}-${date}`;
        },
    },
    methods: {
        dayOfWeek(num){
            const dayOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
            return dayOfWeek[num]
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