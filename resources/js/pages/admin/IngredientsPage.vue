<template>
    <div>
        <div class="zakaz">
            <button @click="open()">Добавить ингредиент</button>
        </div>

        <div v-if="showAdd" class="overlay">
            <div class="popup">
                <div class="popuphead">
                    <span>{{ id ? 'Изменить' : 'Добавить' }} ингредиент</span>
                    <button @click="showAdd = false">×</button>
                </div>
                <div class="popupbody">
                    <label>Название</label>
                    <input type="text" v-model="name" :class="{ error: errors.name }" />
                    <div v-if="errors.name" class="errortext">{{ errors.name.join('. ') }}</div>
                    <label>Единица измерения</label>
                    <select v-model="unit" :class="{ error: errors.unit }">
                        <option value="">-- Выберите --</option>
                        <option v-for="u in ['кг','г','мл','л','шт','ст.л','ч.л']" :key="u" :value="u">{{ u }}</option>
                    </select>
                    <div v-if="errors.unit" class="errortext">{{ errors.unit[0] }}</div>
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
                <div class="popupbody">Удалить ингредиент "{{ name }}"?</div>
                <div class="popupfoot">
                    <button class="gray" @click="showDelete = false">Отмена</button>
                    <button class="red" @click="remove">Удалить</button>
                </div>
            </div>
        </div>

        <div class="zakaz">
            <table>
                <thead>
                    <tr><th>ID</th><th>Название</th><th>Единица</th><th>Действия</th></tr>
                </thead>
                <tbody>
                    <tr v-for="ing in ingredients" :key="ing.id">
                        <td>{{ ing.id }}</td>
                        <td>{{ ing.name }}</td>
                        <td>{{ ing.unit }}</td>
                        <td>
                            <button class="gray" @click="open(ing)">Изменить</button>
                            <button class="red" @click="id = ing.id; name = ing.name; showDelete = true">Удалить</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'IngredientsPage',
    props: ['server'],
    data() {
        return { id: '', name: '', unit: '', errors: {}, ingredients: [], showAdd: false, showDelete: false };
    },
    mounted() {
        this.load();
    },
    methods: {
        load() {
            this.server('ingredient').then(r => { this.ingredients = Object.values(r); });
        },
        open(ing = null) {
            this.id = ing ? ing.id : '';
            this.name = ing ? ing.name : '';
            this.unit = ing ? ing.unit : '';
            this.errors = {};
            this.showAdd = true;
        },
        save() {
            let fd = new FormData();
            fd.append('name', this.name);
            fd.append('unit', this.unit);
            this.server(this.id ? 'ingredient/' + this.id : 'ingredient', 'POST', fd).then(r => {
                if (r.errors) { this.errors = r.errors; }
                else { this.showAdd = false; this.load(); }
            });
        },
        remove() {
            this.server('ingredient/' + this.id, 'DELETE').then(() => { this.showDelete = false; this.load(); });
        },
    },
};
</script>