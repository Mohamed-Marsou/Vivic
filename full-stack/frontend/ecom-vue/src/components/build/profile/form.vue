<script setup>
import { RouterLink , useRouter } from 'vue-router';
import { ref, onMounted } from 'vue'
import countriesJSON  from '../../../assets/contries.json'
import api from '../../../http/api';
const router = useRouter()



const countries = ref(countriesJSON)
const userContry = ref('Select your country')


</script>
<template>
    <div class="form-c">
        <div class="slot">
                <label for="firstName">First Name <small>*</small>:</label>
                <input type="text" name="firstName" id="firstName" placeholder="First Name">
            </div>

            <div class="slot">
                <label for="lastName">Last Name <small>*</small>:</label>
                <input type="text" name="lastName" id="lastName" placeholder="Last Name">
            </div>

            <div class="slot">
                <label for="profile-email">Email <small>*</small>:</label>
                <input type="profile-email" name="profile-email" id="profile-email" placeholder="Email">
            </div>
            <div class="slot">
                <label for="country">Country:</label>
                <select name="country" id="country" v-model="userContry">
                    <option disabled>Select your country</option>
                    <option v-for="(c, i) in countries" :key="i">{{ c.name }}</option>
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
</template>

<style lang="scss">
    .form-c
    {
        width: 100%;
        height: fit-content;
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
            >p{
                color: #555;
                font-size: .9rem;
                padding: 1rem 0;
            }
            small {
                color: red;
            }
            >label{
                padding: 0 .5vw;
                font-size: .9rem;
                color: #555;
            }
            >input {
                width: 90%;
                height: 3rem;
                border-radius: 50px;
                border: 1px solid #55555567;
                padding: 0 20px;
            }

            >select {
                width: 10rem;
                height: 2.7rem;
                border-radius: 50px;
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
</style>