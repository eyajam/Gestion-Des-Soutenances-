<template>
    <div class="msgContainer">  
    <div class="msg">
    <i class="fi fi-sr-left icon1" @click="goToAccount"></i>     
    Once the oral examination calendar is finalised, we will provide you with your schedule.
    </div>
    </div>

</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();
const goToAccount = () => {
      router.push({ name: 'TeacherDashboard' });
    }

</script>

<style scoped>
.msgContainer{
    display: flex;
    align-items: center;
    justify-content: center;
}
.msg{
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffffec;
    width: 800px;
    height: 100px;
    border-radius: 42px;
background: linear-gradient(145deg, #fab895, #d39b7d);
box-shadow:  11px 11px 22px #966e59,
             -11px -11px 22px #fecaad;
}
.icon1{
    font-size:larger;
    position: relative; 
    font-size: large; 
    color: white;
    right: 30px;
    top: 4px;
    cursor: pointer;
}
</style>