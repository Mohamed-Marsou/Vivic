<script setup>
import categoryCarousel from '../components/build/category-carousel.vue';
import Mail from "../components/build/mail-container.vue";

import { useRouter } from 'vue-router';

const router = useRouter();
const navBack = () => {
    router.back();
};
</script>

<template>
    <div class="collections">
        <div class="banner">
            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/accessories-page-title.jpg"
                alt="cover">
            <div>
                <i @click="navBack" class="fa-solid fa-arrow-left"></i>
                <h2>Collections</h2>
            </div>
        </div>
        <div class="heading">
            <p>There are some redeeming factors</p>
            <h2>Recommended Categories</h2>
        </div>
        <categoryCarousel />

        <div class="mega-bx">
            <div>
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/mag-safe-accessories-category.jpg.webp"
                    alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
            <div>
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/protective-cases-category.jpg.webp"
                    alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
            <div>
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/watches-straps-category.jpg.webp"
                    alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
            <div id="mb-cate">
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/new-arrivals-banner.jpg?id=226"
                    alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
        </div>

        <div class="newIn-bx">
            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/new-arrivals-banner.jpg?id=226"
                alt="cover">

            <div>
                <h2>Discover Our Latest Arrivals</h2>
                <p>Explore our newest collection of accessories that will enhance your devices and elevate your experience.
                </p>
                <button>Check now</button>
            </div>
        </div>

        <Mail />
    </div>
</template>
<style lang="scss">
@import '@/style/_global.scss';

.collections {
    width: 100%;
    min-height: 100vh;
    font-family: $ff;

    .banner {
        width: 100%;
        height: 35vh;
        position: relative;

        >img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        >div {
            width: fit-content;
            height: fit-content;
            @include flex();
            gap: 15px;
            position: absolute;
            top: 50%;
            right: 50%;
            transform: translate(50%, -50%);
            color: #fff;

            h2 {
                font-size: 3rem;
            }

            i {
                font-size: 1.5rem;
                color: #fff;
                transition: .3s ease-in;
                cursor: pointer;

                &:hover {
                    color: #848484;
                }
            }
        }
    }

    .heading {
        width: 100%;
        min-height: 5rem;
        text-align: center;

        p {
            padding: 2rem .5rem .5rem .5rem;
            color: #2E6BC6;
            font-size: .9rem;
        }

        h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

    }

    .mega-bx {
        width: 80%;
        min-height: 30vh;
        margin: 1rem auto;
        display: flex;
        justify-content: space-evenly;
        box-shadow: 0px 5px 13px 2px rgba(0, 0, 0, 0.168627451);

        // --- todo show 4th el in >768px
        >div {
            width: 33%;
            height: 55vh;
            position: relative;
            transition: .3s ease-in-out;
            overflow: hidden;

            >img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                position: relative;
                transition: .3s ease-in-out;

                &:hover {
                    transform: scale(1.08);
                }

            }

            >div {
                position: absolute;
                top: 10px;
                left: 1rem;

                p {
                    color: #2E6BC6;
                    font-size: .9rem;
                }

                h2 {
                    text-transform: uppercase;
                    font-size: 2rem;
                    padding: 1rem 0;
                }

                button {
                    color: #fff;
                    background: #2E6BC6;
                    width: 6rem;
                    height: 2rem;
                    border-radius: 10px;
                    border: none;
                    cursor: pointer;
                }
            }
        }

        // --- todo show me
        #mb-cate {
            display: none;
        }

    }

    .newIn-bx {
        width: 80%;
        height: 40vh;
        margin: 4rem auto;
        position: relative;
        box-shadow: 0px 5px 13px 2px rgba(0, 0, 0, 0.168627451);

        >img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        >div {
            position: absolute;
            top: 1rem;
            left: 1rem;
            width: 45%;
            padding: 1rem 2vw;
            h2 {
                font-size: 2rem;
                padding: 1rem 0;
            }

            p {
                color: #555;
                padding-bottom: 1rem;
                font-size: .9rem;
            }

            button {
                color: #fff;
                background: #2E6BC6;
                width: 8rem;
                height: 3rem;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                text-transform: uppercase;
            }

        }
    }
}</style>