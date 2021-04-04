<template>
    <div>
        <v-card class="mx-auto my-12" max-width="320" link>
            <template slot="progress">
                <v-progress-linear color="deep-purple" height="10" indeterminate></v-progress-linear>
            </template>
            <v-img height="200" :src="path + image">
                <!-- <v-overlay :switch="n.isSelected"></v-overlay> -->
            </v-img>
            <v-card-title class="py-2">{{ title }}
            </v-card-title>
            <v-card-text>
                <div v-html="description"></div>
            </v-card-text>
            <div class="d-flex">
            <v-card-subtitle class="py-2">
                <b>{{ decimalSeparator(price) }}</b>
            </v-card-subtitle>
            <v-card-subtitle class="py-2">
                <b>{{ calculateHour(duration) }}</b>
            </v-card-subtitle>
            </div>
            <v-divider class="my-0"></v-divider>
            <v-card-text class="px-2 py-2">
                <v-chip small color="indigo" text-color="white" class="mx-1 my-1" v-for="(type, j) in types" :key="j">
                {{ type }}
                </v-chip>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import indication from "../mixins/indication";
export default {
    props: ['image', 'title', 'description', 'price', 'duration', 'types'],
    mixins: [indication],
}
</script>