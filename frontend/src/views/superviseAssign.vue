<template>
    <div class="content-SA">
      <h2>
        Supervision Request & Project Assignments
        <i class="fi fi-sr-selection" style="margin-left: 10px;"></i>
      </h2>
      <div class="group-SA">
        <div class="button-group-SA">
          <button
            class="userType-SA"
            :class="{ active: activeTab === 'supervision' }"
            @click="activeTab = 'supervision'"
          >
            Supervision Request
          </button>
          <button
            class="userType-SA"
            :class="{ active: activeTab === 'assignments' }"
            @click="activeTab = 'assignments'"
          >
            Project Assignments
          </button>
        </div>
      </div>
      <div class="content-list">
        <div v-if="activeTab === 'supervision'">
          <div v-if="students.length>0" class="student" v-for="student in students" :key="student.cin">
            <p>CIN : {{ student.cin }}</p>
            <p> {{ student.first_name }} {{ student.last_name }}</p>
            <p> {{ student.specialty }}</p>
            <p> {{ student.project_type }}</p>
          </div>
          <div v-else style="color: red; font-family: 'lato'; display: flex; gap: 15px; align-items: center;justify-content: center;">
            <i class="fi fi-rr-not-found-alt"></i>
           <div> No unsupervised projects found </div>
          </div>
        </div>
        <div v-if="activeTab === 'assignments'" :class="{ 'blur-background ': showProjects }">
          <div class="teacher" v-for="teacher in teachers" :key="teacher.email">
            <p> {{ teacher.first_name }} {{ teacher.last_name }}</p>
            <p> {{ teacher.grade }}</p>
            <p> Projects: {{ teacher.project_count }}</p>
            <i class="fi fi-rr-multiple" @click="assignProject(teacher)" style="color: #cfedbb;cursor: pointer;"></i>
          </div>
        </div>
      </div>
      <button v-if="activeTab === 'supervision' && students.length>0" class="action-button" @click="sendRequest">Send Request</button>
    <div v-if="showProjects" class="modal-overlay" ></div> 
    <div v-if="showProjects" class="modal" @click="closeProjectList">
    <div class="project-list" @click.stop>
      <span class="close-SA" @click="closeProjectList">&times;</span>
      <h3>Unassigned Projects</h3>
      <div class="specialty">
      <div style="position: relative; font-size: 15px; ">Specialty :</div>
      <dropDown2 :modelValue="selectedSpecialty" :options="specialties" @update:modelValue="updateSpecialty"/></div>
      <div v-if="uniqueFilteredProjects.length > 0">
        <div class="UP">
      <div v-for="project in uniqueFilteredProjects" class="project-item">
        <label style="color: #2d343a;margin: 0;font-size: 14px;">
          <input type="checkbox" :value="project.student_id" v-model="selectedProjects" />
          {{ project.project_title }}
        </label>
      </div>
    </div>
      <button class="add-project" @click="addProjectToTeacher">Add Project</button>
    </div>
    <div v-else>
        <p style="color: #637484c2; font-size: 14px; font-weight: bold; letter-spacing:1px;">No Unassigned<br> projects found</p>
      </div>
  </div> 
    </div>
    </div>  
  </template>
  
  <script setup>
  import { onMounted, ref, computed } from 'vue';
  import dropDown2 from '../components/dropDown2.vue';
  import axios from 'axios';
  import { eventBus } from '../eventBus';

  const activeTab = ref('supervision');
  const authToken = localStorage.getItem('authToken');
const students = ref([]);
const fetchStudentsWithoutSupervision = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getStudentsWithoutSupervision',
    {headers: {'Authorization': `Bearer ${authToken}`}});
    students.value = response.data;
  } catch (error) {
    console.error('Error fetching students:', error);
  }
};
const teachers = ref([]);
const fetchTeachersWithProjectCount = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getTeachersWithProjectCount',
    {headers: {'Authorization': `Bearer ${authToken}`}});
    teachers.value = response.data;
  } catch (error) {
    console.error('Error fetching teachers:', error);
  }
};
const selectedSpecialty = ref('');
const specialties = ['BIS', 'BI', 'Accounting', 'Finance', 'Marketing','Management','HRM','BE','FEE'];
const updateSpecialty = (newSpecialty) => {
  selectedSpecialty.value = newSpecialty;
};

const showProjects = ref(false);
const selectedTeacher = ref(null);
const selectedProjects = ref([]);

const sendRequest = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/sendUnsupervisedStudentsList',{},
    {headers: {'Authorization': `Bearer ${authToken}`}});
    
    alert(response.data.message);
    // Emit an event to notify the teacher component to update its project list
    eventBus.emit('requestSent');
  } catch (error) {
    console.error('Error sending request:', error);
  }
};
const assignProject = (teacher) => {
  showProjects.value = true;
  selectedTeacher.value = teacher.email;
};
const addProjectToTeacher = async () => {
  if (selectedProjects.value.length === 0) {
    alert('Please select at least one project.');
    return;
  }
  try {
    const response = await axios.post('http://localhost:8000/api/assignProjectsToTeacher', {
      teacher: selectedTeacher.value,
      student_ids: selectedProjects.value,  // Envoyer les IDs des étudiants associés aux projets
    }, { headers: { 'Authorization': `Bearer ${authToken}` }});
    
    alert('Projects successfully assigned!');
    closeProjectList(); // Ferme la fenêtre après l'attribution
  } catch (error) {
    console.error('Error assigning projects:', error);
  }
};


const projects = ref([]);
const fetchUnsupervisedProjects = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/unsupervised-projects',
    {headers: {'Authorization': `Bearer ${authToken}`}}
    );
    projects.value = response.data; // Mettre à jour la liste des projets
  } catch (error) {
    console.error('Erreur lors de la récupération des projets non supervisés :', error);
  }
};
const uniqueFilteredProjects = computed(() => {
  const seenGroups = new Set();

  // Commence par filtrer les projets selon la spécialité choisie (s'il y en a une)
  const filtered = !selectedSpecialty.value 
    ? projects.value  // Si aucune spécialité n'est sélectionnée, retourner tous les projets
    : projects.value.filter(project => project.specialty === selectedSpecialty.value);

  // Ensuite, applique la logique d'unicité basée sur project_group
  return filtered.filter(project => {
    if (!seenGroups.has(project.project_group)) {
      seenGroups.add(project.project_group);
      return true;
    }
    return false;
  });
});

const closeProjectList = () => {
  showProjects.value = false;
  selectedProjects.value = [];
};
onMounted(() => {
  fetchStudentsWithoutSupervision();
  fetchTeachersWithProjectCount();
  fetchUnsupervisedProjects();
});
  </script>
  
  <style scoped>
.blur-background {
  filter: blur(2px);
}
.UP{
  max-height: 150px; 
  max-width: 250px;
  overflow-y: auto;
  padding: 0 10px; 
  display: contents;
}
  .group-SA {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-top: 20px;
  }
  
  .button-group-SA {
    display: flex;
    gap: 15px;
    border: none;
  }
  
  .userType-SA {
    background-color: transparent;
    padding: 10px;
    cursor: pointer;
    border:none;
    gap: 15px;
    color: #2d343a;

  }
  
  .userType-SA.active {
    font-weight: bold;
    border: none;   
    text-decoration:underline;
  }

.content-list {
  max-height: 300px; 
  overflow-y: auto; 
  margin-top: 20px;
  width: 100%; 
  padding: 0 16px;
}

.student, .teacher {
  background: #96add6e4;
  padding: 6px 18px;
  margin-bottom: 10px;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 500px;
}

.action-button {
  background-color: #bbd6a9eb;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}
/* project scrollbar */
.UP::-webkit-scrollbar {
  width: 4px;
}
.UP::-webkit-scrollbar-track {
  background: transparent;
  border-radius: 10px;
}

.UP::-webkit-scrollbar-thumb {
  background: #96add6e4;
  border-radius: 10px;
}

/* Custom scrollbar */
.content-list::-webkit-scrollbar {
  width: 8px;
}

.content-list::-webkit-scrollbar-track {
  background: transparent;
  border-radius: 10px;
}

.content-list::-webkit-scrollbar-thumb {
  background: #ffffff;
  border-radius: 10px;
}
.modal-overlay {
    position: absolute;
  top: 50%;
  left: 60%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
  /* color: #3e464ef3; */
}
.modal {
  position: fixed;
  top: 61%;
  left: 48%;
  transform: translate(0%, -50%);
  background-color: white;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  color: #2d343a;
  border-radius: 10px;
}
.project-list {
  background-color: #fff;
}
.project-item {
  display: flex;
  padding-bottom: 5px;

}
.add-project{
  background-color: #bbd6a9eb;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}
.close-SA {
  position: absolute;
  top: 5px;
  right: 10px;
  font-size: 25px;
  cursor: pointer;
  color: #778ea1;
}
.close-SA:hover{
  color: #2d363e;}
.specialty{
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    gap: 15px;
  }

  </style>
  