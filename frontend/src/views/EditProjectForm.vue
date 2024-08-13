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
            <label for="Company">Company :</label>
            <input class="input" id="company" v-model="form.company" type="text" required>
           </div> 
           <div class="form-group">
            <label for="AcademicSupervisor">Academic supervisor :</label>
            <input class="input" id="AcademicSupervisor" v-model="form.AcademicSupervisor" type="text" required>
           </div>
           <div class="form-group">
            <label class="file" for="specs"><i class="fi fi-tr-file-upload"></i>Upload specifications</label>
            <input type="file" id="specs" @change="handleFileUpload">
            </div > 
         </div>
        </div>
        <button class="btn2" type="submit">Apply Modifications</button>
        <router-link class="home" :to="{ name: 'StudentDashboard'}">⬅ Return to your account</router-link> 
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
    const props = defineProps({
    userType: String
  });
    const form = ref({
      firstname: '',
      lastname: '',
      specialty: '',
      ProjectType: '',
      title: '',
      AcademicSupervisor: '',
      ase: '',
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
    </script>
    <style >
    .btn2 {
      margin-top: 30px;
      margin-left: 190px;
      width: 150pt;
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
    font-size: small;
  }
  .home:hover{
    color: #1b293a;
  }
</style>