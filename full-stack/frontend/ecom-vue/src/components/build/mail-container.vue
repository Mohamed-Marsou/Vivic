<script setup>
import api from '../../http/api'
import { ref } from 'vue'

const email = ref('')
const showResponse = ref(false)
const response = ref('')

const submit = async () => {
    const res = await api.post('/subscribe/new', { email: email.value })
    if (res.status === 200 || res.status === 201) {
        email.value = ''
        response.value = 'Thank you for joining our family, we will be concted'
        showResponse.value = true
        setTimeout(() => {
            showResponse.value = false
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }, 3000)
    } else {
        response.value = 'Please Enter a valid Email address'
        showResponse.value = true
        setTimeout(() => {
            showResponse.value = false
        }, 3000)
    }
}

</script>
<template>
    <div class="mail-caontainer">
        <h2>
            Be the First to Know! Subscribe for updates, deals, and more straight to your inbox
        </h2>
        <p v-if="showResponse">{{ response }}.</p>
        <div class="inptCont">
            <i class="fa-solid fa-paper-plane" @click="submit"></i>
            <input type="text" placeholder="Enter Email address" @keyup.enter="submit" v-model="email">
        </div>
    </div>
</template>

<style lang="scss">
.mail-caontainer {
    width: 100%;
    height: 40vh;
    background: rgb(47, 112, 210);
    background: linear-gradient(121deg, rgba(47, 112, 210, 1) 0%, rgba(5, 105, 255, 0.4738270308123249) 100%);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    flex-direction: column;
    gap: 3rem;
    box-shadow: 1px 1px 8px 3px #00000058;
    position: relative;
    padding: 5px 1vw;

    >h2 {
        text-transform: uppercase;
        font-size: 1.2rem;
        text-align: center;
    }
    >p{
        text-align: center;
        font-size: .9rem;
    }

    .inptCont {
        width: 70%;
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

@media screen and (max-width: 1024px) {
    .mail-caontainer {
        .inptCont {
            width: 90%;
        }
    }
}

@media screen and (max-width: 768px) {
    .mail-caontainer {
        gap: 2rem;

        >h2 {
            font-size: 1.1rem;
        }


        .inptCont {
            width: 95%;


            >i {
                width: 10%;
            }

            >input {
                width: 90%;
            }
        }
    }
}

@media screen and (max-width: 550px) {
    .mail-caontainer {
        gap: .5rem;
        >h2 {
            font-size: 1.1rem;
        }
        .inptCont {
            width: 95%;

            >i {
                width: 13%;
            }
            >input {
                width: 87%;
            }
        }
    }
}
@media screen and (max-width: 550px) {
    .mail-caontainer {
        height: 35vh;
        gap: 1rem;
        >h2 {
            font-size: 1rem;
            padding: 0 1vw;
        }
        .inptCont {
            width: 98%;
            height: 3.2rem;

            >i {
                width: 15%;
            }
            >input {
                width: 85%;
            }
        }
    }
}
</style>