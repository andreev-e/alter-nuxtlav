<template>
  <div class="container page">
    <div class="row">
      <div class="col-lg-8 m-auto">
        <b-card title="Вход">
          <p>Внимание! Из-за смены системы хранения паролей старые пароли были сброшены.</p>
          <p>Вы можете <router-link :to="{ name: 'password.request' }">восстановить</router-link> пароль через  почту</p>
          <form @submit.prevent="login" @keydown="form.onKeydown($event)">
            <!-- Email -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
              <div class="col-md-7">
                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email" class="form-control">
                <has-error :form="form" field="email" />
              </div>
            </div>

            <!-- Password -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">Пароль</label>
              <div class="col-md-7">
                <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="form-control">
                <has-error :form="form" field="password" />
              </div>
            </div>

            <!-- Remember Me -->
            <div class="form-group row">
              <div class="col-md-3" />
              <div class="col-md-7 d-flex">
                <checkbox v-model="remember" name="remember">
                  Запомнить
                </checkbox>

                <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">
                  Забыли пароль
                </router-link>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-7 offset-md-3 d-flex">
                <!-- Submit Button -->
                <b-button @click="login" :disabled="form.busy">
                  Войти
                </b-button>

                <!-- GitHub Login Button -->
                <login-with-github />
              </div>
            </div>
          </form>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  middleware: 'guest',

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false
  }),

  head () {
    return { title: this.$t('login') }
  },

  methods: {
    async login () {
      this.form.busy = true;
      let data

      // Submit the form.
      try {
        const response = await this.form.post('/login')
        data = response.data
      } catch (e) {
        return
      }

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.$router.push({ name: 'secure' })
    }
  }
}
</script>
