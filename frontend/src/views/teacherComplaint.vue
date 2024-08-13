 <template>
    <div class="complaint-container">
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
  <div class="studsPrjts">
    <div v-if="selectedType === 'Complaints'">
      <div v-for="user in users" :key="user.id" class="complaint-item">
        <div class="username">
            <i class="fi fi-ts-user-graduate"></i>
            {{ user.username }}
            " {{ user.specialty }} "
          </div>
        <div class="icons">
          <i class="fi fi-rr-file-circle-info"></i>  
          <i style="margin-left: 12px;" class="fi fi-rr-paper-plane" @click="navigateToComplaint(user)"></i>
        </div>
      </div>
    </div>
    <div v-if="selectedType === 'Projects'">
      <div v-if="projects.length > 0">
        <div style="display: flex; padding-bottom: 15px; color: #244a62; gap: 15px;">
        <div>Add a new project from these unsupervised projects :</div>
        <dropDown2 :modelValue="selectedSpecialty" :options="specialties" @update:modelValue="updateSpecialty"/></div>
        <div v-for="project in projects" :key="project.id" class="complaint-item">
          <div class="username">
            <i class="fi fi-ts-user-graduate"></i>
            {{ project.username }}
            " {{ project.specialty }} "
          </div>
          <div class="icons2">
            <i class="fi fi-rr-file-circle-info"></i>  
            <div style="margin-left: 12px; background-color:  #367298; border-radius:15px; padding: 3px; cursor: pointer;">
              <div style="color: #fff; padding: 3px; font-size: 14px;">Add Project</div> 
            </div>
          </div>
        </div>
      </div>
      <div v-else style="position: relative; bottom: 10px;">
        <p class="no-projects-message" style="color: #dfdfdf; font-weight: bold; letter-spacing: 1px;">
          No unsupervised<br>projects found.</p>
      </div>  
    </div>
  </div>
      <router-link class="home2" :to="{ name: 'TeacherDashboard'}">⬅ Return to your account</router-link>
    </div>
  </template>
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import dropDown2 from '../components/dropDown2.vue';

  const selectedType = ref('Complaints');
  const users = ref([
    { id: 1, username: 'eya jammoussi', specialty: 'BIS' },
    { id: 2, username: 'ines ben rabeh', specialty: 'BIS' },
    { id: 3, username: 'malek seghair', specialty: 'BI' },
    { id: 4, username: 'Omar ben salem', specialty: 'BIS' },
    { id: 5, username: 'malek seghair', specialty: 'BI' },
    { id: 6, username: 'Omar ben salem', specialty: 'BIS' },
  ]);
  const selectedSpecialty = ref('specialty');
const specialties = ['BIS', 'BI', 'Accounting', 'Finance', 'Marketing','Management','HRM','BE','FEE'];
const updateSpecialty = (newSpecialty) => {
  selectedSpecialty.value = newSpecialty;
};
  const projects = ref([
   { id: 1, username: 'aziz gharbi', specialty: 'CS' },
  { id: 2, username: 'mohamed jammoussi', specialty: 'IT' }, 
  // Add more projects or students without supervisors
]);
  const router = useRouter();
  const navigateToComplaint = (user) => {
  router.push({ name: 'complaintT', params: { username: user.username, specialty: user.specialty } });
};

const choisenType=(type) => {
  selectedType.value = type;
  
    }
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
  width: 6px;
}
.studsPrjts::-webkit-scrollbar-track {
  background: transparent; 
}
.studsPrjts::-webkit-scrollbar-thumb {
  background-color: white; 
  border-radius: 10px; 
}
  </style> 
 