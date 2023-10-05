<script setup>
import { ref, onMounted } from 'vue'
import api from '../../../http/api';
import Loading from '../loading.vue';
const importResponse = ref('Please Select CSV file : ')
const activeImpBtn = ref(false)
const overlayVisible = ref(false)
const selectedFile = ref(null)
const reviewsDATA = ref([])
const loaded = ref(false)
const resLen = ref(0)
onMounted(async () => {
    importReviews()
})

const importReviews =async () =>{
    try {
        const res = await api.get('/reviews')
        reviewsDATA.value = res.data
        resLen.value = Object.keys(reviewsDATA.value).length
        loaded.value = true
    } catch (error) {
        console.error(error);
    }
}
const handleFileInputChange = (event) => {
    selectedFile.value = event.target.files[0];

    if (selectedFile.value) {
        importResponse.value = 'Selected file: ' + selectedFile.value.name
        activeImpBtn.value = true
    }
};
async function postFile() {

    if (selectedFile.value) {
        loaded.value = false

        const formData = new FormData();
        formData.append('csv_file', selectedFile.value);

        await api.post('/reviews/upload-csv', formData)
            .then(async response => {

            await importReviews()
            toggleOverlayImp()
            console.log(response);

            })
            .catch(error => {
                console.error(error);
            });
            loaded.value = true
    } else {
        alert('Please Select a file first !! ')
    }
}
function toggleOverlayImp() {
    overlayVisible.value = !overlayVisible.value
    selectedFile.value = null
    activeImpBtn.value = false

    importResponse.value = "Please Select CSV file : "
}
const deleteReviews = async (slug) => {
    const confirmation = window.confirm(`Are you sure you want to DELETE: ${slug}`);

    if (confirmation) {
        loaded.value = false

        try {
            // Send the delete request
            const res = await api.delete(`reviews/${slug}`);
            
            if (res.status === 200) {
                // Remove the deleted review from reviewsDATA
                delete reviewsDATA.value[slug];
                resLen.value = Object.keys(reviewsDATA.value).length
            } else {
                // Handle any other response status codes or errors here
                console.error('Delete request failed:', res);
            }
        } catch (error) {
            // Handle any exceptions or errors that occur during the request
            console.error('An error occurred:', error);
        }
    }
    loaded.value = true

}; 
</script>
<template>
    <div v-if="loaded" class="reviews-Container">

        <div class="overlay" :class="{ showOverlay: overlayVisible }">
            <div class="reviewsImpWrap">
                <i class="fa-solid fa-xmark" @click="toggleOverlayImp"></i>
                <h3>Import Product Reviews</h3>

                <div class="customFileInput">
                    <small>
                        {{ importResponse }}
                    </small>
                    <i class="fa-solid fa-file-csv"></i>
                    <span>Click/Drop here to upload a file</span>

                    <input type="file" @change="handleFileInputChange($event)" accept=".csv">
                </div>
                <button @click="postFile" :class="{ activeBtn: activeImpBtn }">IMPORT REVIEWS</button>
                <button @click="toggleOverlayImp" id='cancelBtn'>Cancel</button>
            </div>
        </div>

        <header>
            <h2>
                <i title="Reviews" class="fa-solid fa-ranking-star"></i>
                Product Reviews
            </h2>
            <button @click="toggleOverlayImp">IMPORT</button>
        </header>

        <table v-if="resLen > 0">
            <thead>
                <tr>
                    <th>Product name</th>
                    <th>Average Rating</th>
                    <th>Number of Reviews</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="r in reviewsDATA" :key="r.slug">
                    <td>
                        <RouterLink :to="{ name: 'product-page', params: { slug: r.slug } }">
                            {{ r.slug }}
                        </RouterLink>
                    </td>
                    <td>{{ (r.average_rating).toFixed(2) }}</td>
                    <td>{{ r.review_count }}</td>
                    <td>{{ r.category }}</td>
                    <td><button @click="deleteReviews(r.slug)">DELETE</button></td>
                </tr>

            </tbody>
        </table>

        <div v-else class="noReview">
            <h2>No reviews were imported</h2>
            <span class="loader"></span>
        </div>
    </div>

    <div v-else class="loading">
        <loading />
    </div>
</template>
<style lang="scss">
* {
    button {
        cursor: pointer;
        border: none;
    }
}

.loading {
    width: 95%;
    height: 95%;
    background: #fff;

}

.reviews-Container {
    width: 95%;
    height: 95%;
    background: #fff;
    border-radius: 5px;
    display: flex;
    gap: 3rem;
    padding: 2rem 0;
    flex-direction: column;
    position: relative;

    .noReview {
        width: 100%;
        text-align: center;
        font-size: 1.5rem;
        margin-top: 10%;
        text-transform: uppercase;

        >.loader {
            width: 60px;
            height: 60px;
            display: block;
            margin: 20px auto;
            position: relative;
            background: radial-gradient(ellipse at center, #2c2e3e 69%, rgba(0, 0, 0, 0) 70%), linear-gradient(to right, rgba(0, 0, 0, 0) 47%, #2c2e3e 48%, #2c2e3e 52%, rgba(0, 0, 0, 0) 53%);
            background-size: 20px 20px, 20px auto;
            background-repeat: repeat-x;
            background-position: center bottom, center -5px;
            box-sizing: border-box;
        }

        >.loader::before,
        >.loader::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: -20px;
            top: 0;
            width: 20px;
            height: 60px;
            background: radial-gradient(ellipse at center, #2c2e3e 69%, rgba(0, 0, 0, 0) 70%), linear-gradient(to right, rgba(0, 0, 0, 0) 47%, #2c2e3e 48%, #2c2e3e 52%, rgba(0, 0, 0, 0) 53%);
            background-size: 20px 20px, 20px auto;
            background-repeat: no-repeat;
            background-position: center bottom, center -5px;
            transform: rotate(0deg);
            transform-origin: 50% 0%;
            animation: animPend 1s linear infinite alternate;
        }

        .loader::after {
            animation: animPend2 1s linear infinite alternate;
            left: 100%;
        }

        @keyframes animPend {
            0% {
                transform: rotate(22deg);
            }

            50% {
                transform: rotate(0deg);
            }
        }

        @keyframes animPend2 {

            0%,
            55% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-22deg);
            }
        }
    }

    .overlay {
        width: 100%;
        height: 100%;
        background: #000000a2;
        position: fixed;
        top: 0;
        right: 0;
        justify-content: center;
        align-items: center;
        display: none;
        z-index: 66;

        .reviewsImpWrap {
            width: 38rem;
            min-height: 28rem;
            background: #fff;
            position: relative;
            border-radius: 2px;
            display: flex;
            flex-direction: column;
            gap: 2rem;

            .fa-xmark {
                position: absolute;
                right: 15px;
                top: 15px;
                font-size: 1.5rem;
                cursor: pointer;
            }

            >h3 {
                padding: 1rem;
                text-transform: uppercase;
            }

            .customFileInput {
                position: relative;
                color: #333;
                border: 1px dotted #33333332;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                width: 90%;
                margin: 0 auto;
                padding: 1.5rem 0;
                gap: 10px;
                position: relative;

                >input {
                    width: 100%;
                    height: 100%;
                    z-index: 3;
                    cursor: pointer;
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    opacity: 0;
                }

                i,
                span {
                    z-index: 1;
                }

                i {
                    font-size: 4.5rem;
                }

                span {
                    color: #555;
                    font-size: .9rem;
                }

                small {
                    position: absolute;
                    top: -25px;
                    left: 0;
                }
            }

            >button {
                width: 90%;
                margin: 0 auto;
                height: 3rem;
                transition: .3s ease-in;
                cursor: default;
            }
        }
    }

    >header {
        width: 100%;
        height: 6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 2rem;

        >button {
            color: #fff;
            background: #2c2e3e;
            padding: .75rem 2rem;
            border-radius: 2px;
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

        td:has(button) {
            padding: 0;

            >button {
                width: 100%;
                height: 100%;
                padding: 0 0.5rem;
                border: none;
                border: none;
                background: transparent;
                color: #555;
                font-weight: bold;
                opacity: .7;
                cursor: pointer;
                transition: .3s ease-out;
            }

            #delete:hover {
                background: #c90324;
                color: white;
            }
        }

        tbody tr {
            transition: .5s ease-in-out;

            &:hover {
                background: #0000000a;
            }

        }

    }
}

.activeBtn {
    color: #fff;
    background: #2c2e3e;
    cursor: pointer !important;
}

#cancelBtn {
    cursor: pointer !important;

    &:hover {
        color: #fff;
        background: rgba(255, 0, 0, 0.671) !important;
    }
}

.showOverlay {
    display: flex !important;
}</style>