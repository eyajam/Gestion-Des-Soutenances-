<template>
    <div class="container">
      <nav class="navbar">
        <div class="logo">
          <img src="/images/ISG.png" alt="Logo">
        </div>
        <div class="time">
        <div v-if="timeRemaining && !isChronometerStopped" >
              <h5 style="margin-top:-15px;margin-bottom: 0; color:#bbd6a9eb;position: absolute; ">Remaining Time :</h5> 
              <span style="color: #bbd6a9eb">{{ timeRemaining.days }} d</span> : 
              <span style="color: #bbd6a9eb">{{ timeRemaining.hours }} h </span>: 
              <span style="color: #bbd6a9eb">{{ timeRemaining.minutes }} m </span>: 
              <span style="color: #bbd6a9eb">{{ timeRemaining.seconds }} s</span>
          </div>
            <div v-else>
              <p v-if="!isChronometerStopped">The deadline has passed.</p>
            </div>
          </div>
          <div 
            @click="stopChronometer" style="color: red;position: absolute; top: 105px; right: 30px; cursor: pointer;" >
            <i  v-if="!isChronometerStopped" class="fi fi-bs-stop-circle"></i>
          </div>
        <ul class="nav-links">
          <li>
            <router-link to="/dashboard/stats" class="nav-item">
              <i class="fi fi-tr-stats dash" style="margin-right: 10px;  font-size: 17px;"></i>Dashboard</router-link>
          </li>
          <li>
            <router-link to="/dashboard/complaints" class="nav-item">
              <i class="fi fi-tr-newspaper dash" style="margin-right: 10px; font-size: 17px;"></i>Complaints</router-link>
          </li>
         <!--  <li>
            <router-link to="/" class="nav-item">
              <i class="fi fi-ts-workshop dash" style="margin-right: 10px; font-size: 17px;"></i>Supervision Request</router-link>
          </li> -->
          <li>
            <router-link to="/dashboard/users" class="nav-item">
              <i class="fi fi-tr-users-alt dash" style="margin-right: 10px;font-size: 17px;"></i>Users</router-link>
          </li>
          <li>
            <router-link to="/dashboard/superviseAssign" class="nav-item">
              <i class="fi fi-tr-student-alt dash" style="margin-right: 10px;font-size: 17px;"></i>Supervision Requests & <br>Project Assignments</router-link>
          </li>
        </ul>
        <div class="generate-link">
          <router-link to="/dashboard/GeneratePlanning" class="nav-item-plan">
            <i class="fi fi-tr-ballot plan" style="margin-right: 10px;font-size: 17px;"></i>Generate Planning</router-link>
        </div>
        <div class="logout" @click="logout">
          <i class="fi fi-br-arrow-left-from-arc" ></i><div style="position: relative; bottom: 3px; font-family: 'lato';"><strong>Logout</strong></div> 
        </div>
      </nav>
      <router-view class="content"/>
    </div>   
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  import { ref,onMounted, onUnmounted } from 'vue';

  const router = useRouter();
  const authToken = localStorage.getItem('authToken');
  const isChronometerStopped = ref(false); 
  const logout = () => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('userDetails');
  localStorage.removeItem('userRole');
  localStorage.removeItem('email');
  
  router.push({ name: 'login' });
};
const timeRemaining = ref(null);  // To store the remaining time
let intervalId;

const calculateTimeRemaining = (deadline) => {
  const now = new Date();
  const timeDiff = new Date(deadline) - now;

  if (timeDiff > 0) {
    const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

    timeRemaining.value = { days, hours, minutes, seconds };
  } else {
    timeRemaining.value = null;  // Time has passed
    clearInterval(intervalId);  // Stop the timer when the deadline has passed
  }
};

const fetchDeadline = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getDeadline',
      {headers: {'Authorization': `Bearer ${authToken}`}}
    );
    const deadline = response.data.end_date;
    const status=response.data.status;

    if (status === 'closed') {
      isChronometerStopped.value = true;
      return;
    }
    // Start the countdown
    intervalId = setInterval(() => {
      calculateTimeRemaining(deadline);
    }, 1000);
  } catch (error) {
    console.error('Error fetching deadline:', error);
  }
};

const stopChronometer = async () => {
  try {
    const authToken = localStorage.getItem('authToken');
    await axios.put('http://localhost:8000/api/stop-supervision-period', {},
    { headers: { 'Authorization': `Bearer ${authToken}`}});
    alert('Chronometer stopped successfully');
    // Stop the timer
    clearInterval(intervalId);
    isChronometerStopped.value = true;
  } catch (error) {
    console.error('Error stopping chronometer:', error);
  }
};
// Fetch the deadline when the component is mounted
onMounted(() => {
  fetchDeadline();
});
onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId);
  }
});
  </script>
  
  <style scoped>
  .logout{
    position: relative;
    right: 10px; 
    bottom: 30px;
    gap: 8px;
    display: flex;
    cursor: pointer;
  }
  .content {
  flex-grow: 1;
  padding: 20px;
  margin-left: 230px; /*cette marge pour s'adapter à la barre de navigation */
}

  .container {
   display: flex; 
   font-family: 'lato';
  }
  
  .navbar {
    width: 230px;
    height: 100vh;
    background-color: #355070;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
    position: fixed; 
    left: 0;
    top: 0;
    overflow: hidden;
    border-top-right-radius: 30px;
    border-bottom-right-radius: 30px;
  }
  
  .logo img {
    width: 115px;
    margin-bottom: 20px;
  }
  
  .nav-links {
    list-style: none;
    padding: 0;
    flex-grow: 1;
  }
  
  .nav-links li {
    width: 100%;
  }
  
  .nav-item {
    display: flex;
    width: 100%;
    padding: 12px 12px;
    text-decoration: none;
    text-align: center;
    color: white;
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
    transition: background-color 0.3s;
    
  }
  .nav-item-plan {
    display: flex;
    width: 100%;
    padding: 10px;
    text-decoration: none;
    text-align: center;
    color: white;
    border-radius: 20px;
    transition: background-color 0.3s;
  }
  .nav-item:hover  {
    background-color: rgba(238, 238, 238, 0.842);
    color: #355070;
  }
  .nav-item-plan:hover  {
    background-color: rgba(238, 238, 238, 0.842);
    color: #355070;
  }
  .dash{
    color: #b9e4ff;
  }
  .nav-item:hover .dash {
  color: #355070;
}
.plan{
  color: #ffe0cf;
  position: relative;
  top: 3px;
}
.nav-item:hover .plan {
  color: #355070;
}
.nav-item-plan:hover .plan {
  color: #355070;
}
  .generate-link {
    margin-bottom: 30px;
    padding: 15px ;
   margin-right: 15px;
   border-top-right-radius: 20px;
   border-bottom-right-radius: 20px;
  }
.time{
  position: relative;
  right: 20px;
}  
  </style>
  