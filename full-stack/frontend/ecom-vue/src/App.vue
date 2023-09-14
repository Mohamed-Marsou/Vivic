<script setup>
import { RouterLink, RouterView ,useRouter } from 'vue-router'
import Nav from "./components/build/nav.vue"
import Footer from "./components/build/footer.vue";
import AdminBanner from "./components/build/dashboard/admin-banner.vue";

const router = useRouter();

</script>

<template>
<main id="main" >

<AdminBanner v-if="router.currentRoute.value.name  !== 'admin-auth' && router.currentRoute.value.name  !== 'dashboard' "/>

<Nav v-if="router.currentRoute.value.name  !== 'admin-auth' && router.currentRoute.value.name  !== 'dashboard' "/>

<RouterView />

<Footer v-if="router.currentRoute.value.name  !== 'admin-auth' && router.currentRoute.value.name  !== 'dashboard'"/>

</main>
</template>

<style lang="scss">
@import '@/style/_global.scss';
#main{
  width: 100%;
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}
// * SCROLL BAR 
/* width */
::-webkit-scrollbar {
  width: 7px;
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: #2e6bc6;
  border-radius: 0 0 
}

</style>
