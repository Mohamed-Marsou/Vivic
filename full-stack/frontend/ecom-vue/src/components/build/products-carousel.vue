<script setup>
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';

</script>
<template>
    <div class="prds-crsl">
        <h2>Most Popular Products</h2>
        <p>Proponents of content strategy may shun of dummy copy designers</p>
        <swiper :slidesPerView="3" :spaceBetween="2" :pagination="{
            clickable: true,
        }" :breakpoints="{
    '@0.00': {
        slidesPerView: 1,
        spaceBetween: 2,
    },
    '@0.75': {
        slidesPerView: 3,
        spaceBetween: 150,
    },
    '@1.00': {
        slidesPerView: 3,
        spaceBetween: 150,
    },
    '@1.50': {
        slidesPerView: 4,
        spaceBetween: 200,
    },
}" class="mySwiper">
            <swiper-slide v-for="i, index in 10" :key="index">
                <div class="prd-silde">
                    <div class="image-ctx">
                        <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/w-accessories-product-15-430x491.jpg"
                            alt="product-image">

                        <div class="prd-link">
                            <i class="fa-solid fa-eye"></i>
                        </div>

                        <div class="badge">
                        -25%
                        </div>
                    </div>
                    <a>
                        Product 2023 Brand X
                    </a>
                    <div class="stars">
                        <i v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                    </div>
                    
                </div>
            </swiper-slide>
        </swiper>
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.prds-crsl {
    width: 90%;
    height: fit-content;
    margin: 2rem auto;
    padding: 1rem 0;
    >h2{
        text-align: center;
        margin-top: 3rem;
        font-size: 2rem;
    }
    >p{
        text-align: center;
        font-size: .8rem;
        color:#555;
        padding: 2rem 1rem;
    }
    .mySwiper {

        .prd-silde {
            width: 18rem;
            height: 22rem;
            display: flex;
            flex-direction: column;
            align-items: center;

            a {
                margin: 1rem 0 0 0;
                color: #555;
                font-weight: bold;
            }

            .image-ctx {
                width: 18rem;
                height: 17rem;
                position: relative;
                overflow: hidden;
                border-radius: 10px;
                &:hover .prd-link {
                    transform: translateY(0);
                    i{
                        transform: translateY(0);
                        transition-delay: 0.2s;
                }
                }
                >img {
                    width: 18rem;
                    height: 16rem;
                    object-fit: contain;
                }

                .prd-link {
                    width: 100%;
                    height: 2.5rem;
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    background: #2E6BC6;
                    cursor: pointer;
                    @include flex();
                    transition: .3s ease;
                    overflow: hidden;
                    transform: translateY(100%);
                    i{
                        transform: translateY(150%);
                        color: #fff;
                        font-size: 1.3rem;
                        transition: .3s ease;
                    }
                }
                .badge{
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

            >.stars {
                width: 15rem;
                margin: 0 auto;
                height: 2rem;
                @include flex();
                color: gold;
            }
        }
    }
}
</style>