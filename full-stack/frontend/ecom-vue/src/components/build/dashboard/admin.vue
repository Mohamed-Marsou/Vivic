<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../../../http/api';
import Loading from '../../build/loading.vue';
import axios from 'axios';

const loading = ref(true)
const overlay = ref(false)
const adminsDATA = ref([])

const responseMessage = ref('')
onMounted(async () => {
    await getAdminData()
    // sett loading to false 
    loading.value = false
})

async function getAdminData() {
    const res = await api.get('/admins')
    adminsDATA.value = res.data.admins
}
async function loadMore(btn) {
    try {
        // Check if there is a next_page_url available
        if (adminsDATA.value.next_page_url) {
            const response = await axios.get(adminsDATA.value.next_page_url);

            adminsDATA.value.data = adminsDATA.value.data.concat(response.data.admins.data);

            adminsDATA.value.next_page_url = response.data.admins.next_page_url;

            if (!adminsDATA.value.next_page_url) {
                btn.target.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading more data:', error);
    }
}

function toggoleModel() {
    overlay.value = !overlay.value
    EmptyValues()
}
function EmptyValues() {
    errors.name = ''
    errors.email = ''
    errors.password = ''
    name.value = ''
    email.value = ''
    password.value = ''

    responseMessage.value = ''

}
const name = ref('');
const email = ref('');
const password = ref('');
const adminRole = ref('');
const errors = reactive({
    name: '',
    email: '',
    password: '',
});
async function addNewAdmin() {

    // Reset previous errors
    for (const key in errors) {
        errors[key] = '';
    }

    // Validation regex patterns
    const nameRegex = /^[a-zA-Z\s]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Perform validation
    let isValid = true;

    if (!name.value || !name.value.match(nameRegex)) {
        errors.name = 'Please enter a valid name';
        isValid = false;
    }

    if (!email.value || !email.value.match(emailRegex)) {
        errors.email = 'Please enter a valid email address';
        isValid = false;
    }

    if (!password.value || password.value.length <= 8) {
        errors.password = 'Password must be at least 8 characters long';
        isValid = false;
    }

    // If validation fails, return without sending a request
    if (!isValid) {
        return;
    }

    // Validation passed, prepare the payload
    const payload = {
        name: name.value,
        email: email.value,
        password: password.value,
        adminRole: adminRole.value,
    };

    try {
        const res = await api.post('/admin', payload);
        toggoleModel()
        getAdminData()
    } catch (error) {
        // Handle any errors that occur during the API request
        console.error('something went wrong in "addNewAdmin" : ');
        console.error(error);

        responseMessage.value = error.response.data.message
    }

}


</script>

<template>
    <div v-if="!loading" class="admin-wrapper">
        <div class="overlay" :class="{ showOverlay: overlay }">

            <div class="adding-container">
                <h1>Add new Admin</h1>
                <span>{{ responseMessage }}</span>
                <div class="add-wrapper">
                    <div class="a_slot">
                        <label for="name">Name <small>*</small>:</label>
                        <input type="text" id="name" placeholder="Name" v-model="name" />
                        <span class="error" v-if="errors.name">{{ errors.name }}</span>
                    </div>
                    <div class="a_slot">
                        <label for="email">Email <small>*</small>:</label>
                        <input type="email" id="email" placeholder="Email" v-model="email" />
                        <span class="error" v-if="errors.email">{{ errors.email }}</span>
                    </div>
                    <div class="a_slot">
                        <label for="password">Password <small>*</small>:</label>
                        <input type="password" id="password" placeholder="Password" v-model="password" />
                        <span class="error" v-if="errors.password">{{ errors.password }}</span>
                    </div>
                    <div class="a_slot">
                        <select v-model="adminRole">
                            <option disabled value="">
                                Select admin Role
                            </option>
                            <option value="admin">admin</option>
                        </select>
                    </div>

                    <div class="a_slot btns">
                        <button @click="addNewAdmin">Submit</button>
                        <button @click="toggoleModel">Cancel</button>
                    </div>
                </div>
                <i @click="toggoleModel" class="fa-solid fa-xmark"></i>

            </div>

        </div>
        <header>
            <h1>
                <i class="fa-solid fa-users"></i>
                Admins
            </h1>

        </header>

        <div v-if="adminsDATA.data.length > 0" class="table-container">
            <div class="t-head">
                <div class="search-div">
                    <input type="text" placeholder="search">
                    <button>
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                </div>
                <button @click="toggoleModel">
                    <i class="fa-solid fa-user-plus"></i>
                    new
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="admin in adminsDATA.data" :key="admin.id">
                        <td>{{ admin.id }}</td>
                        <td>
                            {{ admin.name }}
                        </td>
                        <td>{{ admin.email }}</td>
                        <td>{{ admin.role }}</td>
                    </tr>
                </tbody>
            </table>

            <button @click="loadMore($event)">
                LOAD MORE
                <i class="fa-solid fa-angles-down"></i>
            </button>
        </div>
        <div v-else class="empty-table">
            <img src="../../../assets/images/wired-flat-57-server.gif" alt="empty Cart">
            <h1>NO DATA AVAILABLE</h1>
        </div>
    </div>
    <div v-else class="loading">
        <Loading />
    </div>
</template>

<style lang="scss">
@import '@/style/_global.scss';

.admin-wrapper {
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
        background: #00000092;
        top: 0;
        right: 0;
        @include flex();
        display: none;

        .adding-container {
            width: 40rem;
            min-height: 30rem;
            background: #ffffff;
            border-radius: 10px;
            position: relative;
            display: flex;
            flex-direction: column;

            >h1 {
                padding: 1.5rem;
                text-transform: uppercase;
            }

            >span {
                font-size: .9rem;
                color: red;
                padding: 0 1.5rem;

            }

            .add-wrapper {
                width: 100%;
                display: flex;
                justify-content: center;
                flex-direction: column;
                gap: 2rem;
                margin-top: 1rem;
                padding: 0 2rem;

                .a_slot {
                    width: 100%;
                    display: flex;
                    flex-direction: column;

                    label {
                        color: #555;
                        font-size: .9rem;

                        >small {
                            color: rgba(255, 0, 0, 0.587);
                        }
                    }

                    input {
                        border: #0000001e 1px solid;
                        width: 95%;
                        height: 3rem;
                        padding: 0 15px;
                        border-radius: 5px;
                    }

                    span {
                        font-size: .8rem;
                        color: red;
                        padding: 5px;
                    }

                    select {
                        border: #0000001e 1px solid;
                        width: 10rem;
                        height: 2rem;
                        padding: 5px;
                        border-radius: 5px;
                    }

                }

                .btns {
                    flex-direction: row;
                    margin-bottom: 2rem;
                    justify-content: space-between;

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

        }
    }

    header {
        width: 90%;
        min-height: 3rem;
        @include flex($jc: space-between);
        margin: 4rem auto;

        h1 {
            font-size: 2rem;
            text-transform: uppercase;

            >i {
                margin-right: 5px;
            }
        }


    }

    .table-container {
        width: 80%;
        min-height: 6rem;
        margin: 2rem auto;
        display: flex;
        flex-direction: column;
        .t-head{
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding-right:  2.3rem;
            .search-div {
                border: 1px solid rgba(57, 57, 57, 0.07);
                width: 22rem;
                height: 2.8rem;
                margin: 0 2.7%;
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

            
        >button {
            width: 7rem;
            height: 2.5rem;
            border: none;
            color: #fff;
            background: #2c2e3e;
            cursor: pointer;
            transition: .3s ease-in-out;
            text-transform: capitalize;
            i{
                margin-right: 5px;
            }

            &:hover {
                background: rgb(21, 135, 228);
            }
        }
        }

        table {
            width: 95%;
            margin: 1rem auto;

            td,
            th {
                border: 2px solid rgba(56, 56, 56, 0.075);
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

            button {
                color: #2c2e3e52;
                background: transparent;
                border: none;
                width: 100%;
                height: 100%;
                cursor: pointer;

                &:hover {
                    color: #000;
                    font-weight: bold;
                }
            }
        }

        >button {
            margin: 2rem auto;
            color: #2c2e3e52;
            background: transparent;
            border: none;
            width: 9rem;
            height: 3rem;
            cursor: pointer;
            font-weight: bold;
            border: 2px solid #2c2e3e31;
            transition: .3s ease-out;

            i {
                margin-left: 5px;
                color: #2c2e3e52;
            }

            &:hover {
                border: 2px solid #2c2e3eb1;
                color: #2c2e3eb1;

                i {
                    color: #2c2e3eb1;
                }
            }
        }
    }

    .empty-table {
        width: 80%;
        height: 60%;
        @include flex();
        margin: 0 auto;
        position: relative;

        h1 {
            font-size: 2.5rem;
            z-index: 2;
        }

        >img {
            position: absolute;
            bottom: 50%;
            left: 50%;
            opacity: .8;
            transform: translate(-50%, 10%);
            width: 12rem;
        }
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