<script setup>
import { RouterLink , useRouter } from 'vue-router';
import { ref, onMounted } from 'vue'
import Cookies from 'js-cookie'
const router = useRouter()
import { useProductStore } from '../stores/product';
import  {useAuthtStore} from '../stores/auth'
import userForm from '../components/build/profile/form.vue'
const productStore = useProductStore()
const authStore = useAuthtStore()

const userName = ref('')

onMounted(async () => {
    if(Cookies.get('auth-user')){
        userName.value = JSON.parse(Cookies.get('auth-user')).name
    }
})

////Function to log out the user
const logOut = () => {
    authStore.logOut()
    productStore.whishListCount = 0
    router.push('/user/auth');
};
</script>
<template>
    <div class="user-profile">
        <aside>
            <h2>User Profile</h2>
            <div class="slot">
                <RouterLink :to="{ name: 'home' }">
                    <i class="fa-solid fa-basket-shopping"></i>
                    Back Shopping
                </RouterLink>
            </div>
            <div class="slot">
                <RouterLink :to="{ name: 'home' }">
                    <i class="fa-solid fa-file-pen"></i>
                    Edit your details
                </RouterLink>
            </div>
            <div class="slot">
                <RouterLink :to="{ name: 'home' }">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    History 
                </RouterLink>
            </div>
            <div class="slot">
                <RouterLink :to="{ name: 'home' }">
                    <i class="fa-solid fa-magnifying-glass-location"></i>
                    Track Order
                </RouterLink>
            </div>
            <div class="slot logOut">
                <span @click="logOut">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Logout
                </span>
            </div>
        </aside>

        <div class="main-user-form">
            <div class="slot">
                <h1>Welcome , {{ userName }}</h1>
                <p class="greeting">
                    Welcome to your profile! Personalize your experience by managing preferences, customizing content, and curating your wishlist. Enjoy!
                </p>
            </div>

            <div class="portal">
                <userForm />
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.user-profile {
    width: 100%;
    font-family: $ff;
    min-height: 80vh;
    display: flex;
    padding: 2rem 0;

    aside {
        width: 25%;
        min-height: 80vh;
        position: relative;
        border-right: 1px solid #5555551f;

        a {
            color: #555;
        }

        >h2 {
            padding: 2rem 1vw;
            text-transform: uppercase;
        }

        .slot {
            width: 100%;
            height: 4rem;
            border-bottom: 1px solid #5555551f;
            padding: 5px 1vw;
            display: flex;
            align-items: center;
            gap: 51px;
            font-size: 1rem;
            transition: .3s ease-in-out;

            i {
                margin-right: 5px;
            }

            &:hover {
                border-bottom: 1px solid #555;
            }
        }

        .logOut {
            position: absolute;
            bottom: 0;
            font-size: 1rem;
            cursor: pointer;
            border: none;
            &:hover {
                border: none;
                color: red;
            }
        }
    }

    .main-user-form {
        width: 60%;
        min-height: 84vh;
        h1{
            padding: 1rem;
        }
        .greeting{
            padding:0 5vw 0 1vw;
            color: #555;
            font-size: .9rem;
        }
        .portal{
            width: 100%;
            height: fit-content;
            margin-top: 1rem;
        .slot {
            width: 100%;
            min-height: 4.5rem;
            padding: 5px 1vw;
            display: flex;
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
            font-size: 1rem;
            margin-top: .5rem;
            transition: .3s ease-in-out;
        }
        }

    }
}
@media screen and (max-width : 1024px) {
    .user-profile {
    flex-direction: column;
    aside {
        width: 100%;
        min-height: fit-content !important;
        position: relative;
        >h2 {
            padding: 2rem 2vw;
        }
        .logOut {
           display: none !important;
        }
    }

    .main-user-form {
        width: 100%;
        min-height: 84vh;
      
    }
}
}
</style>