<script setup>
import ProductsCarousel from '../components/build/products-carousel.vue';
import { ref, onMounted, watch, computed, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../http/api'

const route = useRoute();
const router = useRouter();

const product = ref([]);
const productImages = ref([]);
const productCover = ref('');

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
function scrollToTop() {
    window.scrollTo({
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

// Function to update cover image
function updateCoverImage(src) {
    productCover.value = src.url;
}
/// ------------------------------------------------------- overlay images

const currentIndex = 0
const isVisible = ref(false)
const ShowOverLay = () => {
    isVisible.value = !isVisible.value
}

//------------------------------------------------------- discount count down
// TODO Change the hard coded value 
const OrderdNumber = 24
// TODO Change the hard coded value 
const targetDate = new Date();
targetDate.setDate(targetDate.getDate() + 7);

const currentDate = ref(new Date());

const timeRemaining = computed(() => {
    const totalSeconds = Math.max(0, Math.floor((targetDate - currentDate.value) / 1000));
    return {
        days: Math.floor(totalSeconds / (3600 * 24)),
        hours: Math.floor((totalSeconds % (3600 * 24)) / 3600),
        minutes: Math.floor((totalSeconds % 3600) / 60),
        seconds: totalSeconds % 60,
    };
});

const { days, hours, minutes, seconds } = timeRemaining;

const countdownInterval = setInterval(() => {
    currentDate.value = new Date();
}, 1000);

onUnmounted(() => {
    clearInterval(countdownInterval);
});



//* ----------------------------------------------------  Reviews ---------------------------------------------------- 
import reviews from '../assets/products-reviews.json'

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

    return percentage.toFixed(2);
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
const currentReviewsLen = ref(4)
const loading = ref(false)

const visibleReviews = computed(() => {
    return reviewsData.value.slice(0, currentReviewsLen.value);
});
const showMoreReviews = () => {
    if (currentReviewsLen.value != visibleReviews.value) {
        loading.value = true

        setTimeout(() => {
            currentReviewsLen.value = currentReviewsLen.value + 4
            loading.value = false
        }, 1500)
    }
}
// Reviews Data

// get discount %
function getProductDiscount() {
    if (!product.value.sale_price && product.value.sale_price != '0.00') {
        return;
    }

    const regularPrice = parseFloat(product.value.regular_price);
    const salePrice = parseFloat(product.value.sale_price);

    const discountPercentage = ((regularPrice - salePrice) / regularPrice) * 100;
    return discountPercentage.toFixed(0)
}

// add to Cart
const addToCart = async (productId) => {
    productStore.addToCart(productId)
    router.push({ name: 'cart' })
}
// add to wishlist
const addProductToWishlist = async (productId) => {
    productStore.addToWishlist(productId)
    router.push({ name: 'wishlist' })

}

function scrollToReviews() {
    const reviewsElement = document.querySelector('.product-reviews');

    if (reviewsElement) {
        reviewsElement.scrollIntoView({
            behavior: 'smooth', 
            block: 'start',     
        });
    }
}

const showSticky = ref(false);
const toggoleStickyProduct = ()=>{
    showSticky.value = !showSticky.value
}
</script>


<template>
    <div id="product-page-container">

        <div class="sticky_product" :class="{showStickyProduct : showSticky}"  @mouseenter="toggoleStickyProduct" @mouseleave="toggoleStickyProduct">
            <div class="img">
                <img :src="productCover" alt="product-image">
            </div>
            <div class="product-info">
                <p>
                    {{ product.name }}
                </p>
                <div class="starts">
                    <i @click="scrollToReviews" v-for="( index) in 5" :key="index" class="fa-solid fa-star"></i>
                </div>
            </div>
            <div class="actions">
                <span>
                    <small>
                        ${{ product.regular_price ?? product.regular_price }}
                    </small>
                        ${{ product.sale_price ? product.sale_price : product.price }}
                </span>
                <button @click="addToCart(product.id)" :disabled="quantity === 0">ADD TO CART</button>
                <button @click="addProductToWishlist(product.id)">
                    <i class="fa-regular fa-heart"></i>
                    Add to wishlist
                </button>
            </div>
            <button id="mobileBtn" @click="scrollToTop">
                <i class="fa-solid fa-hand-point-up"></i>
                Select Options
            </button>
        </div>

        <div @click="ShowOverLay" class="images-overlay" :class="{ ShowOverLay: isVisible }">
            <div class="image-box" @click.stop>
                <img :src="productCover" alt="product-image">
            </div>
            <!-- <i class="fa-solid fa-arrow-right"></i>
            <i class="fa-solid fa-arrow-left"></i> -->
            <i @click="ShowOverLay" class="fa-solid fa-xmark"></i>
        </div>

        <div class="product-details-box">

            <div class="product-images">
                <div class="main">
                    <img :src="productCover" alt="product-image">
                    <div class="full">
                        <i @click="ShowOverLay" class="fa-solid fa-compress"></i>
                    </div>
                </div>
                <div class="options" v-if="productImages && productImages.length > 0">
                    <div>
                        <img :src="productImages[0].url" @click="updateCoverImage(productImages[0])" alt="product-image">
                    </div>
                    <div>
                        <img :src="productImages[1].url" @click="updateCoverImage(productImages[1])" alt="product-image">
                    </div>
                    <div>
                        <img :src="productImages[2].url" @click="updateCoverImage(productImages[2])" alt="product-image">
                    </div>
                    <div>
                        <img :src="productImages[3].url" @click="updateCoverImage(productImages[3])" alt="product-image">
                    </div>
                </div>
                <div v-if="product.sale_price && product.sale_price != '0.00'" class="badge">
                    <p>-{{ getProductDiscount() }}%</p>
                </div>
            </div>

            <div class="product-details">
                <div class="slot">
                    <h1>{{ product.name }}</h1>
                </div>

                <div class="slot stars-reviews">
                    <h4>
                        <span v-if="product.sale_price && product.sale_price != '0.00'">${{ product.regular_price }}</span>
                        ${{ product.sale_price && product.sale_price != '0.00' ? product.sale_price : product.price }}
                    </h4>
                    <div class="stars-box">
                        <i @click="scrollToReviews" v-for="( index) in 5" :key="index" class="fa-solid fa-star"></i>
                    </div>
                    <p>({{ reviewsData.length }} Reviews)</p>
                </div>

                <div class="slot small_description">
                    <p v-html="product.short_description"></p>
                </div>

                <div v-if="quantity > 1" class="slot inStock">
                    <i class="fa-solid fa-warehouse"></i> {{ quantity }} in stock
                </div>
                <div v-else class="slot outOfStock">
                    <i class="fa-solid fa-store-slash"></i>
                    <p>0 products in stock</p>
                </div>

                <div class="slot quantity_btn">
                    <div class="quantity">
                        <button @click="decreaseCount" :disabled="count == 1">-</button>
                        <p>{{ count }}</p>
                        <button @click="increaseCount" :disabled="quantity <= count">+</button>
                    </div>
                    <button @click="addToCart(product.id)" :disabled="quantity === 0">ADD TO CART</button>
                </div>

                <div class="slot wishlist">
                    <button @click="addProductToWishlist(product.id)">
                        <i class="fa-regular fa-heart"></i>
                        Add to wishlist
                    </button>

                    <div class="share">
                        <p>Share : </p>
                        <i class="fa-brands fa-facebook"></i>
                        <i class="fa-brands fa-square-instagram"></i>
                        <i class="fa-brands fa-x-twitter"></i><i class="fa-brands fa-telegram"></i>
                    </div>
                </div>

                <div class="slot discount">
                    <p>Last chance! Sale ends in : </p>
                    <div class="count_down">
                        <div class="count">
                            <p>
                                {{ timeRemaining.days }}
                                <span>Days</span>
                            </p>
                            <p>
                                {{ timeRemaining.hours }}
                                <span>Hr</span>
                            </p>
                            <p>
                                {{ timeRemaining.minutes }}
                                <span>Min</span>
                            </p>
                            <p>
                                {{ timeRemaining.seconds }}
                                <span>Sec</span>
                            </p>
                        </div>
                        <div class="left">
                            <div>
                                <p>Orderd : <span>14</span></p>
                                <p>Items available: : <span>
                                        {{ quantity }}
                                    </span></p>
                            </div>
                            <div class="chart">
                                <div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="slot acc_pay">
                    <p>Guaranteed Safe Checkout</p>
                    <div class="icons">
                        <img src="../assets/icons/paypal-svgrepo-com.svg" alt="">
                        <img src="../assets/icons/mastercard-old-3-svgrepo-com.svg" alt="">
                        <img src="../assets/icons/visa-classic-svgrepo-com.svg" alt="">
                        <img src="../assets/icons/icons8-apple-pay-50.png" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- // Description  -->
        <div class="product-description" @mouseenter="toggoleStickyProduct" @mouseleave="toggoleStickyProduct">
            <header class="section-header">
                <h3>
                    Description
                </h3>
            </header>
            <div v-html="product.description"></div>
        </div>

        <!-- // Reviews  -->
        <div class="product-reviews"  @mouseenter="toggoleStickyProduct" @mouseleave="toggoleStickyProduct">
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

                    <div class="review-container">


                        <div class="review" v-for="(reviewData, index) in visibleReviews" :key="index"
                            :class="{ 'no-img-review': !reviewData.body_urls }">
                            <div class="img-box">
                                <img v-if="reviewData.body_urls" :src="reviewData.body_urls" alt="review-image">
                                <div class="author">
                                    <div :style="{ backgroundColor: getColor(index) }">{{ reviewData.author.charAt(0) }}
                                    </div>
                                    <p>{{ reviewData.author }}</p>
                                </div>
                            </div>
                            <div class="review_body">
                                <div class="text">
                                    <div class="stars-reveiws">
                                        <i v-for="(star, index) in Math.round(reviewData.rating)" :key="index"
                                            class="fa-solid fa-star">
                                        </i>
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <p> {{ reviewData.body_text.length > 150 ? reviewData.body_text.slice(0, 150) + '...' :
                                        reviewData.body_text
                                    }}</p>
                                </div>
                                <div class="comment">
                                    <i class="fa-solid fa-comments"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!loading && currentReviewsLen != reviewsData.length" class="reviews-pagination">
                        <button @click="showMoreReviews">SHOW MORE</button>
                    </div>
                    <div v-if="loading" class="reviews-pagination loader">
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

#product-page-container {
    width: 100%;
    min-height: 100vh;
    font-family: $ff;
    position: relative;

    i {
        cursor: pointer;
    }

    .sticky_product {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 4.5rem;
        background-color: white;
        border-top: rgba(0, 0, 0, 0.06) solid 1px;
        z-index: 3;
        transition: .3s ease-in;
        display: flex;
        padding: 0 2vw;
        transform: translateY(100%);
        opacity: .5;

        &:hover {
            border-top: rgba(0, 0, 0, 0.318) solid 1px;
        }

        >div {
            height: 100%;
        }

        .img {
            width: 15%;
            display: flex;
            justify-content: center;

            >img {
                height: 100%;
                object-fit: contain;
            }
        }

        .product-info {
            min-width: 30%;
            padding: 10px;

            p {
                font-size: .9rem;
            }

            .starts {
                margin-top: 4px;

                >i {
                    color: gold;
                    font-size: .8rem;
                }
            }
        }

        .actions {
            width: 40%;
            margin-left: 15%;
            display: flex;
            align-items: center;
            gap: 2vw;
            padding: 0 5px;
            span {
                    color: #0065fc;
                    font-weight: bold;
                    padding: 0 5px;
                    display: flex;
                    font-size: 1.5rem;
                    >small{
                        color: #555;
                        text-decoration: line-through;
                        padding: 0 3px;
                        font-size: .75rem;
                    }
                }
                >button {
                    width: 12rem;
                    height: 2.5rem;
                    color: white;
                    border: none;
                    background: #2e6bc6;
                    border-radius: 5px;
                    cursor: pointer;

                    &:nth-child(3){
                        background: transparent;
                        color: #555;
                        width: 8rem;

                    }

                }

                button:disabled {
                    cursor: default;
                    opacity: .5;
                }
        }

        #mobileBtn{
            display: none;
        }
    }

    .images-overlay {
        width: 100%;
        height: 100%;
        background: #000000;
        position: fixed;
        display: none;
        top: 0;
        z-index: 999;

        i {
            color: #fff;
            position: absolute;
            font-size: 2rem;
            transform: translateY(-50%);
            top: 50%;
            cursor: pointer;
        }

        .fa-arrow-right {
            right: 2rem;
        }

        .fa-arrow-left {
            left: 2rem;
        }

        .fa-xmark {
            top: 2rem;
            right: 2rem;
        }

        .image-box {
            width: 35rem;
            height: 75vh;
            background: #fff;
            @include flex();

            >img {
                width: 95%;
                margin: 0 2.5%;
                object-fit: contain;
            }
        }
    }

    .product-details-box {
        width: 70%;
        min-height: 80vh;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;

        .product-images {
            width: 50%;
            height: 80vh;
            position: relative;

            >.badge {
                width: 3.5rem;
                height: 3.5rem;
                background: red;
                border-radius: 50%;
                color: #fff;
                font-weight: bold;
                top: 2rem;
                left: 2rem;
                position: absolute;
                @include flex();
                font-size: .9rem;
                z-index: 2;
            }

            .main {
                width: 100%;
                height: 80%;
                @include flex();
                position: relative;

                .full {
                    position: absolute;
                    bottom: 2rem;
                    left: 2rem;
                    color: #555;

                    >i {
                        cursor: pointer;
                        font-size: 1.4rem;
                        transition: .3s ease-in-out;
                        position: relative;

                        &::after {
                            content: 'Click to enlarge';
                            font-size: .65rem;
                            font-family: $ff;
                            position: absolute;
                            width: 8rem;
                            top: 25%;
                            left: 140%;
                            transform: translateX(-50%);
                            opacity: 0;
                            transition: .3s ease-in-out;
                        }

                        &:hover::after {
                            opacity: 1;
                            transform: translateX(0);

                        }
                    }
                }

                >img {
                    width: 80%;
                    height: 80%;
                    object-fit: contain;
                }
            }

            .options {
                width: 100%;
                height: 20%;
                display: flex;

                >div {
                    width: 25%;
                    height: 100%;
                    @include flex();

                    >img {
                        width: 65%;
                        height: 65%;
                        object-fit: contain;
                        transition: .3s ease-in-out;
                        cursor: pointer;
                        opacity: .8;

                        &:hover {
                            transform: scale(1.1);
                            opacity: 1;
                        }
                    }
                }
            }

        }

        .product-details {
            width: 48%;
            min-height: 80vh;
            padding: 1rem 5px;

            >.slot {
                width: 98%;
                min-height: 3rem;
                margin: 10px 1%;
                @include flex($jc: flex-start);

                >h1 {
                    font-weight: normal;
                    padding: 1rem 0;
                }
            }

            >.stars-reviews {
                gap: .5rem;

                p {
                    font-size: .75rem;
                    color: #555;
                }

                i {
                    color: gold;
                }

                h4 {
                    color: #2e6bc6;
                    display: flex;
                    gap: 2px;
                    font-size: 2rem;
                    margin-right: 5px;

                    >span {
                        font-size: .8rem;
                        color: #555;
                        text-decoration: line-through;
                    }
                }
            }

            .small_description {
                color: #555;
                font-size: .9rem;
                padding: 1rem 1rem 1rem 5px;
            }

            .inStock {
                font-size: .8rem;
                user-select: none;

                i {
                    color: green;
                    margin: 0 10px;
                    font-size: 1.3rem;
                }
            }

            .outOfStock {
                font-size: .8rem;
                user-select: none;

                p {
                    text-decoration: line-through;
                    color: #555;
                }

                i {
                    color: rgb(211, 1, 1);
                    margin: 0 10px;
                    font-size: 1.3rem;
                }
            }

            .quantity_btn {
                gap: 1vw;

                .quantity {
                    border: 1px solid #5555557d;
                    width: 10rem;
                    height: 2.5rem;
                    border-radius: 5px;
                    display: flex;
                    align-items: center;

                    >* {
                        width: 33.33%;
                        background: transparent;
                        border: none;
                        color: #555;

                    }

                    >p {
                        border-right: 1px solid #5555557d;
                        border-left: 1px solid #5555557d;
                        text-align: center;
                        user-select: none;
                    }

                    button {
                        cursor: pointer;
                        font-size: 1.2rem;
                    }



                }

                >button {
                    width: 12rem;
                    height: 2.5rem;
                    color: white;
                    border: none;
                    background: #2e6bc6;
                    border-radius: 5px;
                    cursor: pointer;

                }

                button:disabled {
                    cursor: default;
                    opacity: .5;
                }
            }

            .wishlist {
                justify-content: space-between;
                color: #555;
                border-bottom: 1px solid rgba(46, 46, 46, 0.234);
                margin-top: 2rem;

                >button {
                    background: transparent;
                    height: 2.5rem;
                    border: none;
                    padding: 0 1rem;
                    cursor: pointer;
                }

                >.share {
                    @include flex();
                    gap: 10px;

                    p {
                        font-size: .9rem;
                    }

                    i {
                        cursor: pointer;
                        transition: .3s ease-in-out;

                        &:hover {
                            color: #000;
                        }
                    }
                }
            }

            .discount {
                flex-direction: column;
                align-items: flex-start;

                >p {
                    color: #555;
                    font-size: .9rem;
                    padding: 1rem 5px;
                }

                .count_down {
                    width: 100%;
                    height: 5rem;
                    border-bottom: 1px solid rgba(46, 46, 46, 0.234);
                    display: flex;

                    .count {
                        width: 50%;
                        height: 100%;
                        @include flex($jc: space-evenly);

                        >p {
                            display: flex;
                            flex-direction: column;
                            text-align: center;
                            font-weight: bold;
                            font-size: 1.5rem;

                            >span {
                                font-weight: normal;
                                font-size: .8rem;
                            }
                        }
                    }

                    .left {
                        width: 50%;
                        height: 100%;

                        >div {
                            @include flex($jc: space-between);
                            height: 50%;

                            p {
                                color: #555;
                                font-size: .8rem;

                                span {
                                    font-size: 1rem;
                                    font-weight: bold;
                                    color: #000;
                                }
                            }

                            >div {
                                width: 100%;
                                height: 10px;
                                background: #5e5e5e6f;
                                border-radius: 10px;
                                margin: 0 auto;
                                overflow: hidden;

                                >div {
                                    background: #2e6bc6;
                                    width: 30%;
                                    height: 10px;
                                }
                            }
                        }
                    }
                }
            }

            .acc_pay {
                border-bottom: 1px solid rgba(46, 46, 46, 0.234);
                display: flex;
                flex-direction: column;
                align-items: flex-start;

                >p {
                    color: #555;
                    font-size: .9rem;
                    padding: .5rem 5px;
                }

                .icons {
                    width: 100%;
                    height: 3.5rem;
                    margin-top: 10px;

                    >img {
                        height: 80%;
                        object-fit: contain;
                        margin-left: 1vw;
                    }
                }

            }
        }
    }

    .product-description {
        width: 80%;
        min-height: 20vh;
        margin-top: 1rem;
        @include flex();
        flex-direction: column;
        margin: 1rem auto;

        p {
            margin: 1.5rem 0;
            font-size: .9rem;
            color: #555;
            @include flex($jc: flex-start);
            line-height: 2rem;
            >strong {
                color: #212121;
            }
        }
        ul{
            margin: 0 2vw;
        }
        li {
            color: #555555bd;
            padding: 5px;
            list-style: inside;
            font-size: .9rem;
        }

        li::marker {
            color: #0065fc;
        }

        p:has(img) {
            justify-content: center;

            >img {
                object-fit: contain;
                max-width: 80%;
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
            user-select: none;

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
                padding: 1rem 0;

                .reviews-filters {
                    width: 85%;
                    height: 4rem;
                    box-shadow: 0 0 0 0;
                    @include flex($jc: space-between);
                    border-bottom: 1px solid #00000018;

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

                .review-container {
                    width: 90%;
                    min-height: 40vh;
                    margin: 10px auto;
                    display: flex;
                    justify-content: center;

                    flex-wrap: wrap;
                    padding: 1rem 0;
                    gap: 1vw;

                    .review {
                        width: 21rem;
                        min-height: 26rem;
                        border: 1px solid rgba(71, 71, 71, 0.175);
                        border-radius: 5px;
                        overflow: hidden;

                        &:not(.img-box:has(img)) {
                                height: 18rem;
                            }
                        >.img-box {
                            width: 100%;
                            height: 14rem;
                            position: relative;
                            &:not(:has(img)) {
                                height: 5rem;
                            }

                            .author {
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

                        >.review_body {
                            width: 100%;
                            min-height: 8rem;
                            .text {
                                width: 100%;
                                min-height: 8rem;


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

                                >p {
                                    color: #555;
                                    font-size: .8rem;
                                    padding: 1rem 10px;
                                }
                            }

                            .comment {
                                border-top: 1px solid rgba(71, 71, 71, 0.175);
                                height: 3rem;
                                display: flex;
                                align-items: center;
                                justify-content: flex-end;
                                padding: 0 1rem;
                                color: #555;
                                font-size: .8rem;

                                >i {
                                    cursor: pointer;
                                }
                            }

                        }
                    }


                }


                .reviews-pagination {
                    width: 100%;
                    height: 4rem;
                    box-shadow: 0 0 0 0;
                    @include flex();
                    padding: 0 1vw;
                    margin-top: 1.5rem;

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

    .no-img-review {
        min-height: 15rem !important;
    }

@media screen and (max-width: 1315px) {
    .product-details-box {
        width: 85% !important;
    }
}

@media screen and (max-width: 1024px) {
    .product-details-box {
        width: 90% !important;
        flex-direction: column;

        >div {
            width: 100% !important;
        }

        .product-images {
            .main {

                >img {
                    width: 70% !important;
                    height: 70% !important;
                }
            }

            .options {

                >div {
                    width: 25%;
                    height: 100%;
                    @include flex();

                    >img {
                        width: 80% !important;
                        height: 80% !important;
                    }
                }
            }

        }
    }
}

@media screen and (max-width : 768px) {
    .stat {
        flex-direction: column;

        >div {
            width: 100% !important;
        }

    }

    .sticky_product {
        padding: 0 1.5vw !important;
        .img {
          display: none !important;
        }
        .product-info {
            min-width: 40% !important;
        }
        .actions {
            width: 60% !important;
            margin-left: 0% !important;
            gap: 1.5vw !important;
            span {
                    font-size: 1.7rem !important;
                    >small{
                        font-size: 0.8rem !important;
                    }
                }
                >button {
                    width: 10rem !important;
                }
        }
    }
}

@media screen and (max-width: 600px) {
    .product-images {
        height: 70vh !important;
    }

    .images-overlay {


        .image-box {
            width: 80% !important;
            height: 60vh !important;
            background: #fff;
            @include flex();

            >img {
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
            }
        }
    }

    .sticky_product {
        padding: 0 1.5vw !important;
        .img {
          display: none !important;
        }
        .product-info {
            display: none !important;
        }
        .actions {
            width: 100% !important;
            justify-content: space-between;
        }
    }
}

@media screen and (max-width: 450px) {
    .product-images {
        height: 70vh !important;

        .main {
            >img {
                min-width: 90% !important;
                min-height: 90% !important;
            }
        }

    }

    .product-details {
        height: fit-content !important;
        padding: 2rem 5px !important;

        >.stars-reviews {
            p {
                font-size: .7rem !important;
                display: none;
            }

            i {
                font-size: .9rem !important;
                ;
            }

            h4 {
                font-size: 1.7rem !important;

                >span {
                    font-size: .7rem !important;
                    ;
                }
            }
        }

        .quantity_btn {
            gap: 2vh !important;
            flex-direction: column;
            margin-top: 2rem !important;

            .quantity {
                width: 95% !important;
            }

            >button {
                width: 95% !important;
            }
        }

        .count_down {
            flex-direction: column;
            height: fit-content !important;
            padding: 1rem 0;
            gap: 1rem;

            .count {
                width: 100% !important;
                height: 50% !important;
            }

            .left {
                width: 100% !important;
                height: 50% !important;

                >div {
                    margin-top: 1rem;
                }
            }
        }

        .acc_pay {
            .icons {
                >img {
                    height: 80% !important;
                    margin-left: 2vw !important;
                }
            }

        }
    }

    .product-description {
        padding: 1rem 5vw !important;
    }

    .sticky_product {
        padding: 0  !important;
        height: 3rem !important;
        .img {
          display: none !important;
        }
        .product-info {
            display: none !important;
        }
        .actions {
            display: none !important;
        }
        #mobileBtn{
            display: block !important;
            width: 100%;
            text-transform: uppercase;
            border: none;
            background: #0065fc;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            >i{
                transition: .3s ease-in-out;
                transform: translateY(300%);
                font-size: 1.2rem;
                margin-right: 5px;
            }
            &:hover > i{
                transform: translateY(0);
            }
        }
    }
}

@media screen and (max-width:768px) {
    .product-description {
        width: 90% !important;
        padding: 0 5px;
        h1{
           font-size: 1.5rem !important;
        }
    }
}
@media screen and (max-width:450px) {
    .product-description {
        width: 100% !important;
        padding: 0 5px;
    }
}
@media screen and (max-width:320px) {
    .product-details-box {
        width: 98% !important;
    }

    #product-page-container .product-details-box .product-images {
        height: 33rem !important;
    }
}

.ShowOverLay {
    display: flex !important;
    @include flex();
}
.showStickyProduct
{
    transform: translateY(0) !important;
    opacity: 1 !important;
}
</style>



 