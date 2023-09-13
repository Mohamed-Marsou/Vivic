<script setup>
import { useRoute } from 'vue-router';
import { onMounted, ref } from 'vue';
import { jsPDF } from 'jspdf';
import 'jspdf-autotable';
import api from '../http/api';
// Create a ref for the table element
const myTable = ref(null);

const storeUrl = 'www.commerce-store.com'
const route = useRoute();
const orderInfo = ref([]);
const loading = ref(true);
const tableData = ref([]);

onMounted(async () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    try {
        const orderId = route.query.orderId;
        const response = await api.get(`/order/products/${orderId}`);
        orderInfo.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error(error);
        loading.value = false;
    }
    // Define table data
    tableData.value = orderInfo.value.products ? orderInfo.value.products.map((product) => [
        product.name,
        `$${product.sale_price ? product.sale_price : product.price}`,
        product.pivot.quantity.toString(),
        `$${((product.sale_price ? product.sale_price : product.price) * product.pivot.quantity).toFixed(2)}`,
    ]) : [];

    console.log(orderInfo.value);

});

const calculateTotal = (product) => {
    return product.pivot.quantity * parseFloat(product.sale_price ? product.sale_price : product.price);
};

const generatePdf = () => {

    const doc = new jsPDF();

    const table = myTable.value;

    // Convert the table element to HTML string
    const tableHtml = table.outerHTML;

    // Add title, transaction ID, and date to the PDF
    const title = 'Purchase Details';
    const transactionId = 'Transaction ID :' + route.query.orderId;
    const date = 'Date : ' + new Date().toLocaleDateString();

    // Set the font size for title, transaction ID, and date
    doc.setFontSize(8);
    // TODO CHANGE 
    doc.text(storeUrl, 15, 10);

    doc.setFontSize(20);
    doc.text(title, 15, 30);

    doc.setFontSize(12);
    doc.text(transactionId, 15, 40);
    doc.setFontSize(10);
    doc.text(date, 15, 50);

    // Define table column headers
    const headers = ['Product', 'Price', 'Quantity', 'Total'];

    // Define table data
    const data = tableData.value;

    // Set the font size and style for the table
    doc.setFontSize(14);
    doc.setFont('helvetica', 'normal');

    doc.autoTable({
        head: [headers],
        body: data,
        startY: 60, // Set the y-coordinate for the table
        didParseCell: function (data) {
            if (data.section === 'head' && data.row.index === 0) {
                // Apply custom styling to the header row 
                data.cell.styles.fillColor = [1, 145, 97];
                data.cell.styles.textColor = [255, 255, 255];
            }
        },
        styles: {
            fontSize: 8,
            halign: 'left',
            valign: 'middle',
        },
        columnStyles: {
            0: { cellWidth: 50 },
        },
    });

    // Save the PDF
    doc.save('document.pdf');
}

function copyOrderID() {
    const orderIdElement = document.getElementById('orderId');
    const orderIdText = orderIdElement.innerText;
    const tempTextarea = document.createElement('textarea');
    tempTextarea.value = orderIdText;
    document.body.appendChild(tempTextarea);
    tempTextarea.select();
    document.execCommand('copy');

    document.body.removeChild(tempTextarea);
    alert('Order ID copied to clipboard: ' + orderIdText);
}
</script>

<template>
    <div class="sucss-main-container">
        <div class="sm-bx">
            <h2>Congratulations! Your payment was successful</h2>
            <span title="Copy" @click="copyOrderID" id="orderId">
                <small>Order ID :</small>
                {{ orderInfo?.wp_order_id }}
            </span>
            <p>Secure Your Purchase Details: Download a PDF Copy of Your Order to Keep a Record of Your Bought Items</p>
            <a @click="generatePdf" class="download-link">
                <i class="fa-solid fa-download"></i>
                DOWNLOAD
            </a>
            <img src="../assets/images/hand-holding-debit-credit-card-n.webp" alt="sucss-pay-img">
        </div>
        <div class="bg-bx">
            <h1>Purchase Details</h1>
            <template v-if="loading">
                <p>Loading...</p>
            </template>
            <template v-else>
                <template v-if="orderInfo && orderInfo.products && orderInfo.products.length > 0">
                    <table ref="myTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in orderInfo.products" :key="product.id">
                                <td>{{ product.name }}</td>
                                <td>${{ product.sale_price ? product.sale_price : product.price }}</td>
                                <td>{{ product.pivot.quantity }}</td>
                                <td>${{ calculateTotal(product) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
                <template v-else>
                    <p>No products found for this order.</p>
                </template>
            </template>
        </div>
    </div>
</template>
  

  
  

<style lang="scss">
@import '@/style/_global.scss';

.sucss-main-container {
    width: 100vw;
    min-height: 100vh;
    @include flex();
    font-family: $ff;

    .sm-bx {
        width: 30%;
        height: 100%;
        padding: 2rem 5px;

        h2 {
            font-size: 1.1rem;
            margin-bottom: 2rem;

        }

        >a {
            small {
                color: #5790E6;
            }

            font-size: .9rem;
        }

        p {
            color: #666;
            font-size: .9rem;
            margin-top: 2rem;
        }

        a {

            width: 8rem;
            height: 3rem;
            background-color: #5790E6;
            color: azure;
            border: none;
            @include flex();
            gap: .5rem;
            cursor: pointer;
            transition: .3s ease-in;
            margin: 2rem 50%;
            transform: translateX(-50%);

            &:hover {
                background-color: #2a7ffd;
                border-radius: 5px;
            }

        }

        #orderId {
            cursor: pointer;

            >small {
                cursor: default;
            }
        }

        img {
            margin-top: 2rem;
            width: 90%;
        }
    }

    .bg-bx {
        width: 60%;
        min-height: 100%;
        @include flex();
        flex-direction: column;
        align-self: flex-start;
        margin-top: 5rem;

        h1 {
            margin: 0 0 2rem 2%;
            align-self: flex-start;

        }

        table {
            width: 95%;

            th,
            td {
                color: #555;
                height: 2rem;
                padding: 0 10px;
                border: 2px #55555544 solid;
            }

            th {
                color: #033d2a;
                padding: 10px;
            }
        }
    }
}

@media screen and (max-width: 1024px) {
    .sucss-main-container {
        width: 100vw;
        min-height: 100vh !important;
        flex-direction: column;

        .sm-bx {
            width: 100vw;
            height: fit-content;
            padding: 2rem 5vw;
            margin-top: 2rem;

            img {
                display: none;
            }
        }

        .bg-bx {
            width: 95vw;
            min-height: fit-content;
            @include flex();
            flex-direction: column;
            padding: 2rem 0;
            margin-top: 0rem;

            h1 {
                margin: 0 0 2rem 2%;
                align-self: flex-start;

            }

            table {
                width: 95%;

                th,
                td {
                    color: #555;
                    height: 2rem;
                    padding: 0 10px;
                }
            }
        }
    }
}

@media screen and (max-width: 400px) {
    .sucss-main-container {
        .bg-bx {
            width: 99vw;
            min-height: fit-content;
            @include flex();
            flex-direction: column;
            padding: 2rem 1vw;

            h1 {
                margin: 0 0 1rem 2%;
                align-self: flex-start;
                font-size: 1.3rem;
            }

            table {
                width: 100%;

                th,
                td {
                    color: #555;
                    height: 2rem;
                    padding: 0 5px;
                }
            }
        }
    }
}</style>