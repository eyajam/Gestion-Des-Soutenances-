<template>
<div class="complaint-container" :class="{ 'blur-background': showInfo }">
      <h3 class="complaint">Complaints & Projects</h3>
    <div class="Gfonc">
      <div class="fonc-group">
        <button
            class="fonc"
            :class="{ active: selectedType === 'Complaints' }"
            @click="choisenType('Complaints')">Complaints
        </button>
        <button
            class="fonc"
            :class="{ active: selectedType === 'Projects' }"
            @click="choisenType('Projects')">Projects
        </button>
      </div>
    </div>
<div class="studsPrjts" :class="{ 'blur-background': showInfo }">
  <div v-if="selectedType === 'Complaints'">
    <div v-for="user in users" :key="user.id" class="complaint-item">
        <div class="username">
            <i class="fi fi-ts-user-graduate"></i>
            {{ user.email }}
            " {{ user.specialty }} "
        </div>
        <div class="icons">
          <i class="fi fi-rr-file-circle-info" @click="showInfoDiv(user)"></i>  
          <i style="margin-left: 12px;" class="fi fi-rr-paper-plane" @click="navigateToComplaint(user)"></i>
        </div>
    </div>
  </div>
</div>  
<div v-if="selectedType === 'Projects'" class="studsPrjts">
    <div style="display: flex; padding-bottom: 15px; color: #244a62; gap: 15px;">
      <div>Add a new project from these unsupervised projects :</div>
        <dropDown2 :modelValue="selectedSpecialty" :options="specialties" @update:modelValue="updateSpecialty"/>
    </div>
  <div v-if="filteredProjects.length > 0"> 
    <div v-for="project in filteredProjects" :key="project.cin" class="complaint-item">
      <div class="username">
        <i class="fi fi-ts-user-graduate"></i>
        {{ project.first_name }} {{ project.last_name }} - {{ project.project_type }}<span v-if="project.partner"> -<strong>Partner </strong>: {{ project.partner }}</span>
      </div>
      <div class="icons2">
        <div @click="addProject(project)"
         style="margin-left: 12px; background-color: #367298; border-radius: 10px; padding: 3px 10px; cursor: pointer;">  
          <div style="color: #fff; padding: 3px; font-size: 14px;">Add Project</div> 
        </div>
      </div>
    </div>
  </div>
  <div v-if="projects.length > 0 && filteredProjects.length ===0 ">
    <p style="color: #244a62; font-weight: bold;">
      There are no projects for the speciality selected. 
    </p>
  </div>   
  <div v-if="projects.length === 0" style="position: relative; bottom: 10px;">
    <p class="no-projects-message" style="color: #dfdfdf; font-weight: bold; letter-spacing: 1px;">
      No unsupervised<br>projects found.
    </p>
  </div>  
</div> 
      <router-link class="home2" :to="{ name: 'TeacherDashboard'}">⬅ Return to your account</router-link>     

</div>
  <div v-if="showInfo" class="info-popup">
      <div class="info-content">
        <span @click="hideInfoDiv" class="close-btn">&times;</span>
        <div class="infoClaim">
          <div v-if="currentItem">
          <strong>{{ currentItem.email }}</strong></div>
          <div><strong>Company name :    </strong>{{ projectData.company }}</div>
          <div><strong>project title :   </strong>{{ projectData.project_title }}</div>
          <div><strong>project type :    </strong>{{ projectData.project_type }}</div>
          <div><strong>partner :       </strong>{{ projectData.partner ? projectData.partner : 'no partner' }}</div>
          <div v-if="projectData.specs && projectData.original_filename">
          <strong>specifications :   </strong>
          <a :href="projectData.specs" target="_blank" rel="noopener noreferrer">{{ projectData.original_filename }}</a>
        </div>
        </div>
      </div>
  </div>    
  </template>
  <script setup>
  import { ref,onMounted,computed } from 'vue';
  import { useRouter } from 'vue-router';
  import dropDown2 from '../components/dropDown2.vue';
  import axios from 'axios';
  import { eventBus } from '../eventBus';

  const authToken = localStorage.getItem('authToken');
  const teacherEmail = localStorage.getItem('email');
  const selectedType = ref('Complaints');
  const users = ref([]);

  const showInfo = ref(false);
  const currentItem = ref(null);
  
const showInfoDiv = (item) => {
  currentItem.value = item;
  showInfo.value = true;
  fetchProjectDetails();
};

const hideInfoDiv = () => {
  showInfo.value = false;
  currentItem.value = null;
  
};
const projectData = ref({
  company: '',
  project_title: '',
  project_type: '',
  partner: '',
  specs: '',
  original_filename:'',
})
  const fetchStudents = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getStudents' ,
    { headers: {'Authorization': `Bearer ${authToken}`},
     withCredentials: true,
  }); // Fetch data from your API
    users.value = response.data; // Update the ref with the data
  } catch (error) {
    console.error('Error fetching students:', error); // Handle any errors
  }
};

const fetchProjectDetails = () => {
  if (!currentItem.value || !currentItem.value.email) {
    console.error("L'email n'est pas défini pour l'utilisateur sélectionné.");
    return;
  }
  axios.post('http://localhost:8000/api/get-project-details', { email: currentItem.value.email  },
    { headers: {'Authorization': `Bearer ${authToken}`},
      withCredentials: true,
    }
  )
    .then((response) => {
      const project = response.data;
      projectData.value.company = project.company;
      projectData.value.project_title = project.project_title;
      projectData.value.project_type = project.project_type;
      projectData.value.partner = project.partner;  // If there's a partner, display it in the popup. Otherwise, set it to an empty string.
      projectData.value.specs = project.specs;
      projectData.value.original_filename = project.original_filename;
    })
    .catch((error) => {
      console.error('Error fetching project details:', error);
    });
};
const selectedSpecialty = ref('');
const specialties = ['BIS', 'BI', 'Accounting', 'Finance', 'Marketing','Management','HRM','BE','FEE'];
const updateSpecialty = (newSpecialty) => {
  selectedSpecialty.value = newSpecialty;
};
const projects = ref([]);
const fetchProjects = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getUnsupervisedStudents',
    { headers: {'Authorization': `Bearer ${authToken}`}});
    projects.value = response.data;
  } catch (error) {
    console.error('Error fetching unsupervised projects:', error);
  }
};
const filteredProjects = computed(() => {
  if (!selectedSpecialty.value) {
    return projects.value;  // Return all projects if no specialty is selected
  }
  return projects.value.filter(project => project.specialty === selectedSpecialty.value);
});
// Listen for the 'requestSent' event
eventBus.on('requestSent', () => {
  fetchProjects(); // Refresh the projects list when the event is emitted
});

const addProject = async (project) => {
  try {
    const response = await axios.post('http://localhost:8000/api/addProject', {
      student_id: project.student_id,
      teacher_email: teacherEmail
    }, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    // Remove the project from the list after successful assignment
    projects.value = projects.value.filter(p => p.student_id !== project.student_id);
    alert('student added successfully !');
  } catch (error) {
    console.error('Error assigning project:', error);
  }
};
  const router = useRouter();
  const navigateToComplaint = (user) => {
  router.push({ name: 'complaintT', params: { username: user.email, specialty: user.specialty } });
};

const choisenType=(type) => {
  selectedType.value = type;
    }
    onMounted(() => {
      fetchStudents(); 
      fetchProjects();
    });    
  </script>

  <style scoped>
  .complaint-container {
    width: 630px;
    margin: 0 auto;
    padding: 40px 65px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background: radial-gradient(#ff9b66,rgba(234, 172, 139, 0.67));
    font-family: 'lato';
  }
  .complaint {
    position: relative;
    bottom: 20px;
    color: #b94100;
    text-align: center;
    font-size: 27px;
    
  }
  .complaint-item {
    display: flex;
    justify-content: space-between; 
    align-items: center;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
  }
   .username {
    display: flex;
    position: relative;
    left: 5px;
    gap: 10px;
  } 
  .icons {
    position: relative;
    right: 10px;
    display: flex;
  } 
  .icons i {
    font-size: 20px;
    cursor: pointer;
    color: #367298 ;
  }
  .icons2 {
    position: relative;
    right: 10px;
    display: flex;
    align-items: end;
    
  } 
  .icons2 i {
    font-size: 20px;
    cursor: pointer;
    color: #367298 ;
  }
  .home2{
      position: relative;
      display: flex;
      top: 30px;
      cursor: pointer;
      font-family: 'lato';
      color: #316381 ;
      font-size: 14px;
    } .home2:hover{ color: #204156;}

    .fonc{
      background-color: transparent;
    padding: 10px;
    cursor: pointer;
    border:none;
    gap: 15px;
    color: #367298;
    font-size: 17px;
    
}
.fonc.active {
    font-weight: bold;
    border: none;   
    text-decoration:underline;
}
.fonc-group {
    display: flex;
}
.Gfonc{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  position: relative;
  bottom: 20px;
}
.studsPrjts{
  max-height: 300px; 
  overflow-y: auto;
  padding: 0 10px;
}
.studsPrjts::-webkit-scrollbar {
  width: 4px;
}
.studsPrjts::-webkit-scrollbar-track {
  background: transparent; 
}
.studsPrjts::-webkit-scrollbar-thumb {
  background-color: white; 
  border-radius: 10px; 
}
.info-popup {
  
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
}

.info-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 100%;
}
.infoClaim{
    color: #355070;
    display: flex;
    flex-direction: column;
    align-items: baseline;
    row-gap: 15px;
    font-family: 'lato';
  }
.close-btn {
  position: absolute;
  top: 12px;
  right: 22px;
  font-size: 24px;
  cursor: pointer;
}

.blur-background {
  filter: blur(5px);
}
  </style> 
 