<template>
    <div class="container mt-4">
        <button @click="goBack" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Назад
        </button>

        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-4">
                    <img v-if="recipe.image_url"
                        :src="'/storage/' + recipe.image_url"
                        class="img-fluid rounded-start" style="height:300px;object-fit:cover" />
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h2 class="card-title">{{ recipe.title }}</h2>
                        <p class="card-text">{{ recipe.description }}</p>
                        <p>
                            <i class="bi bi-clock"></i>
                            {{ Math.floor(recipe.cook_time / 60) }}ч
                            {{ recipe.cook_time % 60 }}мин
                            &nbsp;
                            <i class="bi bi-bar-chart"></i> {{ recipe.difficulty }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <h4>Ингредиенты</h4>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ингредиент</th>
                    <th>Количество</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, key) in ingredients" :key="key">
                    <td>{{ key + 1 }}</td>
                    <td>{{ item.ingredient.name }}</td>
                    <td>{{ item.quantity }} {{ item.ingredient.unit }}</td>
                </tr>
            </tbody>
        </table>
        <h4>Шаги приготовления</h4>
        <nav>
            <ul class="pagination flex-wrap">
                <li v-for="st in recipe.steps" :key="st.id"
                    class="page-item"
                    :class="{ active: st.step_number == currentStep }">
                    <a @click.prevent="changeStep(st.step_number)"
                        class="page-link" href="#">
                        {{ st.step_number + 1 }}
                    </a>
                </li>
            </ul>
        </nav>

        <div v-if="recipe.steps && recipe.steps[currentStep]" class="card">
            <div class="row g-0">
                <div class="col-md-4">
                    <img v-if="recipe.steps[currentStep].image_url"
                        :src="'/storage/' + recipe.steps[currentStep].image_url"
                        class="img-fluid rounded-start" />
                    <div v-else class="img-none">
                        <i class="bi bi-card-image"></i>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5>Шаг {{ currentStep + 1 }}</h5>
                        <p>{{ recipe.steps[currentStep].description }}</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-danger"
                                @click="changeStep(currentStep - 1)"
                                :disabled="currentStep == 0">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            <button class="btn btn-danger"
                                @click="changeStep(currentStep + 1)"
                                :disabled="currentStep == recipe.steps.length - 1">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SinglePage',
    props: ['server', 'AuthUser', 'changePage'],
    data() {
        return {
            recipe_id: localStorage.getItem('singleID'),
            ingredients: [],
            recipe: { steps: [] },
            currentStep: 0,
        };
    },
    mounted() {
        this.getRecipe();
        this.getIngredients();
        if (this.AuthUser) {
            this.getUserStep();
        }
    },
    methods: {
        goBack() {
            this.changePage('RecipeListComponent');
        },

        getRecipe() {
            this.server('recipes/' + this.recipe_id)
                .then((result) => { this.recipe = result.recipe; });
        },

        getIngredients() {
            this.server('recipe-ingredients/' + this.recipe_id)
                .then((result) => { this.ingredients = result; });
        },

        changeStep(step) {
            if (step < 0 || step >= this.recipe.steps.length) return;
            this.currentStep = step;
            if (this.AuthUser) {
                let formdata = new FormData();
                formdata.append('step_number', step);
                this.server('step-user/' + this.recipe_id, 'POST', formdata);
            }
        },

        getUserStep() {
            this.server('step-user/' + this.recipe_id)
                .then((result) => {
                    if (result && result.step_number !== null) {
                        this.currentStep = result.step_number;
                    }
                });
        },
    },
};
</script>