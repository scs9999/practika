<template>
    <div>
        <button
            @click="clearModal"
            class="btn btn-outline-danger m-3"
            data-bs-toggle="modal"
            data-bs-target="#recipeModal"
        >
            Добавить рецепт
        </button>
        <div class="modal fade" id="recipeModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ id ? "Изменить" : "Добавить" }} рецепт
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            id="closeRecipeModal"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input
                                type="text"
                                v-model="title"
                                :class="{ 'is-invalid': errors.title }"
                                class="form-control focus-ring focus-ring-danger border"
                            />
                            <div v-if="errors.title" class="invalid-feedback">
                                {{ errors.title.join(". ") }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea
                                v-model="description"
                                rows="3"
                                :class="{ 'is-invalid': errors.description }"
                                class="form-control focus-ring focus-ring-danger border"
                            ></textarea>
                            <div v-if="errors.description" class="invalid-feedback">
                                {{ errors.description.join(". ") }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Время готовки (минуты)</label>
                            <input
                                type="number"
                                v-model="cook_time"
                                :class="{ 'is-invalid': errors.cook_time }"
                                class="form-control focus-ring focus-ring-danger border"
                            />
                            <div v-if="errors.cook_time" class="invalid-feedback">
                                {{ errors.cook_time.join(". ") }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Сложность</label>
                            <div class="btn-group d-block">
                                <input v-model="difficulty" value="easy" type="radio" class="btn-check" id="easy" />
                                <label class="btn btn-outline-danger" for="easy">Легко</label>
                                <input v-model="difficulty" value="medium" type="radio" class="btn-check" id="medium" />
                                <label class="btn btn-outline-danger" for="medium">Средне</label>
                                <input v-model="difficulty" value="difficult" type="radio" class="btn-check" id="difficult" />
                                <label class="btn btn-outline-danger" for="difficult">Сложно</label>
                            </div>
                            <div v-if="errors.difficulty" class="text-danger small mt-1">
                                {{ errors.difficulty[0] }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Категория</label>
                            <select v-model="category_id"
                                :class="{ 'is-invalid': errors.category_id }"
                                class="form-select focus-ring focus-ring-danger border">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <div v-if="errors.category_id" class="invalid-feedback">
                                {{ errors.category_id[0] }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Фото</label>
                            <input type="file" class="form-control" id="recipeImage"
                                :class="{ 'is-invalid': errors.image_url }" />
                            <img v-if="image_url" :src="'/storage/' + image_url" class="mt-2" style="height:100px;" />
                            <div v-if="errors.image_url" class="invalid-feedback">
                                {{ errors.image_url[0] }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="save" class="btn btn-danger w-100">
                            {{ id ? "Сохранить" : "Добавить" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteRecipeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтвердить удаление</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Удалить рецепт "{{ title }}"?</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button @click="remove" data-bs-dismiss="modal" class="btn btn-danger">Удалить</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="stepsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Шаги и ингредиенты — {{ title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h5>Ингредиенты</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ингредиент</th>
                                    <th>Количество</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, key) in recipeIngredients" :key="key">
                                    <td>{{ key + 1 }}</td>
                                    <td>
                                        <select v-model="item.ingredient_id"
                                            class="form-select focus-ring focus-ring-danger border">
                                            <option v-for="ing in allIngredients" :key="ing.id" :value="ing.id">
                                                {{ ing.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" v-model="item.quantity"
                                                class="form-control focus-ring focus-ring-danger border" />
                                            <span v-if="allIngredients.find(i => i.id == item.ingredient_id)" class="input-group-text">
                                                {{ allIngredients.find(i => i.id == item.ingredient_id).unit }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <button @click="recipeIngredients.splice(key, 1)" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mb-3">
                            <button @click="recipeIngredients.push({ ingredient_id: '', quantity: '' })"
                                class="btn btn-outline-danger me-2">
                                + Добавить ингредиент
                            </button>
                            <button @click="saveIngredients" class="btn btn-secondary">
                                <i class="bi bi-floppy"></i> Сохранить ингредиенты
                            </button>
                            <div v-if="ingredientErrors" class="text-danger small mt-2">
                                {{ ingredientErrors }}
                            </div>
                        </div>

                        <hr />
                        <h5>Шаги</h5>
                        <div v-for="(step, key) in steps" :key="key" class="card mb-3">
                            <div class="card-header d-flex justify-content-between">
                                <span>Шаг {{ key + 1 }}</span>
                                <button @click="deleteStep(key)" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-8">
                                    <textarea v-model="step.description" rows="3" placeholder="Описание шага"
                                        class="form-control focus-ring focus-ring-danger border mb-2"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Фото шага</label>
                                    <input type="file" class="form-control step-photo" :id="'step-photo-' + key" />
                                    <img v-if="step.image_url" :src="'/storage/' + step.image_url" class="mt-2 img-fluid rounded" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button @click="steps.push({ description: '', image_url: '' })"
                                class="btn btn-outline-danger me-2">
                                + Добавить шаг
                            </button>
                            <button @click="saveSteps" class="btn btn-secondary">
                                <i class="bi bi-floppy"></i> Сохранить шаги
                            </button>
                            <div v-if="stepErrors" class="text-danger small mt-2">
                                {{ stepErrors }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Время</th>
                    <th>Сложность</th>
                    <th>Категория</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="recipe in recipes.data" :key="recipe.id">
                    <td>{{ recipe.id }}</td>
                    <td>{{ recipe.title }}</td>
                    <td>{{ recipe.cook_time }} мин</td>
                    <td>{{ recipe.difficulty }}</td>
                    <td>{{ recipe.category.name }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-outline-warning" @click="edit(recipe)"
                                data-bs-toggle="modal" data-bs-target="#recipeModal">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-secondary" @click="openSteps(recipe)"
                                data-bs-toggle="modal" data-bs-target="#stepsModal">
                                <i class="bi bi-list-ol"></i>
                            </button>
                            <button class="btn btn-outline-danger" @click="confirmDelete(recipe)"
                                data-bs-toggle="modal" data-bs-target="#deleteRecipeModal">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center mb-3">
            <button class="btn btn-danger m-2" @click="getAll(recipes.current_page - 1)" :disabled="recipes.current_page == 1">
                Назад
            </button>
            <button class="btn btn-danger m-2" @click="getAll(recipes.current_page + 1)" :disabled="recipes.last_page == recipes.current_page">
                Далее
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "RecipesPage",
    props: ["server", "changePage"],
    data() {
        return {
            id: "",
            title: "",
            description: "",
            cook_time: "",
            difficulty: "",
            category_id: "",
            image_url: "",
            ingredientErrors: "",
            stepErrors: "",
            errors: {},
            recipes: { data: [], current_page: 1, last_page: 1 },
            categories: [],
            allIngredients: [],
            recipeIngredients: [],
            steps: [],
        };
    },
    mounted() {
        this.getAll();
        this.getCategories();
        this.getAllIngredients();
    },
    methods: {
        getAll(page = 1) {
            this.server("recipes?page=" + page).then((result) => {
                this.recipes = result;
            });
        },

        getCategories() {
            this.server("categories").then((result) => {
                this.categories = result;
            });
        },

        getAllIngredients() {
            this.server("ingredient").then((result) => {
                this.allIngredients = result;
            });
        },

        clearModal() {
            this.id = "";
            this.title = "";
            this.description = "";
            this.cook_time = "";
            this.difficulty = "";
            this.category_id = "";
            this.image_url = "";
            this.errors = {};
        },

        edit(recipe) {
            this.id = recipe.id;
            this.title = recipe.title;
            this.description = recipe.description;
            this.cook_time = recipe.cook_time;
            this.difficulty = recipe.difficulty;
            this.category_id = recipe.category_id;
            this.image_url = recipe.image_url;
            this.errors = {};
        },

        confirmDelete(recipe) {
            this.id = recipe.id;
            this.title = recipe.title;
        },

        openSteps(recipe) {
            this.id = parseInt(recipe.id);
            this.title = recipe.title;
            this.ingredientErrors = "";
            this.stepErrors = "";
            this.server("steps/" + this.id).then((result) => {
                this.steps = result;
            });
            this.server("recipe-ingredients/" + this.id).then((result) => {
                this.recipeIngredients = result;
            });
        },

        save() {
            let formdata = new FormData();
            formdata.append("title", this.title);
            formdata.append("description", this.description);
            formdata.append("cook_time", this.cook_time);
            formdata.append("difficulty", this.difficulty);
            formdata.append("category_id", this.category_id);
            let img = document.querySelector("#recipeImage");
            if (img && img.files[0]) formdata.append("image_url", img.files[0]);

            const route = this.id ? "recipe/" + this.id : "recipe";
            this.server(route, "POST", formdata).then((result) => {
                if (result.errors) {
                    this.errors = result.errors;
                } else {
                    document.querySelector("#closeRecipeModal").click();
                    this.clearModal();
                    this.getAll();
                }
            });
        },

        remove() {
            this.server("recipe/" + this.id, "DELETE").then(() => {
                this.getAll();
            });
        },

        saveIngredients() {
            this.ingredientErrors = "";
            let formdata = new FormData();
            formdata.append("all_ingredients", JSON.stringify(this.recipeIngredients));
            this.server("save-ingredients/" + this.id, "POST", formdata).then((result) => {
                if (result.errors) {
                    this.ingredientErrors = result.errors.all_ingredients[0];
                } else {
                    alert("Ингредиенты сохранены!");
                }
            });
        },

        saveSteps() {
            this.stepErrors = "";
            let photos = document.querySelectorAll(".step-photo");
            let promises = [];

            this.steps.forEach((step, key) => {
                let formdata = new FormData();
                formdata.append("description", step.description);
                formdata.append("step_number", key);
                if (photos[key] && photos[key].files[0]) {
                    formdata.append("image_url", photos[key].files[0]);
                }
                if (step.id) {
                    promises.push(this.server("step-update/" + step.id, "POST", formdata));
                } else {
                    promises.push(this.server("steps/" + this.id, "POST", formdata));
                }
            });

            Promise.all(promises).then((results) => {
                if (results.some(r => r && r.errors)) {
                    this.stepErrors = "Заполните описание всех шагов";
                } else {
                    this.openSteps({ id: this.id, title: this.title });
                    alert("Шаги сохранены!");
                }
            });
        },

        deleteStep(key) {
            if (this.steps[key].id) {
                this.server("step/" + this.steps[key].id, "DELETE");
            }
            this.steps.splice(key, 1);
        },
    },
};
</script>