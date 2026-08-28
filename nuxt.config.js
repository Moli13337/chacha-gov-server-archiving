module.exports = {
	mode: 'spa',

	/*
  ** Headers of the page
  */
	head: {
		title: '产服通智慧企业服务平台',
		meta: [
			{charset: 'utf-8'},
			{name: 'viewport', content: 'width=device-width, initial-scale=1'},
			{hid: 'description', name: '产服通智慧企业服务平台', content: '产服通智慧企业服务平台'},
			{'http-equiv': 'Cache-Control', content: 'no-cache, no-store, must-revalidate'},
			{'http-equiv': 'Pragma', content: 'no-cache'},
			{'http-equiv': 'Expires', content: '0'}
		],
		link: [
			// {rel: 'icon', type: 'image/x-icon', href: '/favicon.ico'},
			{rel: 'stylesheet', type: 'text/css', href: '/badbrowser.css'}
		],
		script: [
			{src: '/badbrowser.js'}
		]
	},

	/*
  ** Customize the progress-bar color
  */
	loading: {color: '#005192'},

	/*
  ** Global CSS
  */
	css: [
		'~/theme/index.css',
		'~/assets/css/common_avairail.less',
		'~/assets/css/common.less'
	],

	/*
  ** Plugins to load before mounting the App
  */
	plugins: [
		'~/plugins/element-ui',
		'~/plugins/axios',
		'~/plugins/filters',
		'~/plugins/event-bus',
		'~/plugins/helpers',
	],

	/*
  ** Nuxt.js modules
  */
	modules: [
		'@nuxtjs/axios',
		'@nuxtjs/proxy',
		'cookie-universal-nuxt',
	],

	/**
   * network request
   */
	axios: {
		proxy: true
	},

	/**
   * 跨域
   */
	proxy: [
		[
			'/api',
			{
				target: 'http://backend-chacha-tencent-prod.heroera.com',
				// target: 'http://10.12.4.88',
				// target: 'https://048cc72b-e320-4dda-9019-734d9b015226.mock.pstmn.io',
				changeOrigin: true,
				pathRewrite: {'^/api': ''}
			}
		]
		// [
		// 	'/api',
		// 	{
		// 		target: 'http://10.12.4.88',
		// 		changeOrigin: true,
		// 		pathRewrite: {'^/api': ''}
		// 	}
		// ]
	],
	/*
  ** Build configuration
  */
	build: {
		transpile: [/^element-ui/],
		/*
    ** You can extend webpack config here
    */
		extend(config) {
			config.output.globalObject = 'this';
		}
	},
	/**
   * Vue Router
   */
	router: {
		middleware: 'auth'
	},
	env: {
		NODE_ENV: process.env.NODE_ENV
	},
	dev: (process.env.NODE_ENV !== 'production' || process.env.NODE_ENV !== 'sandbox-wenjiang')
};
