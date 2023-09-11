<script setup>
import Loading from '../../build/loading.vue';
import { ref, onMounted } from 'vue'
import api from '../../../http/api';
const loading = ref(true)
const orders = ref([])
const ordersCount = ref(0)
const ordersTotal = ref(0)

onMounted(async () => {
    await getOrders()
    loading.value = false
})
async function getOrders() {
    try {
        const res = await api.get('/orders')
        orders.value = res.data.data
        ordersCount.value = res.data.to
        ordersTotal.value = res.data.total
    } catch (error) {
        console.log(error);
    }
}

const orderActionsState = ref({});

const toggleActions = (orderId) => {
  orderActionsState.value[orderId] = !orderActionsState.value[orderId];
};


function formatDate(value) {
    const date = new Date(value);

    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
    };

    return date.toLocaleDateString('en-US', options);
}
</script>
<template>
    <div v-if="!loading" class="orders-wrapper">
        <header>
            <h1>
                <i class="fa-solid fa-cash-register"></i>
                Orders
            </h1>
        </header>

        <div class="orders-table">
            <div class="table-filters">
                <select>
                    <option>
                        Last 30 Dasy</option>
                    <option>Last 15 Dasy</option>
                    <option>Last 7 Dasy</option>
                    <option>Last 24 Hours</option>
                </select>
                <div class="pagination">
                    <p>{{ ordersCount }} of {{ ordersTotal }}</p>
                    <i class="fa-solid fa-angle-left"></i>
                    <i class="fa-solid fa-angle-right"></i>
                </div>
            </div>

            <div class="wrapper">
                <div class="slot">
                    <div class="id">ID</div>
                    <div class="userId">userId</div>
                    <div class="amount">Amount</div>
                    <div class="transaction_id">Transaction_id</div>
                    <div class="wordPress_id">WordPress_id</div>
                    <div class="date">Date</div>
                    <div class="action">
                        Action
                    </div>

                </div>
                <div class="slot body" v-for=" o in orders" :key="o.id" @mouseleave="orderActionsState = {}">
                    <div class="id">{{ o.id }}</div>
                    <div class="userId">{{ o.user_id }}</div>
                    <div class="amount">${{ o.amount }}</div>
                    <div class="transaction_id">{{ o.transaction_id }}</div>
                    <div class="wordPress_id">{{ o.wp_order_id }}</div>
                    <div class="date"> {{ formatDate(o.created_at) }}</div>
                    <div class="action">
                        <i @click="toggleActions(o.id)" class="fa-solid fa-ellipsis-vertical"></i>
                        <div :class="{ showActions: orderActionsState[o.id] }" class="order_actions">
                            <p>
                                <i class="fa-solid fa-circle-info"></i>
                                Details
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div v-else>
        <Loading />
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.orders-wrapper {
    width: 95%;
    height: 95%;
    background: #fff;
    border-radius: 5px;
    overflow-x: scroll;
    position: relative;

    /* Hide scrollbar for Chrome, Safari and Opera */
    &::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    & {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }


    header {
        width: 100%;
        min-height: 3rem;
        @include flex($jc: space-between);
        padding: 4rem;

        h1 {
            font-size: 2rem;
            text-transform: uppercase;
        }

        >button {
            width: 7rem;
            height: 2.5rem;
            border: none;
            color: #fff;
            background: #2c2e3e;
            border-radius: 5px;
            cursor: pointer;
            transition: .3s ease-in-out;

            &:hover {
                background: rgb(21, 135, 228);
            }
        }

    }

    .orders-table {
        width: 100%;
        min-height: 10rem;
        margin-top: 1rem;

        .table-filters {
            width: 100%;
            height: 4rem;
            margin: 1rem 0;
            @include flex($jc: space-between);
            padding: 0 2vw;

            select {
                border: 1px solid #2c2e3e1c;
                padding: 5px 12px;
                color: #2c2e3ece;
                border-radius: 5px;
                outline: none;
            }

            .pagination {
                display: flex;
                align-items: center;

                p {
                    color: #2c2e3ece;
                    font-size: .9rem;
                }

                >i {
                    width: 2rem;
                    height: 2rem;
                    border: 1px solid #2c2e3e1c;
                    @include flex();
                    color: #2c2e3e80;
                    transition: .3s ease-in;
                    cursor: pointer;
                    border-radius: 5px;
                    margin-left: 15px;

                    &:hover {
                        color: #2c2e3ece;
                    }

                }

            }


        }

        .wrapper {
            width: 95%;
            min-height: 4rem;
            margin: 1rem auto;

            .slot {
                width: 100%;
                height: 3.5rem;
                border-bottom: 1px solid rgba(51, 49, 49, 0.081);
                display: flex;
                transition: .3s ease-in-out;

                >div {
                   width: 14.3%;
                    @include flex($jc: flex-start);
                    padding: 15px;
                    color: #55555594;
                    font-size: .9rem;
                }
                .transaction_id{
                    width: 25%;
                }
                .action {
                    justify-content: center;
                    position: relative;

                    i {
                        font-size: 1rem;
                        cursor: pointer;
                        width: 3rem;
                        height: 2rem;
                        padding-top: .5rem;
                        text-align: center;

                    }

                    &:hover {
                        >i {
                            color: #124cfc;
                        }
                    }

                    .order_actions {
                        width: 8rem;
                        height: fit-content;
                        position: absolute;
                        z-index: 3;
                        border-radius: 5px;
                        border: 1px solid rgba(51, 49, 49, 0.081);
                        background: #fff;
                        top: 75%;
                        left: 40%;
                        display: none;

                        p {
                            padding: 10px 0;
                            cursor: pointer;

                        }
                    }
                }
            }

            .body {
                &:hover {
                    >div {
                        color: #060606ee;
                        background: #6a6a6a0a;
                    }
                }
            }
        }
    }

}

.showActions {
    display: block !important;
}
</style>