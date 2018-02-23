export default [
    {
        path: '/admin',
        name: 'admin',
        component: 'layouts/TheAdminLayout',
        children: [
            {
                path: 'board',
                name: 'admin-dashboard',
                component: 'pages/TheDashboardPage',
            },
            {
                path: 'blog',
                name: 'admin-blog',
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
                name: 'admin-404',
                component: 'pages/TheNotFoundPage',
            },
        ],
    },
];
