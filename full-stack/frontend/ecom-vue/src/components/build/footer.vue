<script setup>

const goTop = () => {
    document.querySelector('#main').scrollIntoView({ behavior: 'smooth' });
}

</script>
<template>
    <footer>
        <div class="box">
            <div class="conatiner-rw">
                <div class="con-rw">
                    <h1 class="logo">
                        Ecommerce
                    </h1>
                    <p>
                        Unlock Endless Possibilities at Marketo:
                        Your Ultimate Online Shopping Destination!
                    </p>
                    <div>
                        <i class="fa-solid fa-envelopes-bulk"></i>
                        <span>
                            Ecommerce@shop.us
                        </span>
                    </div>
                    <div>
                        <i class="fa-solid fa-phone"></i>
                        <span>
                            +221 555 111
                        </span>
                    </div>
                </div>
                <div class="con-rw quick-nav">
                    <h2>QUICK NAVIGATION</h2>
                    <RouterLink to="/">Home</RouterLink>
                    <RouterLink to="/">Shop</RouterLink>
                    <RouterLink to="/">Account</RouterLink>
                    <RouterLink to="/cart">Cart</RouterLink>
                </div>
            </div>
            <div class="conatiner-rw">
                <div class="con-rw">
                    <h2>INFORMATION</h2>
                    <RouterLink to="/">Pravicy policy</RouterLink>
                    <RouterLink to="/">Terms of services</RouterLink>
                    <RouterLink to="/">Shipping Policy</RouterLink>
                    <RouterLink to="/">Refund Policy</RouterLink>
                </div>

                <div class="con-rw form">
                    <form>
                        <label for="email">Email *</label>
                        <input type="email" name="email" placeholder="Email address" required>
                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>

            <div class="goTop" @click="goTop">
                <i class="fa-solid fa-caret-up"></i>
            </div>
        </div>

        <div class="conatiner-cm">
            <p>&copy;Ecommerce 2023. All rights reserved</p>
            <div>
                <img src="../../assets/icons/800px-PayPal.svg.jpg" alt="icon">
                <img src="../../assets/icons/visa-classic-svgrepo-com.svg" alt="icon">
                <img src="../../assets/icons/mastercard-old-3-svgrepo-com.svg" alt="icon">
            </div>
        </div>
    </footer>
</template>

<style lang="scss">
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Patrick+Hand+SC&family=Slackside+One&display=swap');
$ff: 'Poppins', sans-serif;
$logo_font: 'Patrick Hand SC', 'cursive';

footer {
    width: 100%;
    min-height: 50vh;
    background-color: #000000;
    display: flex;
    flex-direction: column;
    color: azure;
    font-family: $ff;

    .box {
        width: 100%;
        min-height: 40vh;
        border-bottom: 1px solid rgba(240, 255, 255, 0.212);
        display: flex;
        justify-content: center;
        gap: 1rem;
        position: relative;

        .goTop{
            width: 2.5rem;
            height: 2.5rem;
            background: rgba(255, 255, 255, 0.619);
            position: absolute;
            right: 1rem;
            bottom: 1rem;
            transition: .3s ease-in;
            cursor: pointer;
            color: #0460ea;
            font-size: 1.5rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            &:hover{
                background: rgba(255, 255, 255, 0.945);
            }
        }
        .conatiner-rw {
            width: 45%;
            min-height: 100%;
            display: flex;

            .con-rw {
                width: 50%;
                min-height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                padding: 1vw;
                gap: 1rem;

                >div {
                    display: flex;
                    align-items: center;
                    gap: .5vw;
                    width: 100%;
                    font-size: .9rem;
                }

                .logo {
                    font-family: $logo_font;
                    font-size: 3rem;
                }

                p {
                    font-size: .9rem;
                    color: #a5a2a2;
                }

                a {
                    color: #a5a2a2;

                    &:hover {
                        color: azure;
                    }
                }
            }

            .form {
                align-items: flex-start;
                gap: 0;

                input {
                    width: 100%;
                    height: 2.5rem;
                    padding: 0 1rem;
                    border: none;
                    outline: none;
                    margin: 0.5em 0 1rem 0;
                }

                button {
                    width: 6rem;
                    height: 2.5rem;
                    padding: 0 1rem;
                    border: none;
                    outline: none;
                    border-radius: 5px;
                    background-color: #2E6BC6;
                    color:aliceblue ;
                    font-size: .9rem;
                    cursor: pointer;
                    transition: .3s ease-in-out;

                    &:hover {

                        background-color: red;
                        font-weight: bold;
                        color: azure;
                    }
                }
            }
        }
    }

    .conatiner-cm {
        width: 100%;
        height: 8vh;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 5vw;
        font-size: .9rem;
        color: #a5a2a2;

        >div {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 2vw;

            >img {
                height: 60%;
                filter: grayscale(100%);
                transition: .5s ease;

                &:hover {
                    filter: grayscale(0);
                }
            }
        }

    }
}

@media screen and (max-width:1200px) {
    footer {
        .box {
            width: 100%;
            min-height: 35vh;

            .conatiner-rw {
                width: 48%;

                .con-rw {
                    h2 {
                        font-size: 1.2rem;
                    }
                }
            }
        }

        .conatiner-cm {
            width: 100%;
            min-height: 8vh;
        }
    }
}

@media screen and (max-width:900px) {
    footer {
        .box {
            width: 100%;
            min-height: 35vh o !important;

            .conatiner-rw {
                width: 90%;
                height: fit-content !important;
                flex-direction: column;

                input {
                    width: 45vw !important;
                }

                .con-rw {
                    margin: 1rem auto;
                    width: 100%;

                    h2 {
                        font-size: 1.2rem;
                    }
                }
            }
        }

        .conatiner-cm {
            width: 100%;
            min-height: 8vh;
        }
    }

    .form {
        margin-top: 4rem !important;
    }
}

@media screen and (max-width:500px) {
    .box {
        flex-direction: column !important;
        align-items: center;
    }

    .conatiner-rw {
        width: 90%;
        height: fit-content !important;
        flex-direction: column;
    }

    .quick-nav {
        display: none !important;
    }

    .con-rw {
        margin: 0 auto !important;
        width: 100%;
    }

    footer .box .conatiner-rw .form input {
        width: 70vw !important;
    }
}

@media screen and (max-width:450px) {
    .conatiner-cm {
        flex-direction: column;
        height: fit-content !important;
        padding: 1rem !important;
        gap: 1rem;

        img {
            height: 2rem !important;
        }
    }

    footer .box .conatiner-rw .con-rw {
        margin-bottom: 1rem !important;
    }
}
</style>