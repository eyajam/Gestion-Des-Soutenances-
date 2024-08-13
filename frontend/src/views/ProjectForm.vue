<template>
    <div class="main-container">
      <form class="form4" @submit.prevent="handleSubmit">
        <div>
            <div class="form-icon">
            <i class="fi fi-sr-student-alt" alt="Icon" style="font-size: 50px; position: relative; left: 245px;"></i></div>
        </div>
        <div class="form-container">
         <div class="container1">
           <div class="form-group">
            <label for="title">Project title :</label>
            <input class="input" id="title" v-model="form.title" type="text" required>
           </div>
           <div class="form-group">
            <label for="ProjectType">Project type :</label>
            <Dropdown :modelValue="selectedProjectType" :options="ProjectType" @update:modelValue="updateProjectType" @change="handleProjectTypeChange"/>
           </div>
            <div class="form-group" v-if="showPartnerField">
             <label for="specialty">Full name partner :</label> <!-- mezlt chenzid listeBoxe des etudiants de sa spécialité -->
             <input type="text" class="input" id="partner" v-model="form.partner" required>
            </div>
         </div>
          <div class="container2">
            <div class="form-group">
            <label for="company">Company :</label>
            <input class="input" id="company" v-model="form.company" type="text" required>
           </div> 
           <div class="form-group">
            <label for="AcademicSupervisor">Academic supervisor :</label>
            <input class="input" id="AcademicSupervisor" v-model="form.AcademicSupervisor" type="text" required>
           </div>
            <div class="form-group">
            <label class="file" for="specs"><i class="fi fi-tr-file-upload"></i>Upload specifications</label>
            <input type="file" id="specs" @change="handleFileUpload">
            <span id="file-name"></span>
            </div > 
         </div>
        </div>
        <button class="btn1" type="submit">Submit</button>
        <router-link class="home" :to="{ name: 'StudentDashboard'}">⬅   Return to your account</router-link> 
      </form>   
    </div>
    </template>
      <script setup>
      import { ref } from 'vue';
      import Dropdown from '../components/DropDown.vue';
      import axios from 'axios';

      const selectedProjectType = ref('');
      const ProjectType =['binomial','monomial'];
      const updateProjectType = (newProjectType) => {
       selectedProjectType.value = newProjectType;
      };

      const showPartnerField = ref(false);
      const handleProjectTypeChange = (value) => {
        showPartnerField.value = (value === 'binomial');
      };
          
      const form = ref({
        specialty: '',
        ProjectType: '',
        partner: '',
        company:'',
        title: '',
        specs: null,
        AcademicSupervisor: '',  
      });
      
      function handleSubmit() {
        const dataToSend = {
        firstname: form.value.firstname,
        lastname: form.value.lastname,
        specialty: form.value.specialty,
        ProjectType: form.value.ProjectType,
        title: form.value.title,
        AcademicSupervisor: form.value.AcademicSupervisor,
        ase: form.value.ase
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
    function  handleFileUpload(event) {
    const fileInput = event.target;
    const fileName = fileInput.files[0] ? fileInput.files[0].name : '';
    document.getElementById('file-name').textContent = fileName;
  };
      </script>
    <style >
    .home{
    position: relative;
    margin-right: 460px;
    cursor: pointer;
    color: #355070;
    font-size: small;
  }
  .home:hover{
    color: #ffffff;
  }
    .main-container{
      width: 100%;
    }
    .form-container {
    display: flex;
    justify-content: space-between;
  }
    .form4{
      background: rgb(137 170 191 / 63%);
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
      font-family: 'lato';
    }
    .input {
      width: 180pt;
      padding: 5px;
      margin: 10px 0; 
      border: none; 
    border-bottom: 2px solid #fff; 
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
    .file{
     background-color: rgba(255, 255, 255, 0.306);
     padding: 10px;
     border-radius: 20px;
     cursor: pointer;
    }
    .file i{
      position: relative;
      top: 3px;
      margin-right: 10px;
    }
     input[type="file"]{
      display: none;
    } 
    #file-name {
  display: block;
  margin-top: 10px;
  font-size: 14px;
  color: #203043;
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
      font-family: 'lato';
    }
    .home:hover{
      color: #1b293a;
    }
    .form-icon {
    width: 50px; 
    height: 50px; 
    margin: 25px;
    padding: 0 0 15px;
  }
    </style>