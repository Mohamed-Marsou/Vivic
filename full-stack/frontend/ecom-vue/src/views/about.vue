<script setup>

</script>
<template>
    <div class="about-us">
        <div class="intro">
            <div class="i-ctx">
                <span>Some words about us</span>
                <h2>We Help Everyone Enjoy Amazing Products</h2>
                <p>
                    We're dedicated to making your online shopping experience as enjoyable and seamless as possible. With a
                    commitment to exceptional customer service, secure transactions, and timely delivery, we're here to
                    exceed your expectations. Welcome to our world of outstanding products and endless possibilities – we're
                    thrilled to be part of your journey.
                </p>
            </div>
            <div class="i-gall">
                <div>
                    <div class="div1">
                        <img src="https://img.freepik.com/free-photo/businesspeople-meeting-office-working_23-2148908922.jpg?w=740&t=st=1692962134~exp=1692962734~hmac=41aa8f0917a6885f728acceb189b8e57c8ef04f6d62eecae91d47cc2c30866c5"
                            alt="cover">
                    </div>
                    <div class="div2">
                        <img src="https://img.freepik.com/free-photo/guy-shows-document-girl-group-young-freelancers-office-have-conversation-working_146671-13569.jpg?w=900&t=st=1692962160~exp=1692962760~hmac=6cb4c3c9556cb045eca41a576281bb7e2417f0d6d195763a1b949baf1ab4e4fb"
                            alt="cover">
                    </div>
                    <div class="div3">
                        <img src="https://img.freepik.com/free-photo/medium-shot-colleagues-meeting_23-2149006860.jpg?w=900&t=st=1692962183~exp=1692962783~hmac=e5caf6b67be28db43173452dcdefb42d730b6057ef74e5490aa5a3496c75b2d3"
                            alt="cover">
                    </div>
                </div>
            </div>
        </div>

        <div class="work-with-us">
            <span>Buyers trust us</span>
            <h2>Crafting Trust Through Quality. Our Strategy Puts You First in Line for Exceptional Products</h2>
            <p>
                Our e-commerce store is more than just a platform; it's a place where convenience meets creativity. It's a
                hub where you can explore, discover, and bring home items that align with your lifestyle. We're not just
                selling products; we're cultivating a community of individuals who appreciate the value of finding something
                special.
            </p>
            <p>
                At the heart of our venture is a simple yet profound goal: to bring joy and satisfaction to every individual
                through our exceptional products. As an e-commerce destination, we're not just selling items – we're
                crafting experiences. From tech enthusiasts to fashion aficionados, we're here to provide a curated
                selection that caters to diverse tastes and desires.
            </p>

            <div class="qua-box">
                <div class="main-box">
                    <div class="bx">
                        <i class="fa-solid fa-truck-fast"></i>
                        <h5>Fast Delivery</h5>
                        <p>Bringing you the joy of your purchase at lightning speed.</p>
                    </div>
                    <div class="bx">
                        <i class="fa-solid fa-thumbs-up"></i>
                        <h5>Best Quality</h5>
                        <p>Experience the pinnacle of craftsmanship with our unmatched quality standards.</p>
                    </div>
                    <div class="bx">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        <h5>Free Returns</h5>
                        <p>Your satisfaction is our priority – enjoy hassle-free returns within 30 days.</p>
                    </div>
                </div>
            </div>


            <div class="wwu-cta">
                <div>
                    <h1>Join our team</h1>
                    <p>
                        we're not just building products – we're crafting opportunities. Joining our team means becoming
                        part of a dynamic family that's driven by innovation and fueled by passion. We believe in nurturing
                        talents and empowering growth, providing an environment where creativity and collaboration thrive.
                    </p>
                    <button>
                        Join
                    </button>
                </div>

                <img src="https://img.freepik.com/free-photo/picture-many-business-people-working-laptop-computer-people-watching-video-pictures-about-companies-enterprises-firms_549566-791.jpg?w=900&t=st=1692962207~exp=1692962807~hmac=d49dd2252a1b52903a5463b40ca6ffcb7a1e9fc4713c90b1cb55593cdb48342b"
                    alt="cover">
            </div>
        </div>

    </div>
</template>
<style lang="scss">
@import '@/style/_global.scss';

.about-us {
    width: 100%;
    min-height: 80vh;
    font-family: $ff;

    .intro {
        width: 95%;
        min-height: 80vh;
        margin: 1rem auto;
        display: flex;

        >div {
            width: 50%;
            min-height: 80vh;
        }

        .i-ctx {
            padding: 1rem 5vw;
            @include flex($ai: flex-start);
            flex-direction: column;

            span {
                font-size: .9rem;
                color: #2e6bc6;
                padding: 5px 0;
            }

            h2 {
                font-size: 3rem;
            }

            p {
                color: #555;
                font-size: .9rem;
                padding: 1rem 0;
                line-height: 2rem;
            }
        }

        .i-gall {
            @include flex();

            >div {
                width: 80%;
                min-height: 90%;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-template-rows: repeat(6, 1fr);
                grid-column-gap: 0px;
                grid-row-gap: 0px;
                gap: 10px;

                >div {
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 1px 1px 5px 5px #0000001e;

                    >img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                }

                .div1 {
                    grid-area: 1 / 1 / 7 / 3;
                }

                .div2 {
                    grid-area: 1 / 3 / 4 / 5;
                }

                .div3 {
                    grid-area: 4 / 3 / 7 / 5;
                }
            }

        }
    }

    .work-with-us {
        width: 95%;
        min-height: 80vh;
        margin: 2rem auto;
        display: flex;
        flex-direction: column;
        text-align: center;

        >span {
            color: #2e6bc6;
        }

        >h2 {
            margin: 1rem 2vw;
            padding: 1rem 0;
            text-align: center;
        }

        >p {
            padding: 1rem 2vw;
            color: #555;
            font-size: .9rem;
            line-height: 2rem;
            text-align: center;
        }

        .qua-box {
            width: 100%;
            min-height: 40vh;
            padding-bottom: 1rem;

            .main-box {
                width: 80%;
                min-height: 18rem;
                margin: 1rem auto;
                display: flex;
                justify-content: space-around;
                flex-wrap: wrap;

                >div {
                    width: 22vw;

                    height: 16rem;
                    @include flex($jc: space-around);
                    flex-direction: column;
                    padding: 1vw 1rem;

                    >i {
                        font-size: 3rem;
                        color: #2E6BC6;
                    }

                    h5 {
                        font-size: 1.5rem;
                    }

                    p {
                        color: #555;
                    }
                }
            }
        }

        .wwu-cta {
            width: 80%;
            height: 20rem;
            margin: 1rem auto;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            box-shadow: 1px 1px 5px 5px #0000003b;
            color: #fff;

            >img {
                width: 30%;
                height: 100%;
                object-fit: cover;
            }

            >div {
                width: 70%;
                height: 100%;
                background: #2E6BC6;
                @include flex($ai: flex-start);
                flex-direction: column;
                padding: 1rem 2vw;
                gap: 1rem;

                >p {
                    text-align: left;
                }

                >button {
                    width: 8rem;
                    height: 3rem;
                    background: #fff;
                    color: #2E6BC6;
                    border: none;
                    border-radius: 20px;
                    font-weight: bold;
                    cursor: pointer;
                }
            }
        }
    }
}

@media screen and (max-width: 1200px) {
    .about-us {
        .intro {
            .i-ctx {
                padding: 1rem 2vw;
                width: 40%;

                h2 {
                    font-size: 2rem;
                }
            }

            .i-gall {
                width: 60%;

                >div {
                    width: 90%;
                }

            }
        }
        .work-with-us {
            width: 90%;
        }
        .wwu-cta {
            width: 90% !important;
        }
    }
            
}

@media screen and (max-width:1024px) {
    .about-us {
        .intro {
            flex-direction: column;
            margin-top: 3rem;
            >div {
                width: 100% !important;
                min-height: fit-content !important;
                padding: 1rem 1vw;
            }
            .i-gall {
                @include flex();
                >div {
                    width: 85%;
                display: flex;

                }
            }
        }

        .wwu-cta {
            width: 98% !important;
            height: 22rem !important;

            >img {
                width: 0 !important;
            }
            >div {
                width: 100% !important;
            }
        }
    }
}
@media screen and (max-width:768px) {
    .about-us {
        .intro {
            flex-direction: column;
            margin-top: 3rem;
            
        .i-gall {
            @include flex();

            >div {
                width: 100%;
                display: flex;
                flex-direction: column;

                >div {
                    width: 100% !important;
                    height: 30rem !important;
                    &:first-child{
                        display: none;
                    }
                    &:nth-child(3){
                        display: none;
                    }
                }
            }
        }
        }
    }
}
@media screen and (max-width:450px) {

    .work-with-us {
            width: 100% !important;
            margin-bottom: 0 !important;
            >h2{
                font-size: 1.2rem;
            }
        }

        .wwu-cta {
            min-width: 100% !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
            >div {
                width: 100% !important;
                >h1{
                    font-size: 1.5rem;
                }
                >p{
                    font-size: .8rem;
                    margin: 1rem 0;
                }
            }
        }
}
</style>