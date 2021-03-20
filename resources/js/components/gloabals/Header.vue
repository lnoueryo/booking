<template>
    <div>
        <v-navigation-drawer v-model="drawer" absolute bottom temporary>
            <v-list nav dense>
                <v-list-item-group v-model="group" active-class="deep-purple--text text--accent-4">
                    <v-list-item v-for="(item, i) in items" :key="i" :to="item.to" router exact>
                        <v-list-item-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title v-text="item.title" />
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-navigation-drawer>
        <v-app-bar fixed app>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <v-btn>
                <v-icon>mdi-minus</v-icon>
            </v-btn>
            <v-toolbar-title v-text="title" />
            <v-spacer />
            <v-btn>
                <v-icon>mdi-minus</v-icon>
            </v-btn>
        </v-app-bar>
    </div>
</template>

<script>
export default {
    data () {
        return {
            drawer: false,
            items: [
                {
                    icon: 'mdi-apps',
                    title: 'Home',
                    to: '/'
                },
                {
                    icon: 'mdi-apps',
                    title: 'booking',
                    to: '/booking'
                },
            ],
            title: 'Rservierhung',
            group: null,
        }
    },
    watch: {
      group () {
        this.a = false
      },
    },
}
</script>