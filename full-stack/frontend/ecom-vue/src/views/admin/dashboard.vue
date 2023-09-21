<script setup>
import DashVue from '../../components/build/dashboard/dash.vue'
import ProductVue from '../../components/build/dashboard/products.vue'
import AdminVue from '../../components/build/dashboard/admin.vue'
import Cookies from 'js-cookie';

import {useAdminStore} from '../../stores/admin'

const adminStore = useAdminStore()

import { ref,onMounted } from 'vue'
import { RouterLink } from 'vue-router'

const adminName = ref('')
onMounted(()=>{
    adminName.value = JSON.parse(Cookies.get('auth-admin')).name ?? ''
})

const view = ref('Home')
const OpendSideBar = ref(true);


function getFormattedDateTime() {
    const currentDate = new Date();

    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');


    const formattedDate = `${year}-${month}-${day}`;


    return `${formattedDate}`;
}
const OpenAside = () => {
    OpendSideBar.value = !OpendSideBar.value
}
const changeView = (v) => {
    view.value = v
}
const logOut =()=>{
    adminStore.adminLogOut()
}

</script>
<template>
    <section>
        <aside :class="{ closedAside: OpendSideBar }">
            <header>
                <RouterLink to="/">
                    <h2 v-if="!OpendSideBar">Vivic</h2>
                    <i v-if="OpendSideBar" title="Visit website" class="fa-brands fa-hashnode"></i>
                </RouterLink>
            </header>

            <ul>
                <li :class="{ activeLink: view === 'Home' }" @click="changeView('Home')">
                    <a>
                        <i title="Home" class="fa-solid fa-gauge-high"></i>
                        <p>Home</p>
                    </a>
                </li>
                <li :class="{ activeLink: view === 'ProductVue' }" @click="changeView('ProductVue')">
                    <a>
                        <i title="Products" class="fa-solid fa-box-open"></i>
                        <p>Products</p>
                    </a>
                </li>
                <li :class="{ activeLink: view === 'AdminVue' }" @click="changeView('AdminVue')">
                    <a>
                        <i  title="Admins" class="fa-solid fa-user-tag"></i>
                        <p>Admins</p>
                    </a>
                </li>
            </ul>

            <li title="LogOut" id="logOut-Btn">
                <a title="Log Out" @click="logOut" >
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <p>LogOut</p>
                </a>
            </li>
        </aside>
        <main :class="{ mainFullWidth: OpendSideBar }">
            <header>
                <div class="headerBox">
                        <i class="fa-solid fa-bars" @click="OpenAside"></i>
                    <h2>Welecome back , {{ adminName }}</h2>
                </div>
                <div class="headerBox TandN">
                    <h1>Dashboard</h1>
                    <p>{{ getFormattedDateTime() }}</p>
                    <div class="noti-wrapper">
                        <i class="fa-solid fa-bell"></i>

                        <div class="num">
                            <small>0</small>
                        </div>

                    </div>
                </div>
            </header>


            <div class="dash-view">
                <DashVue v-if="view === 'Home'" />
                <ProductVue v-if="view === 'ProductVue'" />
                <AdminVue v-if="view === 'AdminVue'" />
            </div>
        </main>
    </section>
</template>

<style lang="scss">
@import '@/style/_global.scss';

@import url('https://fonts.googleapis.com/css2?family=Racing+Sans+One&display=swap');
$logo_font: 'Racing Sans One', cursive;

section {
    width: 100vw;
    height: 100vh;
    font-family: $ff;
    display: flex;

    aside {
        width: 12%;
        height: 100vh;
        background-color: #2c2e3e;
        color: azure;
        position: relative;
        padding: 0 0 1rem 0;
        transition: .3s ease-in;

        #logOut-Btn {
            margin-top: 4rem;
            width: 100%;
            height: 4rem;
            background: rgba(255, 0, 0, 0.192);
            padding: 1rem;
            bottom: 0;
            position: absolute;
            transition: .3s ease-in;
            cursor: pointer;

            &:hover {
                background: rgba(255, 0, 0, 0.604);
            }

            a {
                @include flex($jc: flex-start);
                gap: 1vw
            }
        }

        a {
            color: azure;
        }

        >header {
            width: 100%;
            height: 5rem;
            font-size: 1.5rem;
            cursor: pointer;
            @include flex();

            h2 {
                font-family: $logo_font;
                letter-spacing: 2px;
            }

            i {
                display: none;
                font-size: 2.2rem;
                transition: .3s ease-in-out;
                &:hover{
                    color: #2563EB;
                }
            }
        }

        ul {
            width: 100%;
            min-height: 15rem;
            margin-top: 6rem !important;
            font-size: .9rem;
            li {
                width: 100%;
                height: 4.5rem;
                padding: 1.3rem 1rem;
                cursor: pointer;
                margin-top: 20px;
                transition: .3s ease-in;
                cursor: pointer;
                position: relative;
                i{
                    font-size: 1.1rem;
                }
                &:hover {
                    background: rgba(255, 253, 253, 0.176);
                    transform: translateY(-5px);
                }

                a {
                    @include flex($jc: flex-start);
                    gap: 1vw
                }

            }
        }
    }

    main {
        width: 88%;
        height: 100vh;
        transition: .3s ease-in;
        header {
            width: 100%;
            height: 5rem;
            display: flex;
            justify-content: space-between;
            padding: 0 1vw;
            background: white;

            .headerBox {
                width: fit-content;
                height: 100%;
                @include flex();
                gap: .5vw;
                padding: 0 1vw;
                user-select: none;

                span {
                    background: #2c2e3e;
                    border-radius: 50%;
                    color: azure;
                    padding: 7px;
                    cursor: pointer;
                }

                h2 {
                    font-size: 1rem;
                }
                .fa-arrow-right{
                    font-size: 1.3rem;
                    cursor: pointer;
                }
            }

            .TandN {
                width: 20rem;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 0;
                position: relative;

                h1 {
                    font-size: 1.2rem;
                    padding: 5px 0;
                }

                p {
                    font-size: .8rem;
                    color: #666;
                }
                .noti-wrapper {
                    position: absolute;
                    right: 15px;
                    top: 15px;
                    i{
                        font-size: 1.7rem;
                    }
                    
                    .num {
                        position: absolute;
                        top: 0;
                        right: -4px;
                        width: 15px;
                        height: 15px;
                        background-color: red;
                        color: azure;
                        border-radius: 50%;
                        @include flex();
                        font-size: .8rem;
                    }
                }
            }
        }
    }

    .dash-view {
        width: 100%;
        background-color: #66666625;
        height: calc(100vh - 5rem);
        @include flex();

        .Comp__Container {
            width: 97%;
            height: 88vh;
            border-radius: 5px 5px 0 0;
            background-color: transparent;
        }
    }
}

.closedAside {
    width: 5vw !important;

    #logOut-Btn {
        a {
            justify-content: center !important;

            p {
                display: none;
            }
        }
    }

    a {
        color: azure;

        p {
            display: none;
        }
    }

    header {
        h2 {
            display: none;
        }

        img {
            display: block !important;
            width: 3rem;
            margin: 1.5rem auto 0 auto;
        }
    }

    ul {
        width: 100%;
        min-height: 15rem;
        margin-top: .5rem;

        li {
            a {
                justify-content: center !important;
            }

        }
    }
}

.mainFullWidth {
    width: 95vw !important;
}

@media screen and (min-width : 1300px) {
    .closedAside {
        width: 4vw !important;
    }

    .mainFullWidth {
        width: 96vw !important;
    }
}
@media screen and (max-width : 1300px) {
    .closedAside {
        width: 6vw !important;

        header {
            h2 {
                display: none;
            }

            img {
                width: 4.5vw !important;
            }
        }
    }

    .mainFullWidth {
        width: 94vw !important;
    }
}

@media screen and (max-width : 1200px) {
    .closedAside {
        width: 7vw !important;
    }

    .mainFullWidth {
        width: 93vw !important;
    }
}

.activeLink {
    &::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background-color: #3da5f4;
    }
}
</style>