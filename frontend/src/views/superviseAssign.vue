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
          <div class="student" v-for="student in students" :key="student.cin">
            <p>CIN : {{ student.cin }}</p>
            <p> {{ student.name }} {{ student.surname }}</p>
            <p> {{ student.specialty }}</p>
            <p> {{ student.projectType }}</p>
          </div>
        </div>
        <div v-if="activeTab === 'assignments'" :class="{ 'blur-background ': showProjects }">
          <div class="teacher" v-for="teacher in teachers" :key="teacher.name">
            <p> {{ teacher.name }} {{ teacher.surname }}</p>
            <p> {{ teacher.grade }}</p>
            <p> Projects: {{ teacher.projects }}</p>
            <i class="fi fi-rr-multiple" @click="assignProject(teacher)" style="color: #cfedbb;cursor: pointer;"></i>
          </div>
        </div>
      </div>
      <button v-if="activeTab === 'supervision'" class="action-button" @click="sendRequest">Send Request</button>
    <div v-if="showProjects" class="modal-overlay" ></div> 
    <div v-if="showProjects" class="modal" @click="closeProjectList">
    <div class="project-list" @click.stop>
      <span class="close-SA" @click="closeProjectList">&times;</span>
      <h3>Unassigned Projects</h3>
      <div class="specialty">
      <div style="position: relative; font-size: 15px; ">Specialty :</div>
      <dropDown2 :modelValue="selectedSpecialty" :options="specialties" @update:modelValue="updateSpecialty"/></div>
      <div v-if="unassignedProjects.length > 0">
        <div class="UP">
      <div v-for="project in unassignedProjects" :key="project.title" class="project-item">
        <label style="color: #2d343a;margin: 0;font-size: 14px;">
          <input type="checkbox" v-model="selectedProjects" :value="project.title" />
          {{ project.title }}
        </label>
      </div>
    </div>
      <button class="add-project" @click="addProjectsToTeacher">Add Project</button>
    </div>
    <div v-else>
        <p style="color: #637484c2; font-size: 14px; font-weight: bold; letter-spacing:1px;">No Unassigned<br> projects found</p>
      </div>
  </div> 
    </div>
    </div>  
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import dropDown2 from '../components/dropDown2.vue';
  const activeTab = ref('supervision');

const students = ref([
  { cin: '12345678', name: 'eya', surname: 'jammoussi', specialty: 'BIS',projectType:'Binomial' },
  { cin: '87654321', name: 'omar', surname: 'ben salem', specialty: 'BI',projectType:'Monomial' },
  { cin: '12345678', name: 'malek', surname: 'seghair', specialty: 'BIS',projectType:'Binomial' },
  { cin: '87654321', name: 'ines', surname: 'ben rebah', specialty: 'BI',projectType:'Monomial' },
  { cin: '12345678', name: 'shirine', surname: 'sarraj', specialty: 'BIS',projectType:'Binomial' },
  { cin: '87654321', name: 'oumaima', surname: 'kiassa', specialty: 'BI',projectType:'Monomial' },
  // Ajoutez plus d'étudiants si nécessaire
]);

const teachers = ref([
  { name: 'Anouer', surname: 'bennajeh', grade: 'Professor', projects: 2 },
  { name: 'chaouki', surname: 'Bayoudhi', grade: 'Associate Professor', projects: 3 },
  { name: 'Anouer', surname: 'bennajeh', grade: 'Professor', projects: 2 },
  { name: 'chaouki', surname: 'Bayoudhi', grade: 'Associate Professor', projects: 3 },
  { name: 'Anouer', surname: 'bennajeh', grade: 'Professor', projects: 2 },
  { name: 'chaouki', surname: 'Bayoudhi', grade: 'Associate Professor', projects: 3 },
  // Ajoutez plus d'enseignants si nécessaire
]);
const selectedSpecialty = ref('');
const specialties = ['BIS', 'BI', 'Accounting', 'Finance', 'Marketing','Management','HRM','BE','FEE'];
const updateSpecialty = (newSpecialty) => {
  selectedSpecialty.value = newSpecialty;
};

const unassignedProjects = ref([
  { title: 'Développement d"une application web pour la gestion des soutenances1' },
  { title: 'Développement d"une application web pour la gestion des soutenances2' },
  { title: 'Développement d"une application web pour la gestion des soutenances3' },
  { title: 'Développement d"une application web pour la gestion des soutenances4' },
  { title: 'Développement d"une application web pour la gestion des soutenances5' },
  { title: 'Développement d"une application web pour la gestion des soutenances6' },
  // Ajoutez plus de projets si nécessaire
]);

const showProjects = ref(false);
const selectedTeacher = ref(null);
const selectedProjects = ref([]);

const sendRequest = () => {
  alert('Request sent successfully');
};

const assignProject = (teacher) => {
  showProjects.value = true;
  selectedTeacher.value = teacher.name;
};

const addProjectsToTeacher = () => {
   // Trouver l'enseignant sélectionné
   const teacher = teachers.value.find(t => t.name === selectedTeacher.value);
  if (teacher) {
    // Ajouter les projets sélectionnés à l'enseignant
    teacher.projects += selectedProjects.value.length;
    // Retirer les projets sélectionnés de la liste des projets non attribués
    unassignedProjects.value = unassignedProjects.value.filter(project => !selectedProjects.value.includes(project.title));
    // Réinitialiser la liste des projets sélectionnés et fermer la modal
    selectedProjects.value = [];
    showProjects.value = false;
    alert(`Projects added to ${selectedTeacher.value}`);
  }
};
const closeProjectList = () => {
  showProjects.value = false;
  selectedProjects.value = [];
};
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
  