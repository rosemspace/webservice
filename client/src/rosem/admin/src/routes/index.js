export default [
    {
        path: '/admin',
        name: 'admin',
        component: 'layouts/TheAdminLayout',
        // components: {
            // default: 'layouts/TheAdminLayout',
            // header: 'partials/TheHeader',
        // },
        children: [
            {
                path: 'board',
                name: 'board',
                component: 'pages/TheDashboardPage',
            },
            {
                path: 'blog',
                name: 'blog',
                component: 'pages/TheBlogPage',
            },
        ],
    },
    {
        path: '*',
        component: 'layouts/TheAdminLayout',
        children: [
            {
                path: '',
                name: '404',
                component: 'pages/TheNotFoundPage',
            },
        ],
    },
];
