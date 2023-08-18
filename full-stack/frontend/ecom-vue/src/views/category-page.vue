<script setup>
import { ref } from 'vue'

const minPrice = ref(0)
const maxPrice = ref(150)

function updateMinPrice(event) {
    minPrice.value = parseFloat(event.target.value);
}
</script>
<template>
    <div class="category-main">
        <div class="banner-cut">
            <div>
                <h1>
                    Lorem ipsum dolor sit amet consectetur.
                </h1>
                <button>Shop now</button>
            </div>
            <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/08/w-accessories-product-link-3-2.jpg"
                alt="cover-image">
        </div>
        <div id="main-prds-box">
            <div class="filter">
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
                        <button type="button">Filter</button>
                    </div>
                </div>
                <div class="filterByStatus">
                    <p>FILTER BY STATUS</p>
                    <div>
                        <input type="checkbox">
                        On sale
                    </div>
                    <div>
                        <input type="checkbox">
                        high ratings
                    </div>
                    <div>
                        <input type="checkbox">
                        In stock
                    </div>
                    <div>
                        <input type="checkbox">
                        New in
                    </div>
                </div>
                <div class="top-prods">
                    <p>TOP RATED PRODUCTS</p>
                    <div v-for="i in 3" :key="i" class="top-prd-box">
                        <img src="https://a6n4d3q9.rocketcdn.me/accessories/wp-content/uploads/sites/7/2022/09/green-case-back-430x491.jpg.webp"
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


            <div class="prds-wrapper">
                <div class="dropDownFilters">
                    <p>Showing 6 results</p>
                    <select>
                        <option disabled selected>Default sorting</option>
                        <option>Sort by popularity</option>
                        <option>Sort by Avrege rating</option>
                        <option>Sort by latset</option>
                        <option>Sort by price : low to high</option>
                        <option>Sort by price : high to low</option>
                    </select>
                </div>
                <div class="section-products">

                    <div v-for="i in 12" :key="i" class="prd-silde">
                        <div class="image-ctx">
                            <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/w-accessories-product-15-430x491.jpg"
                                alt="product-image">

                            <div class="prd-link">
                                <i class="fa-solid fa-cart-plus"></i>
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
        width: 90%;
        min-height: 50vh;
        margin: 2rem auto;
        display: flex;

        .filter {
            width: 30%;
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
                        width: 15%;
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
            width: 70%;
            min-height: 50vh;

            .dropDownFilters {
                width: 100%;
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

                            i {
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


            }
        }
    }
}</style>