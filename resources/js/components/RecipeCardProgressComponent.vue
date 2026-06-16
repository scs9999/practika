<template>
    <div class="col-lg-3 col-md-6 mb-4" v-for="i in 8" :key="i">
        <div class="card">
            <div class="card-img-top bg-secondary" style="height:200px"></div>
            <div class="card-body">
                <h5 class="card-title placeholder-glow">
                    <span class="placeholder col-6"></span>
                </h5>
                <p class="card-text placeholder-glow">
                    <span class="placeholder col-7"></span>
                    <span class="placeholder col-4"></span>
                    <span class="placeholder col-6"></span>
                </p>
                <a class="btn btn-danger disabled placeholder col-6"></a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RecipeCardProgressComponent',
};
</script>