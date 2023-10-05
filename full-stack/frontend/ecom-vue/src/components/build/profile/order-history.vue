<script setup>
import { ref, onMounted } from 'vue'
import api from '../../../http/api';
import jsCookie from 'js-cookie';
import Loading from '../loading.vue';
const ordersData = ref([])
const isLoading = ref(false)
onMounted(async () => {
    try {
        const userId = JSON.parse(jsCookie.get('auth-user')).id

        const res = await api.get(`/user/orders/history/${userId}`)
        ordersData.value = res.data.orders.data
        isLoading.value = true
    } catch (error) {
        console.log(error);
    }
})

const expandOrder = (e) => {
    // Find the next sibling element with class "order__details"
    const orderDetails = e.target.parentElement.nextElementSibling;

    // Check if the next sibling exists
    if (orderDetails) {
        // Toggle the class "showOrderDetails" on the next sibling element
        orderDetails.classList.toggle('showOrderDetails');
    }
};
function getProductPrice(p) {
    return p.sale_prcie && p.sale_prcie != '0.00' ? (p.sale_price * p.pivot.quantity) : (p.price * p.pivot.quantity)
}
</script>

<template>
    <div  v-if="isLoading ">
        <div v-if="ordersData.length >  0" class="OrderHistory">
            <h2>Your recent orders</h2>

            <div class="order-slot" v-for="order in ordersData" :key="order.id">
                <header>
                    <p>
                        <small>
                            Order
                        </small>
                        #{{ order.wp_order_id }}
                    </p>
                    <i title="Expand" class="fa-solid fa-angles-down" @click="expandOrder($event)"></i>
                </header>

                <div class="order__details">
                    <p>Order details : </p>
                    <div class="slot">
                        <div class="img-name">
                            <p>Item(s)</p>
                        </div>
                        <div class="quantity">
                            <p>Quantity</p>
                        </div>
                        <div class="price">
                            <p>Price</p>
                        </div>
                    </div>

                    <div class="slot data" v-for="product in order.products" :key="product.id">
                        <div class="img-name">
                            <img :src="product.images[0].url" alt="">
                            <p>{{ product.name.length > 15 ? (product.name.slice(0, 15) + '...') : product.name }}</p>
                        </div>
                        <div class="quantity">
                            <p>{{ product.pivot.quantity }}</p>
                        </div>
                        <div class="price">
                            ${{ getProductPrice(product) }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="noOrders" v-else>
            <h2>No Orders placed Yet !!</h2>
        </div>
    </div>

    <div class="loader" v-else>
        <Loading />
    </div>
</template>

<style lang="scss" >
.OrderHistory {
    width: 100%;
    min-height: 30vh;
    color: #555;
    display: flex;
    flex-direction: column;
    background: #f1f1f1;
    box-shadow: 1px 1px 6px 1px #00000007;
    padding: 1rem 0;
    border-radius: 5px;
    margin-bottom: 3rem;

    >h2 {
        padding: 1rem;
        text-transform: uppercase;
        font-size: 1rem;
    }

    .order-slot {
        width: 95%;
        min-height: 3.5rem;
        margin: .5rem auto;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-direction: column;
        overflow-x: hidden;
        transition: .3s ease-in-out;
        background: #f1f1f1cd;


        header {
            width: 100%;
            height: 3.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            background: #fff;
            border-radius: 5px;


            >p {
                color: #2e6bc6;
                font-size: .9rem;

                >small {
                    font-size: 1rem;
                    color: #555;
                }
            }

            >i {
                padding: 10px;
                cursor: pointer;
                transition: .3s ease-in-out;
                border-radius: 50%;

                &:hover {
                    background: #f1f1f1cd;
                }
            }
        }

        >.order__details {
            width: 100%;
            min-height: 10rem;
            margin: 1rem 0;
            transition: .3s ease-in-out;
            display: none;
            background: #fff;
            border-radius: 5px;

            >p {
                padding: 10px;
                font-size: .9rem;
            }

            >.slot {
                width: 100%;
                min-height: 3rem;
                border-top: 1px solid #55555513;
                border-bottom: 1px solid #55555513;
                padding: 0;
                flex-direction: row !important;
                gap: 0;

                >div {
                    min-height: 3rem;
                    display: flex;
                    align-items: center;
                    padding: 0 5px;
                    font-size: .9rem;

                    >img {
                        width: 8rem;
                        margin: .5rem;
                    }

                }

                .img-name {
                    width: 40%;
                }

                .quantity {
                    width: 30%;
                }

                .price {
                    width: 30%;
                }
            }
        }
    }

}

.showOrderDetails {
    display: block !important;
}

.loader {
    width: 100%;
    height: 50vh;
}
.noOrders{
    width: 100%;
    text-align: center;
    padding: 4rem 0;
    font-size: 1.5rem;
    text-transform: uppercase;
}
@media screen and (max-width : 768px) {
    .order__details {

        >.data {
            display: flex !important;
            flex-wrap: wrap;

            .img-name {
                width: 100% !important;
            }

            .quantity {
                width: 50% !important;
                padding: 0 1rem !important;
            }

            .price {
                width: 50% !important;
            }
        }
    }
}
</style>