<template>
    <div class="w-50 m-auto align-items-center">
        <transition name="fade" mode="out-in">
            <div class="card card-body" v-if="show==0">
                <div>
                    <div class="form-group">
                        <label for="shop-name">店舗名</label>
                        <input type="text" name="shop-name" placeholder="お店の名前を入れてください" class="form-control" v-model="user.shop_name" :class="[{'is-invalid': errorFor('name')}]">
                        <v-errors :errors="errorFor('name')"></v-errors>
                    </div>
                    <div class="form-group">
                        <label for="last-name">姓</label>
                        <input type="text" name="last-name" placeholder="姓を入れてください" class="form-control" v-model="user.last_name" :class="[{'is-invalid': errorFor('name')}]">
                        <v-errors :errors="errorFor('name')"></v-errors>
                    </div>
                    <div class="form-group">
                        <label for="last-name">名</label>
                        <input type="text" name="name" placeholder="名を入れてください" class="form-control" v-model="user.first_name" :class="[{'is-invalid': errorFor('name')}]">
                        <v-errors :errors="errorFor('name')"></v-errors>
                    </div>
                    <div class="form-group">
                        <label for="email">メール</label>
                        <input type="text" name="email" placeholder="メールアドレスを入れてください" class="form-control" v-model="user.email" :class="[{'is-invalid': errorFor('email')}]">
                        <v-errors :errors="errorFor('email')"></v-errors>
                    </div>
                    <div class="form-group">
                        <label for="number">電話番号</label>
                        <input type="text" name="number" placeholder="メールアドレスを入れてください" class="form-control" v-model="user.phone" :class="[{'is-invalid': errorFor('name')}]">
                        <v-errors :errors="errorFor('name')"></v-errors>
                    </div>
                    <div class="form-group">
                        <label for="password">パスワード</label>
                        <input type="password" name="password" placeholder="パスワードを入れてください" class="form-control" v-model="user.password" :class="[{'is-invalid': errorFor('password')}]">
                        <v-errors :errors="errorFor('password')"></v-errors>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">確認用</label>
                        <input type="password" name="password_confirmation" placeholder="パスワードを入れてください" class="form-control" v-model="user.password_confirmation" :class="[{'is-invalid': errorFor('password_confirmation')}]">
                        <v-errors :errors="errorFor('password_confirmation')"></v-errors>
                    </div>
                    <button class="btn btn-primary btn-lg btn-block" :disabled="loading" @click="register">登録</button>
                    <hr>
                    <div>
                        Already have an account?
                        <router-link :to="{name: 'login'}" class="font-weight-bold">Log-in</router-link>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import validationErrors from "../components/mixins/validationErrors";
export default {
    mixins: [validationErrors],
    data() {
        return {
            user: {
                shop_name: null,
                last_name: null,
                first_name: null,
                email: null,
                phone: null,
                password: null,
                password_confirmation: null
            },
            loading: false,
            errors: null,
            show: 0
        }
    },
    methods: {
        async register(){
            this.loading = true;
            this.errors = null;

            try {
                const response = await axios.post('register',this.user);
                if (201==response.status) {
                    // this.$store.dispatch('loadUser');
                    this.$router.push({name: 'login'})
                }
                    this.$router.push({name: 'login'})
            } catch (error) {
                this.$store.dispatch('logout');
                this.errors = error.response && error.response.data.errors;
            }
            this.loading = false
        }
    }
}
</script>