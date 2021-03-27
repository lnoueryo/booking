<template>
    <div>
        <v-img class="img" src="/images/404.png"></v-img>
        <v-btn class="img white--text" color="indigo" style="height:60px" @click="logout">戻る</v-btn>
    </div>
</template>

<script>
export default {
    methods: {
        logout(){
            this.$store.dispatch('logout')
        }
    }
}
</script>
<style>
    .img{
        transform: translateY(7%) translateX(18%);
        width: 75%;
    }
</style>