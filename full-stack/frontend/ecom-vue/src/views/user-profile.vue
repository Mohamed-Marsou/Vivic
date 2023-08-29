<script setup>
import { RouterLink , useRouter } from 'vue-router';
import { ref, onMounted } from 'vue'
import {useAuthtStore } from '../stores/auth'
import {useProductStore } from '../stores/product'
import api from '../http/api';
import Cookies from 'js-cookie'
const router = useRouter()
const authStore = useAuthtStore()
const productStore = useProductStore()


const countries = ref([])
const userName = ref('')
const userContry = ref('Select your country')

onMounted(async () => {
    await getContries()
    if(Cookies.get('auth-user')){
        userName.value = JSON.parse(Cookies.get('auth-user')).name
    }
})

async function getContries ()
{
       try {
        const res = await api.get('/countries')
        countries.value = res.data.countries
    } catch (error) {
        console.log(error);
    }
}
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
            </div>
            <div class="slot">
                <label for="firstName">First Name <small>*</small>:</label>
                <input type="text" name="firstName" id="firstName" placeholder="First Name">
            </div>

            <div class="slot">
                <label for="lastName">Last Name <small>*</small>:</label>
                <input type="text" name="lastName" id="lastName" placeholder="Last Name">
            </div>

            <div class="slot">
                <label for="email">Email <small>*</small>:</label>
                <input type="email" name="email" id="email" placeholder="Email">
            </div>

            <div class="slot">
                <label for="password">Password <small>*</small>:</label>
                <input type="password" name="password" id="password" placeholder="Password">
            </div>

            <div class="slot">
                <label for="confirmPassword">Confirm Password <small>*</small>:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirme Password">
            </div>

            <div class="slot">
                <label for="country">Country:</label>
                <select name="country" id="country" v-model="userContry">
                    <option disabled>Select your country</option>
                    <option v-for="(c, i) in countries" :key="i">{{ c }}</option>
                </select>
            </div>

            <div class="slot">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" placeholder="City">
            </div>

            <div class="slot">
                <label for="street">Street:</label>
                <input type="text" name="street" id="street" placeholder="Street">
            </div>
            <div class="slot">
                <button>Submit</button>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.user-profile {
    width: 100%;
    font-family: $ff;
    min-height: 93vh;
    display: flex;
    padding: 2rem 0;

    aside {
        width: 20vw;
        min-height: 93vh;
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
            height: 4.5rem;
            border-bottom: 1px solid #5555551f;
            padding: 5px 1vw;
            display: flex;
            align-items: center;
            gap: 51px;
            font-size: 1rem;
            margin-top: 1rem;
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
        width: 50vw;
        min-height: 93vh;
        padding: 0 5vw;

        .slot {
            width: 100%;
            min-height: 4.5rem;
            padding: 5px 1vw;
            display: flex;
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
            font-size: 1rem;
            margin-top: 1rem;
            transition: .3s ease-in-out;

            small {
                color: red;
            }

            >input {
                width: 90%;
                height: 3.5rem;
                border-radius: 50px;
                border: 1px solid #55555567;
                padding: 0 20px;
            }

            >select {
                width: 10rem;
                height: 3rem;
                border-radius: 15px;
                border: 1px solid #55555567;
                padding: 0 10px;
            }

            >button {
                width: 10rem;
                height: 3rem;
                background: #2e6bc6;
                color: #fff;
                border-radius: 25px;
                border: none;
                cursor: pointer;
            }
        }

    }
}</style>