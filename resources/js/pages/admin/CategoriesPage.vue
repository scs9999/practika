<template>
    <div>
        <div class="zakaz">
            <button @click="open()">Добавить категорию</button>
        </div>

        <div v-if="showAdd" class="overlay">
            <div class="popup">
                <div class="popuphead">
                    <span>{{ id ? 'Изменить' : 'Добавить' }} категорию</span>
                    <button @click="showAdd = false">×</button>
                </div>
                <div class="popupbody">
                    <label>Название</label>
                    <input type="text" v-model="name" :class="{ error: errors.name }" />
                    <div v-if="errors.name" class="errortext">{{ errors.name.join('. ') }}</div>
                    <label>Описание</label>
                    <input type="text" v-model="description" :class="{ error: errors.description }" />
                    <div v-if="errors.description" class="errortext">{{ errors.description.join('. ') }}</div>
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
                <div class="popupbody">Удалить категорию "{{ name }}"?</div>
                <div class="popupfoot">
                    <button class="gray" @click="showDelete = false">Отмена</button>
                    <button class="red" @click="remove">Удалить</button>
                </div>
            </div>
        </div>

        <div class="zakaz">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Название</th><th>Описание</th><th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in categories" :key="c.id">
                        <td>{{ c.id }}</td>
                        <td>{{ c.name }}</td>
                        <td>{{ c.description }}</td>
                        <td>
                            <button class="gray" @click="open(c)">Изменить</button>
                            <button class="red" @click="id = c.id; name = c.name; showDelete = true">Удалить</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CategoriesPage',
    props: ['server'],
    data() {
        return { id: '', name: '', description: '', errors: {}, categories: [], showAdd: false, showDelete: false };
    },
    mounted() {
        this.server('categories').then(r => { this.categories = Object.values(r); });
    },
    methods: {
        open(c = null) {
            this.id = c ? c.id : '';
            this.name = c ? c.name : '';
            this.description = c ? c.description : '';
            this.errors = {};
            this.showAdd = true;
        },
        save() {
            let fd = new FormData();
            fd.append('name', this.name);
            fd.append('description', this.description);
            this.server(this.id ? 'category/' + this.id : 'category', 'POST', fd).then(r => {
                if (r.errors) { this.errors = r.errors; }
                else { this.showAdd = false; this.server('categories').then(r => { this.categories = Object.values(r); }); }
            });
        },
        remove() {
            this.server('category/' + this.id, 'DELETE').then(() => {
                this.showDelete = false;
                this.server('categories').then(r => { this.categories = Object.values(r); });
            });
        },
    },
};
</script>