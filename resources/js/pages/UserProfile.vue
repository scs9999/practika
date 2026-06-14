<template>
    <div class="container mt-4">
        <h3>Личный кабинет — {{ user.name }}</h3>
        <template v-if="user.role == 'user'">
            <h4 class="mt-4">Избранные рецепты</h4>
            <div class="row">
                <CardComponent
                    v-for="item in favorites"
                    :key="item.id"
                    :server="server"
                    :favorites="favoriteIds"
                    :changePage="changePage"
                    :user="user"
                    :AuthUser="AuthUser"
                    :recipe="item.recipe"
                />
            </div>
        </template>
        <template v-if="user.role == 'admin'">
    <div class="d-flex gap-2 mt-3 mb-3">
    <button @click="tab = 'ingredients'"
        :style="tab == 'ingredients' ? 'background:#198754; color:white;' : 'background:#e9ecef; color:#333;'"
        style="border:none; padding:8px 16px; cursor:pointer;">
        Ингредиенты
    </button>
    <button @click="tab = 'categories'"
        :style="tab == 'categories' ? 'background:#198754; color:white;' : 'background:#e9ecef; color:#333;'"
        style="border:none; padding:8px 16px; cursor:pointer;">
        Категории
    </button>
    <button @click="tab = 'recipes'"
        :style="tab == 'recipes' ? 'background:#198754; color:white;' : 'background:#e9ecef; color:#333;'"
        style="border:none; padding:8px 16px; cursor:pointer;">
        Рецепты
    </button>
</div>


            <IngredientsPage :server="server" v-if="tab == 'ingredients'" />
            <CategoriesPage  :server="server" v-if="tab == 'categories'" />
            <RecipesPage     :server="server" :changePage="changePage" v-if="tab == 'recipes'" />
        </template>
    </div>
</template>

<script>
import CardComponent    from '../components/CardComponent.vue';
import IngredientsPage  from './admin/IngredientsPage.vue';
import CategoriesPage   from './admin/CategoriesPage.vue';
import RecipesPage      from './admin/RecipesPage.vue';

export default {
    name: 'UserProfile',
    props: ['server', 'user', 'AuthUser', 'changePage'],
    components: { CardComponent, IngredientsPage, CategoriesPage, RecipesPage },
    data() {
        return {
            tab: 'ingredients',
            favorites: [],
            favoriteIds: [],
        };
    },
    mounted() {
        if (this.user.role == 'user') {
            this.getFavorites();
        }
    },
    methods: {
        getFavorites() {
            this.server('favorites')
                .then((result) => {
                    this.favorites   = result;
                    this.favoriteIds = result.map(f => f.recipe_id);
                });
        },
    },
};
</script>