<script setup>
import { ref, onMounted } from 'vue'
import { useProductStore } from '../stores/product';
import { RouterLink, useRouter } from 'vue-router';
import Loading from '../components/build/loading.vue';
const productStore = useProductStore()
const router = useRouter()
const products = ref([])
const loaded = ref(false)

onMounted(async () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    
    const res = await productStore.getWishlistProducts()
    if (res && res.products ) {
        products.value = res.products
    }
    loaded.value = true
})



const getCoverImg = (p) => {
    console.log(p);
    if (p.images) {
        const img =  p.images.find(image => image.pivot.is_cover === true);
        return img.url
    } else {
        return p.image
    }
}

const addToCart = async (productId, SKU) => {
    await productStore.addToCart(productId, SKU)

    removeFromWishList(SKU)
    router.push({ name: 'cart' })
}

const removeFromWishList = async (SKU) => {
    await productStore.removeWishlistItem(SKU)
    const itemIndex = products.value.findIndex((item) => item.SKU === SKU);
    if (itemIndex !== -1) {
            products.value.splice(itemIndex, 1);
    }
    console.log(products.value);

}

function getProductSlug(p) {
    if (!p.slug) {
        // If the product doesn't have a slug, generate one from the name
        const slug = p.name
            .toLowerCase() 
            .replace(/[^\w\s-]/g, '') 
            .replace(/\s+/g, '-') 
            .trim(); 
        return slug;
    } else {
        // If the product already has a slug, return it
        return p.slug;
    }
}
</script>


<template>
    <div class="WishList-container">
        <div class="header">
            <img src="../assets/images/page-header-img.jpg" alt="banner">
            <div>
                <h1>wishlist</h1>
                <small>
                    <RouterLink :to="{ name: 'home' }">Home</RouterLink> / wishlist
                </small>
            </div>
        </div>

        <div v-if="!loaded" class="loader">
            <Loading />
        </div>

        <div v-else>

            <div v-if="products.length === 0" class="emptyWhishlist">
                <i class="fa-regular fa-heart"></i>
                <h4>This wishlist is empty.</h4>
                <p>You don't have any products in the wishlist yet.<br />
                    You will find a lot of interesting products on our "Shop" page.
                </p>
                <RouterLink :to="{ name: 'home' }">
                    RETURN TO SHOP
                </RouterLink>
            </div>

            <div v-else class="full-wishlist-products">
                <h1>My Wihslit</h1>
                <div class="wishList-items-box">

                    <div class="item" v-for="p in products" :key="p.id">
                        <RouterLink :to="{ name: 'product-page', params: { slug: getProductSlug(p) } }">
                            <img :src="getCoverImg(p)" alt="Product image">
                        </RouterLink>
                        <div>
                            <small v-if="p.inStock">InStock</small>
                            <small class="outOfStock" v-else>Out of Stock</small>
                            <p>{{ p.name }}</p>
                            <span v-if="p.average_rating && p.average_rating != '0.00' ">
                                {{ (+p.average_rating).toFixed(1) }}
                                <i class="fa-solid fa-star"></i>
                            </span>
                            <h4>${{ p.sale_price != '0.00' ? p.sale_price : p.price }}</h4>
                            <button :disabled="!p.inStock" @click="addToCart(p.id,p.SKU)">ADD TO CART</button>
                            <i @click="removeFromWishList(p.SKU)" class="fa-solid fa-trash-can"></i>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</template>

<style lang="scss" scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
$ff: 'Poppins', sans-serif;

.WishList-container {
    width: 100%;
    min-height: fit-content;
    font-family: $ff;

    .header {
        width: 100%;
        height: 25vh;
        position: relative;

        >img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        >div {
            position: absolute;
            top: 50%;
            right: 50%;
            transform: translate(50%, -50%);
            display: flex;
            flex-direction: column;
            text-align: center;
            color: #fff;

            h1 {
                font-size: 4rem;
                text-transform: uppercase;
                font-weight: normal;
            }

            a {
                color: #fff;
            }
        }
    }

    .loader {
        width: 100%;
        height: 90vh;
    }

    .emptyWhishlist {
        width: 100%;
        height: calc(90vh - 25vh);
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        text-align: center;
        font-size: .9rem;
        color: #555;
        gap: 5px;

        >i {
            font-size: 15vw;
            color: #0000001a;
        }

        h4 {
            font-size: 1.5rem;
        }

        a {
            background: #2E6BC6;
            width: 10rem;
            height: 3rem;
            border: none;
            border-radius: 50px;
            margin-top: 1rem;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }

    .full-wishlist-products {
        width: 100%;
        min-height: 70vh;

        >h1 {
            font-size: 2rem;
            padding: 1rem 4vw;
        }

        .wishList-items-box {
            width: 60%;
            min-height: 20vh;
            margin: 1rem auto;

            >div {
                width: 100%;
                height: 10rem;
                margin: 1rem auto;
                display: flex;
                align-items: center;
                justify-content: space-evenly;
                border-bottom: 1px solid #0000001a;
                padding: 6px 0;

                >a img {
                    width: 9rem;
                    height: 9rem;
                    object-fit: contain;
                }

                >div {
                    width: calc(90% - 10rem);
                    height: 100%;
                    padding: 5px;
                    position: relative;
                    display: flex;
                    justify-content: space-evenly;
                    flex-direction: column;

                    small {
                        color: green;
                        font-weight: bold;
                    }

                    .outOfStock {
                        color: red;
                    }

                    >p {
                        color: #555;
                        font-size: .9rem;
                        padding: 5px 0;
                    }

                    >span {
                        background: #2E6BC6;
                        color: #fff;
                        font-size: .8rem;
                        padding: 4px 5px;
                        width: fit-content;

                        >i {
                            font-size: .75rem;
                            margin: 0 3px;
                        }
                    }

                    >h4 {
                        margin: 10px 0;
                    }

                    >button {
                        width: 8rem;
                        height: 2.8rem;
                        background: #2E6BC6;
                        color: #fff;
                        border: none;
                        border-radius: 50px;
                        position: absolute;
                        bottom: 25px;
                        right: 5px;
                        transition: .3s ease-in-out;
                        cursor: pointer;

                        &:not([disabled]):hover {
                            background: red;
                        }

                    }

                    button[disabled] {
                        opacity: .5;
                        cursor: not-allowed;

                    }

                    >i {
                        position: absolute;
                        top: 15px;
                        right: 1rem;
                        color: #555;
                        font-size: 1.1rem;
                        cursor: pointer;

                        &:hover {
                            color: red;
                        }
                    }
                }

            }
        }
    }

}

@media screen and (max-width : 1024px) {
    .wishList-items-box {
        width: 80% !important;
    }
}

@media screen and (max-width : 768px) {
    .wishList-items-box {
        width: 98% !important;

        >div {
            >a img {
                width: 7rem !important;
                height: 7rem !important;
            }

            >div {
                width: calc(90% - 8rem) !important;
            }
        }
    }
}

@media screen and (max-width : 500px) {
    .wishList-items-box {
        width: 98% !important;

        >div {
            padding: 0 2vw !important;

            >a img {
                display: none;
            }

            >div {
                width: 100% !important;

                >button {
                    width: 7rem !important;
                    font-size: .75rem !important;
                }
            }
        }
    }
}
</style>