<script setup>
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';

</script>
<template>
    <div class="Carousel">
        <p>Popular Categories</p>
        <swiper :slidesPerView="4" :spaceBetween="1" :pagination="{
            clickable: true,
        }" :breakpoints="{
    '@0.00': {
        slidesPerView: 3,
        spaceBetween: 100,
    },
    '@0.75': {
        slidesPerView: 3,
        spaceBetween: 50,
    },
    '@1.00': {
        slidesPerView: 3,
        spaceBetween: 150,
    },
    '@1.50': {
        slidesPerView: 5,
        spaceBetween: 100,
    },
    }"  class="mySwiper">
            <swiper-slide v-for="i,index in 6" :key="index">
                <div class="slider-box">
                    <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/08/wood-accessories-category-4.jpg.webp" alt="category-image">
                    <p>Category A</p>
                    <span>54 products</span>
                </div>
            </swiper-slide>
        </swiper>
    </div>
</template>

<style lang="scss" scoped>
@import '@/style/_global.scss';

.Carousel {
    width: 80%;
    margin: 2rem auto;
    min-height: 12rem;
    >p{
        font-size: 2rem;
        padding:2rem 1rem;
        font-weight: bold;
    }
    .slider-box{

        width: fit-content;
        padding: 0 3vw;
        height: 11.5rem;
        cursor:pointer;
        @include flex();
        flex-direction: column;
        gap: 5px;
        >img{
            width: 7rem;
            height: 7rem;
            border-radius: 50%;
            border: 1px solid #5555553f;
        }
            span{
                font-size: .8rem;
                color: #555;
            }
        
    }
}
</style>