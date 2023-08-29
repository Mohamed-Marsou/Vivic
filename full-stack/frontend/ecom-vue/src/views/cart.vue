<script setup>
import {ref,onMounted } from 'vue'
import { useProductStore } from '../stores/product';
import { useAuthtStore } from '../stores/auth';
const productStore = useProductStore()
const authStore = useAuthtStore()
const products = ref([])
onMounted(async()=>{
    window.scrollTo({
        top: 0,
        behavior: 'smooth' 
    });
    const res = await productStore.getInCartProducts()
    console.log(res);

    if (res.products &&  res.products.length > 0) {
        products.value = res.products
    }
})
const getCoverImg =(p) =>{
    const coverImg =  p.images.find(image => image.pivot.is_cover === true);
    return coverImg.url
}
const isShowingShippingFields = ref(false)

const showShippingFields = ()=>{
    isShowingShippingFields.value = !isShowingShippingFields.value
}
const removeItem =async (pId)=>{
    const res = await productStore.removeCartItem(pId)
    if(res)
    {
        const index = products.value.findIndex(product => product.product.id === pId);
          if (index !== -1) {
            products.value.splice(index, 1);
            
          }
    }
} 

</script>
<template>
    <div class="cart-main-box">
        <div v-if="products.products">
        <div class="products-table">
            <div v-for="p in products " :key="p.id" class="cell">
                <div class="left">
                    <div class="cancel">
                        <span title="REMOVE ITEM" @click="removeItem(p.product.id)">X</span>
                    </div>
                    <div class="product-image">
                        <img :src="getCoverImg(p.product)" alt="Product image">
                    </div>
                </div>
                <div class="right">
                    <div class="product-name">
                        <h4>Product</h4>
                        <p>{{ p.product.name }}</p>
                    </div>
                    <div class="price">
                        <h4>Price</h4>
                        <h6><small v-if="p.product.sale_price">${{p.product.sale_price ? p.product.price: ''}}</small> ${{ p.product.sale_price ? p.product.sale_price:p.products.price }} </h6>
                    </div>
                    <div class="quantity">
                        <h4>Quantity</h4>
                        <div class="controllers">
                            <button>-</button>
                            <p>{{ p.quantity }}</p>
                            <button>+</button>
                        </div>
                    </div>
                    <div class="sub-total">
                        <h4>Subtotal</h4>
                        <h5>${{ p.product.price * p.quantity  }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <!-- * mob version  -->
        <div id="product-table-mobile">
            <div class="mb-slot" v-for="p in products " :key="p.id">
                <img :src="getCoverImg(p.product)" alt="Product image">
                <div class="prd">
                    <div class="p-header">
                        <h3>L{{ p.product.name }}</h3>
                        <span title="REMOVE ITEM" @click="removeItem(p.product.id)">X</span>
                    </div>
                    <div class="full-box">
                        <p>Price</p>
                        <p>
                            ${{ p.product.sale_price ? p.product.sale_price:p.product.price }}
                        </p>
                    </div>
                    <div class="full-box">
                        <p>Quantity</p>
                        <div class="controllers">
                            <button>-</button>
                            <p>{{ p.quantity }}</p>
                            <button>+</button>
                        </div>
                    </div>
                    <div class="full-box">
                        <p>Subtotal</p>
                        <h6>${{ p.product.price * p.quantity }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <!-- * mob version  -->

        <div class="copun-container">
            <p>Coupon :</p>
            <div>
                <input type="text" placeholder="Coupon Code ">
                <button>Applay coupon</button>
            </div>
        </div>


        <div class="checkout-container">
            <div class="shipping-details">
                <h2>BILLING DETAILS</h2>
                <div class="slot">
                    <label for="fname">First Name <small>*</small>:</label>
                    <input type="text" id="fname" name="fname" required placeholder="First name">
                </div>
                <div class="slot">
                    <label for="lname">Last Name <small>*</small>:</label>
                    <input type="text" id="lname" name="lname" required placeholder="Last name">
                </div>
                <div class="slot">
                    <label for="country">Country <small>*</small>:</label>
                    <input type="text" id="country" name="country" required placeholder="Enter your country">
                </div>
                <div class="slot">
                    <label for="city">City <small>*</small>:</label>
                    <input type="text" id="city" name="city" required placeholder="Enter your city">
                </div>
                <div class="slot">
                    <label for="street">Street Address <small>*</small>:</label>
                    <input type="text" id="street" name="street" required placeholder="Enter your street address">
                </div>
                <div class="slot">
                    <label for="email">Email <small>*</small>:</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="slot-diffrent-address">
                    <input type="checkbox" @input="showShippingFields">
                    <p>Ship to a different address?</p>
                    <div class="box" :class="{ show : isShowingShippingFields}">
                        <div class="slot">
                            <label for="_fname">First Name <small>*</small>:</label>
                            <input type="text" id="_fname" name="_fname" aria-required="">
                        </div>
                        <div class="slot">
                            <label for="_lname">Last Name <small>*</small>:</label>
                            <input type="text" id="_lname" name="_lname" required>
                        </div>
                        <div class="slot">
                            <label for="_country">Country <small>*</small>:</label>
                            <input type="text" id="_country" name="_country" required placeholder="Enter Country">
                        </div>
                        <div class="slot">
                            <label for="d-city">City <small>*</small>:</label>
                            <input type="text" id="d-city" name="d-city" required placeholder="Enter City">
                        </div>
                        <div class="slot">
                            <label for="d-street">Street Address <small>*</small>:</label>
                            <input type="text" id="d-street" name="d-street" required placeholder="Enter street ">
                        </div>
                        <div class="slot">
                            <label for="d-email">Email <small>*</small>:</label>
                            <input type="d-email" id="d-email" name="d-email" required placeholder="Enter  Email">
                        </div>
                    </div>
                </div>
            </div>
            <div class="shipping-summ">
                <h1>Your Order</h1>
                <div class="full-w">
                    <h4>Product</h4>
                    <h4>Subtotal</h4>
                </div>
                <div v-for="p in products" :key="p.id" class="full-w">
                    <p>{{ p.product.name }}</p>
                    <h4>${{ p.product.sale_price ? (p.product.sale_price * p.quantity): (p.product.price * p.quantity)}}</h4>
                </div>
              
                <div class="full-w">
                    <h5>Subtotal </h5>
                     <!-- TODO CHANGE ME LATER  -->
                    <span>$5124.36</span>
                </div>
                <div class="full-w total">
                    <h2>total </h2>
                        <!-- TODO CHANGE ME LATER  -->
                    <span id="total">$5124.36</span>
                </div>
                <div class="full-w">
                    <img src="https://packnrun.com/wp-content/uploads/2020/12/Payment-Logo-01.png" alt="">
                </div>
                <div class="full-w">
                    <button id="payInit">Chose Payment</button>
                </div>

            </div>
        </div>
        </div>
        <div class="EmptyCart" v-else>
            <!--todo  cahnge me   -->
            <h1>Empty Cart ):</h1>
        </div>
    </div>
</template>

<style lang="scss"  >
@import '@/style/_global.scss';

.cart-main-box {
    width: 100%;
    min-height: 100vh;
    font-family: $ff;
    display: flex;
    flex-direction: column;

    #product-table-mobile {
        display: none;
    }

    .products-table {
        width: 1020px;
        min-height: fit-content;
        margin: 1rem auto;

        .cell {
            width: 100%;
            min-height: fit-content;
            display: flex;
            border-bottom: 1px solid rgba(102, 85, 85, 0.227);
            margin-top: 1rem;

            .left {
                width: 20%;
                height: 7rem;
                display: flex;

                .cancel {
                    width: 10%;
                    height: 100%;
                    @include flex();
                    font-weight: bold;
                    >span {
                        cursor: pointer;
                        color: #555;
                        font-size: .8rem;
                    }
                }

                .product-image {
                    width: 90%;
                    height: 100%;
                    @include flex();

                    >img {
                        width: 80%;
                        height: 80%;
                        object-fit: contain;
                    }
                }

            }

            .right {
                width: 80%;
                height: 7rem;
                display: flex;
                justify-content: space-evenly;

                >div {
                    width: fit-content;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: space-between;
                    padding: 15px 1vw;

                    &:first-child {
                        width: 50%;
                    }

                    h4 {
                        text-transform: uppercase;
                    }

                    p {
                        font-size: .9rem;
                        color: #555;
                    }

                    h6 {
                        display: flex;
                        font-size: 1rem;

                        >small {
                            font-weight: normal;
                            font-size: .8rem;
                            padding: 0 5px;
                            text-decoration: line-through;
                             color: #555;

                        }
                    }

                    h5 {
                        font-size: 1.1rem;
                        color: #007AFF;
                    }

                    .controllers {
                        border: 1px solid rgba(102, 85, 85, 0.456);
                        display: flex;
                        width: 5rem;
                        height: 2rem;
                        border-radius: 20px;
                        overflow: hidden;

                        >* {
                            width: 33%;
                            height: 100%;
                            border: none;
                            @include flex();
                            color: #555;
                            cursor: pointer;
                        }

                        p {
                            border-left: 1px solid rgba(102, 85, 85, 0.456);
                            border-right: 1px solid rgba(102, 85, 85, 0.456);
                        }
                    }
                }

            }
        }
    }


    .copun-container {
        width: 1020px;
        height: 8rem;
        margin: 1rem auto;
        padding: 1rem 1vw;
        border-bottom: 1px solid rgba(102, 85, 85, 0.227);

        p {
            color: #555;
            font-size: .9rem;
        }

        >div {
            display: flex;
            padding: 10px;
            margin: 1rem auto;

            >input {
                border: 1px solid rgba(102, 85, 85, 0.227);
                width: 80%;
                height: 3rem;
                border-radius: 40px;
                padding: 0 15px;
            }

            >button {
                width: 8rem;
                border-radius: 40px;
                border: none;
                color: #fff;
                background: #007AFF;
                margin-left: 5%;
            }

        }

    }

    .checkout-container {
        width: 80vw;
        min-height: 30vh;
        margin: 2rem auto;
        display: flex;

        >div {
            width: 50%;
            min-height: 30vh;
            padding: 1rem 1vw;

            >h2 {
                margin: 0 0 2rem 0;
            }

            .slot {
                width: 100%;
                min-height: 3rem;
                display: flex;
                flex-direction: column;

                label {
                    font-size: .9rem;
                    color: #555;
                }

                small {
                    color: red;
                }

                >input {
                    height: 3rem;
                    border-radius: 20px;
                    border: 1px solid rgba(102, 85, 85, 0.227);
                    margin: .5rem 0;
                    padding: 0 1vw;
                }
            }

            .slot-diffrent-address {
                width: 100%;
                min-height: 3rem;
                display: flex;
                padding: 1rem 0;
                align-items: center;
                gap: .7vw;
                flex-wrap: wrap;

                >p {
                    font-size: .9rem;
                }

                .box {
                    width: 100%;
                    min-height: 20vh;
                    display: none;
                }
            }
        }

        .shipping-summ {

            >h1 {
                font-size: 1.5rem;
                text-transform: uppercase;
                text-align: center;
                margin-bottom: 2rem;
            }

            .full-w {
                width: 100%;
                min-height: 4rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 1vw;

                >p {
                    font-size: .9rem;
                    color: #555;
                }

                >h2,
                h4,
                h5 {
                    text-transform: uppercase;
                }

                span {
                    color: #007AFF;
                }

                #total {
                    font-weight: bold;
                }

                >img {
                    margin: 1rem auto;
                    height: 8rem;
                    object-fit: contain;
                }

                #payInit {
                    width: 100%;
                    height: 3rem;
                    color: #fff;
                    border: #007AFF;
                    background: #2c2e2f;
                    cursor: pointer;
                    text-transform: uppercase;
                    font-weight: bold;
                    transition: .3s ease;

                    &:hover {
                        background: #000;
                    }
                }
            }

            .total {
                border-bottom: 1px solid rgba(102, 85, 85, 0.227);
                margin-bottom: 2rem;
            }
        }
    }
}
.EmptyCart
{

    width: 100%;
    height: 80vh;
    @include flex();
    color: #555;
}
.show {
    display: block !important; 
}
@media screen and (max-width : 1024px) {
    .cart-main-box {
        .products-table {
            width: 95%;
        }

        .copun-container {
            width: 95%;
        }
    }
}

@media screen and (max-width : 768px) {

    .products-table {
        display: none;
    }

    #product-table-mobile {
        display: flex !important;
        flex-direction: column;
        width: 95%;
        min-height: fit-content;
        margin: 1rem auto;

        .mb-slot {
            width: 100%;
            min-height: 12rem;
            border-bottom: 1px solid rgba(113, 113, 113, 0.256);
            margin: 10px 0;
            display: flex;

            >img {
                width: 20%;
                object-fit: contain;

            }

            .prd {
                width: 80%;
                height: 10rem;
                .full-box {
            border-bottom: 1px solid rgba(0, 0, 0, 0.212);
                
                }
                .p-header,
                .full-box {
                    width: 100%;
                    height: fit-content;
                    display: flex;
                    justify-content: space-between;
                    font-size: .9rem;
                    padding: 10px;


                    span {
                        font-weight: bold;
                        cursor: pointer;
                    }

                    h6 {
                        font-size: 1rem;
                        color: #007AFF;
                    }

                    .controllers {
                        border: 1px solid rgba(102, 85, 85, 0.456);
                        display: flex;
                        width: 5rem;
                        height: 2rem;
                        border-radius: 20px;
                        overflow: hidden;

                        >* {
                            width: 33%;
                            height: 100%;
                            border: none;
                            @include flex();
                            color: #555;
                            cursor: pointer;
                        }

                        p {
                            border-left: 1px solid rgba(102, 85, 85, 0.456);
                            border-right: 1px solid rgba(102, 85, 85, 0.456);
                        }
                    }
                }
            }
        }
    }

    .checkout-container {
        width: 90vw !important;
        flex-direction: column;

        >div {
            width: 100% !important;
        }
    }

}

@media screen and (max-width :500px) {


    #product-table-mobile {
        width: 98%;

        .mb-slot {
            width: 100%;
            min-height: 14rem;

            >img {
                width: 25%;
            }

            .prd {
                width: 75%;
                height: fit-content !important;

                .p-header,
                .full-box {
                    h3 {
                        font-size: .9rem;
                    }

                    p {
                        font-size: .8rem;
                    }

                    h6 {
                        font-size: 1.2rem;
                    }

                    .controllers {
                        width: 6rem;
                        height: 2rem;
                    }
                }
            }
        }
    }
    .copun-container {
        min-height:12rem !important;
        padding: 5px 1vw !important;
        >div {
            display: flex;
            padding: 10px;
            margin: 1rem auto;
             min-height: 8rem;
            flex-direction: column;
            >input {
                width: 100% !important;
                height: 3rem;
            }

            >button {
                height: 3rem;
                margin: 1rem 0 !important;
            }

        }

    }
}</style>