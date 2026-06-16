<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success px-3">
        <a class="navbar-brand fw-bold" href="#" @click.prevent="changePage('RecipeListComponent')">
            Кулинарный помощник
        </a>
        <div class="d-flex gap-2 ms-auto align-items-center">
            <button @click="changePage('RecipeListComponent')" class="btn btn-sm btn-outline-light">Главная</button>
            <template v-if="AuthUser">
                <button @click="changePage('UserProfile')" class="btn btn-sm btn-outline-light">
                    {{ user.name }}
                </button>
                <button data-bs-toggle="modal" data-bs-target="#logoutModal" class="btn btn-sm btn-outline-light">
                    Выйти
                </button>
            </template>
            <template v-else>
                <button @click="changePage('LoginPage')" class="btn btn-sm btn-outline-light">Войти</button>
            </template>
        </div>
    </nav>

    <RecipeListComponent v-if="page == 'RecipeListComponent'" :changePage="changePage" :server="server" :user="user"
        :AuthUser="AuthUser" :isLoad="isLoad" :page="page" />
    <LoginPage v-if="page == 'LoginPage'" :loginUser="loginUser" :server="server" :changePage="changePage" />
    <RegisterPage v-if="page == 'RegisterPage'" :loginUser="loginUser" :server="server" :changePage="changePage" />
    <UserProfile v-if="page == 'UserProfile'" :changePage="changePage" :AuthUser="AuthUser" :user="user"
        :server="server" />
    <SinglePage v-if="page == 'SinglePage'" :AuthUser="AuthUser" :server="server" :changePage="changePage" />

    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Выход</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Выйти из аккаунта?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
                    <button class="btn btn-success" data-bs-dismiss="modal" @click="logout">Да</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import RecipeListComponent from './components/RecipeListComponent.vue'
import LoginPage from './pages/LoginPage.vue'
import RegisterPage from './pages/RegisterPage.vue'
import SinglePage from './pages/SinglePage.vue'
import UserProfile from './pages/UserProfile.vue'

export default {
    name: 'App',
    components: { RecipeListComponent, LoginPage, RegisterPage, SinglePage, UserProfile },
    data() {
        return {
            page: localStorage.getItem('page') || 'RecipeListComponent',
            serverAPI: 'http://127.0.0.1:8000/api/',
            AuthUser: false,
            user: {},
            isLoad: false
        }
    },
    mounted() {
        if (localStorage.getItem('token')) {
            this.getUser()
        } else {
            this.isLoad = true
        }
    },
    methods: {
        changePage(page, id) {
            this.page = page
            localStorage.setItem('page', page)
            if (id != null) localStorage.setItem('singleID', id)
        },
        loginUser(token) {
            localStorage.setItem('token', token)
            this.getUser()
            this.changePage('RecipeListComponent')
        },
        logout() {
            this.server('logout', 'POST')
            localStorage.removeItem('token')
            this.AuthUser = false
            this.user = {}
            this.changePage('RecipeListComponent')
        },
        getUser() {
            this.server('user').then(d => {
                this.user = d.user
                this.AuthUser = true
                this.isLoad = true
            }).catch(() => { this.isLoad = true })
        },
        async server(route, method = 'GET', formdata = null) {
            let headers = new Headers()
            headers.append('Accept', 'application/json')
            if (localStorage.getItem('token')) {
                headers.append('Authorization', 'Bearer ' + localStorage.getItem('token'))
            }
            let options = { method, headers }
            if (method != 'GET') options.body = formdata
            let res = await fetch(this.serverAPI + route, options)
            if (res.status == 401) {
                localStorage.removeItem('token')
                this.AuthUser = false
            }
            return res.json()
        }
    }
}
</script>