<template>  
 <div v-if="visible" class="modal" >
 <div class="modal-content" @click.stop >
    <span class="close" @click="closeModal">&times;</span> 
    <h2>Edit User</h2>
   <form >
     <div class="firstSecondC">
      <div class="firstContainer">
       <div class="group">
         <label class="EUA" for="firstname">First Name :</label>
         <input class="champ" id="firstname" v-model="form.firstname" type="text" required>
       </div>
       <div class="group">
         <label class="EUA" for="lastname">Last Name :</label>
         <input class="champ" id="lastname" v-model="form.lastname" type="text" required>
       </div>
       <div v-if="userType === 'teacher'" class="group">
         <label class="EUA" for="lastname">Grade :</label>
         <input class="champ" type="text" id="grade" v-model="form.grade" required/> 
       </div>
       <div v-if="userType === 'student'" class="group">
         <label class="EUA" for="cin">CIN student :</label>
         <input type="text" class="champ" id="cin" v-model="form.cin" required>
       </div>
       
       <div v-if="userType === 'student'" class="group">
         <label class="EUA" for="specialty">Specialty :</label>
         <input class="champ" id="specialty" v-model="form.specialty" type="text"/>
       </div>
     </div>
       <div class="secondContainer">
         <div v-if="userType === 'student'"class="group">
           <label class="EUA" for="cin">Phone number :</label>
           <input type="text" class="champ" id="number" v-model="form.number" required>
         </div> 
         <div class="group">
           <label class="EUA" for="email">Email :</label>
           <input class="champ" id="email" v-model="form.email" type="email" required>
         </div>
         <div v-if="userType === 'student'" class="group">
         <label class="EUA" for="cin">Status (new or repeating) :</label>
         <input type="text" class="champ" id="CIN" v-model="form.status" required>
       </div>
       </div>
     </div>
   </form>
   <div class="btn-MAJ"><button type="submit" @click="updateUserData" class="MAJ">update</button></div> 
   </div>
 </div>
</template>
<script setup>
import { ref,watch } from 'vue';
import axios from 'axios';

 const userType = localStorage.getItem('userRole');
 const authToken = localStorage.getItem('authToken');
 const emit = defineEmits(['close']);

  const form = ref({
    firstname: '',
    lastname: '',
    cin: '',
    status:'',
    specialty: '',
    grade:'',
    number:'',
    email: '',
    

  });
  const props = defineProps({
  visible: Boolean,
});
const fetchUserData = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/user', {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });

    const userData = response.data;

    form.value.firstname = userData.name;
    form.value.lastname = userData.lastName;
    form.value.cin = userData.cin;
    form.value.status = userData.status;
    form.value.specialty = userData.specialty;
    form.value.grade = userData.grade;
    form.value.number = userData.number;
    form.value.email = userData.email;

  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    fetchUserData();
  }
});
const closeModal = () => {
  emit('close'); // Emit a 'close' event to the parent
};
const updateUserData = async () => {
  try {
    await axios.put('http://localhost:8000/api/userEdit', {
      firstname: form.value.firstname,
      lastname: form.value.lastname,
      cin: form.value.cin,
      status: form.value.status,
      specialty: form.value.specialty,
      grade: form.value.grade,
      number: form.value.number,
      email: form.value.email,
      
    }, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    alert('User information updated successfully');
    closeModal();
  } catch (error) {
    console.error('Error updating user data:', error);
    alert('An error occurred while updating your information.');
  }
};
</script>
   <style scoped>
 .modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
} 
   .modal-content {
  position: relative;
  background-color: #ffffff;
  padding: 35px;
  border-radius: 8px;
  text-align: left;
}

.close {
  position: absolute;
  top: 10px;
  right: 7px;
  font-size: 25px;
  cursor: pointer;
  color: #778ea1;
}
.close:hover{
  color: #2d363e;
}
.group{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 5px;
}
.firstSecondC{
  display: flex;
  justify-content: center;
  /* align-items: center; */
  gap: 30px;
}
.editUser{
  display:flex;
  justify-content: center;
  align-items: center;
}
.EUA{
  color: #4a5a68;
  margin-bottom: 5px;
}
.champ{
 border-radius:8px ;
 border: 1px solid #4a5a68;
 height: 25px;
 background-color: #e7e7e771
}
.MAJ{
  background-color:#96ADD6 ;
  color: #fff;
}
.btn-MAJ{
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  top: 30px;
}
   </style>