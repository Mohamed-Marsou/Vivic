<script setup>
import { RouterLink, useRouter } from 'vue-router';
import { ref, onMounted } from 'vue'
import Cookies from 'js-cookie'
const router = useRouter()
import { useProductStore } from '../stores/product';
import { useAuthtStore } from '../stores/auth'
import userForm from '../components/build/profile/form.vue'
import history from '../components/build/profile/order-history.vue'
const productStore = useProductStore()
const authStore = useAuthtStore()

const userName = ref('')

const view = ref('edit')


onMounted(async () => {
    if (Cookies.get('auth-user')) {
        userName.value = JSON.parse(Cookies.get('auth-user')).name
    }
})


////Function to log out the user
const logOut = () => {
    authStore.logOut()
    productStore.whishListCount = 0
    router.push('/user/auth');
};
const changeView = (v) => {
    view.value = v
}
</script>

<template>
    <div class="user-profile">
        <aside>
            <h2>User Profile</h2>

            <div class="slot" @click="changeView('edit')" :class="{ active: view === 'edit' }">
                <a>
                    <i class="fa-solid fa-file-pen"></i>
                    Edit your details
                </a>
            </div>
            <div class="slot" @click="changeView('history')" :class="{ active: view === 'history' }">
                <a>
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Orders history
                </a>
            </div>
            <div class="slot">
                <RouterLink :to="{ name: 'trackOrders' }">
                    <i class="fa-solid fa-magnifying-glass-location"></i>
                    Track Orders
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
                    Welcome to your profile! Personalize your experience by managing preferences, customizing content, and
                    curating your wishlist. Enjoy!
                </p>
            </div>

            <div class="portal">

                <userForm v-if="view === 'edit'" />
                <history v-if="view === 'history'" />


            </div>
        </div>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.user-profile {
    width: 100%;
    font-family: $ff;
    height: fit-content;
    display: flex;
    padding: 1rem 0;

    aside {
        width: 15vw;
        position: relative;
        border-right: 1px solid #5555550f;
        color: #555;
        height: calc(100vh - 7rem);

        a {
            color: #555;
            cursor: pointer;
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
            position: relative;

            i {
                margin-right: 5px;
            }

            &::after {
                position: absolute;
                content: '';
                bottom: 0;
                left: 0;
                background: #2e6bc6;
                width: 0;
                height: 2px;
                transition: .5s ease-in-out;
            }

            &:not(.active):hover::after {
                width: 100%;
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
        width: 60vw;
        min-height: 80vh;
        padding: 0 3vw;

        h1 {
            padding: 1rem;
        }

        .greeting {
            padding: 0 5vw 0 1vw;
            color: #555;
            font-size: .9rem;
        }

        .portal {
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

.active {
    border-right: 3px solid #0468ffeb !important;
}

@media screen and (max-width : 1315px) {
    aside {
        width: 20vw !important;

        .slot {
            padding: 1rem !important;
        }

        border-bottom: 1px solid rgba(68, 68, 68, 0.097);
        padding-bottom: 3rem;
    }

    .main-user-form {
        width: 75vw !important;
    }
}

@media screen and (max-width : 1024px) {
    .user-profile {
        flex-direction: column;

        aside {
            width: 100% !important;
            height: fit-content !important;

            .logOut {
                position: relative !important;
            }

            .slot {

                &::after {
                    height: 0px !important;
                }

                &:not(.active):hover::after {
                    width: 0 !important;
                }
            }
        }

        .main-user-form {
            width: 100% !important;
            padding: 2rem 1rem;
        }
    }

}</style>