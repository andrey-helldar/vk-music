//router.redirect({
//    '/':     '/',
//});

router.map({
    '*':        {
        component: Vue.component('index')
    },
    '/my':      {
        component: Vue.component('audio')
    },
    '/friends': {
        component: Vue.component('friends')
    },
    '/groups':  {
        component: Vue.component('groups')
    },
    '/search':  {
        component: Vue.component('search')
    }
});

//var routerApp = Vue.extend();

//router.start(routerApp, 'main');