<template>
    <div class="app">
        <transition name="fade" mode="out-in">
            <router-view :key="$route.fullPath"></router-view>
        </transition>
    </div>
</template>

<script>
export default {
    name: 'App'
}
</script>

<style>
    .app {
        display: flex;
        justify-content: center;
    }
</style>
