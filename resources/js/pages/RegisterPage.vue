<template>
    <div class="form">
        <h3>Регистрация</h3>
        <div class="mb-3">
            <label class="form-label">Имя</label>
            <input type="text" v-model="name"
                :class="{ 'is-invalid': errors.name }"
                class="form-control focus-ring focus-ring-danger border" />
            <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name.join('. ') }}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" v-model="email"
                :class="{ 'is-invalid': errors.email }"
                class="form-control focus-ring focus-ring-danger border" />
            <div v-if="errors.email" class="invalid-feedback">
                {{ errors.email.join('. ') }}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Пароль</label>
            <input type="password" v-model="password"
                :class="{ 'is-invalid': errors.password }"
                class="form-control focus-ring focus-ring-danger border" />
            <div v-if="errors.password" class="invalid-feedback">
                {{ errors.password.join('. ') }}
            </div>
        </div>
        <div class="d-grid gap-2">
            <button @click="register" class="btn btn-danger">Зарегистрироваться</button>
            <p>Уже есть аккаунт?
                <a href="#" @click.prevent="changePage('LoginPage')"
                    class="link link-danger">Войти</a>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RegisterPage',
    props: ['changePage', 'server', 'loginUser'],
    data() {
        return { name: '', email: '', password: '', errors: {} };
    },
    methods: {
        register() {
            let formdata = new FormData();
            formdata.append('name', this.name);
            formdata.append('email', this.email);
            formdata.append('password', this.password);
            this.server('register', 'POST', formdata)
                .then((result) => {
                    if (result.errors) this.errors = result.errors;
                    if (result.token)  this.loginUser(result.token);
                });
        },
    },
};
</script>