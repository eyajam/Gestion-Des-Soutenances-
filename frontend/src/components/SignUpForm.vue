<!-- components/SignUpForm.vue -->
<template>
    <div class="form2">
     <div class="welcome">
      <h2>Welcome</h2>
      <div>
      <i class="fi fi-ts-hand-wave" style=" color: rgb(255, 255, 255);position: relative;top: -16px;left: 15px;font-size: 20px;"></i></div>
     </div>
      <p>New to this plateforme ,<br>
      Create your account</p>
      <router-link class="url" :to="{ name: 'SignUp', params: { userType: 'student' }}">Are you a student ?</router-link>
      <router-link class="url" :to="{ name: 'SignUp', params: { userType: 'teacher' }}">Are you a teacher ?</router-link>
    </div>
  </template>
  <style>
  .welcome{
    display: flex;
  align-items: center;
  justify-content: center;
    
  }
  p{
    color: #ffffff;
    position:relative;
    top: -10%;
   
  }
  .url{
    color: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 10px 0;
    font-size: 14px;
    letter-spacing: 1px;
  }
  .url:hover{
    color:#4E5174 ;
  }
  .form2 {
    background: radial-gradient(#ffbc98,#ffaa6e);
    padding: 20px;
    border-radius: 10px;
    margin: 10px;
    width: 350px;
    text-align: center;
    height: 55vh;
    font-family: 'lato'; 
  }
  .form2 h2 {
    margin-bottom: 60px;
    color: #ffffff;
    letter-spacing: 1px;
  }
  
</style>
  <script setup >
   
  </script>
  