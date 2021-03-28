<template>
    <div v-resize="onResize">
        <transition name="fade">
            <div v-show="ready">
                <router-view name="header"></router-view>
            </div>
        </transition>
        <v-main>
            <v-container>
                <transition name="fade" mode="out-in">
                    <div v-show="ready"><router-view></router-view></div>
                </transition>
            </v-container>
        </v-main>
        <img @load="load" src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg" alt="" style="display:none">
    </div>
</template>

<script>
export default {
    data() {
        return {
            ready: false,
            windowSize: {
                x: 0,
                y: 0,
            },
        }
    },
    mounted () {
        this.onResize()
    },
    methods:{
        load(){
            this.ready = true
        },
        onResize () {
            this.$store.dispatch('windowSize', { x: window.innerWidth, y: window.innerHeight });
        },
    },
    async beforeCreate(){
        const session = (await axios.get('/api/check')).data;
        const user = localStorage.getItem('user');
        if(session){
            this.$store.dispatch('loadUser')
        } else if(!session && user) {
            this.$store.dispatch('logout')
        }
        // Push.create('ログインが完了しました',{
        //     icon: '/images/logo/logo01.png',
        //     timeout: 30000,
        //     onClick: function () {
        //         window.focus();
        //         this.close();
        //     }
        // })
    }
}
</script>