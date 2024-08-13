<template>
  <div class="complaint-form1">
    <h2 class="comp">Complaint</h2>
    <p><strong> {{ username }} " {{ specialty }} "</strong></p>
    <form class="form5" @submit.prevent="submitForm">
      <div class="form-group">
        <label class="labels2" for="title">Object :</label>
        <input class="title" type="text" id="title" v-model="title" />
      </div>
      <div class="form-group">
        <label class="labels2" for="message">Message :</label>
        <textarea id="message" v-model="message"></textarea>
      </div>
      <button class="Cbtn" type="submit">Submit</button>
    </form>
    <router-link class="previous" :to="{ name: 'teacherComplaint'}">⬅ Previous page</router-link>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();

const username = route.params.username;
const specialty = route.params.specialty;

</script>

<style >
.complaint-form1 {
  width: 600px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 10px;
  background: radial-gradient(#ff9b66,#ffc4a5);
  font-family:'Lato';
  font-size:large ;
} 
.comp{
  color: #b94100;
}

.form-group {
  margin-bottom: 20px;
}

.labels2 {
  display: block;
  margin-bottom: 5px;
  color: #557e98dc;
  margin-left: 15px;
  font-size: large;
}
.title{
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  border-radius: 15px;
  border: none;
  
}
.form5 textarea {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  height: 100px;
  border: none;
  border-radius: 15px; 
}

.Cbtn {
  width: 100pt;
  padding: 16px 24px; 
  background: rgba(234, 234, 234, 0.418);
  border: none;
  border-radius: 15px;
  color: white;
  font-size: 16px;
  cursor: pointer;
  margin: 10px;
}

.Cbtn:hover {
  background-color: rgba(234, 234, 234, 0.632);}
.previous{
      position: relative;
      right: 240px;
      top: 10px;
      cursor: pointer;
      font-family: 'lato';
      color: #367298 ;
      font-size: medium;
    }  
.previous:hover{
  color: #367298;
}    
</style>
