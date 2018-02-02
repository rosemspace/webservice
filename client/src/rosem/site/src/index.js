import App from 'rosem-kernel';
import VueRouter from 'vue-router';
// import store from '../../store';
import routes from './routes';
import resolveViews from 'rosem-kernel/resolveViews';

export default new App({
    el: '#app',
    // metaInfo: {
    //     titleTemplate: '%s | Your company'
    // },
    // store,
    router: new VueRouter({
        routes: resolveViews(routes, path => import(`./view/${path}`)),
        linkActiveClass: 'active',
        mode: 'history',
        scrollBehavior (to, from, savedPosition) {
            return savedPosition || {x: 0, y: 0};
        }
    }),

    render: h => (<router-view />),

    created () {
        this.$root.$on('overlay', $event => document.body.classList[$event ? 'add' : 'remove']('overlapped'));
    }
});
