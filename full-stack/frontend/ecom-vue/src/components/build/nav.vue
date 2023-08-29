<script setup>
import { ref, onMounted,computed ,watch } from 'vue'
import { RouterLink } from 'vue-router';
import { useAuthtStore } from '../../stores/auth'
import { useProductStore } from '../../stores/product';

const authtStore = useAuthtStore()
const productStore = useProductStore()

const isUserAuth = computed(() => authtStore.isAuth)

const wishlistItems = ref(productStore.whishListCount);
const inCartItems = ref(productStore.whishListCount);

const visibleLinks = ref(false)
const visibleAuth = ref(false)

onMounted(async()=>{
    authtStore.checkAuth();
    productStore.getWishlistProducts();
    productStore.getInCartProducts();
})
watch(
  () => productStore.whishListCount,
  (newValue, oldValue) => {
    wishlistItems.value = newValue
  }
);
watch(
  () => productStore.inCartCount,
  (newValue, oldValue) => {
    inCartItems.value = newValue
  }
);
const toggleLinks = () => {
    visibleLinks.value = !visibleLinks.value;
    // Toggle body scrolling
    document.body.style.overflow = visibleLinks.value ? 'hidden' : 'auto';
};

const toggleAuth = () => {
    visibleAuth.value = !visibleAuth.value;
    // Toggle body scrolling
    document.body.style.overflow = visibleAuth.value ? 'hidden' : 'auto';
};
const clearPage = () => {
    toggleLinks()
    toggleAuth()
} 
const profileRoute = isUserAuth.value ? { name: 'user-auth' } : { name: 'profile' } ;
</script>

<template>
    <nav id="main-nav">
        <RouterLink :to="{ name: 'home' }"> Ecommrece </RouterLink>
        <ul class="NavLinks" :class="{ showMobileNav: visibleLinks }">
            <div id="search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Type product name">
            </div>
            <li>
                <RouterLink :to="{ name: 'home' }"> Home </RouterLink>
            </li>
            <li>
                <RouterLink :to="{ name: 'new' }"> New </RouterLink>
            </li>
            <li>
                <RouterLink :to="{ name: 'collections' }"> Collections </RouterLink>
            </li>
            <li>
                <RouterLink :to="{ name: 'contact' }"> Contact </RouterLink>
            </li>
            <li>
                <RouterLink :to="{ name: 'about' }"> About us </RouterLink>
            </li>


            <li class="mobile-li">
                <a>Track Orders</a>
            </li>
            <li class="mobile-li">
                <RouterLink :to="{ name: 'wishlist' }"> Wishlist </RouterLink>
            </li>
            <li class="mobile-li">
                <RouterLink  :to="profileRoute" > Profile </RouterLink>
            </li>
            <li class="mobile-li">
                <a>FAQs</a>
            </li>
        </ul>
        <ul class="short-icns" >
            <p :class="{ hidden : isUserAuth}" @click="toggleAuth">
                <span>Login</span>
                <small>/</small>
                <span>Register</span>
            </p>
            <RouterLink :to="{ name: 'profile' }" id="userIcon" :class="{ show : isUserAuth}"> 
                <i class="fa-regular fa-user"></i>
            </RouterLink>
            <div class="icon-container">
                <RouterLink :to="{ name: 'wishlist' }"> 
                    <i class="fa-regular fa-heart"></i> 
                </RouterLink>
                
                <span>{{ wishlistItems }}</span>
            </div>
            <RouterLink :to="{ name: 'cart' }"> 
            <div class="icon-container Cart">
                    <span>{{ inCartItems }}</span>
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </RouterLink>
        </ul>
        <div class="mobile-menu " :class="{ clickedHam: visibleLinks }" @click="toggleLinks">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <div class="overlay" :class="{ hideEl: visibleLinks }" @click="toggleLinks"></div>
        <div class="overlay2" :class="{ hideEl: visibleAuth }" @click="toggleAuth"></div>

        <div id="auth-wrapper" :class="{ showAuth: visibleAuth }">
            <div class="auth-header">
                <p>Login</p>
                <span @click="toggleAuth">
                    X Close
                </span>
            </div>
            <div class="slot">
                <label for="email">Email <small>*</small>:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="slot">
                <label for="password">Password <small>*</small>:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="slot lst">
                <div>
                    <input type="checkbox">
                    <p>Remember me</p>
                </div>
                <span>Forgot Password ?</span>
            </div>
            <div class="slot ">
                <button>Login</button>
            </div>
            <div class="no-acc">

                <RouterLink :to="{ name: 'user-auth' }" @click="clearPage"> Create an account </RouterLink>
                <i class="fa-regular fa-user"></i>
            </div>
        </div>
    </nav>
</template>

<style lang="scss" scoped>
@import '@/style/_global.scss';

#main-nav {
    width: 100%;
    height: 4.5rem;
    font-family: $ff;
    @include flex();
    padding: 0 3vw;
    position: relative;

    >a {
        font-size: 1.45rem;
        font-weight: bold;
        cursor: pointer;
        width: 20%;

    }

    >ul {
        width: 50%;
        height: 4.5rem;
        display: flex;
        align-items: center;
        gap: 2.5rem;
        padding: 0 1vw;
        font-size: .9rem;

        .mobile-li {
            display: none !important
        }

        >* {
            cursor: pointer;
        }

        >p {
            transition: .3s ease;

            small {
                padding: 0 5px;
            }

            &:hover {
                color: #555;
            }
        }


        i {
            font-size: 1.3rem;
        }

        .icon-container {
            @include flex();
            position: relative;

            >span {
                position: absolute;
                font-size: .7rem;
                top: -8px;
                right: -8px;
                background: #2e6bc6;
                color: #fff;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                @include flex();

            }
        }

        #search {
            display: none !important;
        }

    }

    .NavLinks {
        justify-content: center;
    }

    .short-icns {
        justify-content: flex-end;
        width: 30%;

    }

    .mobile-menu {
        width: 2.4rem;
        height: 2rem;
        position: absolute;
        left: 1rem;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        display: none;

        >div {
            transition: .3s ease-in;
            width: 100%;
            height: 5px;
            background: black;
        }
    }

}

#auth-wrapper {
    position: absolute;
    top: 0;
    right: 0;
    height: 100vh;
    background: #fff;
    z-index: 2;
    width: 25vw;
    transform: translateX(110%);
    opacity: .5;
    transition: .4s ease-in-out;

    .auth-header {
        width: 100%;
        height: 4.5rem;
        border-bottom: 1px solid #55555541;
        @include flex($jc: space-between);
        padding: 0 1.5rem;
        font-weight: bold;
        font-size: 1.2rem;
        color: #555;

        >span {
            font-size: .9rem;
            cursor: pointer;


            &::first-letter {
                font-weight: normal;
                font-size: 1rem;
                padding: 4px;
            }
        }
    }

    .slot {
        width: 100%;
        min-height: fit-content;
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 5px 1vw;

        small {
            color: red;
        }

        label {
            font-size: .9rem;
        }

        >input {
            width: 95%;
            height: 3rem;
            padding: 0 7px;
            border: 1px solid #55555541;
        }

        >button {
            width: 95%;
            height: 3rem;
            padding: 0 7px;
            border: none;
            background: #2e6bc6af;
            color: #fff;
            cursor: pointer;
            transition: .3s ease;

            &:hover {
                background: #1368e7;
            }

        }

    }

    .no-acc {
        width: 100%;
        min-height: 10rem;
        margin-top: 2rem;
        @include flex();
        position: relative;

        a {
            font-size: 1rem;
            text-transform: uppercase;
            padding: 2px;
            font-weight: bold;
            cursor: pointer;
             position: relative;


            &::after {
                content: "";
                width: 100%;
                height: 3px;
                background: #2e6bc6;
                position: absolute;
                bottom: 0;
                right: 0;
            }
        }

        i {
            position: absolute;

            font-size: 3.5rem;
            color: #5555553a;
        }
    }

    .lst {
        margin-top: 10px;
        flex-direction: row;
        justify-content: space-between;
        font-size: .85rem;

        span {
            color: #2e6bc6;
            text-decoration: underline;
        }

        >div {
            display: flex;
            color: #555;
            gap: 4px;
        }
    }
}

#userIcon {
    display: none;
}

@media screen and (max-width:1200px) {
    #auth-wrapper {
        width: 30vw !important;
    }
}

@media screen and (max-width:1024px) {
    #main-nav {
        justify-content: flex-end;

        >a {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        >a {
            font-size: 1.7rem;
            width: fit-content;
        }

        .short-icns {

            p,
            div,
            i {
                display: none;
            }

            .Cart {
                display: flex;

                >i {
                    display: block;
                }
            }
        }

        .NavLinks {
            position: absolute;
            left: 0;
            top: 4.5rem;
            width: 30vw;
            min-height: calc(100vh - 4.5rem) !important;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: 0;
            z-index: 3;
            background: #ffffff;
            transition: .3s ease-in;
            transform: translateX(-100%);
            overflow-y: scroll;

            >li {
                width: 100%;
                height: 4rem;
                border-bottom: 1px solid #5555554a;
                display: flex;
                padding: 0 1rem;
                align-items: center;
                transition: .3s ease-in-out;

                &:hover {
                    border-bottom: 2px solid #2e6bc6;
                }

                a {
                    font-weight: bold;
                }
            }

            .mobile-li {
                display: flex !important
            }

            #search {
                width: 100%;
                height: 3.5rem;
                border-bottom: 1px solid #5555554a;
                display: flex !important;
                margin-bottom: 1rem;

                >i {
                    width: 20%;
                    height: 100%;
                    @include flex();
                    background-color: #2e6bc6;
                    color: #fff;
                }

                input {
                    width: 80%;
                    height: 100%;
                    border: none;
                    outline: none;
                    padding: 0 1vw;
                }
            }

        }

        .mobile-menu {
            display: flex !important;
            cursor: pointer;
        }

        .overlay {
            width: 100%;
            min-height: 100vh !important;
            background: #4c4c4c73;
            position: absolute;
            top: 4.5rem;
            left: 0;
            display: none;
            z-index: 2;
        }

    }

    #auth-wrapper {
        width: 40vw !important;
    }
}

@media screen and (max-width:768px) {
    #main-nav {
        .NavLinks {
            width: 45vw;
        }
    }
    #auth-wrapper {
        width: 55vw !important;
    }
}

@media screen and (max-width:450px) {
    #main-nav {
        >a {
            font-size: 1.4rem;
        }

        .NavLinks {
            width: 65vw;
        }
    }
    #auth-wrapper {
        width: 90vw !important;
    }
}

.showHamMenu {
    display: flex !important;
}

.hideEl {
    display: block !important;
}
.show{
    display: flex !important;
}
.hidden
{
    display: none !important;
}
.showMobileNav {
    transform: translateX(0) !important;
}

.showAuth {
    transform: translateX(0) !important;
    opacity: 1 !important;
}
.overlay2 {
            width: 100%;
            min-height: 100vh !important;
            background: #4c4c4c73;
            position: absolute;
            top: 4.5rem;
            left: 0;
            display: none;
            z-index: 2;
        }
.clickedHam {
    >div:nth-child(1) {
        transform: rotate(-45deg) translate(-8px, 5px);
    }

    >div:nth-child(2) {
        opacity: 0;
    }

    >div:nth-child(3) {
        transform: rotate(45deg) translate(-8px, -6px);
    }
}</style>