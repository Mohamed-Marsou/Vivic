<script setup>
import { ref, onMounted } from 'vue';
import "leaflet/dist/leaflet.css"
import { LMap, LTileLayer, LMarker } from "@vue-leaflet/vue-leaflet"
import { useRouter } from 'vue-router';

import api from '../http/api';
const router = useRouter();
const countdown = ref(4);
const isNotValidName = ref(false)
const isNotValidEmail = ref(false)
const isNotValidMessage = ref(false)

const formSent = ref(false)
let zoom = ref(10)
const minZoom = ref(4);
let center = ref([34.0522, -118.2437]) // Los Angeles coordinates
let markerPosition = ref([34.0542, -118.2437]) // Marker Pointer 

const name = ref('')
const email = ref('')
const message = ref('')

const submitForm = async () => {
    // Perform validation
    isValidName(name.value) ? isNotValidName.value = false : isNotValidName.value = true
    isValidEmail(email.value) ? isNotValidEmail.value = false : isNotValidEmail.value = true
    isValidMessage(message.value) ? isNotValidMessage.value = false : isNotValidMessage.value = true
    if (isNotValidName.value || isNotValidEmail.value || isNotValidMessage.value) {
        return;
    }
    const formData = {
        name: name.value,
        email: email.value,
        message: message.value
    };


    //* API CALL 
    await api.post('/contact-us/submit', formData)
        .then(res => {
            document.querySelector('.vue-container').style.overflowY = 'hidden'
            document.querySelector('#navigation_bar').scrollIntoView();
            formSent.value = true

            name.value = ""
            email.value = ""
            message.value = ""
        }).catch(err => {
            router.push('/error');
            console.log(err)
        })
};

// Validation functions
const isValidName = (value) => value.trim() !== '';
const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
const isValidMessage = (value) => value.trim() !== '';


const mapOptions = {
      scrollWheelZoom: false, // Disable zoom with mouse scroll
      zoomControl: true, // Show zoom control buttons
    };


</script>
<template>
    <div class="contatc-us">
        <div class="mapContainer">
            <l-map ref="map" v-model:zoom="zoom" v-model:center="center" :options="mapOptions"
            :useGlobalLeaflet="false" :min-zoom="minZoom">
                <l-tile-layer url="https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png" layer-type="base"
                    name="Stadia Maps Basemap"></l-tile-layer>
                <l-marker :lat-lng="markerPosition"></l-marker>
            </l-map>
        </div>

        <div class="contact-form-container">

<div class="form">
    <header>
        <p>Leave us a message</p>
    </header>
    <div>
        <label for="name">Name <span>*</span></label>
        <small :class="{ showErrMessage: isNotValidName }">Please Enter your name </small>
        <input type="text" id="name" placeholder="Name" v-model="name">
    </div>
    <div>
        <label for="email">Email <span>*</span></label>
        <small :class="{ showErrMessage: isNotValidEmail }">Please Enter your Email </small>
        <input v-model="email" type="email" id="email" placeholder="Email Address">
    </div>
    <div>
        <label for="message">Message <span>*</span></label>
        <small :class="{ showErrMessage: isNotValidMessage }">Please Enter your message </small>
        <textarea v-model="message" name="message" id="message" placeholder="Your message"></textarea>
    </div>
    <div>
        <button @click="submitForm">SEND</button>
    </div>
</div>
<div class="store-details">
    <header>
        <p>Our Store</p>
    </header>

    <div>
        <p>
            Lorem ipsum dolor sit.
        </p>
        <p>
            Lorem ipsum dolor sit Lorem ipsum dolor sit.
        </p>
        <p>
            LA, USA
        </p>
        <h4>Hours of Operation</h4>
        <p>Got Questions ? Call us 24/7!</p>
        <p>+754 455 221</p>
        <h4>
            Careers
        </h4>
        <p>
            If you’re interested in employment opportunities, please email us.</p>
    </div>
</div>
</div>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.contatc-us {
    width: 100%;
    min-height: 100%;
    font-family: $ff;
    .mapContainer {
        width: 100%;
        height: 70vh;
        margin: 0 auto;
        .leaflet-container {
            z-index: 1;
        }
    }
    .contact-form-container {
        width: 100%;
        min-height: 70vh;
        margin: 2rem auto;
        padding: 0 1vw;
        display: flex;
        position: relative;



        .form {
            width: 70%;
            min-height: 60vh;
            
            >div {
                width: 100%;
                min-height: 5rem;
                display: flex;
                flex-direction: column;
                padding: 1rem 2vw;

                small {
                    margin-top: 10px;
                    color: red;
                    display: none;
                }

                label {
                    font-size: .9rem;
                    color: #555;

                    span {
                        color: red;
                        font-weight: bold;
                    }
                }

                >input,
                textarea {
                    width: 80%;
                    height: 3rem;
                    margin-top: .5rem;
                    border: 1px solid rgba(85, 102, 102, 0.435);
                    border-radius: 8px;
                    padding: 0 1vw;
                    outline: none;
                }

                textarea {
                    height: 15rem;
                    padding-top: 1rem;
                }

                button {
                    width: 10rem;
                    height: 3rem;
                    border-radius: 20px;
                    border: none;
                    background: #2e6bc6;
                    color: white;
                    transition: .3s ease-in;
                    cursor: pointer;

                    &:hover {
                        background: #2f80f9;
                    }
                }
            }
        }

        .store-details {
            width: 30%;
            min-height: 60vh;

            >div {
                width: 100%;
                height: fit-content;
                padding: 1rem 1vw;

                >p,
                h4 {
                    margin-top: 1rem;
                }

                p {
                    color: #555;
                    font-size: .9rem;
                }
            }
        }
    }
}

header {
    width: 90%;
    height: 4rem;
    border-bottom: 1px solid rgba(85, 102, 102, 0.453);
    display: flex;
    align-items: flex-end;
    padding: 0 2vw;
    font-size: 1.5rem;
    margin-bottom: 2rem;
    font-weight: bold;

    >p {
        position: relative;
        padding-bottom: 3px;

        &::after {
            position: absolute;
            bottom: -3px;
            right: -5%;
            content: '';
            background: #2e6bc6;
            width: 110%;
            height: 5px;
            border-radius: 20px;
        }
    }
}


@media screen and (max-width:900px) {
    .contact-form-container {
        flex-direction: column-reverse;

        .form {
            padding: 0 2vw;
            width: 95% !important;

            >div {

                >input,
                textarea {
                    width: 90% !important;
                }

                button {
                    width: 10rem;
                    height: 3rem;
                    border: none;
                    background: #013d29;
                    color: white;
                    border-radius: 25px;
                    transition: .3s ease-in;
                    cursor: pointer;

                    &:hover {
                        background: #04bf81;

                    }
                }
            }
        }

        .store-details {
            width: 95% !important;
            min-height: 60vh;
            padding: 0 2vw;
        }
    }
}

@media screen and (max-width:450px) {
    .contact-form-container {
        .form {
            width: 100% !important;

            >div {

                >input,
                textarea {
                    width: 95% !important;
                }

            }
        }

        .store-details {
            width: 100% !important;
        }
    }
}
</style>