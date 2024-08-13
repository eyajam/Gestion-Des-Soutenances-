<template>
  <div class="main-container">
    <form class="form3" @submit.prevent="handleSubmit">
      <div>
       <img src="/images/circle-user.png" alt="Icon" class="form-icon" />
      </div>
      <div class="form-container">
       <div class="container1">
        <div class="form-group">
         <label for="firstname">First Name :</label>
         <input class="input" id="firstname" v-model="form.firstname" type="text" required>
        </div>
        <div class="form-group">
         <label for="lastname">Last Name :</label>
         <input class="input" id="lastname" v-model="form.lastname" type="text" required>
        </div>
        <div v-if="props.userType === 'teacher'" class="form-group">
         <label for="grade">Grade :</label>
         <Dropdown :modelValue="selectedGrade" :options="grades" @update:modelValue="updateGrade" @change="handleGradeChange" />
        </div>
        <div v-if="props.userType === 'student'" class="form-group">
         <label for="cin">CIN student :</label>
         <input type="text" class="input" id="cin" v-model="form.cin" required>
        </div>
        <div v-if="props.userType === 'student'" class="form-group">
         <label for="status">Status (new or repeating) :</label>
         <input type="text" class="input" id="status" v-model="form.status" required>
        </div>
        <div v-if="props.userType === 'student'" class="form-group">
           <label for="specialty">Specialty :</label>
           <Dropdown :modelValue="selectedSpecialty" :options="specialties" @update:modelValue="updateSpecialty" @change="handleSpecialtyChange" />
          </div>
       </div>
        <div class="container2">
         <div v-if="props.userType === 'student'" class="form-group">
          <label for="number">Phone number :</label>
          <input type="text" class="input" id="number" v-model="form.number" required>
         </div> 
         <div class="form-group">
          <label for="email">Email :</label>
          <input class="input" id="email" v-model="form.email" type="email" required>
         </div>
         <div class="form-group">
          <label for="password">Password :</label>
          <input class="input" id="password" v-model="form.password" type="password" required>
         </div>
         <div class="form-group">
          <label for="password">Confirm your password :</label>
          <input class="input" id="password_confirmation" v-model="form.passwordConfirmation" type="password" required>
         </div>
       </div>
      </div>
      <button class="btn1" type="submit">Sign Up</button>
      <router-link class="home" :to="{ name: 'login'}">⬅ Return to home page</router-link> 
    </form>   
  </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import Dropdown from '../components/DropDown.vue';
  import axios from 'axios';
  const props = defineProps({
  userType: String
});
const selectedGrade = ref('');
const grades = ['maître assistant', 'assistant', 'vacataire', 'contractuel', 'PES'];
const updateGrade = (newGrade) => {
  selectedGrade.value = newGrade;
};
const handleGradeChange = (newGrade) => {
  console.log('Selected grade changed to:', newGrade);
};

const selectedSpecialty = ref('');
const specialties = ['BIS', 'BI', 'Accounting', 'Finance', 'Marketing','Management','HRM','BE','FEE'];
const updateSpecialty = (newSpecialty) => {
  selectedSpecialty.value = newSpecialty;
};
const handleSpecialtyChange = (newSpecialty) => {
  console.log('Selected grade changed to:', newSpecialty);
};
  const form = ref({
    firstname: '',
    lastname: '',
    cin: '',
    specialty: '',
    grade:'',
    status:'',
    number:'',
    email: '',
    password: '',
    passwordConfirmation: '',
    role: props.userType
  });
  
  function handleSubmit() {
    if (form.value.password !== form.value.passwordConfirmation) {
    alert('Passwords do not match!');
    return;}
    const dataToSend = {
    firstname: form.value.firstname,
    lastname: form.value.lastname,
    cin: form.value.userType === 'student' ? form.value.cin : undefined, // Conditionnellement inclure CIN s'il s'agit d'1 étudiant
    email: form.value.email,
    password: form.value.password,
    role: form.value.role
  };
  axios.post('http://localhost:8000/api/signUp', dataToSend)
    .then(response => {
      // Traitement en cas de succès
      console.log('Registration Success:', response.data);
      alert('Registration successful!');
    })
    .catch(error => {
      // Traitement en cas d'erreur
      console.error('Registration Error:', error);
      alert('An error occurred during registration.');
    });
  }
  </script>
  <style scoped>
  .main-container{
    width: 100%;
    /* padding-top: 70px; */
  }
  .form-container {
  display: flex;
  justify-content: space-between;
  gap: 24px;
}
  .form3{
    background-color:rgb(137 170 191 / 63%) ; 
    padding: 20px;
    border-radius: 10px;
    margin: 10px;
    width: 600px;
    display:grid;
    justify-content: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  label {
    display: block;
    margin-top: 10px;
    color: rgb(250, 250, 250);
    font-size:medium;
  }
  .input {
    width: 180pt;
    padding: 5px;
    margin: 10px 0; 
    border: none;  
    border-bottom: 2px solid #ffffff; 
    background: transparent; 
    color: #fff; 
    outline: none; 
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
  }
  .form-group {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  }
  .btn1 {
    margin-top: 30px;
    margin-left: 210px;
    width: 100pt;
    padding: 18px;
    background: rgba(234, 234, 234, 0.418);
    border: none;
    border-radius: 15px;
    color: white;
    font-size: 16px;
    cursor: pointer;
  }
  .home{
    position: relative;
    margin-right: 460px;
    cursor: pointer;
    color: #355070;
  }
  .home:hover{
    color: #1a2636;
  }
  .form-icon {
  width: 50px; 
  height: 50px; 
  margin: 25px;
}
.EUA{
  color: #355070;
}
  </style>
  