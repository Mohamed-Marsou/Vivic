<script setup>
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import { defineProps } from 'vue';

const props = defineProps({
    smallHeader: String,
    headerText: String,
    productList: Array
});


const getCover = (p) => {
    const coverImg = p.images.find(image => image.pivot.is_cover === true);
    return coverImg.url
}

// get discount %
function getProductDiscount(p) {
    if (!p.sale_price && p.sale_price !== '0.00' ) {
        return;
    }

    const regularPrice = parseFloat(p.regular_price);
    const salePrice = parseFloat(p.sale_price);

    const discountPercentage = ((regularPrice - salePrice) / regularPrice) * 100;
    return discountPercentage.toFixed(0)
}
</script>
<template>
    <div class="prds-crsl">
        <h2>{{ headerText }}</h2>
        <p>{{ smallHeader }}</p>
        <swiper :slidesPerView="3" :spaceBetween="2" :pagination="{
            clickable: true,
        }" :breakpoints="{
    '@0.00': {
        slidesPerView: 2,
        spaceBetween: 250,
    },
    '@0.75': {
        slidesPerView: 3,
        spaceBetween: 100,
    },
    '@1.00': {
        slidesPerView: 4,
        spaceBetween: 150,
    },
    '@1.50': {
        slidesPerView: 5,
        spaceBetween: 150,
    },
}" class="mySwiper">
            <swiper-slide v-for="p  in productList " :key="p.id">
                <div class="prd-silde">
                    <div class="image-ctx">
                        <RouterLink :to="{ name: 'product-page',  params: { slug: p.slug } }">
                            <img :src="getCover(p)" alt="Product image">
                        </RouterLink>
                        <RouterLink :to="{ name: 'product-page',  params: { slug: p.slug } }">
                            <div class="prd-link">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </RouterLink>

                        <div v-if="p.sale_price != '0.00'" class="badge">
                            -{{getProductDiscount(p)}}%
                        </div>
                    </div>

                    <div>

                        <RouterLink :to="{ name: 'product-page' ,  params: { slug: p.slug }}">
                            {{  p.name  }}
                        </RouterLink>
                        <div class="stars">
                            <i v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                        </div>

                    </div>
                </div>
            </swiper-slide>
        </swiper>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.prds-crsl {
    width: 95%;
    height: fit-content;
    margin: 2rem auto;
    padding: 1rem 0;
    margin-bottom: 4rem;

    >h2 {
        text-align: center;
        margin-top: 1rem;
        font-size: 2rem;
    }

    >p {
        text-align: center;
        font-size: .9rem;
        color: #555;
        padding: 2rem 1rem;
    }

    .mySwiper {

        .prd-silde {
            width: 16rem;
            height: 18rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            a {
                margin: 1rem 0 0 0;
                color: #555;
                font-size: .9rem;
            }

            .image-ctx {
                width: 100%;
                height:13rem;
                position: relative;
                overflow: hidden;
                border-radius: 10px;
                @include flex();
                margin-bottom: 10px;

                &:hover .prd-link {
                    transform: translateY(0);

                    i {
                        transform: translateY(0);
                        transition-delay: 0.2s;
                    }
                }

                > a > img {
                    width: 80%;
                    height: 80%;
                    object-fit: contain;
                }

                .prd-link {
                    width: 100%;
                    height: 2.5rem;
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    background: #5790e6;
                    cursor: pointer;
                    @include flex();
                    transition: .3s ease;
                    overflow: hidden;
                    transform: translateY(100%);
                     i {
                        transform: translateY(150%);
                        color: #fff;
                        font-size: 1.3rem;
                        transition: .3s ease;
                    }
                    &:hover{
                    background: #2E6BC6;

                    }
                }

                .badge {
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 50%;
                    position: absolute;
                    top: 10px;
                    left: 10px;
                    z-index: 3;
                    background: #2E6BC6;
                    color: #fff;
                    @include flex();
                    font-size: .8rem;
                    font-weight: bold;
                }

            }

            .stars {
                width: 15rem;
                margin: 0 auto;
                height: 2rem;
                @include flex();
                color: gold;
            }
        }
    }
}</style>