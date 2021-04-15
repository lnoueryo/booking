<template>
    <div>
        <v-dialog v-model="dialog" persistent max-width="600px">
            <v-card height="100%">
                <v-card-title>
                <span class="headline">PLAN</span>
                </v-card-title>
                <v-card-text>
                <v-container>
                    <v-row>
                    <v-col cols="12">
                        <v-text-field label="プランタイトル" required v-model="plan.title"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" sm="6">
                        <v-text-field label="料金" required v-model="plan.price"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" sm="6">
                        <v-text-field label="時間" required v-model="plan.duration"></v-text-field>
                    </v-col>
                    <v-col cols="12">
                    <v-autocomplete v-model="plan.types" :items="planTypes" filled chips small-chips color="blue-grey lighten-2" label="タイプ" multiple>
                        <template v-slot:selection="data">
                        <v-chip small color="indigo" text-color="white" v-bind="data.attrs" close @click="data.select" @click:close="remove(data.item)">
                            {{ data.item }}
                        </v-chip>
                        </template>
                    </v-autocomplete>
                    </v-col>
                    <v-col cols="12">
                    <v-file-input ref="input" label="File input" filled prepend-icon="mdi-camera" @change="loadImage($event)"></v-file-input>
                    </v-col>
                    <v-col cols="12" md="6" sm="6">
                    <v-img :src="path + plan.image" width="200" height="150" style="margin: auto;" v-if="!src"></v-img>
                    <v-img :src="src" width="200" height="150" style="margin: auto;" v-if="src"></v-img>
                    </v-col>
                    <v-col cols="12" md="6" sm="6">
                        <v-textarea counter rows="5" maxlength="120" label="説明" required v-model="plan.description"></v-textarea>
                    </v-col>
                    </v-row>
                </v-container>
                <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="dialog = false">
                    Close
                </v-btn>
                <v-btn color="blue darken-1" text @click="update">
                    Save
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import indication from "../mixins/indication"
export default {
    mixins: [indication],
    props: {
        id: Number,
        title: String,
        types: Array || String,
        price: Number,
        duration: Number,
        description: String,
        image: String
    },
    data () {
        return {
            plan: {},
            src: null,
            dialog: false,
            planTypes: ['カット', 'パーマ', 'カラー', 'トリートメント', 'スパ'],
        }
    },
    watch: {
        types: {
            handler(value){
                this.plan = {}
                if(value){
                    this.plan = Object.assign({}, this.plan, {shop_id: this.$route.params.sid, title: this.title, types: [], price: this.price, duration: this.duration, description: this.description, image: this.image})
                    this.types.forEach((type)=>{
                        this.plan.types.push(type);
                    })
                }
            },
            immediate: true
        }
    },
    methods: {
        async update(){
            if (this.id) {
                try {
                    if(this.src){
                        Object.assign(this.plan, {new_image: this.src});
                    }
                    const params = this.plan;
                    const response = await axios.put(`/api/${this.$route.params.sid}/shop-plan/${this.id}`, params);
                    this.$emit('reloadPlan')
                } catch (error) {
                    console.log(error)
                }
            } else {
                try {
                    Object.assign(this.plan, {new_image: this.src});
                    const params = this.plan;
                    const response = await axios.post(`/api/${this.$route.params.sid}/shop-plan`, params);
                    this.$emit('reloadPlan')
                } catch (error) {
                    console.log(error)
                }
            }
        },
        remove (item) {
            const index = this.plan.types.findIndex((plan)=>{return plan === item})
            if (index >= 0) this.plan.types.splice(index, 1)
        },
        loadImage(e){
            if(e){
                const reader = new FileReader();
                let that = this;
                reader.onload = () => {
                    that.src = reader.result;
                    // that.plan.image = reader.result;
                }
                reader.readAsDataURL(e)
            } else {
                this.src = null;
            }
        },
    }
}
</script>