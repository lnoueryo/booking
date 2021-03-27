import router from "./routes.js";
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: {},
        ready: false
    },
    mutations: {
        user(state, payload) {
            state.user = {};
            state.user = Object.assign({}, state.user, payload);
        },
        ready(state, payload) {
            state.ready = payload;
        },
    },
    actions: {
        loadUser(context) {
            const user = localStorage.getItem('user');
            if(user){
                context.commit('user', JSON.parse(user))
            }
            context.commit('ready', true)
        },
        login(context, payload) {
            const params = payload
            // const params = {shop_id: '3b00d758-8ad7-4da8-9834-739022c01346', email: 'popo62520908@gmail.com', password: 'popo0908'}
            axios.post('/login', params)
            .then((response)=>{
                localStorage.setItem('user', JSON.stringify(response.data))
                context.commit('user', response.data)
                context.commit('ready', true)
                window.location.href = `/home/${response.data.shop_id}`;
            })
            // .error((err)=>{console.log(err)})
        },
        async logout(context) {
            // try{
                const {data, err} = (await axios.post('/logout'));
            //     console.log(err.message)
            //     console.log('hello')
            // } catch(err){
            //     console.log(err)
            //     window.location.href = `/`
            // } finally {
            //     window.location.href = `/`
            // }
            if(data){
                localStorage.removeItem('user')
                context.commit('user', {});
                window.location.href = `/`
            } else {
                console.log(JSON.parse(err))
                window.location.href = `/`
            }
        },
    },
    getters: {
        user: state => state.user
    },
});