<template>
    <div>
        <div class="zakaz">
            <button @click="open()">Добавить рецепт</button>
        </div>

        <div v-if="showAdd" class="overlay">
            <div class="popuplg">
                <div class="popuphead">
                    <span>{{ id ? 'Изменить' : 'Добавить' }} рецепт</span>
                    <button @click="showAdd = false">×</button>
                </div>
                <div class="popupbody">
                    <label>Название</label>
                    <input type="text" v-model="title" :class="{ error: errors.title }" />
                    <div v-if="errors.title" class="errortext">{{ errors.title.join('. ') }}</div>

                    <label>Описание</label>
                    <textarea v-model="description" rows="3"></textarea>
                    <div v-if="errors.description" class="errortext">{{ errors.description.join('. ') }}</div>

                    <label>Время (минуты)</label>
                    <input type="number" v-model="cook_time" />
                    <div v-if="errors.cook_time" class="errortext">{{ errors.cook_time.join('. ') }}</div>

                    <label>Сложность</label><br>
                    <button v-for="d in ['easy','medium','difficult']" :key="d"
                        @click="difficulty = d" :class="difficulty == d ? '' : 'white'" style="margin:3px;">
                        {{ {easy:'Легко', medium:'Средне', difficult:'Сложно'}[d] }}
                    </button>
                    <div v-if="errors.difficulty" class="errortext">{{ errors.difficulty[0] }}</div>

                    <br><label>Категория</label>
                    <select v-model="category_id">
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <div v-if="errors.category_id" class="errortext">{{ errors.category_id[0] }}</div>

                    <label>Фото</label>
                    <input type="file" id="recipeImage" />
                    <img v-if="image_url" :src="'/storage/' + image_url" style="height:80px; margin-top:6px; display:block;" />
                    <div v-if="errors.image_url" class="errortext">{{ errors.image_url[0] }}</div>
                </div>
                <div class="popupfoot">
                    <button class="gray" @click="showAdd = false">Отмена</button>
                    <button @click="save">{{ id ? 'Сохранить' : 'Добавить' }}</button>
                </div>
            </div>
        </div>

        <div v-if="showDelete" class="overlay">
            <div class="popup">
                <div class="popuphead">
                    <span>Удаление</span>
                    <button @click="showDelete = false">×</button>
                </div>
                <div class="popupbody">Удалить рецепт "{{ title }}"?</div>
                <div class="popupfoot">
                    <button class="gray" @click="showDelete = false">Отмена</button>
                    <button class="red" @click="remove">Удалить</button>
                </div>
            </div>
        </div>

        <div v-if="showSteps" class="overlay">
            <div class="popuplg">
                <div class="popuphead">
                    <span>Шаги и ингредиенты — {{ title }}</span>
                    <button @click="showSteps = false">×</button>
                </div>
                <div class="popupbody">
                    <h4>Ингредиенты</h4>
                    <table>
                        <thead><tr><th>#</th><th>Ингредиент</th><th>Количество</th><th></th></tr></thead>
                        <tbody>
                            <tr v-for="(item, key) in recipeIngredients" :key="key">
                                <td>{{ key + 1 }}</td>
                                <td>
                                    <select v-model="item.ingredient_id" style="width:100%;">
                                        <option v-for="ing in allIngredients" :key="ing.id" :value="ing.id">{{ ing.name }}</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" v-model="item.quantity" style="width:80px;" />
                                    <span>{{ allIngredients.find(i => i.id == item.ingredient_id)?.unit }}</span>
                                </td>
                                <td><button class="red" @click="recipeIngredients.splice(key, 1)">Удалить</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <button class="white" @click="recipeIngredients.push({ ingredient_id: '', quantity: '' })">+ Ингредиент</button>
                    <button class="gray" @click="saveIngredients">Сохранить ингредиенты</button>
                    <div v-if="ingredientErrors" class="errortext">{{ ingredientErrors }}</div>

                    <hr>
                    <h4>Шаги</h4>
                    <div v-for="(step, key) in steps" :key="key" class="zakaz" style="margin:0 0 10px 0;">
                        <b>Шаг {{ key + 1 }}</b>
                        <button class="red" style="float:right;" @click="deleteStep(key)">Удалить</button>
                        <br>
                        <textarea v-model="step.description" rows="3" placeholder="Описание шага"></textarea>
                        <label>Фото шага</label>
                        <input type="file" class="step-photo" :id="'step-photo-' + key" />
                        <img v-if="step.image_url" :src="'/storage/' + step.image_url" style="height:80px; margin-top:6px; display:block;" />
                    </div>
                    <button class="white" @click="steps.push({ description: '', image_url: '' })">+ Шаг</button>
                    <button class="gray" @click="saveSteps">Сохранить шаги</button>
                    <div v-if="stepErrors" class="errortext">{{ stepErrors }}</div>
                </div>
            </div>
        </div>

        <div class="zakaz">
            <table>
                <thead>
                    <tr><th>ID</th><th>Название</th><th>Время</th><th>Сложность</th><th>Категория</th><th>Действия</th></tr>
                </thead>
                <tbody>
                    <tr v-for="recipe in recipes.data" :key="recipe.id">
                        <td>{{ recipe.id }}</td>
                        <td>{{ recipe.title }}</td>
                        <td>{{ recipe.cook_time }} мин</td>
                        <td>{{ recipe.difficulty }}</td>
                        <td>{{ recipe.category.name }}</td>
                        <td>
                            <button class="gray" @click="open(recipe)">Изменить</button>
                            <button class="gray" @click="openSteps(recipe)">Шаги</button>
                            <button class="red" @click="id = recipe.id; title = recipe.title; showDelete = true">Удалить</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <button @click="load(recipes.current_page - 1)" :disabled="recipes.current_page == 1">← Назад</button>
            {{ recipes.current_page }} / {{ recipes.last_page }}
            <button @click="load(recipes.current_page + 1)" :disabled="recipes.last_page == recipes.current_page">Далее →</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RecipesPage',
    props: ['server', 'changePage'],
    data() {
        return {
            id: '',
             title: '',
              description: '',
               cook_time: '',
                difficulty: '',
            category_id: '',
             image_url: '',
              ingredientErrors: '',
               stepErrors: '',
                errors: {},
            recipes: { data: [], current_page: 1, last_page: 1 },
            categories: [],
             allIngredients: [],
              recipeIngredients: [],
               steps: [],
            showAdd: false, showDelete: false, showSteps: false,
        };
    },
    mounted() {
        this.load();
        this.server('categories').then(r => { this.categories = r; });
        this.server('ingredient').then(r => { this.allIngredients = r; });
    },
    methods: {
        load(page = 1) {
            this.server('recipes?page=' + page).then(r => { this.recipes = r; });
        },
        open(recipe = null) {
            this.id = recipe ? recipe.id : '';
            this.title = recipe ? recipe.title : '';
            this.description = recipe ? recipe.description : '';
            this.cook_time = recipe ? recipe.cook_time : '';
            this.difficulty = recipe ? recipe.difficulty : '';
            this.category_id = recipe ? recipe.category_id : '';
            this.image_url = recipe ? recipe.image_url : '';
            this.errors = {};
            this.showAdd = true;
        },
        openSteps(recipe) {
            this.id = parseInt(recipe.id);
            this.title = recipe.title;
            this.ingredientErrors = '';
            this.stepErrors = '';
            this.server('steps/' + this.id).then(r => { this.steps = r; });
            this.server('recipe-ingredients/' + this.id).then(r => { this.recipeIngredients = r; });
            this.showSteps = true;
        },
        save() {
            let fd = new FormData();
            fd.append('title', this.title);
            fd.append('description', this.description);
            fd.append('cook_time', this.cook_time);
            fd.append('difficulty', this.difficulty);
            fd.append('category_id', this.category_id);
            let img = document.querySelector('#recipeImage');
            if (img && img.files[0]) fd.append('image_url', img.files[0]);
            this.server(this.id ? 'recipe/' + this.id : 'recipe', 'POST', fd).then(r => {
                if (r.errors) { this.errors = r.errors; }
                else { this.showAdd = false; this.load(); }
            });
        },
        remove() {
            this.server('recipe/' + this.id, 'DELETE').then(() => { this.showDelete = false; this.load(); });
        },
        saveIngredients() {
            let fd = new FormData();
            fd.append('all_ingredients', JSON.stringify(this.recipeIngredients));
            this.server('save-ingredients/' + this.id, 'POST', fd).then(r => {
                if (r.errors) { this.ingredientErrors = r.errors.all_ingredients[0]; }
                else { alert('Сохранено!'); }
            });
        },
        saveSteps() {
            let photos = document.querySelectorAll('.step-photo');
            for (let i = 0; i < this.steps.length; i++) {
                if (!this.steps[i].description) { this.stepErrors = 'Заполните поля'; return; }
                let fd = new FormData();
                fd.append('description', this.steps[i].description);
                fd.append('step_number', i);
                if (photos[i]?.files[0]) fd.append('image_url', photos[i].files[0]);
                this.server(this.steps[i].id ? 'step-update/' + this.steps[i].id : 'steps/' + this.id, 'post', fd);
            }
            this.openSteps({ id: this.id, title: this.title });
            alert('Сохранено!');
        },
        deleteStep(key) {
            if (this.steps[key].id) this.server('step/' + this.steps[key].id, 'DELETE');
            this.steps.splice(key, 1);
        },
    },
};
</script>