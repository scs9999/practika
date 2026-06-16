<template>
    <div>
        <div class="zakaz">
            <input type="text" v-model="search" placeholder="Найти рецепт..." style="width:250px; margin-right:6px;" />
            <button @click="applyFilter">Найти</button>
            <button class="gray" @click="clearFilter" style="margin-left:4px;">Сбросить</button>

            <br><br>
            <div style="display:flex; gap:30px; align-items:flex-start;">
                <div>
                    <b>Категория:</b><br>
                    <span v-for="cat in categories" :key="cat.id">
                        <button @click="toggleCategory(cat.id)" :class="checkedCategories.includes(cat.id) ? '' : 'white'" style="margin:3px;">{{ cat.name }}</button>
                    </span>
                </div>
                <div>
                    <b>Время:</b><br>
                    <button @click="setTime('[0,30]')" :class="checkedTime == '[0,30]' ? '' : 'white'" style="margin:3px;">до 30 мин</button>
                    <button @click="setTime('[30,60]')" :class="checkedTime == '[30,60]' ? '' : 'white'" style="margin:3px;">30-60 мин</button>
                    <button @click="setTime('[60,6000]')" :class="checkedTime == '[60,6000]' ? '' : 'white'" style="margin:3px;">более 60</button>
                </div>
                <div>
                    <b>Сложность:</b><br>
                    <button @click="setDif('easy')" :class="checkedDif == 'easy' ? '' : 'white'" style="margin:3px;">Легко</button>
                    <button @click="setDif('medium')" :class="checkedDif == 'medium' ? '' : 'white'" style="margin:3px;">Средне</button>
                    <button @click="setDif('difficult')" :class="checkedDif == 'difficult' ? '' : 'white'" style="margin:3px;">Сложно</button>
                </div>
                <div>
                    <b>Сортировка:</b><br>
                    <select v-model="selectSort" @change="changeSort" style="padding:4px; width:auto; margin-top:3px;">
                        <option v-for="(s, k) in sortOptions" :key="k" :value="k">{{ s.text }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="products-container">
            <div v-for="recipe in recipes.data" :key="recipe.id" class="product-box">
                <img :src="'/storage/' + recipe.image_url" alt="" />
                <p><b>{{ recipe.title }}</b></p>
                <p>{{ recipe.cook_time }} мин | {{ recipe.difficulty }} | {{ recipe.category.name }}</p>
                <button @click="changePage('SinglePage', recipe.id)">Открыть</button>
                <button v-if="AuthUser && user.role == 'user'" @click="toggleFav(recipe.id)" :class="favorites.includes(recipe.id) ? 'red' : 'white'">♥</button>
            </div>
        </div>

        <div class="zakaz">
            <button @click="getRecipes(recipes.current_page - 1)" :disabled="recipes.current_page == 1">← Назад</button>
            {{ recipes.current_page }} / {{ recipes.last_page }}
            <button @click="getRecipes(recipes.current_page + 1)" :disabled="recipes.last_page == recipes.current_page">Далее →</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RecipeListComponent',
    props: ['isLoad', 'AuthUser', 'user', 'server', 'changePage', 'page'],
    data() {
        return {
            search: localStorage.getItem('f_search') || '',
            checkedCategories: JSON.parse(localStorage.getItem('f_categories') || '[]'),
            checkedTime: localStorage.getItem('f_time') || '',
            checkedDif: localStorage.getItem('f_dif') || '',
            selectSort: parseInt(localStorage.getItem('f_sort') || '0'),
            recipes: { data: [], current_page: 1, last_page: 1 },
            favorites: [],
            categories: [],
            sort: { field: 'created_at', by: 'desc' },
            sortOptions: [
                { text: 'Сначала новые', field: 'created_at', by: 'desc' },
                { text: 'Сначала старые', field: 'created_at', by: 'asc' },
                { text: 'Время ↑', field: 'cook_time', by: 'asc' },
                { text: 'Время ↓', field: 'cook_time', by: 'desc' },
            ],
        };
    },
    mounted() {
        this.sort = this.sortOptions[this.selectSort];
        if (localStorage.getItem('token')) this.getFavoriteIds();
        this.getRecipes();
        this.getCategories();
    },
    methods: {
        saveFilters() {
            localStorage.setItem('f_search', this.search);
            localStorage.setItem('f_categories', JSON.stringify(this.checkedCategories));
            localStorage.setItem('f_time', this.checkedTime);
            localStorage.setItem('f_dif', this.checkedDif);
            localStorage.setItem('f_sort', this.selectSort);
        },
        toggleCategory(id) {
            if (this.checkedCategories.includes(id)) {
                this.checkedCategories = this.checkedCategories.filter(c => c !== id);
            } else {
                this.checkedCategories.push(id);
            }
            this.saveFilters();
            this.getRecipes();
        },
        setTime(val) {
            this.checkedTime = this.checkedTime == val ? '' : val;
            this.saveFilters();
            this.getRecipes();
        },
        setDif(val) {
            this.checkedDif = this.checkedDif == val ? '' : val;
            this.saveFilters();
            this.getRecipes();
        },
        applyFilter() {
            this.saveFilters();
            this.getRecipes();
        },
        clearFilter() {
            this.checkedTime = '';
            this.checkedDif = '';
            this.checkedCategories = [];
            this.search = '';
            this.selectSort = 0;
            this.sort = this.sortOptions[0];
            this.saveFilters();
            this.getRecipes();
        },
        changeSort(e) {
            this.selectSort = parseInt(e.target.value);
            this.sort = this.sortOptions[this.selectSort];
            this.saveFilters();
            this.getRecipes();
        },
        getRecipes(page = 1) {
            let fd = new FormData();
            fd.append('sort', JSON.stringify(this.sort));
            if (this.checkedCategories.length > 0) fd.append('categories', JSON.stringify(this.checkedCategories));
            if (this.checkedDif) fd.append('difficulty', this.checkedDif);
            if (this.checkedTime) fd.append('checkedTime', this.checkedTime);
            if (this.search) fd.append('search', this.search);
            this.server('recipes?page=' + page, 'POST', fd).then(r => { this.recipes = r; });
        },
        getFavoriteIds() {
            this.server('favorite-ids').then(r => { this.favorites = r.map(f => f.recipe_id); });
        },
        getCategories() {
            this.server('categories').then(r => { this.categories = r; });
        },
        toggleFav(id) {
            if (this.favorites.includes(id)) {
                this.favorites = this.favorites.filter(f => f !== id);
            } else {
                this.favorites.push(id);
            }
            this.server('favorite/' + id, 'POST');
        },
    },
};
</script>