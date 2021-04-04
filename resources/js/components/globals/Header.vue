<template>
    <div>
        <v-navigation-drawer v-model="drawer" fixed bottom temporary src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg" dark>
            <v-list>
                <!-- <v-list-item>
                <v-list-item-avatar>
                    <v-img src="https://cdn.vuetifyjs.com/images/john.png"></v-img>
                </v-list-item-avatar>
                </v-list-item> -->

                <v-list-item link @click="show = !show">
                <v-list-item-content>
                    <v-list-item-title>
                    {{ user.last_name }}&nbsp;{{ user.first_name }}
                    </v-list-item-title>
                    <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action>
                <v-spacer></v-spacer>

                <!-- <v-btn icon> -->
                    <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                <!-- </v-btn> -->
                </v-list-item-action>
                </v-list-item>
                <v-expand-transition>
                    <div v-show="show">
                        <v-divider></v-divider>
                        <v-list-item link @click="show = !show">

                        <v-card-text>
                        I'm a thing. But, like most politicians, he promised more than he could deliver. You won't have time for sleeping, soldier, not with all the bed making you'll be doing. Then we'll go with that data file! Hey, you add a one and two zeros to that or we walk! You're going to do his laundry? I've got to find a way to escape.
                        </v-card-text>
                        </v-list-item>
                    </div>
                </v-expand-transition>
            </v-list>
            <v-divider></v-divider>
            <v-list nav dense>
                <v-list-item-group v-model="group" active-class="black--text">
                <!-- <v-list-item-group v-model="group" active-class="deep-purple--text text--accent-1"> -->
                    <v-list-item v-for="(item, i) in items" :key="i" :to="item.to" router exact class="text-decoration-none">
                        <v-list-item-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title v-text="item.title" />
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
            <template v-slot:append>
                <v-divider></v-divider>
                <v-list nav dense>
                    <v-list-item @click="logout" link>
                        <v-list-item-action>
                            <v-icon>mdi-logout</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title v-text="'Logout'" />
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </template>
        </v-navigation-drawer>
        <v-app-bar fixed app src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg" dark v-if="loadShopName">
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <!-- <v-btn>
                <v-icon>mdi-minus</v-icon>
            </v-btn> -->
            <v-toolbar-title v-text="loadShopName.name"/>
            <v-spacer />
            <!-- <v-btn>
                <v-icon>mdi-minus</v-icon>
            </v-btn> -->
        </v-app-bar>
    </div>
</template>

<script>
export default {
    data () {
        return {
            drawer: false,
            show: false,
            items: [
                {
                    icon: 'mdi-home',
                    title: 'HOME',
                    to: `/home/${this.$route.params.sid}`,
                },
                {
                    icon: 'mdi-notebook-multiple',
                    title: 'PLAN',
                    to: `/plan/${this.$route.params.sid}`
                },
                {
                    icon: 'mdi-notebook-multiple',
                    title: 'BOOKINGPAGE',
                    to: `/booking/${this.$route.params.sid}`
                },
            ],
            title: 'Reservierung',
            group: null,
        }
    },
    computed: {
        user () {
            return this.$store.getters.user;
        },
        loadShopName(){
            const shopName = this.$store.state.user.shop;
            return shopName;
        }
    },
    methods: {
        logout(){
            this.$store.dispatch('logout')
        }
    },
    mounted(){

    }
}
</script>