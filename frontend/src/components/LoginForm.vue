<!-- components/LoginForm.vue -->
<template>
    <form class="form1" method="post" @submit.prevent="$emit('submit')">
      <h2>Sign In</h2>
      <input type="email" placeholder="Email" />
      <input type="password" placeholder="Password" />
      <a class="FP" @click="goToFP">Forgot your password?</a><br>
      <button class="btn88" type="submit">Sign In</button><br>
    </form>
  </template>

<script setup>
import { useRouter } from 'vue-router';
    
    const router1 = useRouter();
    const goToFP = () => {
      router1.push({ name: 'forgetPassword' });
    }
 const emit = defineEmits(['submit']);
  document.body.style.overflowX = 'hidden';
</script>

<style>

  .FP{
   color: #4e5174da;
   font-size: small;
   position: relative;
   margin-right: 150px;
   cursor: pointer;
  }
  .FP:hover{
    color: #353a70;
  }
  
  .form1 {
    background: rgb(255, 255, 255);
    padding: 20px;
    border-radius: 10px;
    margin: 10px;
    width: 300px;
    text-align: center;
    font-family: 'lato';
  }
  
  .form1 h2 {
    margin-bottom: 20px;
    color:#355070 ;
  }
  
  .form1 input {
    width: 200pt;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 10px;
  }
  
  .btn88 {
    margin-top: 20px;
    width: 100pt;
    padding: 10px;
    background-color: #355070;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 16px;
    cursor: pointer;
  }
  
  </style>

  
  