<template>
<div class="container">     
    <div class="container-content">
      <form class="form8" @submit.prevent="handleSubmit">   
        <h2 style="color:white;">Forgot Password <i class="fi fi-ss-key" style="font-size: 18px; margin-left: 5px; "></i></h2>
        <div>
          <label for="email" style="color: #355070; display: flex;flex-direction: column;align-items: flex-start;">Your Email :</label>
          <input class="input2" id="email" v-model="form.email" type="email" required>
         </div>
         <div>
          <label for="password"style="color: #355070;display: flex;flex-direction: column;align-items: flex-start;">New-Password :</label>
          <input class="input2" id="password" v-model="form.password" type="password" required>
         </div>
         <div>
          <label for="password"style="color: #355070;display: flex;flex-direction: column;align-items: flex-start;">Confirm New-Password :</label>
          <input class="input2" id="password_confirmation" v-model="form.passwordConfirmation" type="password" required>
         </div>
         <button class="update" type="submit" style="display: flex;align-items: center;justify-content: center;">Update</button>
         <!-- <router-link style="position: relative; right: 100px;font-size: small; top: 25px;color: #3c5572ec;" :to="{ name: 'login'}">⬅ Return to home page</router-link> -->
         <router-link class="WP" :to="{ name: 'login'}">⬅ Return to home page</router-link>
        </form>
    </div> 
</div>
</template>
<script setup>
import { ref } from 'vue';
import axios from 'axios';
  const form = ref({
    email: '',
    password: '',
    passwordConfirmation: '',
  });

  function handleSubmit() {
  if (form.value.password !== form.value.passwordConfirmation) {
    alert('Passwords do not match!');
    return;
  }

  const dataToSend = {
    email: form.value.email,
    password: form.value.password,
    password_confirmation: form.value.passwordConfirmation,
  };

  axios.post('http://localhost:8000/api/password-reset', dataToSend)
    .then(response => {
      alert('Password updated successfully!');
    })
    .catch(error => {
      console.error(error);
      alert('There was an error updating the password.');
    });
};

</script>
<style scoped>
 .WP{
  position: relative;
  right: 100px;
  font-size: small;
  top: 25px;
  color: #3c5572ec;
} 
.update{
  
  background: #355070;
  color: #ffffff;
  margin-top: 10px;
  width: 100px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  border-radius: 15px;
  border-color: transparent;
  background: #355070;
  box-shadow: inset 11px 11px 15px #355070,
            inset -11px -11px 15px #456892;
}
.container{
  padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 31px;
    height: 450px;
    width: 350px;
    background: #8ba5b59e;
    box-shadow:  20px 20px 80px #355070ad,
               -20px -20px 80px #ffffff;
    font-family: 'lato';           
}
.container-content{
    display: flex;
    align-items: center;
    justify-content: center;
}
.input2 {
    width: 180pt;
    padding: 5px;
    margin: 10px 0; 
    border-radius: 8px; 
    border: 2px solid #ffffff; 
    background: transparent;  
    outline: none; 
  }
</style> 
<!-- <template>
</template><script></script><style></style> -->