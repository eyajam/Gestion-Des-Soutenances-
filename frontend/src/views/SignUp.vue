<template>
  <div class="main-container">
    <div v-if="showActivationPopup" class="popup">
      <h4 style="margin-top: 0px; font-family: 'lato';">Enter Activation Code :</h4>
      <div style="display: flex; justify-content: center; gap: 25px">
      <input v-model="activationCode" placeholder="Enter your code" style="border-radius: 6px; border: 1px solid #6482AD;" >
      <div @click="verifyCode" class="verifBtn"><i class="fi fi-br-check" style="color: white; text-align: center;"></i></div></div>
    </div>
    <form class="form3" @submit.prevent="handleSubmit">
      <div style="margin-bottom: 30px;">
       <!-- <img src="/images/circle-user.png" alt="Icon" class="form-icon" /> -->
       <i class="fi fi-rr-circle-user form-icon" alt="Icon"></i>
      </div>
      <div class="form-container">
       <div class="container1">
        <div class="form-group">
         <label for="firstname">First Name :</label>
         <input class="input" id="firstname" v-model="form.firstname" type="text" required>
        </div>
        <div class="form-group">
         <label for="lastname">Last Name :</label>
         <input class="input" id="lastname" v-model="form.lastname" type="text" required>
        </div>
        <div v-if="props.userType === 'teacher'" class="form-group">
         <label for="grade">Grade :</label>
         <Dropdown :modelValue="form.grade" :options="grades" @update:modelValue="updateGrade" @change="handleGradeChange" />
        </div>
        <div v-if="props.userType === 'student'" class="form-group">
         <label for="cin">CIN student :</label>
         <input type="text" class="input" id="cin" v-model="form.cin" required>
        </div>
        <div v-if="props.userType === 'student'" class="form-group">
         <label for="status">Status (new or repeating) :</label>
         <input type="text" class="input" id="status" v-model="form.status" required>
        </div>
        <div v-if="props.userType === 'student'" class="form-group">
           <label for="specialty">Specialty :</label>
           <Dropdown :modelValue="form.specialty" :options="specialties" @update:modelValue="updateSpecialty" @change="handleSpecialtyChange" />
          </div>
       </div>
        <div class="container2">
         <div v-if="props.userType === 'student'" class="form-group">
          <label for="number">Phone number :</label>
          <input type="text" class="input" id="number" v-model="form.number" required>
         </div> 
         <div class="form-group">
          <label for="email">Email :</label>
          <input class="input" id="email" v-model="form.email" type="email" required>
         </div>
         <div class="form-group">
          <label for="password">Password :</label>
          <input class="input" id="password" v-model="form.password" type="password" required>
         </div>
         <div class="form-group">
          <label for="password">Confirm your password :</label>
          <input class="input" id="password_confirmation" v-model="form.passwordConfirmation" type="password" required>
         </div>
       </div>
      </div>
      <button class="btn1" type="submit">Sign Up</button>
      <router-link class="home" :to="{ name: 'login'}">⬅ Return to home page</router-link> 
    </form>   
  </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import Dropdown from '../components/DropDown.vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router';
  const router = useRouter();
  const props = defineProps({
  userType: String
});
const showActivationPopup = ref(false);
const activationCode = ref('');
const form = ref({
    firstname: '',
    lastname: '',
    cin: '',
    specialty: '',
    grade:'',
    status:'',
    number:'',
    email: '',
    password: '',
    passwordConfirmation: '',
    role: props.userType
  });
const grades = ['maître assistant', 'assistant', 'vacataire', 'contractuel', 'PES'];
const updateGrade = (newGrade) => {
  form.value.grade = newGrade;
};
const handleGradeChange = (newGrade) => {
  console.log('Selected grade changed to:', newGrade);
};

const specialties = ['BIS', 'BI', 'Accounting', 'Finance', 'Marketing','Management','HRM','BE','FEE'];
const updateSpecialty = (newSpecialty) => {
  form.value.specialty = newSpecialty;
};
const handleSpecialtyChange = (newSpecialty) => {
  console.log('Selected specialty changed to:', newSpecialty);
};
function formDataToObject(formData) {
  const obj = {};
  formData.forEach((value, key) => {
    obj[key] = value;
  });
  return obj;
}
// validations des champs 
function validateForm() {
  let errors = [];
  // Validation des champs communs
  if (!form.value.firstname) {
    errors.push('Firstname is required.');
  }
  if (!form.value.lastname) {
    errors.push('Lastname is required.');
  }
  if (!form.value.email) {
    errors.push('Email is required.');
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.push('Invalid email format.');
  }
  if (!form.value.password) {
    errors.push('Password is required.');
  } else if (form.value.password !== form.value.passwordConfirmation) {
    errors.push('Passwords do not match.');
  }
  if (!form.value.role) {
    errors.push('Role is required.');
  } else if (!['student', 'teacher', 'admin'].includes(form.value.role)) {
    errors.push('Invalid role selected.');
  }
  // Validation des champs spécifiques à l'étudiant
  if (form.value.role === 'student') {
    if (!form.value.cin || form.value.cin.length !== 8) {
      errors.push('CIN is required and must be 8 characters.');
    }
    if (!form.value.specialty) {
      errors.push('Specialty is required.');
    }
    if (!form.value.status) {
      errors.push('Status is required.');
    }
    if (!form.value.number || !/^\d{8}$/.test(form.value.number)) {
      errors.push('Number is required and must be a valid 8-digit number.');
    }
  }
  // Validation des champs spécifiques à l'enseignant
  if (form.value.role === 'teacher') {
    if (!form.value.grade) {
      errors.push('Grade is required.');
    }
  }
  // Affichage des erreurs s'il y en a
  if (errors.length > 0) {
    alert(errors.join('\n'));
    return false;
  }
  return true;
}
function handleSubmit() {
  if (!validateForm()) {
    return;
  }
  // Vérification si l'email existe déjà
  axios.post('http://localhost:8000/api/checkEmailExists', { email: form.value.email })
    .then(response => {
      if (response.data.exists) {
        alert('This email already exists. Please choose another.');
      } else {
        // Si le rôle est "student", vérifie l'unicité du CIN
        if (form.value.role === 'student') {
          axios.post('http://localhost:8000/api/checkCinExists', { cin: form.value.cin })
            .then(cinResponse => {
              if (cinResponse.data.exists) {
                alert('This CIN already exists. Please choose another.');
              } else {
                submitForm();  // Continue avec l'envoi du formulaire après validation
              }
            })
            .catch(error => {
              console.error('CIN Check Error:', error.response ? error.response.data : error.message);
              alert('An error occurred while checking the CIN.');
            });
        } else {
          submitForm();  // Continue directement si le rôle n'est pas "student"
        }
      }
    })
    .catch(error => {
      console.error('Email Check Error:', error.response ? error.response.data : error.message);
      alert('An error occurred while checking the email.');
    });
}

  function submitForm() {
    if (form.value.password !== form.value.passwordConfirmation) {
    alert('Passwords do not match!');
    return;}
    
    // Créer un objet FormData pour une meilleure flexibilité
  const dataToSend = new FormData();
  dataToSend.append('firstname', form.value.firstname);
  dataToSend.append('lastname', form.value.lastname);
  dataToSend.append('email', form.value.email);
  dataToSend.append('password', form.value.password);
  dataToSend.append('password_confirmation', form.value.passwordConfirmation);
  dataToSend.append('role', props.userType);
  
  // Ajout conditionnel de champs spécifiques à l'étudiant
  if (form.value.role === 'student') {
    dataToSend.append('cin', form.value.cin);
    dataToSend.append('specialty', form.value.specialty);
    dataToSend.append('status', form.value.status);
    dataToSend.append('number', form.value.number);
  }
  // Ajout conditionnel de champs spécifiques à l'enseignant
  if (form.value.role === 'teacher') {
    dataToSend.append('grade', form.value.grade);
  }
  // Envoi de la requête avec axios
  axios.post('http://localhost:8000/api/sendVerificationCode', {email: form.value.email} )
    .then(response => {
      showActivationPopup.value = true;
      const verificationCode = response.data.encrypted_code;
      const dataObject = formDataToObject(dataToSend);
      dataObject.verification_code = verificationCode;
       // Stocker temporairement les données de l'utilisateur jusqu'à la vérification
       localStorage.setItem('tempUserData', JSON.stringify(dataObject));
    })
    .catch(error => {
      // Affichage des détails de l'erreur pour plus de clarté
      console.error('Registration Error:', error.response ? error.response.data : error.message);
      alert('An error occurred during registration.');
    });
  }
  // Méthode de vérification du code

    function verifyCode() {
    // Récupérer les données temporaires de l'utilisateur
    const tempUserData = JSON.parse(localStorage.getItem('tempUserData'));
    axios.post('http://localhost:8000/api/verify-code', {
        code: activationCode.value,
        encrypted_code: tempUserData.verification_code,
    })
    .then(response => {
        if (response.data.success) {
          showActivationPopup.value = false;
          const decryptCode=response.data.verification_code;
          tempUserData.verification_code=decryptCode;
            axios.post('http://localhost:8000/api/registerUser', tempUserData)
                .then((response) => {
                    alert('Account verified and registered successfully! Log in now.');
                    localStorage.removeItem('tempUserData');
                    router.push({ name: 'login' });
                    // const role = response.data.role;
                    /* if (role === 'student') {
            router.push({ name: 'StudentDashboard' });
          } else if (role === 'teacher') {
            router.push({ name: 'TeacherDashboard' });
          } else {
            alert('Unknown role. Cannot redirect.');
          } */
                })
                .catch(error => {
                  console.error('Error registering user:', error.response.data);
                });
        } else {
            alert('Invalid code. Please try again.');
        }
    })
    .catch(error => {
      alert('Invalid code. Please try again.');
        console.error('Verification Error:', error.response.data);
    });
}
  </script>
  <style scoped>
  .popup{
    position: absolute;
    background-color: white;
    padding: 20px 50px;
    transform: translate(50%, 0%);
    top: 0px;
    border-radius: 6px;
    box-shadow:  10px 10px 60px #ebebebda,
             -10px -10px 60px #ebebebda;
}
  .verifBtn{
    padding:5px 14px; cursor: pointer; background-color: #213a63; 
     border-radius: 10px; color: #fff;
  }
  .main-container{
    width: 100%;
    /* padding-top: 70px; */
  }
  .form-container {
  display: flex;
  justify-content: space-between;
  gap: 24px;
}
  .form3{
    background-color:rgb(137 170 191 / 63%) ; 
    padding: 20px;
    border-radius: 10px;
    margin: 10px;
    width: 600px;
    display:grid;
    justify-content: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  label {
    display: block;
    margin-top: 10px;
    color: rgb(250, 250, 250);
    font-size:medium;
  }
  .input {
    width: 180pt;
    padding: 5px;
    margin: 10px 0; 
    border: none;  
    border-bottom: 2px solid #ffffff; 
    background: transparent; 
    color: #fff; 
    outline: none; 
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
  }
  .form-group {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  }
  .btn1 {
    margin-top: 30px;
    margin-left: 210px;
    width: 100pt;
    padding: 18px;
    background: rgba(234, 234, 234, 0.418);
    border: none;
    border-radius: 15px;
    color: white;
    font-size: 16px;
    cursor: pointer;
  }
  .home{
    position: relative;
    margin-right: 460px;
    cursor: pointer;
    color: #355070;
  }
  .home:hover{
    color: #1a2636;
  }
  .form-icon {
  font-size: xxx-large; 
  margin: 35px;
  color: #355070;
}
.EUA{
  color: #355070;
}
  </style>
  