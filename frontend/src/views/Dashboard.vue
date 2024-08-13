<template>
    <div class="container">
      <nav class="navbar">
        <div class="logo">
          <img src="/images/ISG.png" alt="Logo">
        </div>
        <ul class="nav-links">
          <li>
            <router-link to="/stats" class="nav-item">
              <i class="fi fi-tr-stats dash" style="margin-right: 10px;  font-size: 17px;"></i>Dashboard</router-link>
          </li>
          <li>
            <router-link to="/complaints" class="nav-item">
              <i class="fi fi-tr-newspaper dash" style="margin-right: 10px; font-size: 17px;"></i>Complaints</router-link>
          </li>
         <!--  <li>
            <router-link to="/" class="nav-item">
              <i class="fi fi-ts-workshop dash" style="margin-right: 10px; font-size: 17px;"></i>Supervision Request</router-link>
          </li> -->
          <li>
            <router-link to="/users" class="nav-item">
              <i class="fi fi-tr-users-alt dash" style="margin-right: 10px;font-size: 17px;"></i>Users</router-link>
          </li>
          <!-- <li>
            <router-link to="/" class="nav-item">
              <i class="fi fi-rr-download dash" style="margin-right: 10px;font-size: 17px;"></i>Projects Submitted</router-link>
          </li> -->
          <li>
            <router-link to="/superviseAssign" class="nav-item">
              <i class="fi fi-tr-student-alt dash" style="margin-right: 10px;font-size: 17px;"></i>Supervision Requests & <br>Project Assignments</router-link>
          </li>
        </ul>
        <div class="generate-link">
          <router-link to="/" class="nav-item-plan">
            <i class="fi fi-tr-ballot plan" style="margin-right: 10px;font-size: 17px;"></i>Generate Planning</router-link>
        </div>
      </nav>
      <router-view class="content"/>
    </div>   
  </template>
  
  <script setup>
  

  </script>
  
  <style scoped>
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
  
  </style>
  