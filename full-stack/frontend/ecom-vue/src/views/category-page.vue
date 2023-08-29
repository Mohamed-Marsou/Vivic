<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute, RouterLink , useRouter} from 'vue-router'
import { useProductStore } from '../stores/product';
import api from '../http/api';

const title = ref('Discover Our Stunning Product Collection')
const bannerImg =
    ref('https://img.freepik.com/free-vector/realistic-style-new-smartphone-model_23-2148380821.jpg?w=740&t=st=1692964248~exp=1692964848~hmac=274740091045b4e89aa510904eb08e015caf03b925e2aa348a8a6300ee444e45')

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const products = ref([])
const total = ref(0)
const category = ref(null)
onMounted(async () => {
    scrollToTop()
    if (route.fullPath === '/newset') {
        const res = await productStore.getNewArrivals()
        products.value = res[0].data
        lastPage.value = res.last_page
        total.value = res[0].total

        maxPrice.value = Math.floor(res[1]) + 1

    } else {
        let id = route.params.id
        const res = await productStore.getCategoryProducts(id)
        products.value = res.data.products.data
        lastPage.value = res.data.products.last_page
        total.value = res.data.products.total


        category.value = res.data.category
        title.value = category.value.name
        bannerImg.value = category.value.image

        maxPrice.value = Math.floor(res.data.maxPrice) + 1
    }
})
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

}
function updateMinPrice(event) {
    minPrice.value = parseFloat(event.target.value);
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
const getCoverImg = (p) => {
    const coverImg = p.images.find(image => image.pivot.is_cover === true);
    return coverImg.url
}

// **** Pagination **** 
const loading = ref(false)
const currentPage = ref(1);
const lastPage = ref(2);

const loadMoreProducts = async () => {
    loading.value = true;

    try {
        const nextPage = currentPage.value + 1;
        let responseData;

        if (route.fullPath === '/newset') {
            const response = await api.get(`/products/new-arrivals?page=${nextPage}`);
            responseData = response.data[0];
            lastPage.value = response.data[0].last_page

        } else {
            const response = await api.get(`/products/category/${route.params.id}?page=${nextPage}`);
            responseData = response.data.products;
            lastPage.value = response.data.products.last_page
        }
        // Append new products to the existing array
        products.value = [...products.value, ...responseData.data];
        loading.value = false;

        // Update current page
        currentPage.value = nextPage;
    } catch (error) {
        console.error('Error loading more products:', error);
    }
}

// **** Filter ****
const minPrice = ref(0)
const maxPrice = ref(0)
const rangeFilterSubmit = async () => {
    try {
        const res = await api.get(`/products/range/${minPrice.value}`);
        const newProducts = res.data

        // Remove existing products with matching IDs
        const newProductIds = newProducts.map(product => product.id);
        products.value = products.value.filter(product => !newProductIds.includes(product.id));

        // Add new products to the front of the array
        products.value.unshift(...newProducts);

    } catch (error) {
        console.log('Error in category page: ' + error);
    }
}

const checkBox = reactive({});

const checkedCheckboxes = [];

const handleCheckboxChange = (checkboxName) => {
    if (checkBox[checkboxName]) {
        checkedCheckboxes.push(checkboxName);
    } else {
        const index = checkedCheckboxes.indexOf(checkboxName);
        if (index !== -1) {
            checkedCheckboxes.splice(index, 1);
        }
    }
    sendCheckedCheckboxes()
};

const sendCheckedCheckboxes = async () => {
    if (checkedCheckboxes.length === 0) {
        return;
    }
    let id = route.params.id
    try {
        const checkedData = {
            checkboxes: checkedCheckboxes,
            categoryId: id
        };
        loading.value = true;
        api.get('/products/filterd', {
            params: checkedData
        }).then((res) => {
            if (res.data.data.length > 1) {
                products.value = []
                loading.value = false;
                products.value = res.data.data
            } else {
                loading.value = false;
                alert('No products with Selected Filters')
            }
        })
            .catch((err) => console.log(err))


    } catch (error) {
        console.error('Error sending checked checkboxes:', error);
    }
};
// toggoleFilters
const visibleFilters = ref(false)
const toggoleFilters = ()=>{
    visibleFilters.value = !visibleFilters.value 
}
// add to Cart
const  addToCart =async  (productId) =>
{
    productStore.addToCart(productId)
    router.push({name : 'cart'})

}
</script>
<template>
    <div class="category-main">
        <div class="banner-cut">
            <div>
                <h1>
                    {{ title }}
                </h1>

                <button>Shop now</button>
            </div>
            <img :src="bannerImg" alt="cover-image">
        </div>
        <div id="main-prds-box">
            <div class="filter" :class="{ showFilters : visibleFilters}">

                <div class="filterToggle">
                        <p title="Show Filters" @click="toggoleFilters">Filters
                            <i v-if="!visibleFilters" class="fa-solid fa-filter"></i>
                            <i v-else class="fa-regular fa-circle-xmark"></i>
                        </p>
                </div>

                <div class="price-filter">
                    <p>filter by Price</p>
                    <div class="controlers">
                        <input type="range" :min="0" :max="maxPrice" :value="minPrice" @input="updateMinPrice">

                    </div>
                    <div class="output">
                        <span>
                            Price :
                            <p>
                                ${{ minPrice }} - ${{ maxPrice }}
                            </p>
                        </span>
                        <button @click="rangeFilterSubmit" type="button">Filter</button>
                    </div>
                </div>

                <div class="filterByStatus">
                    <p>FILTER BY STATUS</p>
                    <div>
                        <input type="checkbox" @change="handleCheckboxChange('sale')" v-model="checkBox.sale">
                        On sale
                    </div>
                    <div>
                        <input type="checkbox" @change="handleCheckboxChange('rating')" v-model="checkBox.rating">
                        High ratings
                    </div>
                    <div>
                        <input type="checkbox" @change="handleCheckboxChange('inStock')" v-model="checkBox.inStock">
                        In stock
                    </div>

                </div>

                <div class="top-prods">
                    <p>TOP RATED PRODUCTS</p>
                    <div v-for="i in 3" :key="i" class="top-prd-box">
                        <img src="https://images.squarespace-cdn.com/content/v1/5b56aa1da2772c2408dab0e6/1670822734917-0V4XWINJD2PJ8XFLB7T7/avery-snap-phone-case-by-talia-designs.jpg"
                            alt="">
                        <div>
                            <span>
                                Iphone 12 Pro Case Olive
                            </span>
                            <div class="starts">
                                <i v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                            </div>
                            <p>$954.99</p>
                        </div>
                    </div>

                </div>
            </div>


            <div class="prds-wrapper" :class="{'full-width' : visibleFilters}">
                <div class="dropDownFilters">
                    <p>Showing {{ products.length }} results out of {{ total }}</p>
                    <select>
                        <option disabled selected>Default sorting</option>
                        <option>Sort by Avrege rating</option>
                        <option>Sort by latset</option>
                        <option>Sort by price ASC</option>
                        <option>Sort by price DESC</option>
                    </select>
                </div>
                <div class="section-products">

                    <div v-for="p in products" :key="p.id" class="prd-silde">
                        <div class="image-ctx">
                            <RouterLink :to="{ name: 'product-page', params: { slug: p.slug } }">
                                <img :src="getCoverImg(p)" alt="Product image">
                            </RouterLink>
                            <div class="prd-link" @click="addToCart(p.id)">
                                <i class="fa-solid fa-cart-plus"></i>
                            </div>

                            <div v-if="p.sale_price" class="badge">
                                -{{ getProductDiscount(p) }}%
                            </div>
                        </div>
                        <RouterLink :to="{ name: 'product-page', params: { slug: p.slug } }">
                            {{ p.name }}
                        </RouterLink>
                        <p v-if="p.sale_price">
                            ${{ p.sale_price }}
                        </p>
                        <p v-else>
                            ${{ p.price }}
                        </p>
                        <div class="stars">
                            <i v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                        </div>
                    </div>


                    <div v-if="!loading" class="products-pagination">
                        <button v-if="currentPage != lastPage" @click="loadMoreProducts">SEE MORE</button>
                    </div>
                    <div v-else class="loader">
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
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.category-main {
    width: 100%;
    min-height: 100vh;
    font-family: $ff;

    .banner-cut {
        width: 90%;
        height: 20rem;
        background-color: #fcf0e4;
        margin: 1rem auto;
        @include flex($jc: space-between);
        padding: 1rem 5vw;

        >img {
            width: 17rem;
            height: 17rem;
            object-fit: cover;
            mix-blend-mode: multiply;
        }

        >div {
            h1 {
                padding: 1rem 0;
                text-transform: uppercase;
            }

            button {
                color: #fff;
                background: #2E6BC6;
                width: 8rem;
                height: 3rem;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                text-transform: uppercase;
                margin-top: 1rem;
            }
        }
    }

    #main-prds-box {
        width: 98%;
        min-height: 50vh;
        margin: 2rem auto;
        display: flex;
        position: relative;

        .filter {
            width: 18rem;
            min-height: 50vh;
            padding: 1rem 0;


            >div {
                width: 100%;
                min-height: 8rem;
                padding: 10px;
                border-bottom: 1px solid #55555553;
                margin-bottom: 2rem;

                >p {
                    text-transform: uppercase;
                    font-weight: bold;
                }
            }

            .filterToggle {
                min-height: 3rem !important;
                justify-content: flex-end;
                display: none;
                >p {
                    font-size: .9rem;
                    display: flex;
                    align-items: center;
                    gap: 2px;
                    flex-direction: row-reverse;
                    cursor: pointer;
                    color: #488ffa;
                    position: relative;
                    &::after{
                        content: '';
                        width: 0;
                        height: 2px;
                        background: #488ffa;
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        transition: .3s ease-in-out;
                    }
                    &:hover::after{
                        width: 100%;
                    }
                }
            }


            .price-filter {

                .controlers {
                    width: 100%;
                    height: 2rem;
                    @include flex();
                    margin: 1rem 0;

                    /* Remove default styles */
                    input[type="range"] {
                        appearance: none;
                        width: 90%;
                    }

                    /* Track style */
                    input[type="range"]::-webkit-slider-runnable-track {
                        width: 100%;
                        height: 4px;
                        background: #ccc;
                        border-radius: 2px;
                    }

                    input[type="range"]::-moz-range-track {
                        width: 100%;
                        height: 4px;
                        background: #ccc;
                        border-radius: 2px;
                    }

                    /* Thumb style */
                    input[type="range"]::-webkit-slider-thumb {
                        -webkit-appearance: none;
                        appearance: none;
                        width: 7px;
                        height: 16px;
                        background: #00a2ff;
                        border: 0;
                        cursor: pointer;
                        margin-top: -6px;
                    }


                    /* Highlighted range between thumb and track */
                    input[type="range"]::-webkit-slider-thumb:active {
                        background: #02c061;
                        height: 24px;
                        margin-top: -10px;
                    }


                }

                .output {
                    width: 100%;
                    height: 2rem;
                    @include flex($jc: space-between);
                    padding: 0 1vw;

                    button {
                        color: #fff;
                        background: #2E6BC6;
                        width: 4rem;
                        height: 2rem;
                        border-radius: 5px;
                        border: none;
                        cursor: pointer;
                        text-transform: uppercase;
                    }

                    span {
                        display: flex;
                        color: #555;
                        font-size: .9rem;

                        p {
                            font-weight: bold;

                            &:first-child {
                                margin-left: 5px;
                            }
                        }
                    }
                }

            }

            .filterByStatus {


                >div {
                    padding: .5rem 1vw;
                    color: #555;
                    font-size: .9rem;

                    input {
                        margin-right: 5px;
                    }
                }

            }

            .top-prods {
                .top-prd-box {
                    width: 100%;
                    height: 5rem;
                    margin-top: 1rem;
                    display: flex;

                    >img {
                        width: 20%;
                        height: 100%;
                        object-fit: contain;
                    }

                    >div {
                        width: 80%;
                        height: 100%;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-evenly;
                        padding: 0 1vw;

                        >p {
                            color: #2E6BC6;
                            font-weight: bold;
                        }

                        span {
                            font-size: .8rem;
                            color: #555;
                        }

                        >div {
                            width: fit-content;
                            height: fit-content;

                            i {
                                color: gold;
                                font-size: .8rem;
                            }

                        }

                    }
                }
            }
        }

        .prds-wrapper {
            width: calc(100% - 18rem);
            min-height: 50vh;
            .dropDownFilters {
                width: 95%;
                height: 3rem;
                @include flex($jc: space-between);
                padding: 0 1rem;

                >p {
                    color: #555;
                    font-size: .9rem;
                }

                >select {
                    padding: 10px;
                    border: none;
                    border-bottom: 1px solid #5555552d;
                    color: #555;
                    outline: none;
                }
            }

            .section-products {
                width: 100%;
                min-height: 50vh;
                margin: 1rem 0;
                display: flex;
                justify-content: space-evenly;
                flex-wrap: wrap;

                .prd-silde {
                    width: 18rem;
                    height: 22rem;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    margin-top: 1rem;

                    >p {
                        font-size: .9rem;
                        padding: 5px;
                        margin-top: 10px;
                        color: #555;
                    }

                    a {
                        margin: 1rem 0 0 0;
                        font-weight: bold;
                        color: #555;
                    }

                    .image-ctx {
                        width: 18rem;
                        height: 16rem;
                        position: relative;
                        overflow: hidden;
                        border-radius: 10px;

                        &:hover .prd-link {
                            transform: translateY(0);

                            i {
                                transform: translateY(0);
                                transition-delay: 0.2s;
                            }
                        }

                        >a img {
                            width: 100%;
                            height: 14rem;
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

                            i {
                                transform: translateY(150%);
                                color: #fff;
                                font-size: 1.3rem;
                                transition: .3s ease;
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

                    >.stars {
                        width: 15rem;
                        margin: 0 auto;
                        height: 2rem;
                        @include flex();
                        color: gold;
                    }
                }

                .products-pagination {
                    width: 100%;
                    height: 4rem;
                    margin: 4rem 0;
                    @include flex();

                    >button {
                        width: 10rem;
                        height: 3rem;
                        border: none;
                        border-radius: 50px;
                        background: #0065fc;
                        color: #fff;
                        cursor: pointer;
                        transition: .3s ease-in;

                        &:hover {
                            background: #277bf9;
                        }
                    }
                }

                .loader {
                    width: 100%;
                    height: 4rem;
                    margin: 4rem 0;
                    @include flex();

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
}

@media screen and (max-width: 768px) {
  
    .category-main {
        .banner-cut {
        width: 100%;
        padding: 1rem 2vw;
        >div {
            h1 {
                font-size: 1.5rem;
            }
        }
    }
        #main-prds-box {

            .filter {
                width: 5rem;
                padding: 0 !important;
                
                >div{
                    display: none;
                }
            }
            .prds-wrapper {
                width: calc(100% - 7rem ) !important;
                transition: .3s ease-in-out;
            .dropDownFilters {
                width: 100%;
                justify-content: flex-end;
                >p {
                    display: none;
                }
            }

            .section-products {
                .prd-silde {
                    width: 18rem;
                    height: 24rem !important;
                    .image-ctx {
                        width: 100%;
                        >a img {
                            width: 100%;
                        }
                    }

                    >.stars {
                        width: 100%;
                    }
                }

            }
        }
        }
    }

    .filterToggle {
        display: flex !important;
        border: none !important;
    }

}


@media screen and (max-width:600px) {
  
    .category-main {
        .banner-cut {
        img{
                display: none !important;
            }
        >div {
            h1 {
                font-size: 1.5rem;
            }
            
        }
    }
}
}

.showFilters 
{
    width: 50vw !important;
    z-index: 4 !important;
    height: 100% !important;
    position: absolute;
    left: 0;
    top: 0;
    transition: .3s ease-in-out;
    border-right: 1px solid #5555552d;
    background: #fff;
    >div:not(.filterToggle){
        z-index: 4 !important;
        display: block !important;
    }
}
.full-width
{
    min-width: 100% !important;
}
@media screen and (max-width: 450px) {
    .prd-silde {
        width: 100% !important;
              
    }
    .showFilters 
    {
        width: 70vw !important;
    }

    .category-main {
        .banner-cut {
        >div {
            display: flex;
            flex-direction: column;
            align-items: center;
            h1 {
                font-size: 1.3rem;
                text-align: center;
            }
            
        }
    }
}
}
</style>