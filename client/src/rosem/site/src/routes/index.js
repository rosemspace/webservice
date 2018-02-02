export default [
    {
        path: '*',
        name: 404,
        component: 'pages/TheNotFoundPage',
    },
    {
        path: '/',
        component: 'layouts/TheMainLayout',
        children: [
            {
                path: '',
                name: 'home',
                component: 'pages/TheHomePage',
            },
            {
                path: 'about',
                name: 'about',
                component: 'pages/TheAboutPage',
            },
        ],
    },
    // {
    //     path: '/',
    //     name: 'home',
    //     component: 'TheHomePage',
    //     children: [
    //         {
    //             path: 'about',
    //             name: 'about',
    //             component: 'TheAboutPage',
    //         },
    //     ],
    // },
];
