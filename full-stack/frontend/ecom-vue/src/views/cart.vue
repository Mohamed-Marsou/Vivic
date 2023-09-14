<script setup>
import { ref, onMounted } from 'vue'
import { useProductStore } from '../stores/product';
import { useAuthtStore } from '../stores/auth';
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'
import { loadStripe } from '@stripe/stripe-js';
import countryCodes from '../assets/countries'
import api from '../http/api';
import Loading from '../components/build/loading.vue'
import axios from 'axios';

const router = useRouter()
const productStore = useProductStore()
const authStore = useAuthtStore()
let products = ref([])
const loaded = ref(false)
const VITE_STRIPE_PUBLIC_KEY = import.meta.env.VITE_PK_STRIPE

const showCouponMsg = ref(false)
const couponCode = ref('')
let stripe = {}

onMounted(async () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    stripe = loadStripe(VITE_STRIPE_PUBLIC_KEY);
    stripe.then((s) => {
        stripe = s
        const elements = stripe.elements();
        cardElement = elements.create('card');
    });
    await getInCartProductsList()
    loaded.value = true;
})
async function getInCartProductsList() {
    const res = await productStore.getInCartProducts()
    if (res) {
        if (res.products !== undefined) {
            products.value = res.products;
        } else {
            products.value = res;
        }
    } else {
        products.value = []
    }
}
const getCoverImg = (p) => {
    const coverImg = p.images.find(image => image.pivot.is_cover === true);
    return coverImg.url
}


const removeItem = async (pId) => {
    await productStore.removeCartItem(pId)
    await getInCartProductsList()
}

const decreaseCount = async (product) => {

    if (authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id;
        const authToken = Cookies.get('auth-token');
        try {
            const response = await api.put(`/product/cart/decrease/${userId}/${product.product.id}`, {
                quantity: product.quantity - 1
            }, {
                headers: {
                    Authorization: `Bearer ${authToken}`
                }
            });

            // Update the inCart products with the updated response
            const updatedProduct = response.data.product;
            const updatedProducts = products.value.map(item => {
                if (item.product.id === updatedProduct.id) {
                    return {
                        product: updatedProduct,
                        quantity: updatedProduct.quantity
                    };
                }
                return item;
            });

            products.value = updatedProducts;
            if (response.status === 200) {
                product.quantity--
            }
        } catch (error) {
            console.error('Error updating item quantity in cart:', error);
        }

    } else {
        // GUEST USER
        let stock = product.product.inStock;
        let quantity = product.quantity;

        if (quantity > 1 && quantity <= stock) {
            // Update the quantity in localStorage and products array
            product.quantity--;
            updateLocalStorage(product.product.id, -1);
            console.log('Quantity decreased successfully.');
        } else if (quantity === 1) {
            // Remove the product from localStorage and products array
            products = products.filter(item => item.product.id !== product.product.id);
            updateLocalStorage(product.product.id, 0);
            console.log('Product removed successfully.');
        } else {
            console.log('Cannot decrease quantity. Stock and quantity are equal or less than 1.');
        }
    }
};
const increaseCount = async (product) => {

    if (authStore.isAuth) {
        const authToken = Cookies.get('auth-token')
        const userId = JSON.parse(Cookies.get('auth-user')).id
        const pId = product.product.id

        const response = await api.put(`/product/cart/increase/${userId}/${pId}`, {
            headers: {
                Authorization: `Bearer ${authToken}`
            }
        })
        if (response.status === 200) {
            product.quantity++
        }
    } else {
        // GUEST USER
        let stock = product.product.inStock;
        let quantity = product.quantity;
        if (quantity < stock) {
            // Update the quantity in localStorage and products array
            product.quantity++;
            updateLocalStorage(product.product.id, 1);
            console.log('Quantity increased successfully.');
        } else {
            console.log('Cannot increase quantity. Exceeds stock limit.');
        }
    }
}
// helper func //
const updateLocalStorage = (productId, quantityChange) => {
    let inCart = JSON.parse(localStorage.getItem('inCart')) || [];
    let productIndex = inCart.findIndex(item => item.productId === productId);

    if (productIndex !== -1) {
        if (quantityChange > 0) {
            inCart[productIndex].quantity++;
        } else if (quantityChange < 0 && inCart[productIndex].quantity > 1) {
            inCart[productIndex].quantity--;
        } else {
            inCart.splice(productIndex, 1);
        }
        localStorage.setItem('inCart', JSON.stringify(inCart));
    }
};

function getTotalAmount() {
    let totalAmount = 0;

    products.value.forEach(product => {
        const price = product.product.sale_price ? parseFloat(product.product.sale_price) : parseFloat(product.product.price);
        totalAmount += price * product.quantity;
    });

    return totalAmount.toFixed(2);
}

const handleCouponSub = () => {
    showCouponMsg.value = true
    setTimeout(() => {
        showCouponMsg.value = false
        couponCode.value = ''
    }, 3000)
}


// ------- user form
const countries = ref(countryCodes)

// User Data
const userFname = ref('');
const userFnameError = ref('');

const userLname = ref('');
const userLnameError = ref('');

const userEmail = ref('');
const userEmailError = ref('');

const userCountry = ref('Select your country');
const userCountryError = ref('');

const userAddress = ref('');
const userAddressError = ref('');

const userCity = ref('');
const userCityError = ref('');
// is Diffrent address 
const isShowingShippingFields = ref(false)
const showShippingFields = () => {
    isShowingShippingFields.value = !isShowingShippingFields.value
}
// Shipment Data
const shipmentFname = ref('');
const shipmentFnameError = ref('');

const shipmentLname = ref('');
const shipmentLnameError = ref('');

const shipmentCountry = ref('Select your shipment country');
const shipmentCountryError = ref('');

const shipmentCity = ref('');
const shipmentCityError = ref('');

const shipmentAddress = ref('');
const shipmentAddressError = ref('');

// --------- payInit
const paymentInit = ref(false)
let cardElement = {}

const payInit = async (e) => {

    if (validateUserData() && (!isShowingShippingFields.value || validateShipmentData())) {
        // Hide PAY NOW BTN 
        e.target.parentNode.style.display = 'none';
        // SHOW CONTAINER 
        paymentInit.value = true;
        // ! mount the credit card "input" to the DOM
        cardElement.mount('#payment-element');
        //! Initialize the PayPal SDK
        paypal
            .Buttons({
                style: {
                    layout: 'horizontal',
                    tagline: 'true',
                    color: 'black'
                },
                createOrder: (data, actions) => {

                    return actions.order.create({
                        purchase_units: [
                            {
                                amount: {
                                    currency_code: 'USD',
                                    value: getTotalAmount(),
                                },
                            },
                        ],
                    });
                },
                onApprove: (data, actions) => {
                    return actions.order.capture().then(response => {
                        handlePaypalSubmission(response);
                    });
                },
                onError: err => {
                    console.error('Error during PayPal payment:', err);
                },
            })
            .render('#paypal-button-container');
    }
}

const validateUserData = () => {
    const namePattern = /^[a-zA-Z\s]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    userFnameError.value = userFname.value.length >= 3 && namePattern.test(userFname.value) ? '' : 'Invalid first name';
    userLnameError.value = userLname.value.length >= 3 && namePattern.test(userLname.value) ? '' : 'Invalid last name';
    userEmailError.value = emailPattern.test(userEmail.value) ? '' : 'Invalid email';
    userCountryError.value = userCountry.value !== 'Select your country' ? '' : 'Select a country';
    userCityError.value = userCity.value.length >= 3 ? '' : 'Invalid city';
    userAddressError.value = userAddress.value.length > 3 ? '' : 'Invalid address';

    return (
        userFnameError.value === '' &&
        userLnameError.value === '' &&
        userEmailError.value === '' &&
        userCountryError.value === '' &&
        userCityError.value === '' &&
        userAddressError.value === ''
    );
}

const validateShipmentData = () => {
    const namePattern = /^[a-zA-Z\s]+$/;

    shipmentFnameError.value = shipmentFname.value.length >= 3 && namePattern.test(shipmentFname.value) ? '' : 'Invalid first name';
    shipmentLnameError.value = shipmentLname.value.length >= 3 && namePattern.test(shipmentLname.value) ? '' : 'Invalid last name';
    shipmentCountryError.value = shipmentCountry.value !== 'Select your shipment country' ? '' : 'Select a country';
    shipmentCityError.value = shipmentCity.value.length >= 3 ? '' : 'Invalid city';
    shipmentAddressError.value = shipmentAddress.value.length > 3 ? '' : 'Invalid address';

    return (
        shipmentFnameError.value === '' &&
        shipmentLnameError.value === '' &&
        shipmentCountryError.value === '' &&
        shipmentCityError.value === '' &&
        shipmentAddressError.value === ''
    );
};


///////////////////////////// ------------------------------- !!! Error Handling !!!
/////////////////////////////
/////////////////////////////
const isProcessing = ref(false)
const costomerOrder = ref({})
// handling Stripe payment
const stripePaymentSubmit = async () => {

    isProcessing.value = true

    // Create a payment method using the Stripe API.
    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: userFname.value + ' ' + userLname.value,
            email: userEmail.value,
            address: {
                city: userCity.value,
                line1: userAddress.value,
                country: userCountry.value.split(":")[0]
            },
        },
    });


    if (error) {
        throw new Error(error.message);
    }

    // Prepare the payload for the WordPress API.
    const orderdProducts = products.value.map(item => ({
        quantity: item.quantity,
        product_id: item.product.id,
        name: item.product.name,
        price: item.product.sale_price ? (item.product.sale_price).toString() : (item.product.price).toString(),
        total: item.product.sale_price ?
            (item.product.sale_price * item.quantity).toString() : (item.product.price * item.quantity).toString()
    }));

    // order details for the customer.
    costomerOrder.value.payment_method_id = paymentMethod.id;
    costomerOrder.value.amount = getTotalAmount();
    costomerOrder.value.userId = authStore.isAuth ? JSON.parse(Cookies.get('auth-user')).id : null;


    const wordpressPayload = {
        status: 'processing',
        currency: 'USD',
        total: costomerOrder.value.amount,
        customer_id: costomerOrder.value.userId,
        billing: {
            first_name: userFname.value,
            last_name: userLname.value,
            address_1: userAddress.value,
            country: userCountry.value.split(":")[0],
            city: userCity.value,
            email: userEmail.value,
        },
        shipping: {
            first_name: shipmentFname.value ? shipmentFname.value : userFname.value,
            last_name: shipmentLname.value ? shipmentLname.value : userLname.value,
            address_1: shipmentAddress.value ? shipmentAddress.value : userAddress.value,
            country: shipmentCountry.value ? shipmentCountry.value.split(":")[0] : userCountry.value.split(":")[0],
            city: shipmentCity.value ? shipmentCity.value : shipmentCity.value,
        },
        user_id: costomerOrder.value.userId,
        line_items: orderdProducts,
    };
    try {


        // Post order details to the WordPress API.
        const wordpressResponse = await axios.post(`${import.meta.env.VITE_WOO_URL}/orders`, wordpressPayload
            , {
                headers: { "Authorization": basicAuth(import.meta.env.VITE_WOO_CK, import.meta.env.VITE_WOO_CS) }
            });

        // 
        costomerOrder.value.wp_order_id = wordpressResponse.data.id;
        costomerOrder.value.costumerName = wordpressResponse.data.billing.first_name + " " + wordpressResponse.data.billing.last_name
        costomerOrder.value.costumerEmail = wordpressResponse.data.billing.email
        costomerOrder.value.costumerAddress = wordpressResponse.data.billing.address_1
        costomerOrder.value.costumerCity = wordpressResponse.data.billing.city
        costomerOrder.value.costumerCountry = userCountry.value

        // --  add a private note to the WooCommerce order
        const note = `Order Tracking Number is : ${costomerOrder.value.wp_order_id}`
        await axios.post(
            `${import.meta.env.VITE_WOO_URL}/orders/${costomerOrder.value.wp_order_id}/notes`,
            {
                note: note,
                customer_note: true
            },
            {
                headers: {
                    Authorization: basicAuth(import.meta.env.VITE_WOO_CK, import.meta.env.VITE_WOO_CS)
                }
            }
        )
        /* ***  
         * HERE WE POST THE OUR API 
         * TO HANDLE SUBMITING ORDER DETAILS
         * TO OUR DATABASE AND STRIPE
         * ***/
        const res = await api.post('/order', costomerOrder.value);

        const laravelPayload = {
            products: orderdProducts,
            order_id: res.data.order_id
        };
        // Fill Order Prods
        await api.post('/orders/add', laravelPayload)
        // Clear user Cart     
        clearUserCart()
        // Redirect user to the success page with the order ID.
        router.push({
            path: '/success',
            query: {
                orderId: costomerOrder.value.wp_order_id
            }
        });

    } catch (error) {
        isProcessing.value = false;
        // Log and handle the error.
        console.error('Error during order submission:', error);
        // Redirect the user to the error page.
        router.push('/error');
    }

}

function basicAuth(key, secret) {
    let hash = btoa(key + ':' + secret);
    return "Basic " + hash;
}
async function clearUserCart() {
    try {

        const id = authStore.isAuth ? JSON.parse(Cookies.get('auth-user')).id : null;
        if (id) {
            // User is logged in, delete their cart via API
            await api.delete(`/user/cart/clear/${id}`);
            products.value = []
            productStore.inCartCount = 0
        } else {
            // User is not logged in, clear 'inCart' from localStorage
            delete localStorage['inCart']
            products.value = []
            productStore.inCartCount = 0
        }
    } catch (error) {
        // Handle any errors that might occur during ID retrieval or API request
        console.error('Error:', error);
    }
}


// TODO implemention needed //TODO implemention needed
//* handel Paypal on succ payment
const handlePaypalSubmission = async (res) => {
    console.log(res);
    // if (res.status === "COMPLETED")
    //     // Redirect user to success page
    //     router.push({
    //         path: '/success',
    //         query: {
    //             orderId: res.id
    //         }
    //     });  
}
</script>
<template>
    <div class="cart-main-box">
        <div v-if="!loaded" class="loader-loader-box">
            <Loading />
        </div>
        <div v-else-if="products && products.length > 0">
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
                            <h6><small v-if="p.product.sale_price">${{ p.product.price }}</small>
                                ${{ p.product.sale_price ? p.product.sale_price : p.product.price }} </h6>
                        </div>
                        <div class="quantity">
                            <h4>Quantity</h4>
                            <div class="controllers">
                                <button :disabled="p.quantity == 1" @click="decreaseCount(p)">-</button>
                                <p>{{ p.quantity }}</p>
                                <button :disabled="p.quantity == p.product.inStock" @click="increaseCount(p)">+</button>
                            </div>
                        </div>
                        <div class="sub-total">
                            <h4>Subtotal</h4>
                            <h5>${{ p.product.sale_price ?
                                (p.product.sale_price * p.quantity).toFixed(2) :
                                (p.product.price * p.quantity).toFixed(2)
                            }}</h5>
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
                            <h3>{{ p.product.name }}</h3>
                            <span title="REMOVE ITEM" @click="removeItem(p.product.id)">X</span>
                        </div>
                        <div class="full-box">
                            <p>Price</p>
                            <p>
                                ${{ p.product.sale_price ? p.product.sale_price : p.product.price }}
                            </p>
                        </div>
                        <div class="full-box">
                            <p>Quantity</p>
                            <div class="controllers">
                                <button :disabled="p.quantity == 1" @click="decreaseCount(p)">-</button>
                                <p>{{ p.quantity }}</p>
                                <button :disabled="p.quantity == p.product.inStock" @click="increaseCount(p)">+</button>
                            </div>
                        </div>
                        <div class="full-box">
                            <p>Subtotal</p>
                            <h6>${{ p.product.sale_price ?
                                (p.product.sale_price * p.quantity).toFixed(2) :
                                (p.product.price * p.quantity).toFixed(2)
                            }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * mob version  -->

            <div class="copun-container">
                <p>Coupon :</p>
                <small v-if="showCouponMsg">Sorry no coupon matched , try again</small>
                <div>
                    <input type="text" placeholder="Coupon Code " v-model="couponCode">
                    <button @click="handleCouponSub">Applay coupon</button>
                </div>
            </div>


            <div class="checkout-container">
                <div class="shipping-details">
                    <h2>BILLING DETAILS</h2>

                    <div class="slot">
                        <label for="fname">First Name <small>*</small>:</label>
                        <small class="error-message">{{ userFnameError }}</small>
                        <input type="text" id="fname" name="fname" v-model="userFname" :disabled="isProcessing" required
                            placeholder="First name">
                    </div>
                    <div class="slot">
                        <label for="lname">Last Name <small>*</small>:</label>
                        <small class="error-message">{{ userLnameError }}</small>
                        <input type="text" id="lname" name="lname" v-model="userLname" :disabled="isProcessing" required
                            placeholder="Last name">
                    </div>
                    <div class="slot">
                        <label for="email">Email <small>*</small>:</label>
                        <small class="error-message">{{ userEmailError }}</small>
                        <input type="email" id="email" name="email" v-model="userEmail" :disabled="isProcessing" required
                            placeholder="Enter your email">
                    </div>
                    <div class="slot">
                        <label for="country">Country <small>*</small>:</label>
                        <small class="error-message">{{ userCountryError }}</small>
                        <select name="country" id="country" v-model="userCountry" :disabled="isProcessing">
                            <option disabled selected value="">Select your country</option>
                            <option v-for="(c, index) in countries" :key="index" :value="c.code + ': ' + c.name">
                                {{ c.name }}
                            </option>
                        </select>
                    </div>
                    <div class="slot">
                        <label for="city">City <small>*</small>:</label>
                        <small class="error-message">{{ userCityError }}</small>
                        <input type="text" id="city" name="city" v-model="userCity" :disabled="isProcessing" required
                            placeholder="Enter your city">
                    </div>
                    <div class="slot">
                        <label for="street">Street Address <small>*</small>:</label>
                        <small class="error-message">{{ userAddressError }}</small>
                        <input type="text" id="street" name="street" v-model="userAddress" :disabled="isProcessing" required
                            placeholder="Enter your street address">
                    </div>

                    <!--? Diffrent Shipment Details  -->
                    <div class="slot-diffrent-address">

                        <input type="checkbox" name="showMoreFields" id="showMoreFields" @input="showShippingFields"
                            :disabled="isProcessing">
                        <label for="showMoreFields">Ship to a different address?</label>

                        <div class="box" :class="{ show: isShowingShippingFields }">
                            <div class="slot">
                                <label for="_fname">First Name <small>*</small>:</label>
                                <small class="error-message">{{ shipmentFnameError }}</small>
                                <input type="text" id="_fname" name="_fname" v-model="shipmentFname"
                                    :disabled="isProcessing" required>
                            </div>
                            <div class="slot">
                                <label for="_lname">Last Name <small>*</small>:</label>
                                <small class="error-message">{{ shipmentLnameError }}</small>
                                <input type="text" id="_lname" name="_lname" v-model="shipmentLname"
                                    :disabled="isProcessing" required>
                            </div>
                            <div class="slot">
                                <label for="_country">Shipment country <small>*</small>:</label>
                                <small class="error-message">{{ shipmentCountryError }}</small>
                                <select name="_country" id="_country" v-model="shipmentCountry" :disabled="isProcessing">
                                    <option disabled selected value="">Select your shipment country</option>
                                    <option v-for="(c, index) in countries" :key="index" :value="c.code + ': ' + c.name">
                                        {{ c.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="slot">
                                <label for="d-city">City <small>*</small>:</label>
                                <small class="error-message">{{ shipmentCityError }}</small>
                                <input type="text" id="d-city" name="d-city" v-model="shipmentCity" :disabled="isProcessing"
                                    required placeholder="Enter City">
                            </div>
                            <div class="slot">
                                <label for="d-street">Street Address <small>*</small>:</label>
                                <small class="error-message">{{ shipmentAddressError }}</small>
                                <input type="text" id="d-street" name="d-street" v-model="shipmentAddress"
                                    :disabled="isProcessing" required placeholder="Enter street ">
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
                        <h4>${{ p.product.sale_price ?
                            (p.product.sale_price * p.quantity).toFixed(2) :
                            (p.product.price * p.quantity).toFixed(2)
                        }}
                        </h4>
                    </div>

                    <div class="full-w">
                        <h5>Subtotal </h5>
                        <span>${{ getTotalAmount() }}</span>
                    </div>
                    <div class="full-w total">
                        <h2>total </h2>
                        <span id="total">${{ getTotalAmount() }}</span>
                    </div>
                    <div class="full-w">
                        <img src="https://packnrun.com/wp-content/uploads/2020/12/Payment-Logo-01.png" alt="">
                    </div>
                    <div class="full-w">
                        <button id="payInit" @click="payInit($event)">Chose Payment</button>
                    </div>
                    <!--????????????????????????? Pyament  ?????????????????????????-->
                    <div class="paymentFieldsHolder" :class="{ paymenFieldsVsible: paymentInit }">
                        <p>Credit card : </p>
                        <!--? Stripe will create form elements here -->
                        <div class="p-cell">
                            <div id="payment-element"></div>
                        </div>
                        <button @click="stripePaymentSubmit" :disabled="isProcessing">
                            PAY NOW
                            <!-- // Loader -->
                            <div class="lds-ring" :class="{ 'clicked': isProcessing }">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </button>
                        <span>Or</span>
                        <!--? Paypal will be here -->
                        <div class="paypal">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="EmptyCart" v-else>
            <h1>Your Cart is Empty</h1>
            <p>
                It's time to fill your cart with the products you love.<br /> Add items and enjoy your shopping journey!
            </p>
            <button>
                <RouterLink :to="{ name: 'home' }">
                    Back Shopping
                </RouterLink>
            </button>
            <img src="../assets/images/wired-outline-146-basket-trolley-shopping-card.gif" alt="">
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

    .loader-loader-box {
        width: 100%;
        height: 100vh;
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

                        button[disabled] {
                            cursor: default;
                            opacity: .5;
                        }
                    }
                }

            }
        }
    }


    .copun-container {
        width: 1020px;
        min-height: 8rem;
        margin: 1rem auto;
        padding: 1rem 1vw;
        border-bottom: 1px solid rgba(102, 85, 85, 0.227);

        small {
            color: red;
        }

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
                    margin-left: .5vw;
                    margin-top: 5px;
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

                >select {
                    width: 10rem;
                    height: 3rem;
                    padding: 0 5px;
                    border-radius: 50px;
                    border: 1px solid rgba(102, 85, 85, 0.227);
                    color: #555;
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

                >label {
                    font-size: .9rem;
                    cursor: pointer;
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
                    font-size: 1.5rem;
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

            .paymentFieldsHolder {
                width: 100%;
                min-height: 10rem;
                border: 1px dotted #2c2e2f72;
                flex-direction: column;
                justify-content: space-evenly;
                align-items: center;
                display: none;
                transition: .3s ease-in-out;
                margin-top: 1rem;
                padding: 1rem 0;

                >button {
                    width: 97%;
                    height: 3.5rem;
                    color: #fff;
                    border: none;
                    background: #2c2e2f;
                    cursor: pointer;
                    text-transform: uppercase;
                    font-weight: bold;
                    transition: .3s ease;
                    margin-top: 1rem;
                    @include flex();
                    gap: 10px;

                    &:hover {
                        background: #000;
                    }

                    .lds-ring {
                        display: inline-block;
                        position: relative;
                        width: 50px;
                        height: 100%;
                        display: none;
                    }

                    .lds-ring div {
                        box-sizing: border-box;
                        display: block;
                        position: absolute;
                        width: 30px;
                        height: 30px;
                        margin: 10px 0 0 0;
                        border: 4px solid #fff;
                        border-radius: 50%;
                        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                        border-color: #fff transparent transparent transparent;
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

                button[disabled] {
                    opacity: .5;
                    cursor: wait;
                }
                >p{
                    color: #555;
                    font-size: .9rem;
                    align-self: flex-start;
                    padding: 0 10px;
                }
                >span {
                    color: #55555578;
                    font-weight: bold;
                    position: relative;
                    margin: 2rem 0;

                    &::after {
                        position: absolute;
                        right: 150%;
                        top: 50%;
                        background: #55555525;
                        width: 15vw;
                        height: 2px;
                        content: '';
                    }

                    &::before {
                        position: absolute;
                        left: 150%;
                        top: 50%;
                        background: #55555525;
                        width: 15vw;
                        height: 2px;
                        content: '';
                    }
                }
            }
        }
    }
}

input[disabled] {
    opacity: .5;
}

.p-cell {
    width: 97%;
    height: 4.5rem;

    >* {
        border: 1px solid rgba(102, 85, 85, 0.227);
        padding: 1rem 5px;
        width: 100%;
        height: 3.5rem;
        margin-top: .5rem;
    }
}

.paypal {
    width: 97%;
}

.EmptyCart {

    width: 100%;
    height: 80vh;
    color: #555;
    @include flex();
    flex-direction: column;
    position: relative;
    text-align: center;
    gap: 25px;

    h1 {
        font-size: 3rem;
        text-transform: uppercase;
    }

    p,
    h1,
    button {
        z-index: 1;
    }

    >button {
        margin-top: 4rem;
        cursor: pointer;
        width: 8rem;
        height: 3rem;
        border-radius: 25px;
        border: none;
        background: #007AFF;
        box-shadow: 1px 1px 6px 2px #0000002e;

        a {
            color: #fff;
            width: 100%;
            height: 100%;
        }
    }

    >img {
        position: absolute;
        top: 50%;
        right: 50%;
        transform: translate(50%, -50%);
        opacity: .2;
    }
}

@media screen and (max-width : 600px) {
    .EmptyCart {
        h1 {
            font-size: 2.5rem;
            padding: 0 2vw;
        }

        p {
            font-size: .9rem;
            padding: 0 2vw;
        }

        >button {
            margin-top: 2rem;
        }

        >img {
            position: absolute;
            top: 50%;
            right: 50%;
            transform: translate(50%, -50%);
            opacity: .2;
            width: 60%;
        }
    }
}

.clicked {
    display: inline-block !important;
}

.show {
    display: block !important;
}

.paymenFieldsVsible {
    display: flex !important;
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

    .checkout-container {
        width: 95% !important;
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
        min-height: 12rem !important;
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
}
</style>