<template>
    <div class="form">
        <h3>Регистрация</h3>
        <label>Имя</label>
        <input type="text" v-model="name" :class="{ error: errors.name }" />
        <div v-if="errors.name" class="errortext">{{ errors.name.join('. ') }}</div>

        <label>Email</label>
        <input type="email" v-model="email" :class="{ error: errors.email }" />
        <div v-if="errors.email" class="errortext">{{ errors.email.join('. ') }}</div>

        <label>Пароль</label>
        <input type="password" v-model="password" :class="{ error: errors.password }" />
        <div v-if="errors.password" class="errortext">{{ errors.password.join('. ') }}</div>

        <button @click="register">Зарегистрироваться</button>
        <p>Уже есть аккаунт? <a href="#" @click.prevent="changePage('LoginPage')">Войти</a></p>
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
            this.server('register', 'POST', formdata).then((result) => {
                if (result.errors) this.errors = result.errors;
                if (result.token) this.loginUser(result.token);
            });
        },
    },
};
</script>