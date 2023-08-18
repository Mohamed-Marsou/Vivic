<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useRouter } from 'vue-router';

import Cookies from 'js-cookie';

import api from '../../http/api';

const email = ref('')
const password = ref('')
const emailErr = ref('')
const passwordErr = ref('')

const errMsg = ref('Something went wrong !')
const showErr = ref(false)
const router = useRouter();
const rememberMe = ref(false)
const login = async () => {
    
    if (!email.value || !isValidEmail(email.value)) {
        emailErr.value = 'Invalid Email';
        return;
    }
    if (!password.value || password.value.length < 6) {
        passwordErr.value = 'Invalid password';
        return;
    }
    passwordErr.value = emailErr.value = '';

    const payload = {
        email: email.value,
        password: password.value
    };

    try {
        // CALL API
    } catch (error) {
        console.log(error);
    }
}
const isValidEmail = (value) => {
    // Basic email format validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(value);
};
</script>
<template>
    <div class="container">

        <div class="login-box">
            <h2>Welecome back </h2>
            <div class="form">
                <p id="errMsg" v-if="showErr">
                    {{ errMsg }}
                </p>
                <label for="email">Email</label>
                <small :class="{ showErrmsg: emailErr }">{{ emailErr }}</small>
                <input :class="{ inputErr: emailErr }" type="email" placeholder="Email address" v-model="email">
                <label for="password">Password</label>
                <small :class="{ showErrmsg: passwordErr }">{{ passwordErr }}</small>
                <input :class="{ inputErr: passwordErr }" type="password" placeholder="Password" v-model="password">
                <div>
                    <input type="checkbox" v-model="rememberMe">
                    <p>remember me</p>
                </div>
                <button @click="login">
                    Login
                </button>
                <RouterLink to="/">
                    Back to home page
                </RouterLink>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
@import '@/style/_global.scss';

.container {
    width: 100vw;
    min-height: 100vh;
    font-family: $ff;
    background: #E5E7EB;
    @include flex();

    .login-box {
        width: 30vw;
        height: 30rem;

        >h2 {
            text-align: center;
            padding: 2rem 1vw;
        }

        .form {
            width: 100%;
            min-height: 25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            box-shadow: 0px 4px 9px 3px #00000026;

            #errMsg {
                color: red;
                font-size: .9rem;
                text-align: center;
                padding: 10px;
            }

            >input {
                width: 85%;
                height: 3rem;
                border: none;
                border-radius: 5px;
                padding: 0 .5rem;
                background: #E5E7EB;
                margin: 5px 5% 2rem 5%;
            }

            >div {
                width: 85%;
                height: 2rem;
                @include flex();
                gap: 1.5vw;
                margin: 0 5%;

                input[type="checkbox"] {
                    width: 15px;
                    height: 15px;
                    border-radius: 5px;
                }

                >p {
                    font-size: .8rem;
                }
            }

            label {
                margin: 5px 5%;
                color: #555;
            }

            small {
                margin: 0 5%;
                font-size: .8rem;
                color: red;
                display: none;
            }

            button {
                width: 85%;
                height: 3rem;
                border: none;
                border-radius: 5px;
                margin: 5px 5%;
                color: white;
                transition: 0.3s ease-in;
                text-transform: uppercase;
                font-size: 1rem;
                cursor: pointer;
                background: #4d85ff;

                &:hover {
                    background: #2563EB;

                }
            }

            >a {
                color: #2563EB;
                font-size: .9rem;
                text-align: center;
                padding: 1rem 0;
            }
        }
    }
}

.showErrmsg {
    display: block !important;
}

.inputErr {
    border: 1px solid red !important;
    outline: 1px solid red !important;
}

@media screen and (max-width : 1024px) {
    .container {
        .login-box {
            height: fit-content;

            >h2 {
                padding: 0rem 1vw;
            }

            width: 45vw;
            min-height:30rem;
        }
    }
}

@media screen and (max-width : 768px) {
    .container {
        .login-box {
            >h2 {
                padding: 0rem 1vw;
            }

            width: 50vw;
            min-height:30rem;
        }
    }
}

@media screen and (max-width : 550px) {
    .container {
        .login-box {
            >h2 {
                padding: 0rem 1vw;
            }

            width: 80vw;
            min-height:30rem;
        }
    }
}</style>