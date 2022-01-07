module.exports = {
    apps: [
      {
        name: 'Altertravel',
        port: 3000,
        exec_mode: 'cluster',
        instances: 'max', // Or a number of instances
        script: './node_modules/nuxt/bin/nuxt-start',
        // args: 'start',
        watch: false,
        // max_memory_restart: '400M'
      }
    ]
  }