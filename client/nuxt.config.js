require('dotenv').config()
const { join } = require('path')
const { copySync, removeSync } = require('fs-extra')

module.exports = {
  ssr: true,

  srcDir: __dirname,

  env: {
    apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
    appName: process.env.APP_NAME || 'Altertravel.ru',
    appLocale: process.env.APP_LOCALE || 'en',
    githubAuth: !!process.env.GITHUB_CLIENT_ID,
    defaulLat: 55.7558,
    defaultLng: 37.6173,
  },

  head: {
    title: process.env.APP_NAME,
    titleTemplate: '%s - ' + process.env.APP_NAME,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  loading: { color: '#007bff' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  css: [
    { src: '~assets/sass/app.scss', lang: 'scss' },
    '~assets/css/common.css',
  ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/vform',
    '~plugins/axios',
    '~plugins/fontawesome',
    '~plugins/nuxt-client-init',
    '~plugins/vue-reactive-storage',
    { src: '~plugins/bootstrap', mode: 'client' },
    { src: '~/plugins/vue2-google-maps', mode: 'client', ssr: false },
    { src: '~/plugins/vue-google-adsense', ssr: false }
  ],

  modules: [
    'bootstrap-vue/nuxt',
    '@nuxtjs/router',
    '@nuxtjs/axios',
  ],

  bootstrapVue: {
    components: [
      'BRow',
      'BCol', 
      'BButton',
      'BButtonGroup', 
      'BTab',
      'BTabs',
      'BCard',
      'BCollapse',
      'BSkeleton',
      'BForm',
      'BFormInput',
      'BFormGroup',  
      'BFormTextarea',
      'BFormInput',
      'BFormCheckboxGroup',
      'BSpinner',
      'BPagination',
      'BCarousel',
      'BCarouselSlide',
      'BImg',
      'BImgLazy',
      'BIcon', 
      'BIconEye', 
      'BIconChat', 
      'BIconStar', 
      'BIconStarFill',
      'BIconHouseDoor',
      'BIconArrowsExpand',
    ],
    directives: [
      'VBToggle'
    ],
    config: {
      'Skeleton': {
        'animation': 'wave'
      },
    }
  },


  build: {
    extractCSS: true,
    transpile: [/^vue2-google-maps($|\/)/],
  },

  hooks: {
    generate: {
      done (generator) {
        // Copy dist files to public/_nuxt
        if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
          const publicDir = join(generator.nuxt.options.rootDir, 'public', '_nuxt')
          removeSync(publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          removeSync(generator.nuxt.options.generate.dir)
        }
      }
    }
  },
  
}
