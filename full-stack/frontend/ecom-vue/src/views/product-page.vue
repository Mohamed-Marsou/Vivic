<script setup>
import { ref, computed, onMounted,watch } from 'vue'
import ProductsCarousel from '../components/build/products-carousel.vue';
import api from '../http/api'
import { useRoute } from 'vue-router'

// Reviews DATA 
import reviews from '../assets/products-reviews.json'

// Reviews data
const reviewsData = ref(reviews.reviews); 
const averageRating = computed(() => {
    // Calculate the average rating
    if (!Array.isArray(reviewsData.value) || reviewsData.value.length === 0) {
        return 0;
    }

    const totalRatings = reviewsData.value.reduce((acc, review) => acc + review.rating, 0);
    const averageRating = totalRatings / reviewsData.value.length;

    // Round to 1 decimal place
    return parseFloat(averageRating.toFixed(1));
});

// Functions to analyze review data
function getReviewCountByRating(rating) {
    return reviewsData.value.filter(review => Math.round(review.rating) === rating).length;
}

function getPercentageByRating(rating) {
    const totalReviews = reviewsData.value.length;
    const reviewsWithRating = getReviewCountByRating(rating);
    const percentage = (reviewsWithRating / totalReviews) * 100;

    return percentage.toFixed(2); // Return the percentage rounded to 2 decimal places
}

// Star ratings
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
    return averageRating.value - fullStars.value < 0.5 && averageRating.value - fullStars.value > 0.3;
});

// Initials background colors
const initialsBackgroundColors = [
    "#FF5733", "#33FF9E", "#FFD633", "#339CFF", "#FF339C", "#9C33FF"
];
function getColor(index) {
    return initialsBackgroundColors[index % initialsBackgroundColors.length];
}

// Product data
const product = ref([]);
const productImages = ref([]);
const productCover = ref('');
const productDiscount = ref(0);
const route = useRoute();


// Props
const props = defineProps({
    slug: String,
});

// Fetch product data before mount
onMounted(async () => {
    // Scroll to the top of the page
    scrollToTop()
    await getProductData();
    quantity.value = product.value.inStock
    await getHighRatedProducts();

});

watch(() => route.params.slug, async (newSlug) => {
    scrollToTop()
    try {
        const res = await api.get(`/product/${newSlug}`);
        product.value = res.data;
        const coverImg = product.value.images.find(image => image.pivot.is_cover === true);
        productCover.value = coverImg.url;
        productImages.value = product.value.images;
    } catch (error) {
        console.error('Error fetching product data:', error);
    }
    quantity.value = product.value.inStock
    await getHighRatedProducts();
});

function scrollToTop()
{window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

}
// Function to fetch product data
async function getProductData() {
    try {
        const res = await api.get(`/product/${props.slug}`);
        product.value = res.data;
        const coverImg = product.value.images.find(image => image.pivot.is_cover === true);
        productCover.value = coverImg.url;
        productImages.value = product.value.images;
    } catch (error) {
        console.error('Error fetching product data:', error);
    }
}

// Function to update cover image
function updateCoverImage(src) {
    productCover.value = src.url;
}

// Scroll to reviews section
const scrollToReviews = () => {
    const productReviewsContainer = document.querySelector('.product-reviews');
    if (productReviewsContainer) {
        productReviewsContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }
}

// Sticky navigation handling
const showStickyNav = ref(false);

const showSticky = () => {
    showStickyNav.value = true;
};

const hideSticky = () => {
    showStickyNav.value = false;
};

// get discount %
function getProductDiscount() {
    if (!product.value.sale_price || !product.value.regular_price) {
        return;
    }

    const regularPrice = parseFloat(product.value.regular_price);
    const salePrice = parseFloat(product.value.sale_price);

    const discountPercentage = ((regularPrice - salePrice) / regularPrice) * 100;
    return discountPercentage.toFixed(0)
}

// TODO CHANGE IT TO SIMILAR PRODUCTS ASAP
import { useProductStore } from '../stores/product'
const productStore = useProductStore()
const highRated = ref([])
async function getHighRatedProducts() {
    const res = await productStore.getHighRated()
    highRated.value = res.data.data
}

// Product Quantity Controllers 
const count = ref(1)
const quantity = ref(0)

const increaseCount = () => {
    if (count.value < quantity.value) count.value++
}
const decreaseCount = () => {
    if (count.value != 1) count.value--
}

// Reviews Filters
const reviewsFilter = ref('Filter reviews')
const filterReviews = () => {
    switch (reviewsFilter.value) {
        case 'With Images':
            // Separate reviews into two arrays: reviews with images and reviews without images
            const reviewsWithImages = reviewsData.value.filter((review) => review.body_urls !== '');
            const reviewsWithoutImages = reviewsData.value.filter((review) => review.body_urls === '');

            // Combine the two arrays to show reviews with images first
            reviewsData.value = [...reviewsWithImages, ...reviewsWithoutImages];
            break;
        case 'Newest':
            // Apply your sorting logic to show newest reviews first
            reviewsData.value.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            break;
        case 'High Rating':
            // Apply your sorting logic to show reviews based on rating
            reviewsData.value.sort((a, b) => b.rating - a.rating);
            break;
        default:
            // If no filter is selected or the filter is not recognized, show the original order of reviews
            reviewsData.value = reviews.reviews;
            break;
    }

};

const currentReviewsLen = ref(8)
const loading = ref(false)

const visibleReviews = computed(() => {
            return reviewsData.value.slice(0, currentReviewsLen.value);
});
const showMoreReviews = ()=> {
    if(currentReviewsLen.value != visibleReviews.value){
        loading.value = true
        
        setTimeout(()=>{
            currentReviewsLen.value = currentReviewsLen.value +4
            loading.value = false
        },1500)
    }
}
</script>
<template>
    <div class="main-cls-wrapper">

        <div class="mobile-prod-pick" :class="{ showBottomNav: showStickyNav }">
            <div class="dtl">
                <img :src="productCover" alt="product-image">
                <div>
                    <p>{{ product.name }} </p>
                    <div>
                        <i @click="scrollToReviews" v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                    </div>
                </div>
            </div>
            <div class="act">
                <h3>
                    <span v-if="product.regular_price">
                        ${{ product.regular_price }}
                    </span>
                    ${{
                        product.sale_price ?
                        product.sale_price : product.price
                    }}
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
                        <img :src="productCover" alt="product-image">
                    </div>
                    <div v-if="productImages.length > 1" class="slider-main-bottom">
                        <div>
                            <img :src="productImages[0].url" @click="updateCoverImage(productImages[0])"
                                alt="product-image">
                        </div>
                        <div>
                            <img :src="productImages[1].url" @click="updateCoverImage(productImages[1])"
                                alt="product-image">
                        </div>
                        <div>
                            <img :src="productImages[2].url" @click="updateCoverImage(productImages[2])"
                                alt="product-image">
                        </div>
                        <div>
                            <img :src="productImages[3].url" @click="updateCoverImage(productImages[3])"
                                alt="product-image">
                        </div>
                    </div>
                    <div v-if="product.sale_price" class="slider-main-badge">
                        -{{ getProductDiscount() }}%
                    </div>
                </div>

                <div class="prd-spc">
                    <div class="slots">
                        <h1>{{ product.name }}</h1>
                    </div>

                    <div class="slots">
                        <h3>
                            <span v-if="product.regular_price">
                                ${{ product.regular_price }}
                            </span>
                            ${{
                                product.sale_price ?
                                product.sale_price : product.price
                            }}
                        </h3>
                    </div>
                    <div class="slots">
                        <p>{{ product.short_description }}</p>
                    </div>
                    <div v-if="quantity && quantity != count" class="slots stock">
                        <i class="fa-solid fa-check"></i> {{ quantity }} in stock
                    </div>

                    <div v-else class="slots outStock">
                        <i class="fa-solid fa-shop-slash"></i>Out of stock
                    </div>
                    <div class="slots">
                        <i @click="scrollToReviews" v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                    </div>

                    <div class="slots container-ctl">
                        <div class="ctrls">
                            <span @click="decreaseCount" :class="{ disabled: count == 1 }"> - </span>

                            <p>{{ count }}</p>

                            <span @click="increaseCount" :class="{ disabled: quantity <= count }">+</span>
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
                    <div class="reviews-filters">
                        <p>Showing {{ currentReviewsLen }} out of {{ reviewsData.length }}</p>
                        <select @click="filterReviews" v-model="reviewsFilter">
                            <option disabled> Filter reviews</option>
                            <option>With Images</option>
                            <option>Newset</option>
                            <option>High Rating</option>
                        </select>
                    </div>

                    <div v-for="(reviewData, index) in visibleReviews" :key="index">
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

                    <div v-if="!loading && currentReviewsLen != reviewsData.length" class="reviews-pagination">
                        <button @click="showMoreReviews">SHOW MORE</button>
                    </div>
                    <div  v-if="loading" class="reviews-pagination loader">
                        <div class="lds-ring">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <ProductsCarousel smallHeader="Exploring Similar Delights? Start Here!" headerText="SIMILAR PRODUCTS"
            :productList="highRated" />
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

        .dtl {
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

            h3 {
                display: flex;

                span {
                    color: #555;
                    font-size: .8rem;
                    text-decoration: line-through;
                }

                color: #0065fc;
                font-size: 1.5rem;
                margin: 0 2vw;
            }

            button {
                color: #fff;
                background: #0065fc;
                width: 8rem;
                height: 2.5rem;
                border: none;
                border-radius: 5px;
                margin-right: 2vw;
            }

            p {
                cursor: pointer;
                color: #555;
                font-size: .8rem;

                i {
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
        width: 90%;
        min-height: 90vh;
        display: flex;
        margin: 1rem auto;

        >div {
            width: 50%;
            min-height: 90%;
            position: relative;

            .slider-main-badge {
                position: absolute;
                top: 2rem;
                left: 10%;
                border-radius: 50%;
                color: white;
                width: 3rem;
                height: 3rem;
                @include flex();
                font-size: .9rem;
                font-weight: bold;
                z-index: 2;
                user-select: none;
                background: red;
            }
        }

        .slider-main-top {
            width: 100%;
            height: 75vh;

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
            height: 15vh;
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
                font-size: 2.5rem;
                display: flex;
                padding: 1rem 0;

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
                padding: 1rem 0;
            }

            .supported-payment {
                display: flex;
                flex-direction: column;
                padding: 1rem 0;
                margin-top: 1rem;

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

        .stock {
            color: #0065fc;
            font-size: .9rem;

            >i {
                margin: 0 1vw;
                font-size: 1.2rem;

            }
        }

        .outStock {
            text-decoration: line-through;
            color: red;
            font-size: .9rem;
            font-weight: bold;

            >i {
                color: red;
                margin: 0 1vw;
                font-size: 1.2rem;
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
                    cursor: default;
                }
            }

            button {
                width: 12rem;
                height: 2.5rem;
                background: #2e6bc6ef;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: .3s ease;
                text-transform: uppercase;

                &:hover {
                    background: #0c5dd6;
                }
            }
        }

        .links {
            display: flex;
            justify-content: space-between;
            align-items: center;

            >h5 {
                cursor: pointer;
                font-size: 1rem;

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
                    font-size: 1.2rem;

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
            width: 100%;
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
            justify-content: center;
            flex-wrap: wrap;
            padding: 1rem 0 3rem 0;

            >div {
                width: 22rem;
                height: 20rem;
                margin: 10px;
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
                    height: 13rem;

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

            .reviews-filters {
                width: 100%;
                height: 4rem;
                box-shadow: 0 0 0 0;
                @include flex($jc: space-between);
                padding: 0 1vw;

                p {
                    font-size: .8rem;
                }

                >select {
                    font-size: .9rem;
                    border: 1px solid #00000025;
                    padding: 5px 10px;
                    border-radius: 5px;
                    color: #555;
                }
            }

            .reviews-pagination {
                width: 100%;
                height: 4rem;
                box-shadow: 0 0 0 0;
                @include flex();
                padding: 0 1vw;

                >button {
                    width: 10rem;
                    height: 3rem;
                    border: none;
                    border-radius: 50px;
                    background: #0065fc;
                    color: #fff;
                    cursor: pointer;
                }
            }

            .loader {
                min-height: 4rem;

                .lds-ring {
                    display: inline-block;
                    position: relative;
                    width: 80px;
                    height: 80px;
                }

                .lds-ring div {
                    box-sizing: border-box;
                    display: block;
                    position: absolute;
                    width: 64px;
                    height: 64px;
                    margin: 8px;
                    border: 8px solid #488ffa;
                    border-radius: 50%;
                    animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                    border-color: #488ffa transparent transparent transparent;
                }

                .lds-ring div:nth-child(1) {
                    animation-delay: -0.45s;
                }

                .lds-ring div:nth-child(2) {
                    animation-delay: -0.3s;
                }

                .lds-ring div:nth-child(3) {
                    animation-delay: -0.15s;
                }

                @keyframes lds-ring {
                    0% {
                        transform: rotate(0deg);
                    }

                    100% {
                        transform: rotate(360deg);
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

.disabled {
    opacity: .5 !important;
    cursor: default !important;
}

.showBottomNav {
    display: flex !important;
    transform: translateY(0) !important;
    transition: .5s ease-in-out;

}</style>

