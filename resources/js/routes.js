import VueRouter from 'vue-router';
import Booking from './pages/Booking';
import Practice from './pages/Practice';
import Detail from './pages/Detail';

const routes = [
    {
        path: '/',
        component: Practice,
        name: 'practice'
    },
    {
        path: '/booking',
        component: Booking,
        name: 'booking'
    },
    {
        path: '/detail/:id',
        component: Detail,
        name: 'detail',
        props: true
    },
];

const router = new VueRouter({
    routes: routes,
    mode: 'history',
});

export default router;