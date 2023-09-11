<script setup>
import { ref, onMounted } from 'vue'
import api from '../../../http/api';
import { RouterLink } from 'vue-router';
import Loading from '../../build/loading.vue';
const loading = ref(true)
const overlay = ref(false)

const isImporting = ref(false)

const showAdvanceOpt = ref(false)
const products = ref([])
onMounted(async () => {
    await getProductsDATA()
})

async function getProductsDATA() {
    const res = await api.get('/products')
    products.value = res.data.data
    // sett loding to fasle 
    loading.value = false
}
function toggoleImport() {
    overlay.value = !overlay.value
    showAdvanceOpt.value = false
}
function toggoleAdvImport() {
    showAdvanceOpt.value = !showAdvanceOpt.value
    restValue()

}

const quickImport = async () => {
    isImporting.value = true
    try {
        const res = await api.get('/woo/products')
        console.log(res);
        isImporting.value = false
        await getProductsDATA()
        overlay.value = !overlay.value
    } catch (error) {
        console.log(error);
    }
}
const status = ref('')
const startDate = ref('')
const finishDate = ref('')
const productIds = ref('')


const errorMessage = ref('')

const count = ref(0)
const showCount = ref(false)


const advanceImport = async () => {
    // Set a flag to indicate that the import process is in progress
    isImporting.value = true;

    // Validate and format the product IDs
    const formattedProductIds = validateAndFormatProductIds(productIds.value);

    // Check if there was an error during validation
    if (errorMessage.value) {
        return;
    }

    // Prepare the payload for the API request
    const payload = {
        status: status.value,
        startDate: startDate.value,
        finishDate: finishDate.value,
        productIds: formattedProductIds,
    };

    try {
        // Make an API request to retrieve filtered products
        const res = await api.get('/woo/filter/products', {
            params: payload,
        });

        // Update the count with the number of successful product additions
        count.value = res.data.successCount;

        // Toggle the import view
        toggoleImport();
        
        // Mark the import process as completed
        isImporting.value = false;
        
        // Retrieve updated product data
        await getProductsDATA();

        // Toggle the display of the synchronization response message
        toggoleSyncResponse();
        
        // Reset the input values
        restValue();

        // Hide the synchronization response message after 3 seconds
        setTimeout(() => {
            toggoleSyncResponse();
        }, 3500);
    } catch (error) {
        // Log any errors that occurred during the API request
        console.error(error);
    }
};

function toggoleSyncResponse (){
    showCount.value = !showCount.value
}
const validateAndFormatProductIds = (ids) => {
    // Remove any leading/trailing whitespace and trailing commas
    const cleanedIds = ids.trim().replace(/,+$/g, '');

    // Split the input by ',' and remove duplicates using a Set
    const uniqueIds = [...new Set(cleanedIds.split(','))];

    // Validate each ID to be a positive integer or empty
    const isValid = uniqueIds.every(id => id === '' || /^\d+$/.test(id));

    if (!isValid) {
        errorMessage.value = 'Invalid IDs. Please enter positive integers separated by commas or leave it empty.';
        return '';
    }

    // Join the unique IDs back into a comma-separated string
    const formattedIds = uniqueIds.join(',');

    return formattedIds;
};

function restValue() {
    status.value = '';
    startDate.value = '';
    finishDate.value = '';
    productIds.value = '';
    errorMessage.value = '';
}
</script>

<template>
    <div v-if="!loading" class="products-wrapper">
        <div class="overlay" :class="{ showOverlay: overlay }">

            <div class="import-container">
                <h1>Import Products to Store</h1>
                <div class="impBtns" :class="{ hide: showAdvanceOpt }">
                    <button :disabled="isImporting" @click="quickImport">
                        Quick import
                    </button>
                    <button :disabled="isImporting" @click="toggoleAdvImport">
                        Advance import
                    </button>
                </div>
                <i @click="toggoleImport" class="fa-solid fa-xmark"></i>

                <div class="advance-import" :class="{ show: showAdvanceOpt }">
                    <h4>Advnace Import </h4>
                    <div class="slot">
                        <div>
                            <label for="start">Start date :</label>
                            <input type="date" :disabled="isImporting" id="start" v-model="startDate">
                        </div>
                        <div>
                            <label for="end">End date :</label>
                            <input type="date" :disabled="isImporting" id="end" v-model="finishDate">
                        </div>
                    </div>

                    <div class="slot">
                        <label for="status" class="productStatus">status:</label>
                        <select id="status" :disabled="isImporting" v-model="status">
                            <option disabled selected> Select Status</option>
                            <option>all</option>
                            <option>publish</option>
                            <option>pending</option>
                            <option>private</option>
                        </select>
                    </div>
                    <div class="slot">
                        <small v-if="errorMessage">Invalid IDs. Please enter positive integers separated by commas.</small>
                        <textarea :disabled="isImporting" placeholder="Enter ids sapreted by ','" v-model="productIds"></textarea>
                    </div>
                    <div class="slot btns">
                        <button :disabled="isImporting" @click="advanceImport">
                            import
                        </button>
                        <button :disabled="isImporting" @click="toggoleAdvImport">
                            cancel
                        </button>
                    </div>

                </div>
            </div>

        </div>
        <header>
            <h1>Products</h1>

            <button @click="toggoleImport">
                import
            </button>
        </header>

        <div class="syncResponse " :class="{ show: showCount }">
            <p v-if="count > 0">
                {{ count }} Products retrieved and processed successfully.
            </p>
            <p v-else>
                No products were imported !!
            </p>
           
        </div>
        <div class="table-container">
            <div class="search-div">
                <input type="text" placeholder="search">
                <button>
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Sale Price</th>
                        <th>Quantity</th>
                        <th>Average Rating</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in products" :key="p.id">
                        <td>{{ p.id }}</td>
                        <td>
                            <RouterLink :to="{ name: 'product-page', params: { slug: p.slug } }">
                                {{ p.name }}
                            </RouterLink>
                        </td>
                        <td>{{ p.price }}</td>
                        <td>{{ p.sale_price ? p.sale_price : 'null' }}</td>
                        <td>{{ p.inStock }}</td>
                        <td>{{ p.average_rating ? p.average_rating : 'null' }}</td>
                        <td>{{ p.category.name }}</td>
                    </tr>


                </tbody>
            </table>

            <button>Load more</button>
        </div>
    </div>
    <div v-else class="loading">
        <Loading />
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.products-wrapper {
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

    .overlay {
        width: 100%;
        height: 100vh;
        position: fixed;
        background: #00000072;
        top: 0;
        right: 0;
        @include flex();
        display: none;

        .import-container {
            width: 40rem;
            min-height: 15rem;
            background: #ffffff;
            border-radius: 10px;
            position: relative;
            display: flex;
            flex-direction: column;

            >h1 {
                padding: 2rem;
                text-transform: uppercase;
            }

            .impBtns {
                width: 100%;
                display: flex;
                justify-content: center;
                gap: 10%;
                margin-top: 2rem;

                >button {
                    width: 10rem;
                    height: 3rem;
                    border: none;
                    color: #fff;
                    background: #2c2e3e;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: .3s ease-in-out;

                    &:first-child:hover {
                        background: rgb(21, 135, 228);
                    }

                    &:nth-child(2):hover {
                        background: transparent;
                        border: 2px solid #2c2e3e;
                        color: #2c2e3e;
                    }
                }

                >button:disabled {
                    cursor: not-allowed;
                    opacity: 0.5;

                    &:hover {
                        background-color: #2c2e3e;
                        color: #fff;
                        border: none;
                    }
                }
            }

            >i {
                position: absolute;
                top: 1rem;
                right: 1rem;
                font-size: 1.8rem;
                cursor: pointer;
            }

            .advance-import {
                width: 100%;
                min-height: 8rem;
                margin: 1rem 0;
                padding: 10px;
                display: none;
              
                .slot {
                    width: 95%;
                    min-height: 4rem;
                    margin: 10px auto;
                    display: flex;
                    align-items: center;
                    padding: 0 1rem;

                    >div {
                        width: 50%;
                        height: 100%;
                        display: flex;
                        flex-direction: column;

                        label {
                            font-size: .9rem;
                            color: #555;
                            padding: 10px 0;
                        }

                        input[type='date'] {
                            padding: .5rem;
                            border: 1px solid rgba(24, 24, 24, 0.134);
                            border-radius: 4px;
                            width: 8rem;
                        }


                    }

                    select {
                        padding: .5rem;
                        border: 1px solid rgba(24, 24, 24, 0.134);
                        border-radius: 4px;
                        width: 8rem;
                    }

                    textarea {
                        width: 90%;
                        height: 6rem;
                        padding: .5rem;
                        border: 1px solid rgba(24, 24, 24, 0.134);
                        border-radius: 4px;
                    }

                    >small {
                        color: red;
                        font-size: .8rem;
                    }

                    >button {
                        width: 9rem;
                        height: 3rem;
                        border: none;
                        color: #fff;
                        background: #2c2e3e;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: .3s ease-in-out;
                        text-transform: uppercase;

                        &:first-child:hover {
                            background: rgb(15, 141, 244);
                        }

                        &:nth-child(2):hover {
                            background: rgb(215, 6, 76);
                            align-items: flex-end;
                        }
                    }
                    button:disabled {
                    cursor: not-allowed;
                    opacity: 0.5;

                    &:hover {
                        background-color: #2c2e3e;
                        color: #fff;
                        border: none;
                    }
                }
                }

                .slot:has(small) {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 10px;
                }

                .slot:has(.productStatus) {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                    margin: 1.5rem 1rem;

                    label {
                        color: #555;
                        font-size: .9rem;
                    }
                }

                .btns {
                    justify-content: space-between;
                    margin-top: 2rem;
                }
            }
        }
    }

    header {
        width: 100%;
        min-height: 3rem;
        @include flex($jc: space-between);
        padding: 3rem 4vw;

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

    .table-container {
        width: 98%;
        min-height: 6rem;
        margin: 2rem auto;
        display: flex;
        flex-direction: column;

        .search-div {
            border: 1px solid rgba(57, 57, 57, 0.07);
            width: 22rem;
            height: 2.8rem;
            margin: 0 5%;
            display: flex;
            overflow: hidden;

            input {
                width: 82%;
                border: none;
                outline: none;
                padding: 0 1rem;
            }

            >button {
                width: 18%;
                background: #40445cfd;
                color: #fff;
                border: none;
                cursor: pointer;
                transition: .3s ease-in-out;

                &:hover {
                    background: #2c2e3e;
                }
            }
        }

        table {
            width: 90%;
            margin: 1rem auto;

            td,
            th {
                border: 2px solid rgba(56, 56, 56, 0.081);
                height: 3rem;

            }

            td {
                height: 3.5rem;
                padding: 0 1vw;
                color: #666;
                cursor: pointer;

                a {
                    color: #666;

                    &:hover {
                        text-decoration: underline;
                    }
                }
            }

            tbody tr {
                transition: .5s ease-in-out;

                &:hover {
                    background: #0000000a;
                }

            }

        }

        >button {
            margin: 2rem auto;
            background: #2c2e3e;
            color: white;
            border: none;
            border-radius: 10px;
            width: 8rem;
            height: 3rem;
            cursor: pointer;
            box-shadow: #2c2e3e3d 1px 1px 5px 1px;
        }
    }

}

.syncResponse {
    width: 80%;
    height: 3.5rem;
    background: #2c2e3e;
    color: white;
    border-radius: 10px;
    margin: 1rem auto;
    text-align: center;
    position: relative;
    display: none;
    transition: .3s ease-in-out;
    p {
        padding: 1.1rem;
        font-size: .9rem;
    }
}

.show {
    display: block !important;
}

.hide {
    display: none !important;
}

.showOverlay {
    display: flex !important;
}
</style>