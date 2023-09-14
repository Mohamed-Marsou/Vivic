<template>
 <div class="dashboard" v-if="isAuth">
  <RouterLink :to="{name : 'dashboard'}">
    <i class="fa-solid fa-house-chimney"></i>
    <p> Dashboard</p>
  </RouterLink>

  <div class="admin_profile">
    <p>Welcome , {{ adminName }}</p>
    <i class="fa-solid fa-user"></i>
  </div>
</div>
</template>
<script setup>
import { RouterLink } from 'vue-router'
import { onMounted, ref, watch } from 'vue'
import { useAdminStore } from '../../../stores/admin'
import jsCookie from 'js-cookie';
       

const adminStore = useAdminStore()
const isAuth = ref(false)
const adminName = ref("")

// Watch for changes in the cookies and update the values accordingly
watch(() =>adminStore.isAdmin, newVal => {
  isAuth.value = newVal
})
  
onMounted(async () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    isAuth.value = adminStore.isAdmin
    adminName.value = JSON.parse(jsCookie.get('auth-admin')).name
})
</script>



<style lang="scss">
@import '@/style/_global.scss';
    
.dashboard{
  width: 100%;
  height: 2rem;
  background: #2e6bc6;
  color: #fff;
  font-family: $ff;
  @include flex($jc:space-between);
  padding: 0 1vw;
  a{
    color: #fff;
    font-size: .9rem;
    gap: 5px;
    @include flex();

  }
  >.admin_profile{
    height: 100%;
    display: flex;
    @include flex();
    gap: 10px;
    font-size: .8rem;
    cursor: pointer;
  }
}
.show
{
    display: flex !important;;
}
</style>