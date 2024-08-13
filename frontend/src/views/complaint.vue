<template>
    <div class="complaint-form">
      <h2>Complaint</h2>
      <form class="form5" @submit.prevent="submitForm">
        <div class="radio-group">
          <label class="Ltype">
            <input class="Itype" type="radio" v-model="complaintType" value="editFormProject" />
            Edit Form Project
          </label>
          <label class="Ltype">
            <input class="Itype" type="radio" v-model="complaintType" value="other" />
            Other
          </label>
        </div>
        <div class="form-group">
          <label class="labels" for="title">Object :</label>
          <input class="title" type="text" id="title" v-model="title" />
        </div>
        <div class="form-group">
          <label class="labels" for="message">Message :</label>
          <textarea id="message" v-model="message"></textarea>
        </div>
        <button class="Cbtn" type="submit">Submit</button>
      </form>
      <router-link class="home" :to="{ name: 'StudentDashboard'}">⬅  Return to your account</router-link>
    </div>
  </template>
  
  <script setup>
import { ref } from 'vue';

const complaintType = ref(null);

  </script>
  
  <style >
  .complaint-form {
    width: 600px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background: rgb(137 170 191 / 63%);
    font-family:'Lato';
    font-size:large ;
  } 
  .radio-group {
    display: flex;
    align-items: center;
    gap: 30px;
    margin: 20px;
    position: relative;
    left: 140px;
  }
  .Ltype{
    font-size: large;
  }
  .form-group {
    margin-bottom: 20px;
  }
  
  .labels {
    display: block;
    margin-bottom: 5px;
    color: #ffffff;
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
  </style>
  