<script setup>
import CategoryCarousel from "../components/build/category-carousel.vue"
import Products from '../components/build/products.vue'
import ProductsCarousel from "../components/build/products-carousel.vue";
import Mail from "../components/build/mail-container.vue";
import { ref,onMounted } from "vue";

const isLoaded = ref(false)

onMounted(()=>{
    isLoaded.value = true
})
</script>
<template>
    <div class="home-main">
        <div class="hero">
            <img src="../assets/images/hero.jpg" alt="hero-image">
            <div class="ctx "  :class="{ 'animate-l-t-r' : isLoaded}">
                <h2>
                    Get the best Products With best Offres !
                </h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Obcaecati nihil molestiae, unde laborum magnam esse
                    Obcaecati nihil molestiae, unde laborum magnam esse
                </p>
                <button id="CTA">
                    Shop Now
                </button>
            </div>
        </div>
        <CategoryCarousel />
        <Products headerText="New Arrivals" />

        <div class="qua-box">
            <h2>We Provide High Quality Goods</h2>
            <p>Rooted in Tradition, Driven by Innovation: Our Products Speak the Language of Superior Quality</p>
            <div class="main-box">
                <div class="bx">
                    <i class="fa-solid fa-truck-fast"></i>
                    <h5>Fast Delivery</h5>
                    <p>Bringing you the joy of your purchase at lightning speed.</p>
                </div>
                <div class="bx">
                    <i class="fa-solid fa-thumbs-up"></i>
                    <h5>Best Quality</h5>
                    <p>Experience the pinnacle of craftsmanship with our unmatched quality standards.</p>
                </div>
                <div class="bx">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <h5>Free Returns</h5>
                    <p>Your satisfaction is our priority – enjoy hassle-free returns within 30 days.</p>
                </div>
            </div>
        </div>
        <Products headerText="Most Popular Products" />

        <div class="deals-wrapper">
            <div class="div1">
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/accessories-banner-2.jpg.webp"
                    alt="cover">
                    <div class="float">
                        <h2>Category4</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                    </div>
            </div>
            <div class="div2">
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/08/accessories-banner-1.jpg.webp"
                    alt="cover">
                    <div class="float">
                        <h2>Category4</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                    </div>
            </div>
            <div class="div3">
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/accessories-banner-3.jpg.webp"
                    alt="cover">
                    <div class="float">
                        <h2>Category4</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                    </div>
            </div>
            <div class="div4">
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/04/accessories-slide-3.jpg"
                    alt="cover">
                    <div class="float">
                        <h2>Category4</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                    </div>
            </div>
        </div>

        <ProductsCarousel />

        <Mail />

    </div>
</template>
<style lang="scss">
@import '@/style/_global.scss';

.home-main {
    width: 100%;
    min-height: 100vh;
    font-family: $ff;

    .hero {
        width: 100%;
        height: 85vh;
        position: relative;
        box-shadow: 0px 5px 13px 2px rgba(0, 0, 0, 0.168627451);

        >img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ctx {
            width: 45vw;
            height: 60vh;
            position: absolute;
            left: 5vw;
            top: 50%;
            transform: translate(-110%,-50%);
            @include flex($ai: flex-start);
            flex-direction: column;
            gap: 2rem;
            opacity: 0.5;
            transition: .8s ease-in;

            h2 {
                font-size: 2.5rem;
            }

            p {
                color: #555;
            }

            button {
                width: 8rem;
                height: 3rem;
                border: none;
                background: #2E6BC6;
                color: #fff;
                border-radius: 5px;
                cursor: pointer;
            }
        }
    }

    .qua-box {
        width: 100%;
        min-height: 40vh;
        padding-bottom: 1rem;

        >h2 {
            text-align: center;
            font-size: 2rem;
            padding: 2rem 1rem 1rem 1rem;
        }

        p {
            font-size: .9rem;
            color: #555;
            text-align: center;
            padding-bottom: 1rem;

        }

        .main-box {
            width: 80%;
            min-height: 18rem;
            margin: 1rem auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;

            >div {
                width: 22vw;
                height: 16rem;
                @include flex($jc: space-around);
                flex-direction: column;
                padding: 1vw 1rem;

                >i {
                    font-size: 3rem;
                    color: #2E6BC6;
                }

                h5 {
                    font-size: 1.5rem;
                }

                p {
                    color: #555;
                }
            }
        }
    }

    .deals-wrapper {
        width: 80%;
        height: 80vh;
        margin: 1rem auto;
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        grid-template-rows: repeat(8, 1fr);
        grid-column-gap: 0px;
        grid-row-gap: 0px;
        gap: 10px;
        border-radius: 10px;

        >div {
            position: relative;
            transition: .5s ease-in;
            border-radius: 10px;
            overflow: hidden;
              box-shadow: 1px 1px 8px 3px #00000023;

            &:hover{
                z-index: 2;
                transform: scale(1.02);
            }
            >img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .float{
                position: absolute;
                top: 1rem;
                left: 1vw;
                width: 70%;
                height: 70%;
                @include flex($ai:flex-start,$jc:space-around);
                flex-direction: column;
                padding: 5px;
                >button{
                    width:6rem;
                    height: 2.5rem;
                    border-radius: 5px;
                    border: none;
                    background: #2E6BC6;
                    color: #fff;
                }
                p{
                    font-size: .9rem;
                    color: #555;
                }
            }
        }

        .div1 {
            grid-area: 1 / 1 / 5 / 5;
        }

        .div2 {
            grid-area: 1 / 5 / 5 / 7;
        }

        .div3 {
            grid-area: 5 / 1 / 9 / 3;
        }

        .div4 {
            grid-area: 5 / 3 / 9 / 7;
        }
    }
}
.animate-l-t-r
{
    transform: translate(0,-50%) !important;
    opacity: 1 !important;
}
</style>