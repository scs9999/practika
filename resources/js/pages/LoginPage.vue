<template>
    <div class="form">
        <h3>Вход в аккаунт</h3>
        <label>Email</label>
        <input type="text" v-model="email" :class="{ error: errors.email }" />
        <div v-if="errors.email" class="errortext">{{ errors.email.join('. ') }}</div>

        <label>Пароль</label>
        <input type="password" v-model="password" :class="{ error: errors.password }" />
        <div v-if="errors.password" class="errortext">{{ errors.password.join('. ') }}</div>

        <button @click="login">Войти</button>
        <p>Нет аккаунта? <a href="#" @click.prevent="changePage('RegisterPage')">Зарегистрироваться</a></p>
    </div>
</template>

<script>
export default {
    name: 'LoginPage',
    props: ['changePage', 'loginUser', 'server'],
    data() {
        return { email: '', password: '', errors: {} };
    },
    methods: {
        login() {
            let formdata = new FormData();
            formdata.append('email', this.email);
            formdata.append('password', this.password);
            this.server('login', 'POST', formdata).then((result) => {
                if (result.errors) this.errors = result.errors;
                if (result.token) this.loginUser(result.token);
            });
        },
    },
};
</script>