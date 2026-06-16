<template>
    <div>
        <div class="zakaz">
            <button @click="goBack">← Назад</button>
            <h2>{{ recipe.title }}</h2>
        </div>

        <div class="zakaz">
            <img v-if="recipe.image_url" :src="'/storage/' + recipe.image_url" style="height:250px; object-fit:cover; display:block; margin-bottom:10px;" />
            <p>{{ recipe.description }}</p>
            <p>Время: {{ recipe.cook_time }} мин</p>
            <p>Сложность: {{ recipe.difficulty }}</p>
        </div>

        <div class="zakaz">
            <h4>Ингредиенты</h4>
            <table>
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
        </div>

        <div class="zakaz">
            <h4>Шаги приготовления</h4>
            <div style="margin-bottom:10px;">
                <button v-for="st in recipe.steps" :key="st.id"
                    @click="changeStep(st.step_number)"
                    :class="st.step_number == currentStep ? '' : 'white'"
                    style="margin:3px;">{{ st.step_number + 1 }}</button>
            </div>

            <div v-if="recipe.steps && recipe.steps[currentStep]">
                <img v-if="recipe.steps[currentStep].image_url"
                    :src="'/storage/' + recipe.steps[currentStep].image_url"
                    style="height:200px; object-fit:cover; display:block; margin-bottom:10px;" />
                <div v-else class="imgblank">НЕТУ ФОТО </div>
                <h5>Описание шага {{ currentStep + 1 }}</h5>
                <p>{{ recipe.steps[currentStep].description }}</p>
                <button @click="changeStep(currentStep - 1)" :disabled="currentStep == 0">← Назад</button>
                <button @click="changeStep(currentStep + 1)" :disabled="currentStep == recipe.steps.length - 1">Далее →</button>
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
    },
    methods: {
        goBack() {
            this.changePage('RecipeListComponent');
        },
        getRecipe() {
            this.server('recipes/' + this.recipe_id).then((result) => {
                this.recipe = result.recipe;
                this.getUserStep();
            });
        },
        getIngredients() {
            this.server('recipe-ingredients/' + this.recipe_id).then((result) => {
                this.ingredients = result;
            });
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
            this.server('step-user/' + this.recipe_id).then((result) => {
                if (result && result.step_number !== null) {
                    this.currentStep = result.step_number;
                }
            });
        },
    },
};
</script>