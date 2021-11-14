<template>
  <div class="container page">
    <div class="row">
      <div class="col-md-3">
        <card title="Настройки" class="settings-card">
          <ul class="nav flex-column nav-pills">
            <li v-for="tab in tabs" :key="tab.route" class="nav-item">
              <router-link :to="{ name: tab.route }" class="nav-link" active-class="active">
                <fa :icon="tab.icon" fixed-width />
                {{ tab.name }}
              </router-link>
            </li>
          </ul>
        </card>
      </div>
      <div class="col-md-9">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  middleware: 'auth',

  computed: {
    tabs () {
      return [
        {
          icon: 'user',
          name: 'Профиль',
          route: 'settings.profile'
        },
        {
          icon: 'lock',
          name: 'Сменить пароль',
          route: 'settings.password'
        }
      ]
    }
  }
}
</script>

<style>
.settings-card .card-body {
  padding: 0;
}
</style>
