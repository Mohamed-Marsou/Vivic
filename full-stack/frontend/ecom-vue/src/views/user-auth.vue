<script setup>
import { ref } from 'vue'
import api from '../http/api';
import Cookies from 'js-cookie';
import { useRouter } from 'vue-router';
import { useAuthtStore } from '../stores/auth'
import { useProductStore } from '../stores/product'

const router = useRouter();
const authPinia = useAuthtStore();
const productPinia = useProductStore();

const isLoginVisible = ref(false)
const heading = ref('Register')
const clicked = ref(false)
const errMsg = ref('')
const toggole = () => {
    isLoginVisible.value = !isLoginVisible.value
    heading.value = isLoginVisible.value ? 'Register' : 'Login'
}
const registerPayload = {
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    confirmPassword: ''
}
const register = async () => {
    // Destructure the values from the registerPayload
    const { firstName, lastName, email, password, confirmPassword } = registerPayload;
    // Create the payload object
    const payload = {
        name: ` ${firstName} ${lastName}`,
        email,
        password,
        password_confirmation: confirmPassword,
    };
    try {
        const res = await api.post('/user/register', payload);
        console.log(res);
        if (res.data.token) {
            if (localStorage.getItem('wishlist')) {
                localStorage.removeItem('wishlist');  
                productPinia.whishListCount = 0
            }
            // Store the token in a cookie
            Cookies.set('auth-token', res.data.token, { expires: 7 });
            const userData = JSON.stringify(res.data.user);
            Cookies.set('auth-user', userData, { expires: 7 });

            authPinia.checkAuth()
            if (localStorage.getItem('userData')) {
                localStorage.removeItem('userData');
            }

            // Redirect
            router.push('/')
        }
    } catch (error) {
        console.error('Error during registration:', error);
        // errMsg.value = error.response.data.message
    }
}
const loginEmail = ref('')
const loginPassword = ref('')
const remember = ref(false)

const login = async () => {
    const payload = {
        email : loginEmail.value,
        password : loginPassword.value
    }
    try {
        const res = await api.post('/user/login', payload)
        errMsg.value =''
        if (res.data.token) {
            if (localStorage.getItem('wishlist')) {
                localStorage.removeItem('wishlist');  
                productPinia.whishListCount = 0
            }
            // Store the token in a cookie
            Cookies.set('auth-token', res.data.token, { expires: remember.value ? 21 : 7 });
            const userData = JSON.stringify(res.data.user);
            Cookies.set('auth-user', userData, { expires: remember.value ? 21 : 7 });
            authPinia.checkAuth()
            if (localStorage.getItem('userData')) {
                localStorage.removeItem('userData');
            }
            // Redirect
            router.push('/')
        }
        console.log(res);
    } catch (error) {
        console.log(error);
        errMsg.value = error.response.data.message
    }
}    
</script>
<template>
    <div class="auth-container">
        <div class="auth-containe-banner">
            <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/accessories-page-title.jpg"
                alt="banner">
            <p>REGISTER</p>
        </div>
        <div class="x-form">
            <div class="login" :class="{ hide: !isLoginVisible }">
                <h1>Login</h1>
                <span v-if="errMsg" style="color: red; margin-left: 2vw;font-size: .9rem;">{{ errMsg }}</span>
                <div class="inp-slot">
                    <label for="Email">Email address </label>
                    <input type="email" id="Email" name="Email" placeholder="Email address" v-model="loginEmail" required>
                </div>
                <div class="inp-slot">
                    <label for="password">Password</label>
                    <input type="password" id="login-password" name="login-password" placeholder="Password"
                        v-model="loginPassword" required>
                </div>
                <div class="inp-slot opt">
                    <div>
                        <input type="checkbox" v-model="remember">
                        <p>Remember me</p>
                    </div>
                    <p>Forgot your Password ?</p>
                </div>

                <button :disabled="clicked" @click="login">Login</button>
            </div>
            <div class="register" :class="{ hide: isLoginVisible }">
                <h1>Register</h1>
                <span v-if="errMsg" style="color: red; margin-left: 2vw;font-size: .9rem;">{{ errMsg }}</span>
                <div class="inp-slot">
                    <label for="register-firstName">First Name <small>*</small> :</label>
                    <input type="text" id="register-firstName" name="register-firstName" placeholder="First name"
                        v-model="registerPayload.firstName" required>
                </div>
                <div class="inp-slot">
                    <label for="register-lastName">Last Name <small>*</small> :</label>
                    <input type="text" id="register-lastName" name="register-lastName" v-model="registerPayload.lastName"
                        placeholder="Last name" required>
                </div>
                <div class="inp-slot">
                    <label for="register-email">Email <small>*</small> :</label>
                    <input type="email" id="register-email" name="register-email" placeholder="Email address"
                        v-model="registerPayload.email" required>
                </div>
                <div class="inp-slot">
                    <label for="register-password">Password <small>*</small> :</label>
                    <input type="password" id="register-password" name="register-password" placeholder="Password"
                        v-model="registerPayload.password" required>
                </div>
                <div class="inp-slot">
                    <label for="register-confirmPassword">Confirm Password <small>*</small> :</label>
                    <input type="password" id="register-confirmPassword" name="register-confirmPassword"
                        placeholder="Confirm Password" v-model="registerPayload.confirmPassword" required>
                </div>
                <button :disabled="clicked" @click="register" type="submit">Register</button>
            </div>
        </div>

        <div class="altr">
            <h1>{{ heading }}</h1>
            <p>Registering for this site allows you to access your order status and history. Just fill in the fields below,
                and we'll get a new account set up for you in no time. We will only ask you for inx-formation necessary to
                make the purchase process faster and easier</p>
            <button @click="toggole">{{ heading }}</button>
        </div>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.auth-container {
    width: 100%;
    min-height: 91vh;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    font-family: $ff;

    .x-form,
    .altr {
        width: 40%;
        min-height: 91vh;
        padding: 2rem 0;
    }

    .x-form {
        display: flex;
        flex-direction: column;

        h1 {
            padding: 1rem 1vw;
            color: #555;
            font-weight: normal;
        }

        button {
            margin: 1rem 2vw;
            width: 90%;
            height: 3rem;
            background: #2e6bc6;
            opacity: .8;
            border: none;
            color: #fff;
            text-transform: uppercase;
            cursor: pointer;
            transition: .3s ease-in;

            &:hover {
                opacity: 1;
                background: #0263f4;
            }

            &:has([disabled]) {
                opacity: 0.5;
                cursor: not-allowed;
            }
        }

        >div>div {
            width: 100%;
            min-height: 4rem;
            display: flex;
            flex-direction: column;
            padding: 1rem 2vw;

            label {
                color: #555;
                font-size: .9rem;
                margin-bottom: 5px;

                small {
                    color: red;
                }
            }

            input {
                padding: 0 10px;
                height: 3rem;
                border: 1px solid #5555556f;
                border-radius: 5px;
            }

        }

        .opt {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            height: 1.5rem;

            >p {
                font-size: .8rem;
                color: #0263f4;
                text-decoration: underline;
                cursor: pointer;
            }

            >div {
                display: flex;
                align-items: center;
                gap: 15px;

                >p {
                    color: #555;
                    font-size: .8rem;
                }

                >input {

                    display: flex;
                }
            }
        }
    }

    .altr {
        @include flex();
        flex-direction: column;
        padding: 5rem 2vw;
        text-align: center;
        justify-content: flex-start;

        p {
            color: #555;
            font-size: .9rem;
            padding: 1rem 5px;
        }

        button {
            margin: 1rem 2vw;
            width: 10rem;
            height: 3rem;
            opacity: .8;
            border-radius: 10px;
            border: none;
            color: #000000;
            text-transform: uppercase;
            cursor: pointer;
            transition: .3s ease-in;

            &:hover {
                opacity: 1;
                border: 1px solid #555;
                font-weight: bold;
            }
        }
    }

    .auth-containe-banner {
        width: 100%;
        min-height: 30vh;
        position: relative;

        >img {
            width: 100%;
            height: 30vh;
            object-fit: cover;
        }

        p {
            position: absolute;
            top: 50%;
            right: 50%;
            transform: translate(50%, -50%);
            color: white;
            font-size: 3.5rem;
            text-transform: uppercase;
        }
    }

}

@media screen and (max-width : 1024px) {
    .auth-container {
        flex-direction: column-reverse;
        width: 100% !important;
        align-items: center;
    }

    .auth-containe-banner {
        display: none !important;
    }

    .x-form,
    .altr {
        width: 80% !important;
        min-height: fit-content !important;
        padding: 1rem 2vw !important;
    }

    .auth-container .x-form button {
        width: 95% !important;
    }
}

@media screen and (max-width : 450px) {
    .auth-container {
        flex-direction: column-reverse;
        width: 100% !important;
        align-items: center;
    }

    .x-form,
    .altr {
        width: 95% !important;
    }

    .opt {
        >div {
            gap: 1vw !important;
        }
    }
}

.show {
    display: felx !important;
}

.hide {
    display: none !important;
}</style>