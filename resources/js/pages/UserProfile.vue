<template>
    <div>
        <div class="zakaz">
            <h3>Личный кабинет ({{ user.name }})</h3>
        </div>

        <template v-if="user.role == 'user'">
            <div class="zakaz"><h4>Избранные рецепты</h4></div>
            <div class="products-container">
                <div v-for="item in favorites" :key="item.id" class="product-box">
                    <img :src="'/storage/' + item.recipe.image_url" alt="" />
                    <p><b>{{ item.recipe.title }}</b></p>
                    <p>{{ item.recipe.category.name }}</p>
                    <button @click="changePage('SinglePage', item.recipe.id)">Открыть</button>
                </div>
            </div>
        </template>

        <template v-if="user.role == 'admin'">
            <div class="zakaz">
                <button @click="tab = 'ingredients'" :class="tab == 'ingredients' ? '' : 'white'">Ингредиенты</button>
                <button @click="tab = 'categories'" :class="tab == 'categories' ? '' : 'white'">Категории</button>
                <button @click="tab = 'recipes'" :class="tab == 'recipes' ? '' : 'white'">Рецепты</button>
            </div>
            <IngredientsPage :server="server" v-if="tab == 'ingredients'" />
            <CategoriesPage :server="server" v-if="tab == 'categories'" />
            <RecipesPage :server="server" :changePage="changePage" v-if="tab == 'recipes'" />
        </template>
    </div>
</template>

<script>
import IngredientsPage from './admin/IngredientsPage.vue';
import CategoriesPage from './admin/CategoriesPage.vue';
import RecipesPage from './admin/RecipesPage.vue';

export default {
    name: 'UserProfile',
    props: ['server', 'user', 'AuthUser', 'changePage'],
    components: { IngredientsPage, CategoriesPage, RecipesPage },
    data() {
        return { tab: 'ingredients', favorites: [] };
    },
    mounted() {
        if (this.user.role == 'user') this.getFavorites();
    },
    methods: {
        getFavorites() {
            this.server('favorites').then((result) => { this.favorites = result; });
        },
    },
};
</script>