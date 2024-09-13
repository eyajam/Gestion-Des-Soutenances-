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
            <label for="AcademicSupervisor">Email Academic Supervisor :</label>
            <Dropdown :modelValue="form.teacher_email" :options="teachers" @update:modelValue="updateTeachers" />
           </div>
           <div class="form-group">
            <label for="ProjectType">Project type :</label>
            <Dropdown :modelValue="form.ProjectType" :options="ProjectTypes" @update:modelValue="updateProjectType" @change="handleProjectTypeChange"/>
           </div>
            <div class="form-group" v-if="showPartnerField">
             <label for="partner">Email partner :</label> 
             <Dropdown :modelValue="form.partner" :options="eStudents" @update:modelValue="updateStudent"  />
            </div>
         </div>
          <div class="container2">
            <div class="form-group">
            <label for="company">Company :</label>
            <input class="input" id="company" v-model="form.company" type="text">
           </div> 
           <div class="form-group">
            <label for="title">Project title :</label>
            <input class="input" id="title" v-model="form.title" type="text">
           </div>
            <div class="form-group">
            <label class="file" for="specs"><i class="fi fi-tr-file-upload"></i>Upload specifications</label>
            <input type="file" id="specs" @change="handleFileUpload">
            <span v-if="selectedFileName" style="font-size: 14px; font-family: 'lato';"><strong>Selected file :</strong> {{ selectedFileName }}</span>
            </div > 
         </div>
        </div>
        <button class="btn1" type="submit">Submit</button>
        <router-link class="home" :to="{ name: 'StudentDashboard'}">⬅   Return to your account</router-link> 
      </form>   
    </div>
    </template>
      <script setup>
      import { ref,onMounted } from 'vue';
      import Dropdown from '../components/DropDown.vue';
      import axios from 'axios';
      const authToken = localStorage.getItem('authToken');  
      const form = ref({
        ProjectType: '',
        partner: '',
        company:'',
        title: '',
        specs: null,
        teacher_email: '',  
      });
      
      const ProjectTypes =['binomial','monomial'];
      const updateProjectType = (newProjectType) => {
       form.value.ProjectType = newProjectType;
       handleProjectTypeChange(newProjectType);
      };
      
      const teachers =ref([]);
      const updateTeachers = (newTeacher) => {
        form.value.teacher_email = newTeacher;
      };
      const showPartnerField = ref(false);
      const handleProjectTypeChange = (value) => {
        showPartnerField.value = (value === 'binomial');
      };

      const currentUserEmail = ref('');
      const currentUserSpecialty = ref('');

      
      const eStudents =ref([]);
      const updateStudent = (newPartner) => {
       form.value.partner = newPartner;
      };
      let email = localStorage.getItem('email');
      
      const fetchStudentsBySpecialty = async () => {
    try {
    // Fetch logged-in student's email and specialty from the backend
    const studentSpecialty = await axios.get('http://localhost:8000/api/student-info',{params: {
                email: email
            }});
    currentUserEmail.value = email;
    currentUserSpecialty.value = studentSpecialty.data.specialty;
    const response = await axios.get(`http://localhost:8000/api/students-by-specialty`, {
      params: {
        specialty: currentUserSpecialty.value,
        email: currentUserEmail.value,
      }
    });
    if (response.status === 200 && Array.isArray(response.data)) {
      eStudents.value = response.data;
      console.log(response.data);
    } else {
      console.error('Unexpected response format:', response);
      eStudents.value = []; // Fallback to empty array
    }
  } catch (error) {
    console.error('Failed to fetch students:', error);
    alert('Failed to retrieve the student list. Please try again later.');
    eStudents.value = []; // Fallback to empty array
  }
};
const fetchTeacherEmails = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/teachers',{headers: { 'Authorization': `Bearer ${authToken}`}});
        teachers.value = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des emails des enseignants', error);
      }
    };
onMounted(() => {
  fetchTeacherEmails();
  fetchStudentsBySpecialty(); // Fetch the student emails when the component is mounted or when "binomial" is selected
});

const selectedFileName = ref('');
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.value.specs = file;  
    selectedFileName.value = file.name;  // Store the file name to display it
  } else {
    form.value.specs= null;
    selectedFileName.value = '';  // Clear file name if no file is selected
  }
};
const validateForm = () => {
  // Clear errors
  let errors = [];

  if (form.value.title && form.value.title.length > 255) {
    errors.push('Project title must be less than 255 characters.');
  }

  if (!form.value.ProjectType) {
    errors.push('Project type is required.');
  } 

  if (form.value.ProjectType === 'binomial' && !form.value.partner) {
    errors.push("Partner is required for binomial projects.");
  }

  if (form.value.company && form.value.company.length > 255) {
    errors.push('Company name must be less than 255 characters.');
  }

  if (form.value.teacher_email && form.value.teacher_email.length > 255) {
    errors.push('Teacher name must be less than 255 characters.');
  }

  if (form.value.specs && form.value.specs.name && typeof form.value.specs.name === 'string') {
  const fileExtension = form.value.specs.name.split('.').pop().toLowerCase();
  if (!['pdf', 'doc', 'docx'].includes(fileExtension)) {
    errors.push('Invalid file type. Only PDF, DOC, and DOCX files are allowed.');
  }
}
  // Affichage des erreurs s'il y en a
  if (errors.length > 0) {
    alert(errors.join('\n'));
    return false;
  }
  return true;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }
  const formData = new FormData();
  formData.append('project_type', form.value.ProjectType);
  if (showPartnerField.value && form.value.partner) {
    formData.append('partner', form.value.partner);
  }
  formData.append('company', form.value.company || '');
  formData.append('title', form.value.title || '');
  formData.append('teacher_email', form.value.teacher_email || '');
  if (form.value.specs) {
    formData.append('specs', form.value.specs);
  }
  try {
    
    const response = await axios.post('http://localhost:8000/api/projects', formData, {
      headers: { 'Authorization': `Bearer ${authToken}`,
                 'Content-Type': 'multipart/form-data' ,
                },
    });
    alert('Project stored successfully');
    console.log('Project stored successfully:', response.data);
  } catch (error) {
    if (error.response && error.response.status === 400) {
      alert(error.response.data.error); // Display the error message from the backend
    } else {
    console.error('Failed to store project:', error);
  }
}
}
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