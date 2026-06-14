<template>
    <div>
        <button
            @click="clearModal"
            class="btn btn-outline-danger m-3"
            data-bs-toggle="modal"
            data-bs-target="#categoryModal"
        >
            Добавить категорию
        </button>
        <div class="modal fade" id="categoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ id ? "Изменить" : "Добавить" }} категорию
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            id="closeCategoryModal"
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
                            <label class="form-label">Описание</label>
                            <input
                                type="text"
                                v-model="description"
                                :class="{ 'is-invalid': errors.description }"
                                class="form-control focus-ring focus-ring-danger border"
                            />
                            <div
                                v-if="errors.description"
                                class="invalid-feedback"
                            >
                                {{ errors.description.join(". ") }}
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
        <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
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
                        Удалить категорию "{{ name }}"?
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
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="category in categories" :key="category.id">
                    <td>{{ category.id }}</td>
                    <td>{{ category.name }}</td>
                    <td>{{ category.description }}</td>
                    <td>
                        <div class="btn-group">
                            <button
                                class="btn btn-outline-warning"
                                @click="edit(category)"
                                data-bs-toggle="modal"
                                data-bs-target="#categoryModal"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button
                                class="btn btn-outline-danger"
                                @click="confirmDelete(category)"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteCategoryModal"
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
    name: "CategoriesPage",
    props: ["server"],
    data() {
        return {
            id: "",
            name: "",
            description: "",
            errors: {},
            categories: [],
        };
    },
    mounted() {
        this.getAll();
    },
    methods: {
        getAll() {
            this.server("categories").then((result) => {
                this.categories = Object.values(result);
            });
        },

        clearModal() {
            this.id = "";
            this.name = "";
            this.description = "";
            this.errors = {};
        },

        edit(category) {
            this.id = category.id;
            this.name = category.name;
            this.description = category.description;
            this.errors = {};
        },

        confirmDelete(category) {
            this.id = category.id;
            this.name = category.name;
        },

       save() {
    let formdata = new FormData();
    formdata.append('name', this.name);
    formdata.append('description', this.description);

    const route = this.id ? 'category/' + this.id : 'category';
    const method = this.id ? 'POST' : 'POST';
    this.server(route, 'POST', formdata).then((result) => {
        if (result.errors) {
            this.errors = result.errors;
        } else {
            document.querySelector('#closeCategoryModal').click();
            this.clearModal();
            this.getAll();
        }
    });
},

        remove() {
            this.server("category/" + this.id, "DELETE").then(() => {
                this.getAll();
            });
        },
    },
};
</script>
