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
      console.log(response.data);
      
      if (response.data.token) {
        const role = response.data.role;
      // Store the token in localStorage 
      localStorage.setItem('authToken', response.data.token);
      localStorage.setItem('userDetails', JSON.stringify(response.data.userDetails));
      localStorage.setItem('userRole', role);
      localStorage.setItem('email',response.data.email);
      
      if (role === 'student') {
        router.push({ name: 'StudentDashboard' });
      } else if (role === 'teacher') {
        router.push({ name: 'TeacherDashboard' });
      } else if (role === 'admin') {
        router.push({ name: 'Dashboard' });
      } else {
        alert('Unknown role. Cannot redirect.');
      }
    }else { alert('aucune données ')}
    })
    .catch(error => {
      console.error('Login Error:', error.response ? error.response.data : error.message);
      if (error.response && error.response.data && error.response.data.message) {
        alert(error.response.data.message);
      } else {
        alert('Erreur inconnue. Veuillez réessayer.');
      }
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

  
  