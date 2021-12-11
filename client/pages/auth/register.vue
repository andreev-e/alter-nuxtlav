<template>
  <div class="container page">
    <div class="row">
      <div class="col-lg-8 m-auto">
        <b-card v-if="mustVerifyEmail" title="Регистрация">
          <div class="alert alert-success" role="alert">
            {{ $t('verify_email_address') }}
          </div>
        </b-card>
        <b-card v-else title="Регистрация">
          <form @submit.prevent="register" @keydown="form.onKeydown($event)">
            <!-- Name -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">Имя</label>
              <div class="col-md-7">
                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" type="text" name="name" class="form-control">
                <has-error :form="form" field="name" />
              </div>
            </div>

            <!-- Email -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">Email</label>
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

            <!-- Password Confirmation -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">Подтверждение пароля</label>
              <div class="col-md-7">
                <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password" name="password_confirmation"
                      class="form-control"
                >
                <has-error :form="form" field="password_confirmation" />
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-7 offset-md-3 d-flex">
                <!-- Submit Button -->
                <b-button :loading="form.busy">
                  {{ $t('register') }}
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
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    }),
    mustVerifyEmail: false
  }),

  head () {
    return { title: this.$t('register') }
  },

  methods: {
    async register () {
      let data

      // Register the user.
      try {
        const response = await this.form.post('/register')
        data = response.data
      } catch (e) {
        return
      }

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true
      } else {
        // Log in the user.
        const { data: { token } } = await this.form.post('/login')

        // Save the token.
        this.$store.dispatch('auth/saveToken', { token })

        // Update the user.
        await this.$store.dispatch('auth/updateUser', { user: data })

        // Redirect home.
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
