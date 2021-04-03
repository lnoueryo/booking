<template>
    <div>
      <v-row>
          <v-col cols="12">
            <v-btn color="indigo" text outlined right absolute @click="openPlanDialog('')" style="z-index:1">create</v-btn>
          </v-col>
      </v-row>
        <v-row>
            <v-col v-for="(plan, i) in plans" :key="i" cols="12" md="4">
                <v-card class="mx-auto my-12" max-width="320" link @click="openPlanDialog(plan)">
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
              <plan-dialog ref="planDialog" v-bind="selectedPlan" @reloadPlan="getPlan"></plan-dialog>
            <!-- <v-dialog v-model="dialog" persistent max-width="600px">
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
                        <v-col cols="12">
                        <v-file-input label="File input" filled prepend-icon="mdi-camera" @change="loadImage($event)" :value="[selectedPlan.image]"></v-file-input>
                        </v-col>
                        <v-col cols="12" md="6" sm="6">
                        <v-img :src="path + selectedPlan.image" width="200" height="150" style="margin: auto;" v-if="!src"></v-img>
                        <v-img :src="src" width="200" height="150" style="margin: auto;" v-if="src"></v-img>
                        </v-col>
                        <v-col cols="12" md="6" sm="6">
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
                    <v-btn color="blue darken-1" text @click="update">
                      Save
                    </v-btn>
                  </v-card-actions>
                </v-card>
            </v-dialog> -->
        </v-row>
    </div>
</template>

<script>
import indication from "../components/mixins/indication.js";
import PlanDialog from '../components/plan/PlanDialog'
export default {
    mixins: [indication],
    components: {
      PlanDialog
    },
    data() {
        return {
            plans: [],
            selectedPlan: '',
            src: null,
            dialog: false,
            autoUpdate: true,
            isUpdating: false,
            name: 'Midnight Crew',
            types: [{name: 'カット'}, {name: 'パーマ'}, {name: 'カラー'}, {name: 'トリートメント'}, {name: 'スパ'}],
            title: 'The summer breeze',
            newPlan: {shop_id: this.$route.params.sid, title: null, types: [], price: null, duration: null, description: null, image: null}
        }
    },
    created(){
      this.getPlan();
    },
    methods: {
      async getPlan(){
        this.plans.splice(0, this.plans.length)
        const {data, err} = (await axios.get(`/api/${this.$route.params.sid}/shop-plan`))
        data.forEach(plan => {
          plan.types = JSON.parse(plan.types)
            this.plans.push(plan)
        });
        this.$refs.planDialog.dialog = false;
      },
      openPlanDialog(plan){
        this.selectedPlan = {};
        this.selectedPlan = plan;
        this.$refs.planDialog.dialog = true;
      },
      async update(){
        try {
          // const types = JSON.stringify(this.selectedPlan.types);
          const params = this.selectedPlan;
          // const params = Object.assign(this.selectedPlan, {types: types});
          const response = axios.put(`/api/${this.$route.params.sid}/shop-plan/${this.selectedPlan.id}`, params);
          console.log(response.data)
          this.dialog = false
        } catch (error) {
          console.log(error)
        }
      },
    }
}
</script>