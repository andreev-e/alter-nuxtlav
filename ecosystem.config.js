module.exports = {
    apps: [
      {
        name: 'Altertravel',
        port: 3000,
        exec_mode: 'cluster',
        instances: '1', // Or a number of instances
        script: './node_modules/nuxt/bin/nuxt.js',
        args: 'start',
        watch: false,
        // max_memory_restart: '400M'
      }
    ]
  }