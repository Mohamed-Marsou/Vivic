<template>
  <div class="dashboard" v-if="isAuth">
    <RouterLink :to="{ name: 'dashboard' }">
      <i class="fa-solid fa-house-chimney"></i>
      <p> Dashboard</p>
    </RouterLink>

    <div class="admin_profile" @click="showQuickOptions">
      <p>Welcome , {{ adminName }}</p>
      <i class="fa-solid fa-user"></i>

      <div class="quickActions" @click="showQuickOptions" :class="{ showQuickActions: displayOptions }">
        <div class="act" @click="showQuickOptions">
          <RouterLink :to="{ name: 'dashboard' }">
            <i class="fa-solid fa-gauge-high"></i>
            <p>
              visit Dashboard
            </p>
          </RouterLink>
        </div>
        <div class="act" @click="loagOut">
          <i class="fa-solid fa-user-slash"></i>
          <p>
            log out
          </p>
        </div>
      </div>
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

// 
watch(() => adminStore.isAdmin, newVal => {
  isAuth.value = newVal
  if (isAuth.value) {
    adminName.value = JSON.parse(jsCookie.get('auth-admin')).name
  }
})

onMounted(async () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
  isAuth.value =adminStore.isAdmin
  if (isAuth.value) {
    adminName.value = JSON.parse(jsCookie.get('auth-admin')).name
  }
})

const displayOptions = ref(false)

function showQuickOptions() {
  displayOptions.value = !displayOptions.value
}

function loagOut() {
  adminStore.adminLogOut()
}



</script>



<style lang="scss">
@import '@/style/_global.scss';

.dashboard {
  width: 100%;
  height: 2rem;
  background: #2e6bc6;
  color: #fff;
  font-family: $ff;
  @include flex($jc: space-between);
  padding: 0 1vw;

  a {
    color: #fff;
    font-size: .9rem;
    gap: 5px;
    @include flex();

  }

  >.admin_profile {
    height: 100%;
    display: flex;
    @include flex();
    gap: 10px;
    font-size: .8rem;
    cursor: pointer;
    position: relative;

    .quickActions {
      width: 10rem;
      height: fit-content;
      background: #3d84ed;
      color: #fff;
      position: absolute;
      top: 2rem;
      right: -10px;
      z-index: 2;
      box-shadow: 1px 1px 4px 0px #0000004d;
      display: none;

      .act {
        width: 100%;
        height: 3rem;
        @include flex($jc: flex-start);
        transition: .3s ease-out;
        padding: 0 10px;
        gap: 10px;
        border-bottom: 1px solid white;
        text-transform: uppercase;

        a {
          font-size: .8rem !important;
        }

        &:last-child {
          border-bottom: none;

          &:hover {
            color: rgb(250, 63, 63);
          }
        }
      }
    }
  }
}

.show {
  display: flex !important;
}

.showQuickActions {
  display: block !important;
  ;
}
@media screen and (max-width : 350px) {
  .dashboard {
    >a{
      margin-left: 5px;
      p{
        display: none;
      }
    }
    >div{
      margin-right: 5px;
      >p{
        display: none;
      }
      i{
        font-size: large;
      }
    }
  }
}
</style>