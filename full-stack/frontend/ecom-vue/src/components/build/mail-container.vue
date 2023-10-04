<script setup>
import api from '../../http/api'
import { ref } from 'vue'

const email = ref('')
const showResponse = ref(false)
const loading = ref(false)
const response = ref('')

const emailValidationRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i
const submit = async () => {
    if (!email.value || !emailValidationRegex.test(email.value)) {
        response.value = 'Please Enter a valid Email address'
        showResponse.value = true
        setTimeout(() => {
            showResponse.value = false
        }, 3000)
        return
    }
    loading.value = true

    try {
        const res = await api.post('/subscribe/new', { email: email.value })

        if (res.status === 200 || res.status === 201) {

            email.value = ''
            response.value = 'Thank you for joining our family; we look forward to staying connected.'
            showResponse.value = true
            loading.value = false

            setTimeout(() => {
                showResponse.value = false
            }, 3000)
        } else {
            response.value = 'Please Enter a valid Email address'
            showResponse.value = true
            setTimeout(() => {
                showResponse.value = false
            }, 3000)
        }
    } catch (error) {
        console.log(error);
        loading.value = false
    }
}

</script>
<template>
    <div class="mail-container">
        <div class="input">
            <h2>
                Be the First to Know! Subscribe for updates, deals, and more straight to your inbox
            </h2>
            <div class="inptCont">
                <i class="fa-solid fa-paper-plane" @click="submit"></i>
                <input type="text" placeholder="Enter Email address" @keyup.enter="submit" v-model="email">
            </div>
            <p v-if="showResponse">{{ response }}</p>
        </div>

        <div class="loading" v-if="loading">
            <span class="loader"></span>
        </div>
    </div>
</template>

<style lang="scss">
.mail-container {
    width: 100%;
    height: 40vh;
    background: rgb(47, 112, 210);
    background: linear-gradient(121deg, rgba(47, 112, 210, 1) 0%, rgba(5, 105, 255, 0.4738270308123249) 100%);
    color: #fff;
    box-shadow: 1px 1px 8px 3px #00000058;
    position: relative;
    padding: 5px 1vw;

    .input {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 3rem;



        h2 {
            text-transform: uppercase;
            font-size: 1.2rem;
            text-align: center;
        }

        p {
            text-align: center;
            font-size: .9rem;
        }

        .inptCont {
            width: 60%;
            height: 4rem;
            border: 1px solid white;
            display: flex;
            border-radius: 5px;
            overflow: hidden;

            >i {
                width: 10%;
                height: 100%;
                background: white;
                color: rgb(47, 112, 210);
                display: flex;
                justify-content: center;
                align-items: center;
                border-right: 2px solid #5555557c;
                font-size: 1.4rem;
                transition: .3s ease-in;
                cursor: pointer;

                &:hover {
                    background: rgb(47, 112, 210);
                    color: white;
                }
            }

            >input {
                width: 90%;
                height: 100%;
                border: none;
                outline: none;
                padding: 0 1rem;
            }
        }
    }

    .loading {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: #5a585823;
        display: flex;
        justify-content: center;
        align-items: center;

        .loader {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 6rem;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }

        .loader:before,
        .loader:after {
            content: "";
            position: absolute;
            border-radius: 50%;
            animation: pulsOut 1.8s ease-in-out infinite;
            filter: drop-shadow(0 0 1rem rgba(255, 255, 255, 0.75));
        }

        .loader:before {
            width: 100%;
            padding-bottom: 100%;
            box-shadow: inset 0 0 0 1rem #97BAF0;
            animation-name: pulsIn;
        }

        .loader:after {
            width: calc(100% - 2rem);
            padding-bottom: calc(100% - 2rem);
            box-shadow: 0 0 0 0 #97BAF0;
        }

        @keyframes pulsIn {
            0% {
                box-shadow: inset 0 0 0 1rem #97BAF0;
                opacity: 1;
            }

            50%,
            100% {
                box-shadow: inset 0 0 0 0 #97BAF0;
                opacity: 0;
            }
        }

        @keyframes pulsOut {

            0%,
            50% {
                box-shadow: 0 0 0 0 #97BAF0;
                opacity: 0;
            }

            100% {
                box-shadow: 0 0 0 1rem #97BAF0;
                opacity: 1;
            }
        }


    }
}

@media screen and (max-width: 1024px) {
    .mail-container {
        .inptCont {
            width: 90%;
        }
    }
}

@media screen and (max-width: 768px) {
    .mail-container {
        gap: 2rem;

        >h2 {
            font-size: 1.1rem;
        }


        .inptCont {
            width: 95%;


            >i {
                width: 15% !important;
            }

            >input {
                width: 85% !important;
            }
        }
    }
}

@media screen and (max-width: 550px) {
    .mail-container {
        gap: .5rem;

        >h2 {
            font-size: 1.1rem;
        }

        .inptCont {
            width: 90% !important;

            >i {
                width: 13% !important;
            }

            >input {
                width: 87% !important;
            }
        }
    }
}

@media screen and (max-width: 450px) {
    .mail-container {
        height: 35vh;
        gap: 1rem;

        >h2 {
            font-size: 1rem;
            padding: 0 1vw;
        }

        .inptCont {
            width: 95% !important;
            height:3.5rem !important;

            >i {
                width: 17% !important;
            }

            >input {
                width: 83% !important;
            }
        }
    }
}
</style>