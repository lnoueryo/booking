<template>
    <div>
        <v-row>
            <v-col v-for="(plan, i) in plans" :key="i" cols="12" md="4">
                <v-card class="mx-auto my-12" max-width="320" link @click="dialog=true;selectedPlan=plan">
                    <template slot="progress">
                        <v-progress-linear color="deep-purple" height="10" indeterminate></v-progress-linear>
                    </template>
                    <v-img height="200" :src="path + plan.image">
                        <!-- <v-overlay :switch="n.isSelected"></v-overlay> -->
                    </v-img>
                    <v-card-title class="py-2">{{ plan.title }}
                    </v-card-title>
                    <v-card-text>
                        <div v-html="plan.description"></div>
                    </v-card-text>
                    <div class="d-flex">
                    <v-card-subtitle class="py-2">
                        <b>{{ decimalSeparator(plan.price) }}</b>
                    </v-card-subtitle>
                    <v-card-subtitle class="py-2">
                        <b>{{ calculateHour(plan.duration) }}</b>
                    </v-card-subtitle>
                    </div>
                    <v-divider class="my-0"></v-divider>
                    <v-card-text class="px-2 py-2">
                      <v-chip small color="indigo" text-color="white" class="mx-1 my-1" v-for="(type, j) in plan.types" :key="j">
                        {{ type }}
                      </v-chip>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog v-model="dialog" persistent max-width="600px">
                <v-card>
                  <v-card-title>
                    <span class="headline">User Profile</span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col cols="12">
                          <v-text-field label="プランタイトル" required v-model="selectedPlan.title"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" sm="6">
                          <v-text-field label="料金" required v-model="selectedPlan.price"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" sm="6">
                          <v-text-field label="時間" required v-model="selectedPlan.duration"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                        <v-autocomplete v-model="selectedPlan.types" :items="types" filled chips small-chips color="blue-grey lighten-2" label="タイプ" item-text="name" item-value="name" multiple>
                          <template v-slot:selection="data">
                            <v-chip v-bind="data.attrs" :input-value="data.selected" close @click="data.select" @click:close="remove(data.item)">
                              {{ data.item.name }}
                            </v-chip>
                          </template>
                          <template v-slot:item="data">
                            <template>
                              <v-list-item-content>
                                <v-list-item-title v-html="data.item.name"></v-list-item-title>
                                <v-list-item-subtitle v-html="data.item.group"></v-list-item-subtitle>
                              </v-list-item-content>
                            </template>
                          </template>
                        </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                          <v-textarea counter rows="3" maxlength="120" label="説明" required v-model="selectedPlan.description"></v-textarea>
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
                    <v-btn color="blue darken-1" text @click="dialog = false">
                      Save
                    </v-btn>
                  </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
import indication from "../components/mixins/indication"
export default {
    mixins: [indication],
    data() {
        return {
            plans: [],
            selectedPlan: '',
            dialog: false,
            autoUpdate: true,
            isUpdating: false,
            name: 'Midnight Crew',
            types: [{name: 'カット'}, {name: 'パーマ'}, {name: 'カラー'}, {name: 'トリートメント'}, {name: 'スパ'}],
            title: 'The summer breeze',
        }
    },
    async created(){
        const {data, err} = await axios.get(`/api/shop-plan/${this.$route.params.sid}`)
        data.forEach(plan => {
          plan.types = JSON.parse(plan.types)
            this.plans.push(plan)
        });
    },
    methods: {
      remove (item) {
        const index = this.selectedPlan.types.indexOf(item.name)
        if (index >= 0) this.selectedPlan.types.splice(index, 1)
      },
    }
}
</script>