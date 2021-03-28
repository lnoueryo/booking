import VueRouter from 'vue-router';
import Booking from './pages/Booking';
import Practice from './pages/Home';
import Detail from './pages/Detail';
import Login from './pages/Login';
import ErrorComponent from './pages/Error';
import Register from './pages/Register';
import Header from './components/globals/Header';
import axios from 'axios';
import Store from './store'

let isAuth;

const routes = [
    {
        path: '/home/:sid',
        components: {
            default: Practice,
            header: Header
        },
        name: 'home',
        meta: { requiresAuth: true }
    },
    {
        path: '/booking/:sid/:id',
        components: {
            default: Booking,
            header: Header
        },
        name: 'booking',
        meta: { requiresAuth: true }
    },
    {
        path: '/detail/:sid/:id',
        components: {
            default: Detail,
            header: Header
        },
        name: 'detail',
        props: true,
        meta: { requiresAuth: true }
    },
    {
        path: '/',
        component: Login,
        name: 'login',
        beforeEnter: async (to, from, next) => {
            const user = await localStorage.getItem('user');
            if(!user){
                next()
            }else{
                next(`home/${(JSON.parse(user)).shop_id}`)
            }
        },
    },
    {
        path: '/sign-up',
        component: Register,
        name: 'register',
    },
    {
        name: '404',
        path: '*',
        component: ErrorComponent
    },
];

const router = new VueRouter({
    routes: routes,
    mode: 'history',
});

router.beforeEach(async (to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {//認証が必要なページ
        const user = localStorage.getItem('user');
        user ? validation() : window.location.href = '/';
        function validation(){
            const parsedUser = JSON.parse(user)
            parsedUser.shop_id == to.params.sid ? next() : next(`home/${parsedUser.shop_id}`);
        }
    } else {
        next();
    }
});

export default router;