<script setup>
import { defineProps } from 'vue';
import { RouterLink } from 'vue-router';

const props = defineProps({
    headerText: String
});

</script>
<template>
    <div class="bg-box">
        <h4>Hurry up to buy</h4>
        <h1>{{ headerText }}</h1>
        <div class="prods-conatiner">
            <div v-for="i in 12" :key="i" class="product-box">
                <div class="img-box">
                <RouterLink :to="{ name: 'product-page' }">  
                    <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/w-accessories-product-15-430x491.jpg" alt="product-image">
                </RouterLink>

                    <div class="badge">-24%</div>
                    <div class="actions">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <i class="fa-regular fa-eye"></i>
                        <i class="fa-regular fa-heart"></i>
                    </div>
                </div>
                <RouterLink :to="{ name: 'product-page' }">  
                    <p>Iphone 12 Max Pro Case - Blue</p>
                </RouterLink>
                <div class="reviews">
                    <i  v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                </div>
                <h4>$195,99</h4>
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
        width: 85%;
        min-height: 40vh;
        margin: 1rem auto;
        display: flex;
        justify-content: flex-start;
        padding:1rem 0;
        flex-wrap: wrap;   
        .product-box{
            width: 18rem;
            height: 25rem;
            margin:1rem 0 1rem 3vw;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
            .img-box{
                width: 18rem;
                height: 18rem;
                position: relative;
                &:hover .actions{ 
                    transform: translateX(0);
                }
                >a > img{
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .badge{
                    position: absolute;
                    left: 5px;
                    top: 5px; 
                    background: #2E6BC6;
                    color: #fff;
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 50%;  
                    @include flex();
                    font-size: .8rem;
                }
                .actions{
                    position: absolute;
                    right: 5px;
                    top: 5px; 
                    color: #555;
                    width: 3.3rem;
                    height:8rem;
                     @include flex($jc:flex-start);
                    flex-direction: column;
                    padding: 1rem 0;
                    gap: 1rem;
                    transition: .3s ease-in;
                    transform: translateX(100px);
                    >i{
                        font-size: 1.3rem;
                        cursor: pointer;
                        z-index: 2;
                    }
                }
            }
            p{
                margin-top: 1rem;
                font-size: .9rem;
                color:#555;
            }
            .reviews{
                width: 90%;
                height: 2rem;
                margin: .3rem 0;
                @include flex();
                >i{
                    font-size: 1rem;
                    color: gold;
                }
            }
            h4{
                color: #2E6BC6;
            }
        }
    }
}
</style>