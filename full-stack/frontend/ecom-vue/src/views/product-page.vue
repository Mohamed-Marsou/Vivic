<script setup>
import { ref, onMounted, computed } from 'vue'
import ProductsCarousel from '../components/build/products-carousel.vue';
// Reviews DATA 
import reviews from '../assets/products-reviews.json'

const reviewsData = ref(reviews.reviews)
const averageRating = computed(() => {
    if (!Array.isArray(reviewsData.value) || reviewsData.value.length === 0) {
        return 0;
    }

    const totalRatings = reviewsData.value.reduce((acc, review) => acc + review.rating, 0);
    const averageRating = totalRatings / reviewsData.value.length;

    // Round to 1 decimal place
    return parseFloat(averageRating.toFixed(1));
    });
    function getReviewCountByRating(rating) {
        return reviewsData.value.filter(review => review.rating === rating).length;
    }
    function getPercentageByRating(rating) {
        const totalReviews = reviewsData.value.length;
        const reviewsWithRating = getReviewCountByRating(rating);
        const percentage = (reviewsWithRating / totalReviews) * 100;

        return percentage.toFixed(2); // Return the percentage rounded to 2 decimal places
    }

    const fullStars = computed(() => {
        let roundedAverage = Math.round(averageRating.value);
        if (roundedAverage >= 5) {
            return 5;
        }
        return roundedAverage;
    });

    const hasHalfStar = computed(() => {
        if (averageRating.value >= 5) {
            return false;
        }
        return averageRating.value - fullStars.value < 0.5 && averageRating.value - fullStars.value > 0;
    });

    let initialsBackgroundColors = [
        "#FF5733", "#33FF9E", "#FFD633", "#339CFF", "#FF339C", "#9C33FF"
    ]
    function getColor(index) {
        return initialsBackgroundColors[index % initialsBackgroundColors.length];
    }
// Reviews DATA 

const showStickyNav = ref(false)

const showSticky = ()=>{
    showStickyNav.value = true
}
const hideSticky = ()=>{
    showStickyNav.value =false
    
}
onMounted(() => {
    //* Scroll to the top of the page
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
const ScrollToreviews = () => {
    const productReviewsContainer = document.querySelector('.product-reviews');
    if (productReviewsContainer) {
        productReviewsContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }
}
</script>
<template>
    <div class="main-cls-wrapper">

        <div  class="mobile-prod-pick" :class="{showBottomNav : showStickyNav}">
            <div class="dtl">
                <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-front.jpg"
                    alt="">

                <div>
                    <p>iPhone 12 Pro Moment Case – Black </p>
                    <div>
                        <i @click="ScrollToreviews" v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                    </div>
                </div>
            </div>
            <div class="act">
                <h3>
                    <span>$24.5</span>
                    $10.54
                </h3>
                <button>Add to Cart</button>
                <p>
                    Add to wishlist :
                    <i class="fa-regular fa-heart"></i>
                </p>
            </div>
        </div>

        <div @mouseover="hideSticky" @mouseleave="showSticky" class="product-v">
            <div class="prd-details">
                <div class="slider-main">
                    <div class="slider-main-top">
                        <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-front.jpg"
                            alt="product-image">
                    </div>
                    <div class="slider-main-bottom">
                        <div>
                            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-front.jpg"
                                alt="product-image">
                        </div>
                        <div>
                            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-front.jpg"
                                alt="product-image">
                        </div>
                        <div>
                            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-front.jpg"
                                alt="product-image">
                        </div>
                        <div>
                            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-front.jpg"
                                alt="product-image">
                        </div>
                    </div>
                    <div class="slider-main-badge">
                        -21%
                    </div>
                </div>

                <div class="prd-spc">
                    <div class="slots">
                        <h1>iPhone 12 Pro Moment Case – Black</h1>
                    </div>

                    <div class="slots">
                        <h3>
                            <span>$199.99</span>
                            $55.55
                        </h3>
                    </div>
                    <div class="slots">
                        <p>your iPhone 12 Pro with our sleek Moment Case in Black. Designed for both style and
                            functionality,
                            this case offers reliable protection and a comfortable grip. Elevate your mobile photography
                            experience with its compatibility with Moment lenses. Get ready to capture stunning moments
                            effortlessly</p>
                    </div>
                    <div class="slots">
                        <i  class="fa-solid fa-check"></i> 15 in stock
                    </div>
                    <div class="slots">
                        <i @click="ScrollToreviews"  v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                    </div>
                    <div class="slots container-ctl">
                        <div class="ctrls">
                            <span>-</span>
                            <p>1</p>
                            <span>+</span>
                        </div>
                        <button>
                            Add to cart
                        </button>
                    </div>
                    <div class="slots links">
                        <h5>
                            <i class="fa-regular fa-heart"></i> Add to wishlist
                        </h5>

                        <div>
                            <p>
                                Share :
                            </p>
                            <i class="fa-brands fa-facebook"></i>
                            <i class="fa-brands fa-square-instagram"></i>
                            <i class="fa-brands fa-x-twitter"></i>
                            <i class="fa-brands fa-square-whatsapp"></i>
                        </div>
                    </div>
                    <div class="slots">
                        <div class="supported-payment">
                            <h5>
                                Guaranteed Safe Checkout
                            </h5>
                            <div class="icons">
                                <img src="../assets/icons/mastercard-old-3-svgrepo-com.svg" alt="">
                                <img src="../assets/icons/paypal-svgrepo-com.svg" alt="">
                                <img src="../assets/icons/visa-classic-svgrepo-com.svg" alt="">
                                <img src="../assets/icons/icons8-apple-pay-50.png" alt="s">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="product-description">
            <header class="section-header">
                <h3>
                    Description
                </h3>
            </header>
            <p v-for="i in 5" key="i">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum pariatur suscipit
                ullam
                amet repellat quaerat vitae id non culpa sit alias, dolorem accusamus fugit ab ex rem maxime eum neque ipsa
                expedita laudantium! Cumque animi consectetur asperiores placeat provident at quisquam ratione cum ipsam
                officia
                error maiores, nihil in eveniet quaerat.</p>
        </div>

        <div class="product-reviews">
            <header class="section-header">
                <h3>
                    Reviews
                </h3>
            </header>

            <div class="wrapper">
                <div class="statis-box">
                    <div class="stat">
                        <div>
                            <h1>
                                {{ averageRating }}
                            </h1>
                            <div class="stars">
                                <i v-for="star, i in fullStars" :key="i" class="fa-solid fa-star"></i>
                                <i v-if="hasHalfStar" class="fa-solid fa-star-half"></i>
                            </div>
                            <p>{{ reviewsData.length }} Reviws</p>
                            <small> Only customers who have purchased the product are eligible to write a review</small>
                        </div>
                        <div>
                            <div v-for="i in 5" :key="i" class="review-slot">
                                <i class="fa-solid fa-star"></i>
                                <span>{{ i }}</span>
                                <div>
                                    <div :style="{ width: getPercentageByRating(i) + '%' }"></div>
                                </div>
                                <h5>{{ getReviewCountByRating(i) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reviews-box">
                    <div v-for="(reviewData, index) in reviewsData" :key="index">
                        <div class="img-bx">
                            <img v-if="reviewData.body_urls" :src="reviewData.body_urls" alt="review-image">
                            <div class="auther">
                                <div :style="{ backgroundColor: getColor(index) }">{{ reviewData.author.charAt(0) }}</div>
                                <p>{{ reviewData.author }}</p>
                            </div>
                        </div>
                        <div class="stars-reveiws">
                            <i v-for="(star, index) in Math.round(reviewData.rating)" :key="index" class="fa-solid fa-star">
                            </i>
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <p> {{ reviewData.body_text.length > 50 ? reviewData.body_text.slice(0, 50) + '...' :
                            reviewData.body_text
                        }}</p>
                    </div>
                </div>
            </div>
        </div>
        <ProductsCarousel />
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.main-cls-wrapper {
    font-family: $ff;
    width: 100%;
    min-height: 100vh;
    position: relative;

    .mobile-prod-pick {

        width: 100%;
        height: 4.5rem;
        background: rgb(255, 255, 255);
        position: fixed;
        top: 90.5vh;
        right: 0;
        z-index: 2;
        justify-content: space-between;
        padding: 0 3vw;
        border-top: 1px solid gainsboro;
        transition: .5s ease-in-out;
        display: none;
        transform: translateY(100%);
        .dtl{
            height: 100%;
            display: flex;
        >img {
            height: 100%;
            width: 8rem;
            object-fit: contain;
        }

        >div {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            padding: 0 1vw;
            font-size: .9rem;
            color: #555;

            i {
                color: #FFD700;
                cursor: pointer;
            }
        }
    }

        .act {
            min-width: 10rem;
            height: 100%;
            display: flex;
            align-items: center;
            h3{
                display: flex;
                span{
                    color: #555;
                    font-size: .8rem;
                    text-decoration: line-through;
                }
                color: #0065fc;
                font-size: 1.5rem;
                margin: 0 2vw;
            }
            button{
                color: #fff;
                background: #0065fc;
                width: 8rem;
                height: 2.5rem;
                border: none;
                border-radius: 5px;
                margin-right: 2vw;
            }
            p{
                cursor: pointer;
                color: #555;
                font-size: .8rem;
                i{
                    font-size: 1.2rem;
                }

            }

        }
    }
}

.product-v {
    width: 100%;
    min-height: 100vh;


    .prd-details {
        width: 100%;
        height: 90vh;
        display: flex;

        >div {
            width: 50%;
            height: 100%;
            position: relative;

            .slider-main-badge {
                position: absolute;
                top: 2rem;
                left: 2rem;
                background: red;
                border-radius: 50%;
                color: white;
                width: 3rem;
                height: 3rem;
                @include flex();
                font-size: .9rem;
                font-weight: bold;
                z-index: 2;
                user-select: none;
            }
        }

        .slider-main-top {
            width: 100%;
            height: 90%;

            cursor: pointer;

            >img {
                height: 100%;
                width: 100%;
                object-fit: contain;
            }

        }

        .slider-main-bottom {
            cursor: pointer;
            width: 100%;
            height: 20%;
            display: flex;
            justify-content: space-evenly;

            >div {
                width: 25%;
                height: 100%;

                >img {
                    height: 100%;
                    width: 100%;
                    object-fit: contain;
                }
            }

        }

        .slots {
            width: 100%;
            min-height: 2rem;
            padding: 0 1.5vw;
            color: #555;

            >h1 {
                padding: 4rem 1vw 0 0;
            }

            >i {
                cursor: pointer;
                color: gold;
            }

            >h3 {
                color: #2E6BC6;
                font-size: 3rem;
                display: flex;
                padding: 2rem 0;

                >span {
                    display: inline-block;
                    font-size: .9rem;
                    margin-top: 10px;
                    color: #555;
                    text-decoration: line-through;
                }
            }

            >p {
                font-size: .9rem;
                line-height: 2rem;
            }

            .supported-payment {
                display: flex;
                flex-direction: column;
                padding: 1rem 0;

                h5 {
                    font-size: .9rem;
                }

                >.icons {
                    width: 100%;
                    height: 3rem;
                    display: flex;
                    justify-content: flex-end;
                    gap: 1vw;

                    >img {
                        height: 90%;
                        object-fit: contain;

                    }
                }
            }
        }

        .container-ctl {
            display: flex;
            gap: 2vw;
            padding: 2rem 1vw;

            >.ctrls {
                width: 10rem;
                height: 2.5rem;
                border: 2px solid #5555556b;
                display: flex;
                border-radius: 5px;
                user-select: none;

                >span,
                p {
                    width: 33%;
                    @include flex();
                    cursor: pointer;
                }

                >p {
                    border-right: 2px solid #5555556b;
                    border-left: 2px solid #5555556b;
                }
            }

            button {
                width: 12rem;
                height: 2.5rem;
                background: #2E6BC6;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: .3s ease;
                text-transform: uppercase;

                &:hover {
                    background: red;
                }
            }
        }

        .links {
            display: flex;
            justify-content: space-between;
            align-items: center;

            >h5 {
                cursor: pointer;
            }

            >div {
                display: flex;
                align-items: center;

                >p {
                    margin-right: 2vw;
                }

                i {
                    margin-right: 1vw;
                    transition: .3s ease;
                    cursor: pointer;

                    &:hover {
                        color: #0065fc;
                    }
                }
            }
        }

    }
}

.product-description {

    width: 100%;
    min-height: 20vh;
    margin-top: 1rem;
    @include flex();
    padding: 1rem 10vw;
    flex-direction: column;

    >p {
        margin: 1rem 0;
    }
}

.product-reviews {

    width: 100%;
    min-height: 50vh;
    margin-top: 1rem;

    .wrapper {
        width: 100%;
        min-height: 50vh;

        >div {
            width: 90%;
            min-height: 30vh;
            margin: 5px auto;
        }

        .stat {
            width: 100%;
            min-height: 30vh;
            display: flex;


            >div {
                width: 50%;
                min-height: 30vh;
                padding: 1rem 1vw;
                @include flex();
                flex-direction: column;

            }

            .stars {
                i {
                    width: 1.5rem !important;
                    color: #FFD700;
                    margin: 0.5rem 1px;
                }
            }

            h1 {
                font-size: 3rem;
                padding: 0 10px;
            }

            p {
                color: #555;
                padding: .5rem 0;
            }

            small {
                color: #555;
                text-align: center;
                padding: 0 1vw;
            }


            .review-slot {
                width: 100%;
                height: 3rem;
                @include flex();


                i {
                    width: 1.2rem !important;
                    color: #FFD700;
                    margin: 0 .5rem;
                }

                >div {
                    width: 60%;
                    height: 8px;
                    background-color: #d1d1d1a1;
                    margin: 0 1rem;

                    >div {
                        width: 40%;
                        height: 8px;
                        background-color: #FFD700;
                    }
                }

                h5 {
                    padding: 5px;
                    border: 1px solid #5555556c;
                    border-radius: 5px;
                    color: #555;
                }
            }

        }

        .reviews-box {
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;

            >div {
                width: 20rem;
                height: 23rem;
                margin: 10px 1rem;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: 1px 1px 6px #00000052;

                &:not(:has(img)) {
                    height: 12rem;
                }

                >p {
                    color: #555;
                    font-size: .9rem;
                    padding: 10px 1vw;
                }

                .img-bx {
                    width: 100%;
                    position: relative;
                    height: 15rem;

                    &:not(:has(img)) {
                        height: 5rem;
                    }

                    .auther {
                        width: 85%;
                        height: 2.5rem;
                        background: #fff;
                        position: absolute;
                        bottom: 1rem;
                        left: 1vw;
                        display: flex;
                        border-radius: 2.5rem;
                        display: flex;
                        align-items: center;
                        user-select: none;

                        >div {
                            background: #0065fc;
                            color: #fff;
                            width: 2.5rem;
                            height: 2.4rem;
                            border-radius: 50%;
                            margin-left: 1px;
                            @include flex();
                            font-size: 1.2rem;
                        }

                        >p {
                            color: #555;
                            font-size: .9rem;
                            font-weight: bold;
                            margin-left: 1vw;
                        }
                    }

                    >img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                }

                .stars-reveiws {
                    width: 100%;
                    margin-top: 1rem;
                    color: #FFD700;
                    padding: 0 1vw;

                    .fa-circle-check {
                        color: rgba(0, 128, 0, 0.681);
                        margin-left: 8px;
                    }
                }

            }
        }

    }
}

.section-header {
    width: 100%;
    border-bottom: 1px solid rgba(68, 68, 68, 0.279);
    @include flex($ai: flex-end);
    margin-bottom: 2rem;

    h3 {
        font-size: 1.5rem;
        text-transform: uppercase;
        padding: 4rem 1vw 0 1vw;
        color: #555;
        position: relative;

        &::after {
            content: '';
            position: absolute;
            bottom: -3px;
            right: 0;
            width: 100%;
            height: 5px;
            background: #0065fc;
        }
    }

}
.showBottomNav{
    display: flex !important;
    transform: translateY(0) !important;
    transition: .5s ease-in-out;

}

</style>

