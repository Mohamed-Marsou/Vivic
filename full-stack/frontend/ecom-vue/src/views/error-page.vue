<script setup>
import { RouterLink } from 'vue-router'
import { onMounted } from 'vue';
onMounted(() => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
})

</script>

<template>
    <div class="page-box">
       <img src="../assets/images/error.webp" alt="404 image">
       <div class="div">
            <h1>Opps !! something went worng 404 </h1>
            <RouterLink id="home" :to="{name : 'home'}">Back Shopping</RouterLink>
            <RouterLink id="contact" :to="{name : 'contact'}">Rebort to us</RouterLink>
       </div>
    </div>
</template>

<style lang="scss">
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
$ff: 'Poppins', sans-serif;

.page-box {
    width: 100vw;
    height: 95vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    font-family: $ff;
    position: relative;
    >img{
        opacity: .8;
        height: 60%;
        object-fit: contain;
    }
    >div{
        font-weight: bold;

        h1{
            font-size: 1.5rem;
            padding:2rem 10px ;
            text-align: center;

        }
        #home {
            float: left;
            background: #2563EB;
        }
        #contact {
            float: right;
            margin-left: 5vw;
            background: red;
        }
        a{
            width: 10rem;
            height: 3.2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 10px;
            color: white;
            border-radius: 5px;
        }
    }
}

@media screen and (max-width : 525px) {
    .page-box {
    >img{
        width: 80%;
        height: fit-content;
    }
    >div{

        h1{
            font-size: 1.1rem;
            margin:3rem 5px;
        }
        #home {
            float: left;
            background: #2563EB;
        }
        #contact {
            float: right;
            margin-left: 5vw;
            background: red;
        }
        a{
            width: 8rem;
            height: 2.8rem;
           font-size: .8rem;
        }
    }
}

}
</style>