<script setup>
import BarChart from './Charts/Bar.vue'
import LineChart from './Charts/Line.vue'
import api from '../../../http/api';
import { ref, onMounted, reactive } from 'vue'
const LineChartLabel1 = ref('New Users')
const LineChartData1 = ref([])

const LineChartLabel2 = ref('New Products')
const LineChartData2 = ref([])

const BarChartData = ref([])
const generalInfo = reactive({
    TotalSales: 0,
    newUsers: 0,
    newOrders: 0,
    income: 0,

});
const recentOrders = ref([])
const dataLoaded = ref(false);
async function getstatistic() {
    try {
        const res = await api.get('/dashboard/info')
        const data = res.data.data;
        generalInfo.TotalSales = data.total_sales;
        generalInfo.newUsers = data.new_users_this_month;
        generalInfo.newOrders = data.total_orders;
        generalInfo.income = data.total_income_this_month;

        LineChartData1.value = data.new_users_data;
        LineChartData2.value = data.new_products_data;
        BarChartData.value = data.income_data;
        dataLoaded.value = true;
    } catch (error) {
        console.error(error);
    }
}
async function getRecentOrders() {
    try {
        const res = await api.get('/dashboard/orders/recent')
        recentOrders.value = res.data.data
    } catch (error) {
        console.log(error);
    }
}
onMounted(async () => {
    getstatistic()
    getRecentOrders()
});
</script>
<template>
    <div class="Comp__Container">
        <div class="mainHeader">
            <div class="box">
                <div class="Bicon">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <h3>
                    Total Sales
                    <p>{{ generalInfo.TotalSales }} <small>/ this month</small></p>
                </h3>
            </div>
            <div class="box">
                <div class="Bicon">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h3>
                    Total Orders
                    <p>{{ generalInfo.newOrders }} <small>/ this month</small></p>
                </h3>
            </div>
            <div class="box">
                <div class="Bicon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <h3>
                    Total income
                    <p>${{ generalInfo.income }} <small>/ this month</small></p>
                </h3>
            </div>
            <div class="box">
                <div class="Bicon">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3>
                    New User
                    <p>{{ generalInfo.newUsers }} <small>/this month</small></p>
                </h3>
            </div>
        </div>

        <!--* Spinner until data arrive (: -->
        <div class="spinner-c" v-if="!dataLoaded">
            <h1>LOADING DATA FROM SERVER ...</h1>
            <div id="bars">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
        <!--* Spinner until data arrive (: -->
        <div v-else class="Charts-container">
            <BarChart v-if="dataLoaded" :data="BarChartData" />
            <div class="LineChartContainer">
                <LineChart v-if="dataLoaded" :data="LineChartData1" :label="LineChartLabel1" />
                <LineChart v-if="dataLoaded" :data="LineChartData2" :label="LineChartLabel2" />
            </div>
            <div class="Ord-Prd">
                <h6>Recent Orders</h6>
                <div v-if="recentOrders">
                    <div v-for="(o, index) in recentOrders" :key="index" class="slot">
                        <img :src="o.cover_image.url" alt="Product-image">
                        <p>
                            {{  o.product_name.lenght > 20 ?
                                o.product_name.slice(0, 20) + '...' : 
                                o.product_name
                            }}
                        </p>
                        <span>{{ o.quantity }}</span>
                        <p>
                            ${{ o.product_price }}
                        </p>

                    </div>
                </div>
                <div v-else>
                    <p style="padding: 0 2vw; color: #555;">No Orders yet !</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" >
.Comp__Container {
    overflow-y: scroll;

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


    .mainHeader {
        width: 100%;
        height: 8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1vw;

        >div {
            width: 18rem;
            height: 6rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            color: azure;
            cursor: default;

            .Bicon {
                width: 55px;
                height: 55px;
                background-color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;


            }

            h3 {
                display: inline-block;
                font-size: 1.4rem;
            }

            p {
                font-size: .9rem !important;
            }

            sapn {
                font-size: 1.6rem;
            }

            &:first-child {

                background: rgb(0, 227, 157);
                background: linear-gradient(90deg, rgba(0, 227, 157, 0.7315301120448179) 0%, rgba(0, 198, 137, 1) 100%);

                i {
                    color: rgba(0, 227, 157, 0.7315301120448179);
                    font-size: 1.1rem;
                }
            }

            &:nth-child(2) {
                background: rgb(61, 165, 244);
                background: linear-gradient(90deg, rgba(61, 165, 244, 1) 0%, rgba(61, 165, 244, 1) 100%);

                i {
                    color: rgb(77, 176, 252);
                    font-size: 1.1rem;

                }
            }

            &:nth-child(3) {
                background: rgb(241, 83, 110);
                background: linear-gradient(90deg, rgba(241, 83, 110, 1) 0%, rgba(241, 83, 110, 1) 100%);

                i {
                    color: rgba(241, 83, 110, 1);
                    font-size: 1.1rem;

                }
            }

            &:nth-child(4) {
                background: rgb(253, 160, 6);
                background: linear-gradient(90deg, rgba(253, 160, 6, 1) 0%, rgba(253, 160, 6, 1) 100%);

                i {
                    color: rgba(253, 160, 6, 1);
                    font-size: 1.1rem;
                }
            }
        }
    }

    .Charts-container {
        width: 100%;
        height: 72vh;
        display: flex;
        align-items: center;
        padding: 1vw;
        justify-content: space-between;

        .LineChartContainer {
            width: 30%;
            height: 60vh;
            display: flex;
            gap: 1vw;
            flex-direction: column;
            
        }

        .Ord-Prd {
            width: 25%;
            height: 60vh;
            background-color: #fff;
            overflow-y: scroll;
            border-radius: 5px;


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

            h6 {
                padding: 1rem;
                font-size: 1.2rem;
                margin-bottom: .5rem;
            }

            .slot {
                width: 100%;
                height: 4rem;
                border-bottom: 1px solid #66666638;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1vw;

                img {
                    width: 3rem;
                    height: 3rem;
                    object-fit: contain;
                }

                p {
                    font-size: .9rem;
                }
            }
        }

    }

}

@media screen and (max-width : 1200px) {
    .mainHeader {
        >div {
            width: 21.5vw !important;
            padding: 1.2vw !important;

            .Bicon {
                width: 50px !important;
                height: 50px !important;
            }

            h3 {
                font-size: 1.2rem !important;
            }

            small {
                font-size: .8rem !important;
                font-weight: normal;
            }

            span {
                font-size: 1.4rem !important;
            }
        }
    }


    .Charts-container {
        width: 100%;
        height: 72vh;
        display: flex;
        align-items: center;
        padding: 1vw;
        justify-content: space-between;
        flex-wrap: wrap;

        .barChart {
            width: 49% !important;
            margin-bottom: 2rem !important;
        }

        .LineChartContainer {
            width: 48% !important;
            margin-bottom: 2rem !important;
        }

        .Ord-Prd {
            width: 100% !important;
            height: 60vh;
            background-color: #fff;
            overflow-y: scroll;


            h6 {
                padding: 1rem 2vw !important;
                font-size: 1.4rem !important;
            }

            .slot {
                width: 100%;
                height: fit-content !important;
                padding: 0 2vw !important;

                img {
                    width: 3.5rem !important;
                    height: 3.5rem !important;
                    margin: .5rem 0;
                }
            }
        }

    }
}

.spinner-c {
    width: 100%;
    height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;

    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
        background-color: #121212;
    }

    ::-webkit-scrollbar-thumb {
        background-color: #181818;
    }

    ::-webkit-scrollbar-thumb:hover {
        background-color: #202020;
    }

    ::-webkit-scrollbar-button {
        background-color: #181818;
    }

    ::-webkit-scrollbar-button:hover {
        background-color: #202020;
    }

    body {
        background-image: url("https://i82.servimg.com/u/f82/19/38/53/40/white_10.png");
        background-attachment: fixed;
    }

    h1 {
        color: #555;
        font: bold 1rem "helvetica neue", helvetica, arial, sans-serif;
        left: 0;
        margin: 20px 0 0 0;
        position: absolute;
        top: 50%;
        text-align: center;
        width: 100%;
    }

    #bars {
        height: 30px;
        left: 50%;
        margin: -30px 0 0 -20px;
        position: absolute;
        top: 50%;
        width: 40px;
    }

    .bar {
        background: #666;
        bottom: 1px;
        height: 3px;
        position: absolute;
        width: 3px;
        animation: sound 0ms -800ms linear infinite alternate;
    }

    @keyframes sound {
        0% {
            opacity: 0.35;
            height: 3px;
        }

        100% {
            opacity: 1;
            height: 28px;
        }
    }

    .bar:nth-child(1) {
        left: 1px;
        animation-duration: 474ms;
    }

    .bar:nth-child(2) {
        left: 5px;
        animation-duration: 433ms;
    }

    .bar:nth-child(3) {
        left: 9px;
        animation-duration: 407ms;
    }

    .bar:nth-child(4) {
        left: 13px;
        animation-duration: 458ms;
    }

    .bar:nth-child(5) {
        left: 17px;
        animation-duration: 400ms;
    }

    .bar:nth-child(6) {
        left: 21px;
        animation-duration: 427ms;
    }

    .bar:nth-child(7) {
        left: 25px;
        animation-duration: 441ms;
    }

    .bar:nth-child(8) {
        left: 29px;
        animation-duration: 419ms;
    }

    .bar:nth-child(9) {
        left: 33px;
        animation-duration: 487ms;
    }

    .bar:nth-child(10) {
        left: 37px;
        animation-duration: 442ms;
    }
}</style>