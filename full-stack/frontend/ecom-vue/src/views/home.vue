<script setup>
import CategoryCarousel from "../components/build/category-carousel.vue"
import Products from '../components/build/products.vue'
import ProductsCarousel from "../components/build/products-carousel.vue";
import Mail from "../components/build/mail-container.vue";
import { RouterLink } from "vue-router";
import { ref, onMounted } from "vue";
import { useProductStore } from '../stores/product'
import Loading from "../components/build/loading.vue";
const productStore = useProductStore()

const isLoaded = ref(false)



const categories = ref([])
const newArrivals = ref([])
const highRated = ref([])
const featuredProducts = ref([])


onMounted(async () => {
    
    await getCategories()
    await getNewArrivals()
    await getHighRatedProducts()
    await getFeaturedProducts()
    isLoaded.value = true

})

async function getNewArrivals() {
    const res = await productStore.getNewArrivals()
    newArrivals.value = res[0].data
}
async function getCategories() {
    const res = await productStore.getCategories()
    categories.value = res.data.response
}

async function getHighRatedProducts() {
    const res = await productStore.getHighRated()
    highRated.value = res.data.data
}

async function getFeaturedProducts() {
    const res = await productStore.getFeaturedProducts()
    featuredProducts.value = res.data.data
}
</script>
<template>
    <div class="home-main">
        <div class="hero">
            <img src="../assets/images/hero.jpg" alt="hero-image">
            <div class="ctx" :class="{ 'animate-l-t-r': isLoaded }">
                <h2>
                    Get the best Products With best Offers !
                </h2>
                <p>
                    Welcome to our store, where you can find a wide range of high-quality products that cater to all your needs. Whether you're looking for the latest gadgets, stylish fashion, or home essentials, we've got you covered. 
                    <br />Our commitment to excellence ensures that you'll always get the best value for your money.
                </p>
                <button id="CTA">
                    <RouterLink :to="{ name: 'new' }">SHOP NOW </RouterLink>
                </button>
            </div>
        </div>

        <CategoryCarousel headerText="Popular Categories" :categoryList="categories" />

        <Products v-if="isLoaded" smallHeader="Hurry up and Buy" headerText="New Arrivals" :productList="newArrivals" />
        <div v-else class="loadingContainer">
            <Loading />
        </div>

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
        <Products v-if="isLoaded" smallHeader="Only for you" headerText="Most wanted" :productList="highRated" />
        <div v-else class="loadingContainer">
            <Loading />
        </div>
        <div class="deals-wrapper">
            <div class="div1">
                <img src="../assets/images/accessories-banner-1.jpg.webp" alt="cover">
                <div class="float">
                    <h2>Category4</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                </div>
            </div>
            <div class="div2">
                <img src="../assets/images/accessories-banner-2.jpg.webp" alt="cover">
                <div class="float">
                    <h2>Category4</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                </div>
            </div>
            <div class="div3">
                <img src="../assets/images/accessories-banner-3.jpg.webp" alt="cover">
                <div class="float">
                    <h2>Category4</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                </div>
            </div>
            <div class="div4">
                <img src="../assets/images/accessories-slide-3.jpg" alt="cover">
                <div class="float">
                    <h2>Category4</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <button>Shop now</button>
                </div>
            </div>
        </div>

        <ProductsCarousel v-if="isLoaded" smallHeader="Check Out Our Featured Products"
            headerText="Featured Products" :productList="featuredProducts" />
            
        <div v-else class="loadingContainer">
            <Loading />
        </div>
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
            transform: translate(-110%, -50%);
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
                border-radius: 5px;
                background: #2E6BC6;
                cursor: pointer;
                transition: .3s ease-in-out;
                &:hover{
                    background: #347deb;
                }
                a{
                    color: #fff;
                }
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
            color: #2E6BC6;
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
        width: 85%;
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

            &:hover {
                z-index: 2;
                transform: scale(1.02);
            }

            >img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .float {
                position: absolute;
                top: 1rem;
                left: 1vw;
                width: 70%;
                height: 70%;
                @include flex($ai: flex-start, $jc: space-around);
                flex-direction: column;
                padding: 5px;

                >button {
                    width: 6rem;
                    height: 2.5rem;
                    border-radius: 5px;
                    border: none;
                    background: #2E6BC6;
                    color: #fff;
                }

                p {
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

    .loadingContainer {
        width: 80%;
        height: 40vh;
        margin: 1rem auto;
    }
}

@media screen and (max-width: 1024px) {
    .home-main {

        .hero {
            .ctx {
                width: 90vw;
                height: 60vh;
                left: 2vw;
            }
        }
    }

    .qua-box {
        padding: 1rem;

        >h2 {
            padding: 1rem 1vw !important;
        }

        .main-box {
            width: 95% !important;
            height: fit-content !important;

            >div {
                width: 33% !important;
                height: 16rem;
            }
        }
    }

    .deals-wrapper {
        width: 95% !important;
    }
}

@media screen and (max-width: 768px) {

    .qua-box {

        >h2 {
            font-size: 1.5rem !important;
        }

        p {
            font-size: .8rem !important;
        }

        .main-box {
            width: 95% !important;
            flex-direction: column;
            align-items: center;

            >div {
                width: 90% !important;
            }
        }
    }

    .home-main .deals-wrapper {
        height: fit-content !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px;

        >div {
            width: 100%;
            height: 15rem !important;

            &:hover {
                z-index: 2;
                transform: scale(1.01) !important;
            }

            .float {
                width: 80% !important;
                min-height: 70% !important;
            }
        }

    }
}

@media screen and (max-width: 550px) {
    .home-main {

        .hero {
            .ctx {
                width: 95vw;
                height: 80vh;

                >h2 {
                    font-size: 2rem;
                }

                >p {
                    font-size: .9rem;
                }
            }
        }
    }
}

@media screen and (max-width: 320px) {
    .home-main {
        .hero {
            .ctx {
                width: 95vw;
                height: 80vh;

                >h2 {
                    font-size: 1.5rem;
                }

                >p {
                    font-size: .8rem;
                }
            }
        }
    }

    .qua-box {

        >h2 {
            font-size: 1.2rem !important;
        }
    }
}


.animate-l-t-r {
    transform: translate(0, -50%) !important;
    opacity: 1 !important;
}</style>