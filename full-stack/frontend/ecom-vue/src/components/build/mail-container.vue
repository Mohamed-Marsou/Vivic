<script setup>

</script>
<template>
    <div class="mail-caontainer">
        <h2>
            Be the First to Know! Subscribe for updates, deals, and more straight to your inbox
        </h2>
        <div class="inptCont">
            <i class="fa-solid fa-paper-plane"></i>
            <input type="text" placeholder="Enter Email address">
        </div>
    </div>
</template>

<style lang="scss">
.mail-caontainer {
    width: 100%;
    height: 40vh;
    background: rgb(47, 112, 210);
    background: linear-gradient(121deg, rgba(47, 112, 210, 1) 0%, rgba(5, 105, 255, 0.4738270308123249) 100%);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    flex-direction: column;
    gap: 3rem;
    box-shadow: 1px 1px 8px 3px #00000058;

    .inptCont{
        width: 70%;
        height: 4rem;
        border: 1px solid white;
        display: flex;
        >i{
            width: 10%;
            height: 100%;
            background: white;
            color: rgb(47, 112, 210);
            display: flex;
            justify-content: center;
            align-items: center;
            border-right: 2px solid #5555557c;
            font-size: 1.4rem;
            transition: .3s ease-in;
            cursor: pointer;
            &:hover{
                background: rgb(47, 112, 210);
                color: white;
            }
        }
        >input{
            width: 90%;
            height: 100%;
            border: none;
            outline: none;
            padding: 0 1rem;
        }
    }
}
</style>