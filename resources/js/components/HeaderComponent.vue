<template>
    <header>
        <span @click="changePage('RecipeListComponent')">CookHelper</span>
        <div>
            <template v-if="AuthUser">
                <button @click="changePage('UserProfile')">{{ user.name }}</button>
                <button class="red" @click="logout">Выйти</button>
            </template>
            <template v-else>
                <button @click="changePage('LoginPage')">Войти</button>
            </template>
        </div>
    </header>
</template>

<script>
export default {
    name: 'HeaderComponent',
    props: ['changePage', 'page', 'AuthUser', 'logout', 'user'],
};
</script>