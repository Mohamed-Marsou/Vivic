<script>

</script>



<template>
    <div class="WishList-container">
        <div class="container">
            <div class="header">
                <h1>Wishlist products</h1>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product name</th>
                        <th>Product Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <img src="https://woodmart.xtemos.com/accessories/wp-content/uploads/sites/7/2022/04/w-accessories-product-15-430x491.jpg" alt="product">
                        </td>
                        <td><b>Lorem ipsum dolor sit amet consectetur adipisicing elit</b></td>

                        <td>
                            <b>
                                $99.99
                            </b>
                        </td>
                        <td>
                            <button>
                                BUY NOW
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="sm-card">
                <div class="top">
                    <img src="https://assets.materialup.com/uploads/ec46f513-9984-4636-b9ac-c237a7d6f01e/preview.jpg"
                        alt="Product">
                </div>
                <div class="main">
                    <p>Lorem ipsum dolor sit amet consecte.</p>
                    <h4>$555</h4>
                    <span>X</span>
                    <button>BUY NOW</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" >
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
$ff: 'Poppins', sans-serif;

.WishList-container {
    width: 100%;
    min-height: 90vh;
    font-family: $ff;

    .container {
        width: 90vw;
        min-height: 80vh;
        margin: 2rem auto;

        .header {
            width: 100%;
            height: 6rem;
            padding: 2rem 0;
            font-size: 1rem;
        }

        table {
            width: 80vw;
            min-height: 20vh;
            margin: 2rem auto;

            thead tr th {
                padding: 1rem;
                color: #555;
            }

            tbody tr td {
                padding: 1rem;
                border-radius: 5px;
                color: #555;
                >img {
                    width: 3rem;
                    object-fit: contain;
                    margin: 0 50%;
                    transform: translateX(-50%);
                }

                >button {
                    width: 100%;
                    height: 3rem;
                    border: none;
                    font-weight: bold;
                    background: rgb(251, 77, 77);
                    border-radius: 5px;
                    color: #ffffff;
                    cursor: pointer;
                    transition: .3s ease-in;

                    &:hover {
                        background: rgb(255, 6, 6);

                    }

                }
            }
        }

    }
}

.sm-card {
    width: 95%;
    height: 18vh;
    margin: 1rem auto;
    display: none;

    .top {
        width: 20%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;

        img {
            width: 90%;
            object-fit: contain;
            border-radius: 10px;
        }
    }

    .main {
        width: 80%;
        height: 100%;
        padding: 1rem 1vw;
        position: relative;

        span {
            position: absolute;
            top: 5px;
            right: 1rem;
            border: 1px solid #5555555e;
            padding: .3rem .6rem;
            border-radius: 5px;
            font-weight: bold;
            color: #55555590;
            cursor: pointer;

        }

        p {
            font-size: .9rem;
            padding: 0 10vw 0 1vw;
        }

        h4 {
            font-size: 1.3rem;
            padding: 1rem 1vw;
        }

        >button {
            width: 8rem;
            position: absolute;
            bottom: 5px;
            right: 1rem;
            height: 3rem;
            border: none;
            font-weight: bold;
            background: rgb(251, 77, 77);
            border-radius: 5px;
            color: #ffffff;
            cursor: pointer;
            transition: .3s ease-in;
            &:hover {
                background: rgb(255, 6, 6);
            }
        }
    }
}

@media screen and (max-width : 768px) {
    table {
        display: none !important;
    }
}
@media screen and (max-width : 768px) {
    .sm-card {
        display: flex !important;
}
}
@media screen and (max-width : 550px) {
    .sm-card {
        flex-direction: column;
        .top {
            width: 100%;
            >img{
                height: 95%;
            }
        }
        .main {
            width: 100%;
        }
}
}
</style>