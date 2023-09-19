<script setup>
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import { defineProps } from 'vue';
import { RouterLink } from 'vue-router'
import { onMounted } from 'vue';

onMounted(() => {
      const swiperWrapper = document.querySelector('.swiper-wrapper');
      if (swiperWrapper.lenght > 4) {
        swiperWrapper.style.justifyContent = 'center';
      }
    });
const props = defineProps({
    headerText: String,
    categoryList: Array
});
const generateSlug = (name) => {
    return name.toLowerCase().replace(/\s+/g, '-');
    
};
</script>
<template>
    <div class="Carousel">
        <p>{{ headerText }}</p>
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
}" class="mySwiper">
            <swiper-slide v-for="c  in categoryList" :key="c.id">
                <div class="slider-box">
                    <RouterLink :to="{name: 'category', params: {slug : generateSlug(c.name)  , id: c.id }}">
                        <div>
                            <img :src="c.image">
                        </div>
                    </RouterLink>
                    <RouterLink :to="{ name: 'category' , params: {slug : generateSlug(c.name) , id: c.id }}">
                        <p>{{ c.name }}</p>
                    </RouterLink>

                    <span>{{ c.products_count }} products</span>
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
    >p {
        font-size: 2rem;
        padding: 2rem 1rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #2e2d2d;
    }
    .slider-box {
        width: fit-content;
        padding: 0 3vw;
        height: 11.5rem;
        cursor: pointer;
        @include flex();
        flex-direction: column;
        gap: 5px;
        >a div {
            width: 7rem;
            height: 7rem;
            border-radius: 50%;
            border: 1px solid #5555553f;
            overflow: hidden;
            @include flex();
            position: relative;

            &:hover>img {
                transform: scale(1.1);
            }

            &::after {
                content: '';
                width: 10%;
                height: 10%;
                background: #00000052;
                position: absolute;
                top: 50%;
                right: 50%;
                transform: translate(50%, -50%);
                transform-origin: center;
                opacity: 0;
                transition: .2s ease-in;
                border-radius: 50%;
                transform-origin: center;

            }

            &:hover {
            border: 0;
            }
            &:hover::after {
                width: 100%;
                height: 100%;
                top: 0;
                right: 0;
                transform: translate(0, 0);
                opacity: .5;
            }

            >img {
                width: 90%;
                height: 90%;
                object-fit: cover;
                transition: .2s ease-in;
            }
        }

        span {
            font-size: .8rem;
            color: #555;
        }

    }
}

@media screen and (max-width:500px) {
    .Carousel {
    width: 90%;
    >p {
        font-size: 5vw;
        
    }
}
}
</style>