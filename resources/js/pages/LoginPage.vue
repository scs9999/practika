<template>
    <div class="form">
        <h3>Вход в аккаунт</h3>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input
                type="text"
                v-model="email"
                :class="{ 'is-invalid': errors.email }"
                class="form-control focus-ring focus-ring-danger border"
            />
            <div v-if="errors.email" class="invalid-feedback">
                {{ errors.email.join(". ") }}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Пароль</label>
            <input
                type="password"
                v-model="password"
                :class="{ 'is-invalid': errors.password }"
                class="form-control focus-ring focus-ring-danger border"
            />
            <div v-if="errors.password" class="invalid-feedback">
                {{ errors.password.join(". ") }}
            </div>
        </div>
        <div class="d-grid gap-2">
            <button @click="login" class="btn btn-danger">Войти</button>
            <p>
                Нет аккаунта?
                <a
                    href="#"
                    @click.prevent="changePage('RegisterPage')"
                    class="link link-danger"
                    >Зарегистрироваться</a
                >
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoginPage",
    props: ["changePage", "loginUser", "server"],
    data() {
        return { email: "", password: "", errors: {} };
    },
    methods: {
        login() {
            let formdata = new FormData();
            formdata.append("email", this.email);
            formdata.append("password", this.password);
            this.server("login", "POST", formdata).then((result) => {
                if (result.errors) this.errors = result.errors;
                if (result.token) this.loginUser(result.token);
            });
        },
    },
};
</script>
