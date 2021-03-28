<template>
    <div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Login</div>

                        <div class="card-body">
                            <v-form ref="form">
                                <div class="form-group row">
                                    <label for="shop_id" class="col-md-4 col-form-label text-md-right">店舗ID</label>

                                    <div class="col-md-6">
                                        <input @keyup.enter="login" id="shop_id" type="text" class="form-control" name="shop_id" required autocomplete="shop_id" autofocus v-model="auth.shop_id">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">メール</label>

                                    <div class="col-md-6">
                                        <input @keyup.enter="login" id="email" type="email" class="form-control" name="email" required autocomplete="email" autofocus v-model="auth.email">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">パスワード</label>

                                    <div class="col-md-6">
                                        <input @keyup.enter="login" id="password" type="password" class="form-control" name="password" required autocomplete="current-password" v-model="auth.password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <v-btn class="btn primary" @click="login">
                                            Login
                                        </v-btn>
                                        <!-- <v-btn class="btn primary" @click="check">
                                            Check
                                        </v-btn> -->
                                    </div>
                                </div>
                            </v-form>
                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <hr class="col-md-8">
                                    <div>
                                        Make an account?
                                        <router-link :to="{name: 'register'}" class="font-weight-bold">Sign-up</router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            auth: {shop_id: '', email: null, password: null}
        }
    },
    methods: {
        login(){
            this.$store.dispatch('login', this.auth)
        },
        logout(){
            axios.post('/logout').then(response => {
                // this.$store.dispatch('logout');
            })
        },
        check(){
            axios.get('/api/check').then(response => {
                console.log(response)
            })
        },
    }
}
</script>