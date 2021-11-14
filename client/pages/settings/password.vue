<template>
  <card title="Пароль">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('password_updated')" />

      <!-- Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">Новый пароль</label>
        <div class="col-md-7">
          <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="form-control">
          <has-error :form="form" field="password" />
        </div>
      </div>

      <!-- Password Confirmation -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">Повтор пароля</label>
        <div class="col-md-7">
          <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password" name="password_confirmation" class="form-control">
          <has-error :form="form" field="password_confirmation" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">
            Обновить
          </v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,

  data: () => ({
    form: new Form({
      password: '',
      password_confirmation: ''
    })
  }),

  head () {
    return { title: 'Настройки' }
  },

  methods: {
    update () {
      this.form.patch('/settings/password').then(() => {
        this.form.reset()
      })
    }
  }
}
</script>
