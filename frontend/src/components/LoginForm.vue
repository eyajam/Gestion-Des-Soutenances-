<!-- components/LoginForm.vue -->
<template>
    <form class="form1" @submit.prevent="handleLogin">
      <h2>Sign In</h2>
      <input type="email" v-model="form.email" placeholder="Email" required/>
      <input type="password" v-model="form.password" placeholder="Password" required/>
      <a class="FP" @click="goToFP">Forgot your password?</a><br>
      <button class="btn88" type="submit">Sign In</button><br>
    </form>
  </template>

<script setup>
import { useRouter } from 'vue-router';
import { reactive } from 'vue';
import axios from 'axios';

  const router1 = useRouter();
  const goToFP = () => {
    router1.push({ name: 'forgetPassword' });
    }
  document.body.style.overflowX = 'hidden';

const router = useRouter();
const form = reactive({
  email: '',
  password: ''
});

function handleLogin() {
  // Send login data to the backend
  axios.post('http://localhost:8000/api/login', form)
    .then(response => {
      const role = response.data.role;
      // Store the token in localStorage 
      localStorage.setItem('authToken', response.data.token);
      localStorage.setItem('userRole', role);
      if (role === 'student') {
        router.push({ name: 'StudentDashboard' });
      } else if (role === 'teacher') {
        router.push({ name: 'TeacherDashboard' });
      } else {
        alert('Unknown role. Cannot redirect.');
      }
    })
    .catch(error => {
      console.error('Login Error:', error.response ? error.response.data : error.message);
      alert('Invalid credentials. Please try again.');
    });
}
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

  
  