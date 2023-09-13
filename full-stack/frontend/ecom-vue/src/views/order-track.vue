<script setup>
import { ref, onMounted } from 'vue'
import api from '../http/api';
import axios from 'axios';
onMounted(() => {
    scrollToTop()
})
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
const orderId = ref('')
const email = ref('')
const submited = ref(false)

const status = ref('')
const costumerNote = ref('')
const placedDate = ref('')
async function trackOrder ()
{
    try {
        submited.value = true
        const res = await axios.get(
            `${import.meta.env.VITE_WOO_URL}/orders/${orderId.value}`,
            {
                headers: {
                    Authorization: basicAuth(import.meta.env.VITE_WOO_CK, import.meta.env.VITE_WOO_CS)
                }
            }
        )

        if(res.data.billing.email != email.value)
        {
            alert('unmatched billing Email Please check your Email')
            submited.value = false
            return
        }
        
        const noteResponse = await axios.get(
            `${import.meta.env.VITE_WOO_URL}/orders/${orderId.value}/notes`,
            {
                headers: {
                    Authorization: basicAuth(import.meta.env.VITE_WOO_CK, import.meta.env.VITE_WOO_CS)
                }
            }
        )
        const note= noteResponse.data.filter((note) => note.customer_note === true);
        costumerNote.value  = note[0].note
        status.value = res.data.status
        placedDate.value = res.data.date_created

    } catch (error) {
        console.log(error);
        submited.value = false
    }
}

function basicAuth(key, secret) {
    let hash = btoa(key + ':' + secret);
    return "Basic " + hash;
}
function getFormattedDate() {
  const currentDate = new Date();

  const options = {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  };

  const formattedDate = currentDate.toLocaleString('en-US', options);
  return formattedDate;
}

function copyOrderID() {
    const orderIdElement = document.getElementById('orderId');
    const orderIdText = orderIdElement.innerText;
    const tempTextarea = document.createElement('textarea');
    tempTextarea.value = orderIdText.split(':')[1];
    document.body.appendChild(tempTextarea);
    tempTextarea.select();
    document.execCommand('copy');

    document.body.removeChild(tempTextarea);
    alert('Order Tracking was copied : ' +  orderIdText.split(':')[1]);
}
</script>
<template>
    <div class="order-track">
        <div class="banner">
            <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/accessories-page-title.jpg"
                alt="banner">
            <h1>Track Orders</h1>
        </div>
        <div class="wrapper">
            <h2>Order Tracking</h2>
            <p>To track your order please enter your Order ID in the box below and press the "Track" button. This was given
                to you on your receipt and in the confirmation email you should have received.</p>
                
            <form  @submit.prevent="trackOrder" v-if="!status">
                <div class="inp">
                    <label for="id">Order ID :</label>
                    <input type="text" id="id" placeholder="Order ID" v-model="orderId" required :disabled="submited">
                </div>
                <div class="inp">
                    <label for="billing-email">billing Email :</label>
                    <input type="text" id="billing-email" placeholder="billing Email" required v-model="email" :disabled="submited">
                </div>
                <button type="submit" :disabled="submited">Truck</button>
            </form>

            <div class="order-data" v-else>
                <p>Order <span>#{{orderId}}</span> was placed {{ placedDate }} and current status is : <span>{{status}}</span></p>
                <h2>Order Updates</h2>
                <p>{{ getFormattedDate()}}</p>
                <h3 id="orderId" title="click to copy" v-if="costumerNote" @click="copyOrderID">{{costumerNote}}</h3>
            </div>

        </div>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.order-track {
    width: 100%;
    height: 90vh;
    font-family: $ff;

    .banner {
        width: 100%;
        height: 30vh;
        position: relative;

        >h1 {
            color: #fff;
            position: absolute;
            top: 50%;
            right: 50%;
            transform: translate(50%, -50%);
            text-transform: uppercase;
            font-size: 3rem;
        }

        >img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    }

    .wrapper {
        width: 70%;
        height: 60vh;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;

       >p {
            color: #555;
            font-size: .9rem;
            padding: 1em 10vw;
        }

        >h2 {
            text-transform: uppercase;
            font-size: 2rem;
            padding: 1rem 1rem;
        }

        >form {
            @include flex($ai: flex-end);
            gap: 1.5vw;
            margin-top: 2rem;

            >div {
                width: 40%;
                display: flex;
                flex-direction: column;
                align-items: flex-start;

                >label {
                    color: #555;
                    font-size: .9rem;
                }

                >input {
                    width: 100%;
                    height: 3rem;
                    margin-top: 5px;
                    padding: 0 1rem;
                    border: 1px solid rgba(51, 51, 51, 0.187);
                    border-radius: 10px;
                    outline: none;
                }
            }

            button {
                background-color: #448bf4;
                width: 8rem;
                height: 3rem;
                border-radius: 20px;
                color: #fff;
                border: none;
                text-transform: uppercase;
                cursor: pointer;
            }
        }
    }
    .order-data{
        width: 80%;
        min-height: 20vh;
        margin: 1rem auto;
        text-align: left;
        font-size: .9rem;
        >p{
            padding: 1rem 5px;
            >span
            {
                background: #448af4c7;
                color: white;
                padding: 0 5px;
            }
        }
        h3{
            width: fit-content;
            padding: 0 5px;
            font-weight: normal;
            font-size: 1rem;
            border-bottom: 1px solid rgba(75, 75, 75, 0.102);
            cursor: pointer;
        }
    }
}

@media screen and (max-width: 1024px) {
    .order-track {
        .wrapper {
            width: 90%;
        }
    }
}
@media screen and (max-width:768px) {
    .order-track {
        height: 100vh!important;
        margin-bottom: 2rem !important;

        .banner {
            >h1 {
                font-size: 6vw;
            }
        }
        .wrapper {
            width: 95%;
            height: fit-content !important;
            >div {
                flex-direction: column;
                gap: 2rem;
                align-items: center;
                >div {
                    width: 95%;
                    display: flex;
                    flex-direction: column;
                    align-items: flex-start;

        
                }

                button {
                align-self: end;
                margin-right: 2vw;
                }
            }
        }
}
}
button:disabled{
        background: #555;
        opacity: 0.5;
        cursor: not-allowed !important;
    }
    input:disabled{
        cursor: not-allowed;
        opacity: 0.5;
    }
</style>