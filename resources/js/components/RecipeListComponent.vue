<template>
    <div class="container mt-3">
        <div class="row mb-3">
            <div class="col-10">
                <input type="text" v-model="search" class="form-control" placeholder="Найти рецепт..." />
            </div>
            <div class="col-2">
                <button @click="applyFilter" class="btn btn-success w-100">Найти</button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <p class="mb-1"><b>Категория</b></p>
                <div class="d-flex flex-wrap gap-2">
                    <div v-for="cat in categories" :key="cat.id">
                        <input class="btn-check" v-model="checkedCategories" :value="cat.id" type="checkbox" :id="'c'+cat.id" />
                        <label class="btn btn-outline-success btn-sm" :for="'c'+cat.id">{{ cat.name }}</label>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><b>Время</b></p>
                <div class="d-flex flex-wrap gap-2">
                    <input v-model="checkedTime" value="[0,30]" type="radio" class="btn-check" id="t1" />
                    <label class="btn btn-outline-success btn-sm" for="t1">до 30 мин</label>
                    <input v-model="checkedTime" value="[30,60]" type="radio" class="btn-check" id="t2" />
                    <label class="btn btn-outline-success btn-sm" for="t2">30-60 мин</label>
                    <input v-model="checkedTime" value="[60,6000]" type="radio" class="btn-check" id="t3" />
                    <label class="btn btn-outline-success btn-sm" for="t3">более 60</label>
                </div>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><b>Сложность</b></p>
                <div class="d-flex flex-wrap gap-2">
                    <input v-model="checkedDif" value="easy" type="radio" class="btn-check" id="d1" />
                    <label class="btn btn-outline-success btn-sm" for="d1">Легко</label>
                    <input v-model="checkedDif" value="medium" type="radio" class="btn-check" id="d2" />
                    <label class="btn btn-outline-success btn-sm" for="d2">Средне</label>
                    <input v-model="checkedDif" value="difficult" type="radio" class="btn-check" id="d3" />
                    <label class="btn btn-outline-success btn-sm" for="d3">Сложно</label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button @click="applyFilter" class="btn btn-success btn-sm">Применить</button>
                <button @click="clearFilter" class="btn btn-outline-secondary btn-sm">Сбросить</button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-4 ms-auto">
                <select v-model="selectSort" @change="changeSort" class="form-select">
                    <option v-for="(s, k) in sortOptions" :key="k" :value="k">{{ s.text }}</option>
                </select>
            </div>
        </div>

        <div class="row">
            <template v-if="isLoad">
                <div v-for="recipe in recipes.data" :key="recipe.id" class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img :src="'/storage/' + recipe.image_url" class="card-img-top" style="height:180px; object-fit:cover;" alt="" />
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ recipe.title }}</h6>
                            <p class="card-text text-muted small">
                                ⏱ {{ recipe.cook_time }} мин &nbsp;
                                📊 {{ recipe.difficulty }} &nbsp;
                                🏷 {{ recipe.category.name }}
                            </p>
                            <div class="mt-auto d-flex gap-1">
                                <button @click="changePage('SinglePage', recipe.id)" class="btn btn-success btn-sm flex-grow-1">
                                    Открыть
                                </button>
                                <button v-if="AuthUser && user.role == 'user'"
                                    @click="toggleFav(recipe.id)"
                                    class="btn btn-sm"
                                    :class="favorites.includes(recipe.id) ? 'btn-danger' : 'btn-outline-secondary'">
                                    ♥
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div v-for="i in 8" :key="i" class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div style="height:180px; background:#eee;"></div>
                        <div class="card-body">
                            <div class="placeholder-glow">
                                <span class="placeholder col-8 mb-2 d-block"></span>
                                <span class="placeholder col-6 d-block"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="d-flex justify-content-center gap-2 mb-4">
            <button class="btn btn-success" @click="getRecipes(recipes.current_page - 1)" :disabled="recipes.current_page == 1">← Назад</button>
            <span class="btn disabled">{{ recipes.current_page }} / {{ recipes.last_page }}</span>
            <button class="btn btn-success" @click="getRecipes(recipes.current_page + 1)" :disabled="recipes.last_page == recipes.current_page">Далее →</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RecipeListComponent',
    props: ['isLoad', 'AuthUser', 'user', 'server', 'changePage', 'page'],
    data() {
        return {
            search: '',
            recipes: { data: [], current_page: 1, last_page: 1 },
            favorites: [],
            categories: {},
            sort: { field: 'created_at', by: 'desc' },
            selectSort: 0,
            sortOptions: [
                { text: 'Сначала новые',   field: 'created_at', by: 'desc' },
                { text: 'Сначала старые',  field: 'created_at', by: 'asc' },
                { text: 'Время ↑',         field: 'cook_time',  by: 'asc' },
                { text: 'Время ↓',         field: 'cook_time',  by: 'desc' },
            ],
            checkedCategories: [],
            checkedTime: '',
            checkedDif: '',
            isFilter: false,
        }
    },
    mounted() {
        if (localStorage.getItem('token')) this.getFavoriteIds()
        this.getRecipes()
        this.getCategories()
    },
    methods: {
        applyFilter() {
            this.isFilter = true
            this.getRecipes()
        },
        clearFilter() {
            this.checkedTime = ''
            this.checkedDif = ''
            this.checkedCategories = []
            this.search = ''
            this.isFilter = false
            this.getRecipes()
        },
        changeSort(e) {
            this.sort.field = this.sortOptions[e.target.value].field
            this.sort.by    = this.sortOptions[e.target.value].by
            this.getRecipes()
        },
        getRecipes(page = 1) {
            let fd = new FormData()
            fd.append('sort', JSON.stringify(this.sort))
            if (this.isFilter) {
                if (this.checkedCategories.length > 0) fd.append('categories', JSON.stringify(this.checkedCategories))
                if (this.checkedDif)  fd.append('difficulty', this.checkedDif)
                if (this.checkedTime) fd.append('checkedTime', this.checkedTime)
                if (this.search)      fd.append('search', this.search)
            }
            this.server('recipes?page=' + page, 'POST', fd).then(r => { this.recipes = r })
        },
       getFavoriteIds() {
    this.server('favorite-ids').then(r => {
        this.favorites = r.map(f => f.recipe_id)
    })
},
        getCategories() {
            this.server('categories').then(r => { this.categories = r })
        },
        toggleFav(id) {
            if (this.favorites.includes(id)) {
                this.favorites = this.favorites.filter(f => f !== id)
            } else {
                this.favorites.push(id)
            }
            this.server('favorite/' + id, 'POST')
        },
    }
}
</script>