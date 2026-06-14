<template>
    <div>
        <button
            @click="clearModal"
            class="btn btn-outline-danger m-3"
            data-bs-toggle="modal"
            data-bs-target="#ingredientModal"
        >
            Добавить ингредиент
        </button>
        <div class="modal fade" id="ingredientModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ id ? "Изменить" : "Добавить" }} ингредиент
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            id="closeIngredientModal"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input
                                type="text"
                                v-model="name"
                                :class="{ 'is-invalid': errors.name }"
                                class="form-control focus-ring focus-ring-danger border"
                            />
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.name.join(". ") }}
                            </div>
                        </div>
                        <div class="mb-3">
    <label class="form-label">Единица измерения</label>
    <select v-model="unit"
        :class="{ 'is-invalid': errors.unit }"
        class="form-select">
        <option value="">-- Выберите --</option>
        <option value="кг">кг</option>
        <option value="г">г</option>
        <option value="мл">мл</option>
        <option value="л">л</option>
        <option value="шт">шт</option>
        <option value="ст.л">ст.л</option>
        <option value="ч.л">ч.л</option>
        <option value="зуб">зуб</option>
    </select>
    <div v-if="errors.unit" class="invalid-feedback">
        {{ errors.unit[0] }}
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
        <div class="modal fade" id="deleteIngredientModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтвердить удаление</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        Удалить ингредиент "{{ name }}"?
                    </div>
                    <div class="modal-footer">
                        <button
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Отмена
                        </button>
                        <button
                            @click="remove"
                            data-bs-dismiss="modal"
                            class="btn btn-danger"
                        >
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Единица</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ingredient in ingredients" :key="ingredient.id">
                    <td>{{ ingredient.id }}</td>
                    <td>{{ ingredient.name }}</td>
                    <td>{{ ingredient.unit }}</td>
                    <td>
                        <div class="btn-group">
                            <button
                                class="btn btn-outline-warning"
                                @click="edit(ingredient)"
                                data-bs-toggle="modal"
                                data-bs-target="#ingredientModal"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button
                                class="btn btn-outline-danger"
                                @click="confirmDelete(ingredient)"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteIngredientModal"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "IngredientsPage",
    props: ["server"],
    data() {
        return {
            id: "",
            name: "",
            unit: "",
            errors: {},
            ingredients: [],
        };
    },
    mounted() {
        this.getAll();
    },
    methods: {
        getAll() {
            this.server("ingredient").then((result) => {
                this.ingredients = Object.values(result);
            });
        },

        clearModal() {
            this.id = "";
            this.name = "";
            this.unit = "";
            this.errors = {};
        },

        edit(ingredient) {
            this.id = ingredient.id;
            this.name = ingredient.name;
            this.unit = ingredient.unit;
            this.errors = {};
        },

        confirmDelete(ingredient) {
            this.id = ingredient.id;
            this.name = ingredient.name;
        },

       save() {
    let formdata = new FormData();
    formdata.append('name', this.name);
    formdata.append('unit', this.unit);

    const route = this.id ? 'ingredient/' + this.id : 'ingredient';
    this.server(route, 'POST', formdata).then((result) => {
        if (result.errors) {
            this.errors = result.errors;
        } else {
            document.querySelector('#closeIngredientModal').click();
            this.clearModal();
            this.getAll();
        }
    });
},

        remove() {
            this.server("ingredient/" + this.id, "DELETE").then(() => {
                this.getAll();
            });
        },
    },
};
</script>
