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
            <Dropdown :modelValue="form.project_type" :options="ProjectTypes" @update:modelValue="updateProjectType" @change="handleProjectTypeChange"/>
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
            <input class="input" id="title" v-model="form.project_title" type="text">
           </div>
            <div class="form-group">
            <label class="file" for="specs"><i class="fi fi-tr-file-upload"></i>Upload specifications</label>
            <input type="file" id="specs" @change="handleFileUpload">
            <!-- Display the original file name -->
          <span v-if="originalFileName" style="font-size: 14px; font-family: 'lato';">
            <strong>Original file:</strong> {{ originalFileName }}
          </span>
          <!-- Display the new selected file name -->
          <span v-if="selectedFileName" style="font-size: 14px; font-family: 'lato';">
            <strong>Selected file:</strong> {{ selectedFileName }}
          </span>
            </div > 
         </div>
        </div>
        <button class="btn2" type="submit">Apply Modifications</button>
        <router-link class="home" :to="{ name: 'StudentDashboard'}">⬅ Return to your account</router-link> 
      </form>   
    </div>
    </template>
    <script setup>
    import { ref,onMounted } from 'vue';
    import Dropdown from '../components/DropDown.vue';
    import axios from 'axios';
  
// Reactive form data
  const form = ref({
    project_type: '',
    partner: '',
    company: '',
    project_title: '',
    specs: null,
    teacher_email: '',  
  });
  const ProjectTypes =['binomial','monomial'];
      const updateProjectType = (newProjectType) => {
       form.value.project_type = newProjectType;
       handleProjectTypeChange(newProjectType);
      };
      
      const teachers =ref([]);
      const updateTeachers = (newTeacher) => {
        form.value.teacher_email = newTeacher;
      };
      const showPartnerField = ref(true);
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
        const response = await axios.get('http://localhost:8000/api/teachers',
        {  headers: { 'Authorization': `Bearer ${authToken}` },});
        teachers.value = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des emails des enseignants', error);
      }
    };
// Variables for file handling
const selectedFileName = ref('');
const originalFileName = ref('');

// Authorization token
const authToken = localStorage.getItem('authToken');

// Fetch project data on component mount
const fetchProjectData = async () => {
  
  try {
    const response = await axios.get('http://localhost:8000/api/fetchDataProject', {
      headers: { 'Authorization': `Bearer ${authToken}` },
      withCredentials: true,
    });
    const projectData = response.data.project;
    // Set the form data
    form.value.teacher_email = projectData.teacher_email || '';
    form.value.project_type = projectData.project_type || '';
    form.value.partner = projectData.partner || '';
    form.value.company = projectData.company || '';
    form.value.project_title = projectData.project_title || '';
    originalFileName.value = projectData.original_filename || '';

    // Optionally log for debugging
    console.log('Project data fetched successfully:', projectData);
  } catch (error) {
    console.error('Failed to fetch project data:', error);
    alert('Failed to fetch project data. Please try again later.');
  }
};

// Handle file upload
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.value.specs = file;
    selectedFileName.value = file.name;
    
  } else {
    form.value.specs = null;
    selectedFileName.value = '';
  }
};
const validateForm = () => {
  if (!form.value.project_type) {
    alert('Project Type is required.');
    return false;
  }
  if (form.value.project_type.toLowerCase() === 'binomial' && !form.value.partner) {
    alert('Partner email is required for binomial projects.');
    return false;
  }
  if (form.value.specs) {
    const allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']; // PDF, DOC, DOCX
    if (!allowedFileTypes.includes(form.value.specs.type)) {
      alert('Invalid file type. Please upload a PDF, DOC, or DOCX file.');
      return false;
    }
  }
  return true;
};
const handleSubmit = async () => {
  try {
    if (!validateForm()) {
    return; // Stop submission if validation fails
  }
  await axios.get('http://localhost:8000/sanctum/csrf-cookie');
  const response = axios.put('http://localhost:8000/api/update', {
  project_title: form.value.project_title,
  project_type: form.value.project_type,
  teacher_email: form.value.teacher_email,
  partner: form.value.partner,
  company: form.value.company,},
  {headers: {'Authorization': `Bearer ${authToken}` }})
.then(response => {
  console.log(response.data);
})
.catch(error => {
  console.error('Error:', error.response.data);
});
if (form.value.specs) {
      const specsData = new FormData();
      specsData.append('specs', form.value.specs);
      // Send the FormData with the file separately
      await axios.post('http://localhost:8000/api/upload-specs', specsData, {
        headers: {'Authorization': `Bearer ${authToken}`}, });
    }  
    console.log('Project updated successfully:', response.data);
    alert('Project updated successfully.');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      console.error('Validation failed:', error.response.data.errors);
      //alert(`Erreurs de validation: ${JSON.stringify(error.response.data.errors)}`);
      // Log the formData for debugging purposes
    } else {
      console.error('Failed to update project:', error);
    }
  }
}; 
// Fetch project data when the component is mounted
onMounted(() => {
  fetchTeacherEmails();
  fetchStudentsBySpecialty();
  fetchProjectData();
});
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