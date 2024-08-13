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
      <path fill="#eaac8bab" d="M52,-67.2C67.4,-60.3,80,-45.2,80.8,-29.4C81.7,-13.5,70.8,3,64.9,21.1C59.1,39.3,58.1,59,48.2,68.3C38.2,77.7,19.1,76.6,0.1,76.4C-18.9,76.3,-37.8,77.2,-50.8,68.9C-63.8,60.5,-70.9,42.9,-71.9,26.5C-73,10.1,-68,-5.2,-62.5,-19.7C-56.9,-34.2,-50.8,-47.9,-40.3,-56.3C-29.8,-64.7,-14.9,-67.9,1.7,-70.2C18.3,-72.5,36.6,-74,52,-67.2Z" transform="translate(100 100)" filter="url(#glow)" />
      <foreignObject x="50" y="60" width="100" height="125">
            <div xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; font-size: 10px;">
              <i class="fi fi-ts-calendar-lines-pen" style="font-size: 25px; position: relative; bottom: 6px; left: 8px;  "></i>
              <span>Select your oral exam availability</span>
              <button class="button-icon" @click="goToCalendar">
                <i class="fi fi-tr-angle-double-small-right" style="position: relative; bottom: 3px; color: #d14900;"></i>
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
      <path fill="#eaac8bab" d="M52,-67.2C67.4,-60.3,80,-45.2,80.8,-29.4C81.7,-13.5,70.8,3,64.9,21.1C59.1,39.3,58.1,59,48.2,68.3C38.2,77.7,19.1,76.6,0.1,76.4C-18.9,76.3,-37.8,77.2,-50.8,68.9C-63.8,60.5,-70.9,42.9,-71.9,26.5C-73,10.1,-68,-5.2,-62.5,-19.7C-56.9,-34.2,-50.8,-47.9,-40.3,-56.3C-29.8,-64.7,-14.9,-67.9,1.7,-70.2C18.3,-72.5,36.6,-74,52,-67.2Z" transform="translate(100 100)" filter="url(#glow)" />
      <foreignObject x="50" y="60" width="100" height="125">
            <div xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; font-size: 10px;">
              <i class="fi fi-tr-invite-alt" style="font-size: 25px; position: relative; bottom: 6px; left: 8px;  "></i>  
              <span> Consult your examination calendar</span>
              <button class="button-icon" @click="goToPlannig">
                <i class="fi fi-tr-angle-double-small-right" style="position: relative; bottom: 3px; color: #d14900;"></i>
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
      <path fill="#eaac8bab" d="M52,-67.2C67.4,-60.3,80,-45.2,80.8,-29.4C81.7,-13.5,70.8,3,64.9,21.1C59.1,39.3,58.1,59,48.2,68.3C38.2,77.7,19.1,76.6,0.1,76.4C-18.9,76.3,-37.8,77.2,-50.8,68.9C-63.8,60.5,-70.9,42.9,-71.9,26.5C-73,10.1,-68,-5.2,-62.5,-19.7C-56.9,-34.2,-50.8,-47.9,-40.3,-56.3C-29.8,-64.7,-14.9,-67.9,1.7,-70.2C18.3,-72.5,36.6,-74,52,-67.2Z" transform="translate(100 100)" filter="url(#glow)" />
      <foreignObject x="50" y="60" width="100" height="125">
            <div xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; font-size: 10px;">
              <i class="fi fi-tr-file-export" style="font-size: 25px; position: relative; bottom: 6px; left: 8px;  "></i>  
              <span>Submit claim ||<br>Add Project</span>
              <button class="button-icon" @click="goToComplaint">
                <i class="fi fi-tr-angle-double-small-right" style="position: relative; bottom: 3px; color: #d14900; "></i>
              </button>
            </div>
          </foreignObject>
    </svg>
    </div>
    </div>
    </section>
    <new_footer style="bottom: -805px;" />
    </template>
    
    <script setup>
    import NavBar from '../components/NavBar.vue';
    import new_footer from '../components/new_footer.vue';
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    
    const router = useRouter();
    const isProfileEditing = ref(false); // Pour contrôler le flou

    const handleProfileEditVisibility = (isVisible) => {
      isProfileEditing.value = isVisible;
    };
    const goToCalendar = () => {
      router.push({ name: 'TeacherCalendar' });
    }
    const goToPlannig = () => {
      router.push({ name: 'teacherPlanning' });
    }
    const goToComplaint = () => {
      router.push({ name: 'teacherComplaint' });
    }
    document.body.style.overflowX = 'hidden';
    const currentStep = ref(0);
    const steps = ref([
      { image: '/images/selectCalendar.png', description: 'We have a calendar at your disposal for the selection of your availability for the oral exams.' },
      { image: '/images/ViewCalendar.png', description: 'Once the schedule has been generated by the administrator, you will be able to view and print your customized exam timetable.' },
      { image: '/images/sendClaim.png', description: 'If your supervised students do not contact you at all during the supervision period, or for any other reason, you can submit a complaint to the administrator.' },
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
      margin-left: 10px; 
      }
      .arrowR {
      margin-right: 10px; 
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
      margin: 0 0 190px;
      gap: 40px;  
    }
    .svg1 , .svg2 , .svg3 , .svg4{
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
      background-color:#6c8a9d2a; 
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
      color: #6b8a9d;
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