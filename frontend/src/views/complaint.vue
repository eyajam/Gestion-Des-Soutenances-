<template>
    <div class="complaint-form">
      <h2>Complaint</h2>
      <form class="form5" @submit.prevent="submitForm">
        <div class="radio-group">
          <label class="Ltype">
            <input class="Itype" type="radio" v-model="form.complaint_type" value="editForm" />
            Edit Form Project
          </label>
          <label class="Ltype">
            <input class="Itype" type="radio" v-model="form.complaint_type" value="other" />
            Other
          </label>
        </div>
        <div class="form-group">
          <label class="labels" for="title">Object :</label>
          <input class="title" type="text" id="title" v-model="form.object" />
        </div>
        <div class="form-group">
          <label class="labels" for="message">Message :</label>
          <textarea id="message" v-model="form.message"></textarea>
        </div>
        <button class="Cbtn" type="submit">Submit</button>
      </form>
      <router-link class="home" :to="{ name: 'StudentDashboard'}">⬅  Return to your account</router-link>
    </div>
  </template>
  
  <script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const form =ref({
  complaint_type: null,
  object: '',
  message: '',
})
const router = useRouter();

const submitForm = async () => {
  if (!form.value.complaint_type || !form.value.object || !form.value.message) {
    alert('Please fill out all fields before submitting !');
    return;
  }
  const authToken = localStorage.getItem('authToken');
  try {
    const response = await axios.post('http://localhost:8000/api/complaints', {
      complaint_type: form.value.complaint_type,
      object: form.value.object,
      message: form.value.message,
    },{headers: {'Authorization': `Bearer ${authToken}` }});
    alert('Complaint submitted successfully'); 
    const errorC =response.data.complaint;
    if (errorC) {
      alert(errorC);
    } 
    // Clear the form
    form.value.complaint_type = null;
    form.value.message = '';
    form.value.object = '';
    // Optionally, navigate to another page
    router.push({ name: 'StudentDashboard' });
  } catch (error) {
    if (error.response) {
      const errorMessage = error.response.data.error || 'An error occurred while submitting your complaint.';
      alert(errorMessage);
    } else {
      alert('An error occurred while submitting your complaint.');
    }
  }
};

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
  