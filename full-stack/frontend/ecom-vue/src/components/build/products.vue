<script setup>
import { defineProps } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useProductStore } from '../../stores/product'
const router = useRouter()
const productStore = useProductStore()
const props = defineProps({
    smallHeader: String,
    headerText: String,
    productList: Array
});


const getCoverImg = (p) => {
    const coverImg = p.images.find(image => image.pivot.is_cover === true);
    return coverImg.url
}
// get discount %
function getProductDiscount(product) {
    if (!product.sale_price || !product.regular_price) {
        return;
    }

    const regularPrice = parseFloat(product.regular_price);
    const salePrice = parseFloat(product.sale_price);

    const discountPercentage = ((regularPrice - salePrice) / regularPrice) * 100;
    return discountPercentage.toFixed(0)
}

const addProductToWishlist = async (productId , SKU) => {
    productStore.addToWishlist(productId, SKU)
    router.push({ name: 'wishlist' })

}
const addToCart = async (productId, SKU) => {
    productStore.addToCart(productId, SKU)

    router.push({ name: 'cart' })

}

</script>
<template>
    <div class="bg-box">
        <h4>{{ smallHeader }}</h4>
        <h1>{{ headerText }}</h1>
        <div class="prods-conatiner">
            <div v-for="p in productList" :key="p.id" class="product-box" :class="{ 'product-outOfStock': p.inStock == 0 }">
                <div class="img-box">

                    <RouterLink :to="{ name: 'product-page', params: { slug: p.slug } }">
                        <img :src="getCoverImg(p)" alt="Product image">
                    </RouterLink>

                    <div v-if="p.sale_price && p.sale_price != '0.00' && !p.inStock == 0" class="badge">
                        -{{ getProductDiscount(p) }}%
                    </div>
                    <div v-if="p.inStock == 0" class="badge-outOfStock" title="Out Of Stock">
                        <i class="fa-solid fa-shop-slash"></i>
                    </div>
                    <div class="actions">
                        <i class="fa-solid fa-cart-shopping" v-if="!p.inStock == 0" @click="addToCart(p.id,p.SKU)"></i>
                        <i class="fa-regular fa-heart" @click="addProductToWishlist(p.id , p.SKU)"></i>
                        <RouterLink :to="{ name: 'product-page', params: { slug: p.slug } }">
                            <i class="fa-regular fa-eye"></i>
                        </RouterLink>
                    </div>
                </div>
                <RouterLink :to="{ name: 'product-page', params: { slug: p.slug } }">
                    <p>{{ p.name }}</p>
                </RouterLink>
                <div class="reviews">
                    <i v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                </div>
                <h4>${{ p.price }}</h4>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@import '@/style/_global.scss';

.bg-box {
    width: 100%;
    min-height: 60vh;
    font-family: $ff;

    >h4 {
        text-align: center;
        margin-top: 4rem;
        color: #2E6BC6;
        font-weight: normal;
    }

    h1 {
        text-align: center;
        font-size: 2.5rem;
        padding: 5px;
    }

    .prods-conatiner {
        width: 95%;
        min-height: 40vh;
        margin: 1rem auto;
        display: flex;
        justify-content: center;
        gap: 2rem;
        padding: 1rem 0;
        flex-wrap: wrap;

        .product-box {
            width: 20rem;
            height: 22rem;
            margin: 1rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;

            .img-box {
                width: 100%;
                height: 14rem;
                position: relative;
                &:hover .actions {
                    transform: translateX(0);
                }

                &:hover .badge {
                    background: #fe0303;
                    font-weight: bold;

                }

                a{
                    @include flex();
                    overflow: hidden;
                    width: 100%;
                    height: 100%;
                    img {
                        width: 80%;
                        height: 80%;
                        object-fit: contain;
                        margin: auto;
                        transition: .3s ease-in-out;
                        
                        &:hover {
                            transform: scale(1.08);
                        }
                    }

                }

                .badge {
                    position: absolute;
                    left: 5px;
                    top: 0;
                    background: #2E6BC6;
                    color: #fff;
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 50%;
                    @include flex();
                    font-size: .8rem;
                    transition: .3s ease-in-out;

                }

                .badge-outOfStock {
                    position: absolute;
                    left: 5px;
                    top: 5px;
                    background: #ef1e1ea3;
                    color: #fff;
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 50%;
                    @include flex();
                    font-size: .8rem;
                }

                .actions {
                    position: absolute;
                    right: 5px;
                    top: 0;
                    color: #555;
                    width: 3.3rem;
                    height: 8rem;
                    @include flex($jc: flex-start);
                    flex-direction: column;
                    padding: 1rem 0;
                    gap: 1rem;
                    transition: .3s ease-in;
                    transform: translateX(100px);
                    background: #ffffff95;

                    &:hover {
                        background: #fff;

                    }

                    a {
                        color: #555;
                    }

                    >a i,
                    >i {
                        font-size: 1.3rem;
                        cursor: pointer;
                        z-index: 2;
                        transition: .3s ease-in;

                        &:hover {
                            color: #2E6BC6;
                        }
                    }
                }
            }

            p {
                margin-top: 2rem;
                font-size: .91rem;
                color: #555;

            }

            .reviews {
                width: 90%;
                height: 2rem;
                margin: .3rem 0;
                @include flex();

                >i {
                    font-size: 1rem;
                    color: gold;
                }
            }

            h4 {
                color: #2E6BC6;
            }
        }

        .product-outOfStock {
            opacity: .7;
        }
    }
}

@media screen and (max-width:1024px) {
    .bg-box {

        .prods-conatiner {
            gap: 2vw;
        }
    }
}
@media screen and (max-width:630px) {
    .bg-box {
        .prods-conatiner {
            width: 97%;

            .product-box {
                width: 14rem;
            }
        }
    }
}
@media screen and (max-width:350px) {
    .bg-box {
        .prods-conatiner {
            .product-box {
                width: 90%;
            }
        }
    }
}
</style>