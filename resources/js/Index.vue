<template>
    <div>
        <header-view></header-view>
        <v-main>
            <v-container>
                <transition name="fade" mode="out-in">
                    <router-view></router-view>
                </transition>
            </v-container>
        </v-main>
    </div>
</template>

<script>
import HeaderView from "../js/components/gloabals/Header";
export default {
    components: {
        HeaderView
    }
}
</script>