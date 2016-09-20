var router = new VueRouter();

router.map({
    '/my':              {},
    '/recommendations': {},
    '/popular':         {}
});

router.redirect({
    '*':        '/404',
    '/search':  '/search',
    '/groups':  '/groups',
    '/friends': '/friends'
});
