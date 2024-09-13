<template>
    <nav class="navbar" :class="{ 'blur-background': props.visible }">
      <div class="navbar-logo">
        <img src="/images/ISG.png" alt="ISGT Logo">
      </div>
     <!-- <div class="icon-container" >
      <div class="navbar-icon" v-if="isLoggedIn"> 
      <i class="fi fi-rs-circle-user"></i></div>
    </div> --> 
    <div class="icon-container" v-if="isUserLoggedIn">
      <div class="navbar-icon" style="font-size: 20px; color: #ff5b3ec8;">
        <i class="fi fi-rr-bell-notification-social-media"></i></div>
      <div class="navbar-icon" style="font-family: 'lato'; font-size: 17px;">{{ user.firstName }} {{ user.lastName }}</div>
      <div class="navbar-icon" @click="toggleDropdown" style="font-size: 30px;"> 
        <i class="fi fi-rs-circle-user"></i></div>
    </div>
    <div v-if="isDropdownVisible" class="dropdown-menu">
        <ul>
          <li @click="editProfile">Your Profile</li>
          <li @click="signOut">Sign Out</li>
        </ul>
    </div>
    <div :visible="isEditingProfile" class="modal-overlay"></div>
    <profileEdit :visible="isEditingProfile" @close="closeProfileEdit"/>
    </nav>
    
  </template>
  <script setup>
  import { ref,onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import profileEdit from './profileEdit.vue';

  const isUserLoggedIn = ref(false);
  const user = ref({
  firstName: '',
  lastName: ''
  });
  function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

  const isDropdownVisible = ref(false);
  const isEditingProfile = ref(false);
  const emit = defineEmits(['profileEditVisibility']);

  function toggleDropdown() {
    isDropdownVisible.value = !isDropdownVisible.value;
  }

  function editProfile() {
    isDropdownVisible.value = false; // Close dropdown
    isEditingProfile.value = true;   // Open profile edit form
    emit('profileEditVisibility', true); // Emit event to parent
  }
  
  function closeProfileEdit() {
  isEditingProfile.value = false;  // Close profile edit form
  emit('profileEditVisibility', false); // Emit event to parent
  } 
  const props = defineProps({
      visible: Boolean,
    });
    const router = useRouter();
onMounted(() => {
  const authToken = localStorage.getItem('authToken'); 
  if (authToken) {
    isUserLoggedIn.value = true;
    const userDetails = JSON.parse(localStorage.getItem('userDetails'));
    if (userDetails) {
      user.value.firstName =capitalizeFirstLetter(userDetails.firstName);
      user.value.lastName =capitalizeFirstLetter(userDetails.lastName);
    }
  }
});
const signOut = () => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('userDetails');
  localStorage.removeItem('userRole');
  localStorage.removeItem('email');
  isUserLoggedIn.value = false;
  router.push({ name: 'login' });
};
</script>
  
  <style scoped>
  body{
    background-color: rgba(238, 238, 238, 0.842);
  }
  .icon-container{
    display: flex;
    align-items: center;
  gap: 17px; 
  position: relative;
  right: 50px;
  }
  .navbar {
    display: flex;
    justify-content: space-between;
    padding: 8px;
    align-items: center;
    background-color: #355070; 
    width: 100vw;
    margin: 0; 
    position: absolute;
    top: 0;
    left: 0;
  }
  .navbar-logo img {
    position: relative;
    max-height: 55px; 
    left: 10px;

  }
  .navbar-icon {
  
  color: #f0f0f0; 
  position: relative;
  /* right: 50px; */
  cursor: pointer;
}
.dropdown-menu {
  position: absolute;
  right: 60px;
  top: 60px;
  background-color: white;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  overflow: hidden;
}
.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.dropdown-menu li {
  padding: 5px 30px 5px 15px;
  cursor: pointer;
  color: #355070;
  font-size: 15px;
  font-family: 'lato';
  display: flex;
}
.dropdown-menu li:hover {
  background-color: #f0f0f0;
}
.modal-overlay {
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
    .blur-background {
  filter: blur(5px);
}

  </style>
  