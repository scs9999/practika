<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" @click.prevent="changePage('RecipeListComponent')" href="#">
                <i class="bi bi-egg-fried"></i> CookHelper
            </a>

            <div class="navbar-collapse collapse">
                <ul class="navbar-nav mb-lg-0 mb-2 me-auto">
                    <li class="nav-item">
                        <button class="nav-link" @click="changePage('RecipeListComponent')"
                            :class="{ 'link-danger': page == 'RecipeListComponent' }">
                            <i class="bi bi-list-nested"></i> Главная
                        </button>
                    </li>
                    <li v-if="page == 'RecipeListComponent'" class="nav-item">
    <button
        class="nav-link"
        data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasFilters">
        <i class="bi bi-funnel-fill"></i> Фильтры
    </button>
</li>
                    <li>
                        <button class="nav-link"
                            @click="themeToggle(theme == 'dark' ? 'light' : 'dark')"
                            type="button">
                            <i :class="theme == 'dark' ? 'bi-moon-stars-fill' : 'bi-brightness-high-fill'"
                                class="bi"></i> Тема
                        </button>
                    </li>
                    <template v-if="AuthUser">
                        <li class="nav-item">
                            <button class="nav-link" @click="changePage('UserProfile')"
                                :class="{ 'link-danger': page == 'UserProfile' }">
                                <i class="bi bi-person-fill"></i> {{ user.name }}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link"
                                data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right"></i> Выйти
                            </button>
                        </li>
                    </template>
                    <template v-else>
                        <li class="nav-item">
                            <button class="nav-link" @click="changePage('LoginPage')"
                                :class="{ 'link-danger': page == 'LoginPage' || page == 'RegisterPage' }">
                                <i class="bi bi-door-open-fill"></i> Войти
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </nav>
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Выйти из аккаунта?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Отмена</button>
                    <button type="button" @click="logout"
                        data-bs-dismiss="modal" class="btn btn-danger">Выйти</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'HeaderComponent',
    props: ['changePage', 'page', 'AuthUser', 'logout', 'user'],
};
</script>