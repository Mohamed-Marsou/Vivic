<script setup>
import { ref } from 'vue'
const isLoginVisible = ref(false)
const heading = ref('Register')

const toggole =() => {
    isLoginVisible.value = !isLoginVisible.value
    heading.value = isLoginVisible.value ?  'Register' : 'Login' 
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
            <div class="login" :class="{hide : !isLoginVisible}">
                <h1>Login</h1>
                <div class="inp-slot">
                    <label for="Email">Email address </label>
                    <input type="text" id="Email" name="Email" placeholder="Email address" required>
                </div>
                <div class="inp-slot">
                    <label for="password">Password</label>
                    <input type="login-password" id="login-password" name="login-password" placeholder="Password" required>
                </div>
                <div class="inp-slot opt">
                    <div>
                        <input type="checkbox">
                        <p>Remember me</p>
                    </div>
                    <p>Forgot your Password ?</p>
                </div>

                <button type="submit">Login</button>
            </div>
            <div class="register" :class="{hide : isLoginVisible}">
                <h1>Register</h1>
                <div class="inp-slot">
                    <label for="firstName">First Name <small>*</small> :</label>
                    <input type="text" id="firstName" name="firstName" placeholder="First name" required>
                </div>
                <div class="inp-slot">
                    <label for="lastName">Last Name <small>*</small> :</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Last name" required>
                </div>
                <div class="inp-slot">
                    <label for="email">Email <small>*</small> :</label>
                    <input type="email" id="email" name="email" placeholder="Email address" required>
                </div>
                <div class="inp-slot">
                    <label for="password">Password <small>*</small> :</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="inp-slot">
                    <label for="confirmPassword">Confirm Password <small>*</small> :</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password"
                        required>
                </div>
                <button type="submit">Register</button>
            </div>
        </div>

        <div class="altr">
            <h1>{{  heading }}</h1>
            <p>Registering for this site allows you to access your order status and history. Just fill in the fields below, and we'll get a new account set up for you in no time. We will only ask you for inx-formation necessary to make the purchase process faster and easier</p>
            <button @click="toggole">{{  heading }}</button>
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
        .opt{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            height: 1.5rem;
            >p{
                font-size: .8rem;
                color: #0263f4;
                text-decoration: underline;
                cursor: pointer;
            }
            >div{
                display: flex;
                align-items: center;
                gap: 15px;
                >p{
                    color: #555;
                    font-size: .8rem;
                }
                >input{
                    
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
    .auth-container{
        flex-direction: column-reverse;
        width: 100% !important;
        align-items: center;
    }
    .auth-containe-banner{
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
    .auth-container{
        flex-direction: column-reverse;
        width: 100% !important;
        align-items: center;
    }
    .x-form,
    .altr {
        width: 95% !important;
    }
    .opt{
        >div{
            gap: 1vw !important;
        }
    }
}

.show {
    display: felx !important;
}

.hide {
    display: none !important;
}
</style>