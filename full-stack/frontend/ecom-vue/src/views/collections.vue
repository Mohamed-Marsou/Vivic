<script setup>
import categoryCarousel from '../components/build/category-carousel.vue';
import Mail from "../components/build/mail-container.vue";
import { ref, onMounted } from "vue";
import { useProductStore } from '../stores/product'
import { useRouter } from 'vue-router';
 
import Loading from '../components/build/loading.vue';
const loaded = ref(false)

const productStore = useProductStore()

const router = useRouter();

const categories = ref([])

const navBack = () => {
    router.back();
};
onMounted(() => {
    getCategories()
})
async function getCategories() {
    const res = await productStore.getCategories()
    categories.value = res.data.response
    loaded.value = true
}
</script>

<template>
    <div class="collections">
        <div class="banner">
            <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/accessories-page-title.jpg"
                alt="cover">
            <div>
                <i @click="navBack" class="fa-solid fa-arrow-left"></i>
                <h2>Collections</h2>
            </div>
        </div>

        <div class="heading">
            <p>Explore our diverse range of categories for your interests</p>
            <h2>Recommended Categories</h2>
        </div>
        <categoryCarousel headerText="" :categoryList="categories" v-if="loaded"/>
        <div v-else class="loadingCategories">
            <Loading />
        </div>
        <div class="mega-bx">
            <div>
                <img src="../assets/images/accessories-banner-1.jpg.webp" alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
            <div>
                <img src="../assets/images/accessories-banner-2.jpg.webp" alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
            <div>
                <img src="../assets/images/accessories-banner-3.jpg.webp" alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
            <div id="mb-cate">
                <img src="../assets/images/accessories-slide-3.jpg" alt="cover">
                <div>
                    <p>Lorem ipsum dolor sit lop</p>
                    <h2>Category X</h2>
                    <button>Checkout</button>
                </div>
            </div>
        </div>

        <div class="newIn-bx">
            <img src="../assets/images/accessories-slide-3.jpg" alt="cover">

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
<style lang="scss" >
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
                text-transform: uppercase;
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
        
        >div {
            width: 32.5%;
            height: 55vh;
            position: relative;
            transition: .3s ease-in-out;
            overflow: hidden;
            box-shadow: 0px 5px 13px 2px rgba(0, 0, 0, 0.115);
            border-radius: 10px;
            margin-right: 5px;

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
                top: 20px;
                left: 1rem;
                width: 100%;
                user-select: none;
                p { 
                    color: #2E6BC6;
                    font-size: .9rem;
                }

                h2 {
                    text-transform: uppercase;
                    font-size: 2.5rem;
                    padding: 1rem 0;
                }

                button {
                    color: #fff;
                    background: #2E6BC6;
                    width: 8rem;
                    height: 3rem;
                    border-radius: 20px;
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

    .Carousel>p {
        display: none;
    }
}
.loadingCategories{
    width: 90%;
    height: 14rem ;
    margin: 2rem auto;
    @include flex();
}

@media screen and (max-width:1024px) {
    .collections {
        .mega-bx {
            width: 95%;
        }

        .newIn-bx {
            width: 95%;
        }
    }
}

@media screen and (max-width:768px) {
    .collections {
        .mega-bx {
            flex-direction: column;
            box-shadow: none !important;

            >div {
                width: 100%;
                height: 20rem;
                box-shadow: 0px 5px 13px 2px rgba(0, 0, 0, 0.168627451);
                margin-top: 1rem;
                border-radius: 15px;

                >img {
                    &:hover {
                        transform: scale(1.02);
                    }
                }

                >div {
                    width: 70%;
                    height: 70%;
                    padding: 5px;
                    top: 2rem;
                    left: 2rem;

                    button {
                        width: 8rem;
                        height: 2.5rem;
                        border-radius: 30px;
                    }
                }
            }

            #mb-cate {
                display: block;
            }

        }
        .newIn-bx {
            width: 95%;
            >div {
            width: 90%;
            }
        }
    }
}

@media screen and (max-width:450px) {
    .collections {
        .banner {
            >div {
                h2 {
                    font-size: 2rem !important;
                }
            }
        }
        .mega-bx {
            >div {
                >div {
                    h2 {
                        font-size: 1.5rem;
                    }
                }
            }
        }
        .newIn-bx {
            width: 95%;
            >div {
            width: 90%;
            h2{
                font-size: 1.5rem;
            }
            }
        }
    }
}

@media screen and (max-width:320px) {
    #main>div>div.heading>h2 {
        font-size: 10vw !important;
    }
}</style>