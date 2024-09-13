<template>
<NavBar @profileEditVisibility="handleProfileEditVisibility" />
<section class="steps-container" :class="{ 'blur-background': isProfileEditing }">
  <h2><span class="arrowR">&#9654;</span>To help you get started</h2>
    <div class="carousel">
      <div class="carousel-content" :style="{ transform: `translateX(-${currentStep * 100}%)` }">
        <div class="step" v-for="(step, index) in steps" :key="index">
          <img :src="step.image" alt="Step image" />
          <p>{{ step.description }}</p>
        </div>
      </div>
      <button @click="prevStep" class="carousel-button left">&#10094;</button>
      <button @click="nextStep" class="carousel-button right">&#10095;</button>
    </div>
  </section>
<section :class="{ 'blur-background': isProfileEditing }">
  <h2>Get started now <span class="arrowL">&#9660;</span></h2>
<div class="svg-container">
<div class="svg1" >
<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <filter id="glow">
        <feGaussianBlur stdDeviation="8" result="coloredBlur"/>
        <feMerge>
          <feMergeNode in="coloredBlur"/>
          <feMergeNode in="SourceGraphic"/>
        </feMerge>
      </filter>
    </defs>
  <path fill="#89aabf80" d="M52,-67.2C67.4,-60.3,80,-45.2,80.8,-29.4C81.7,-13.5,70.8,3,64.9,21.1C59.1,39.3,58.1,59,48.2,68.3C38.2,77.7,19.1,76.6,0.1,76.4C-18.9,76.3,-37.8,77.2,-50.8,68.9C-63.8,60.5,-70.9,42.9,-71.9,26.5C-73,10.1,-68,-5.2,-62.5,-19.7C-56.9,-34.2,-50.8,-47.9,-40.3,-56.3C-29.8,-64.7,-14.9,-67.9,1.7,-70.2C18.3,-72.5,36.6,-74,52,-67.2Z" transform="translate(100 100)" filter="url(#glow)" />
  <foreignObject x="50" y="60" width="100" height="125">
        <div xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; font-size: 10px;">
          <i class="fi fi-rr-form" style="font-size: 25px; position: relative; bottom: 6px; left: 8px;  "></i>
          <span>Fill out the Senior Project Form</span>
          <button class="button-icon" @click="goToProjectForm">
            <i class="fi fi-tr-angle-double-small-right" style="position: relative; bottom: 3px; color: #213a49;"></i>
          </button>  
        </div>
      </foreignObject>
</svg>
</div>
<div class="svg2">
<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <filter id="glow">
        <feGaussianBlur stdDeviation="8" result="coloredBlur"/>
        <feMerge>
          <feMergeNode in="coloredBlur"/>
          <feMergeNode in="SourceGraphic"/>
        </feMerge>
      </filter>
    </defs>
  <path fill="#89aabf80" d="M52,-67.2C67.4,-60.3,80,-45.2,80.8,-29.4C81.7,-13.5,70.8,3,64.9,21.1C59.1,39.3,58.1,59,48.2,68.3C38.2,77.7,19.1,76.6,0.1,76.4C-18.9,76.3,-37.8,77.2,-50.8,68.9C-63.8,60.5,-70.9,42.9,-71.9,26.5C-73,10.1,-68,-5.2,-62.5,-19.7C-56.9,-34.2,-50.8,-47.9,-40.3,-56.3C-29.8,-64.7,-14.9,-67.9,1.7,-70.2C18.3,-72.5,36.6,-74,52,-67.2Z" transform="translate(100 100)" filter="url(#glow)" />
  <foreignObject x="50" y="60" width="100" height="125">
        <div xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; font-size: 10px;">
          <i class="fi fi-rr-file-edit" style="font-size: 25px; position: relative; bottom: 6px; left: 8px;  "></i>  
          <span> Edit the form information</span>
          <button class="button-icon" @click="goToEditProjectForm">
            <i class="fi fi-tr-angle-double-small-right" style="position: relative; bottom: 3px; color: #213a49;"></i>
          </button>
        </div>
      </foreignObject>
</svg>
</div>
<div class="svg3">
<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <filter id="glow">
        <feGaussianBlur stdDeviation="8" result="coloredBlur"/>
        <feMerge>
          <feMergeNode in="coloredBlur"/>
          <feMergeNode in="SourceGraphic"/>
        </feMerge>
      </filter>
    </defs>
  <path fill="#89aabf80" d="M52,-67.2C67.4,-60.3,80,-45.2,80.8,-29.4C81.7,-13.5,70.8,3,64.9,21.1C59.1,39.3,58.1,59,48.2,68.3C38.2,77.7,19.1,76.6,0.1,76.4C-18.9,76.3,-37.8,77.2,-50.8,68.9C-63.8,60.5,-70.9,42.9,-71.9,26.5C-73,10.1,-68,-5.2,-62.5,-19.7C-56.9,-34.2,-50.8,-47.9,-40.3,-56.3C-29.8,-64.7,-14.9,-67.9,1.7,-70.2C18.3,-72.5,36.6,-74,52,-67.2Z" transform="translate(100 100)" filter="url(#glow)" />
  <foreignObject x="50" y="60" width="100" height="110">
        <div xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; font-size: 10px;">
          <i class="fi fi-rr-file-export" style="font-size: 25px; position: relative; bottom: 6px; left: 8px;  "></i>  
          <span>Submit claim</span>
          <button class="button-icon" @click="goToComplaint">
            <i class="fi fi-tr-angle-double-small-right" style="position: relative; top: 10px; color: #213a49; "></i>
          </button>
        </div>
      </foreignObject>
</svg>
</div>
</div>
</section>
<new_footer style="bottom: -810px;" />
</template>

<script setup>
import NavBar from '../components/NavBar.vue';
import new_footer from '../components/new_footer.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const isProfileEditing = ref(false); // Pour contrôler le flou
const authToken = localStorage.getItem('authToken');

const handleProfileEditVisibility = (isVisible) => {
  isProfileEditing.value = isVisible;
};
const goToProjectForm = () => {
  router.push({ name: 'ProjectForm' });
}
const goToComplaint = () => {
  router.push({ name: 'complaint' });
}
const goToEditProjectForm = async () => {
  try {
    const response = await axios.get(`http://localhost:8000/api/check-edit-form-complaint`,{
      headers: { 'Authorization': `Bearer ${authToken}` }});
    const complaint = response.data; 
    
    if (!complaint || Object.keys(complaint).length === 0) {
      alert('Vous devez d\'abord soumettre une réclamation de type "editForm" pour pouvoir mettre à jour votre formulaire.');
      return;
    }
    const status = complaint.status;

    if (status === 'approved') {
      router.push({ name: 'EditProjectForm' });
    } else if (status === 'in_process') {
      alert('Votre réclamation est en cours de traitement.');
    } else if (status === 'disapproved') {
      alert('Votre réclamation a été refusée.');
    }
  } catch (error) {
    console.error('Erreur lors de la vérification de la réclamation', error);
    alert('Une erreur est survenue lors de la vérification de votre réclamation.');
  }
};
document.body.style.overflowX = 'hidden';
const currentStep = ref(0);
const steps = ref([
  { image: '/images/fillin-form.png', description: 'Fill out the form by providing all the information about your senior project, and be sure to review it when you are done.' },
  { image: '/images/Edit-form.png', description: 'You wish to edit your project form information, send a request by submitting a claim of type edit information. Once your request has been validated, you will be able to update your form. ' },
  { image: '/images/send-claim.png', description: 'If you are experiencing difficulty in finding an academic supervisor or in any other capacity, do not hesitate to submit a claim but only every two weeks' },
]);

const nextStep = () => {
  if (currentStep.value < steps.value.length - 1) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

</script>

<style scoped>
 .arrowL {
  margin-left: 10px; /* Ajustez l'espacement selon vos besoins */
  }
  .arrowR {
  margin-right: 10px; /* Ajustez l'espacement selon vos besoins */
  }
.button-icon {
  font-size: 20px;
  cursor: pointer;
  background: none;
  border: none;
  }
.svg-container {
  display: flex;
  justify-content: center;
  align-items: center;  
  width: 92%;
  /* padding-bottom: 100px;   */
  margin: 0 0 190px;
  gap: 40px;  
}
.svg1 , .svg2 , .svg3 {
  width: 310px;
  height: 200px; 
  background: none;
  font-family:'Lato';
}
.steps-container {
  width: 90%;
  overflow: hidden;
  position: relative;
  margin: 90px 10px 100px 10px;
}
.carousel {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  background-color:#e88b7d2a; 
  border-radius: 20px;
  font-family:'Lato';
}

.carousel-content {
  display: flex;
  transition: transform 0.5s ease;
}

.step {
  min-width: 100%;
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.step img {
  position: relative;
  width: 350px;
  height: 350px;
  right: 70px;
}

.step p {
  max-width: 350px;
  font-size: 16px;
  color: #333;
}

.carousel-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 30px;
  cursor: pointer;
  padding: 10px;
  z-index: 1;
  color: #eaac8b;
}

.carousel-button.left {
  left: 20px;
}

.carousel-button.right {
  right: 20px;
}
.blur-background {
      filter: blur(5px);
    }
</style>