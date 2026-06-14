<template>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <img :src="'/storage/' + recipe.image_url" class="card-img-top" alt="" />
            <div class="card-body">
                <h5 class="card-title">{{ recipe.title }}</h5>
                <p class="card-text">
                    <i class="bi bi-clock"></i>
                    {{ Math.floor(recipe.cook_time / 60) }}ч
                    {{ recipe.cook_time % 60 }}мин
                    <br />
                    <i class="bi bi-bar-chart"></i> {{ recipe.difficulty }}
                    <br />
                    <i class="bi bi-tag"></i> {{ recipe.category.name }}
                </p>
                <button @click="changePage('SinglePage', recipe.id)"
                    class="btn btn-danger">Подробнее</button>
                <button
                    v-if="AuthUser && user.role == 'user'"
                    :class="isFavorite ? 'btn-danger' : 'btn-outline-secondary'"
                    @click="clickFavorite(recipe.id)"
                    class="btn m-1">
                    <i :class="isFavorite ? 'bi-suit-heart-fill' : 'bi-suit-heart'" class="bi"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CardComponent',
    props: ['recipe', 'AuthUser', 'user', 'changePage', 'favorites', 'server'],
    data() {
        return {
            isFavorite: false,
        };
    },
    mounted() {
        this.isFavorite = this.favorites.includes(this.recipe.id);
    },
    methods: {
        clickFavorite(id) {
            this.isFavorite = !this.isFavorite;
            this.server(`favorite/${id}`, 'POST');
        },
    },
};
</script>