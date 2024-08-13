<template>
    <div class="user-management">
      <div class="header":class="{ 'blur-background ': isModalOpen || state.isAddUserModalOpen}">
        <h2 style="font-size: 30px;">All users</h2>
        <div class="actions">
          <button class="add-user" @click="openAddUserModal">
            <i class="fi fi-rr-user-add" style="position: relative; top: 2px; right: 7px;"></i>Add user
          </button>
        </div>
      </div>
      <div class="search-bar" :class="{ 'blur-background ': isModalOpen || state.isAddUserModalOpen }">
        <i class="fi fi-sr-member-search" style="position: relative; top: 8px;font-size: 18px; left: 10px;"></i>
        <input type="text" placeholder="Search for users" v-model="searchQuery" />
        <div>
            <i class="fi fi-sr-trash" style="position: relative; top: 8px;font-size: 18px;right: 10px;cursor: pointer;"@click="deleteSelectedUsers"></i>
        </div>
      </div>
      <table class="user-table" :class="{ 'blur-background ': isModalOpen || state.isAddUserModalOpen }">
        <thead>
          <tr>
            <th><input type="checkbox" @change="toggleAll" /></th>
            <th>Name</th>
            <th>Role</th>
            <!-- <th>Status</th> -->
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in filteredUsers" :key="user.email">
            <td><input type="checkbox" v-model="user.selected" /></td>
            <td class="user-info">
              <div>
                <div class="user-name">{{ user.name }}</div>
                <div class="user-email">{{ user.email }}</div>
              </div>
            </td>
            <td>{{ user.Role }}</td>
            <!-- <td>
              <span :class="{'status-active': user.status === 'Active', 'status-inactive': user.status === 'Inactive'}">
                {{ user.status }}
              </span>
            </td -->
            <td>
              <button class="edit-user" @click="openModal(user.Role)"><i class="fi fi-rr-user-pen" style="position: relative; top: 2px; margin-right: 5px;"></i>Edit user</button>
              <button class="delete-user" @click="deleteUser(user)"><i class="fi fi-sr-trash" style="position: relative; top: 2px; margin-right: 5px;"></i>Delete user</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="isModalOpen" class="modal-overlay" @click="closeModal"></div>
      <div v-if="isModalOpen" class="modal">
       <div class="modal-content" @click.stop>
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
            <label class="EUA" for="cin">Status (new or repeating) :</label>
            <input type="text" class="champ" id="CIN" v-model="form.status" required>
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
            <div class="group">
              <label class="EUA" for="password">Password :</label>
              <input class="champ" id="password" v-model="form.password" type="password" required>
            </div>
            <div class="group">
              <label class="EUA" for="password">Confirm your password :</label>
              <input class="champ" id="password_confirmation" v-model="form.passwordConfirmation" type="password" required>
            </div>
          </div>
        </div>
      </form>
      <div class="btn-MAJ"><button type="submit" class="MAJ">update</button></div> 
      </div>
      </div>
    <div v-if="state.isAddUserModalOpen" class="modal-overlay" @click="closeAddUserModal"></div>
    <div v-if="state.isAddUserModalOpen" class="modal">
      <div class="modal-content" @click.stop>
        <span class="close" @click="closeAddUserModal">&times;</span>
        <h2>Add User</h2>
        <form>
          <div class="group">
    <div class="button-group">
        <button
            class="userType"
            :class="{ active: state.newUserType === 'teacher' }"
            @click="setUserType('teacher')">Teacher
        </button>
        <button
            class="userType"
            :class="{ active: state.newUserType === 'student' }"
            @click="setUserType('student')">Student
        </button>
    </div>
</div>
          <div class="firstSecondC">
            <div class="firstContainer">
              <div class="group">
                <label class="EUA" for="firstname">First Name :</label>
                <input class="champ" id="firstname" v-model="state.newUserForm.firstname" type="text" required>
              </div>
              <div class="group">
                <label class="EUA" for="lastname">Last Name :</label>
                <input class="champ" id="lastname" v-model="state.newUserForm.lastname" type="text" required>
              </div>
              <div v-if="state.newUserType === 'teacher'" class="group">
                <label class="EUA" for="grade">Grade :</label>
                <input class="champ" id="grade" v-model="state.newUserForm.grade" type="text" required>
              </div>
              <div v-if="state.newUserType === 'student'" class="group">
                <label class="EUA" for="cin">CIN student :</label>
                <input class="champ" id="cin" v-model="state.newUserForm.cin" type="text" required>
              </div>
              <div v-if="state.newUserType === 'student'" class="group">
                <label class="EUA" for="status">Status (new or repeating) :</label>
                <input class="champ" id="status" v-model="state.newUserForm.status" type="text" required>
              </div>
              <div v-if="state.newUserType === 'student'" class="group">
                <label class="EUA" for="specialty">Specialty :</label>
                <input class="champ" id="specialty" v-model="state.newUserForm.specialty" type="text" required>
              </div>
            </div>
            <div class="secondContainer">
              <div v-if="state.newUserType === 'student'" class="group">
                <label class="EUA" for="phoneNumber">Phone number :</label>
                <input class="champ" id="phoneNumber" v-model="state.newUserForm.phoneNumber" type="text" required>
              </div>
              <div class="group">
                <label class="EUA" for="email">Email :</label>
                <input class="champ" id="email" v-model="state.newUserForm.email" type="email" required>
              </div>
              <div class="group">
                <label class="EUA" for="password">Password :</label>
                <input class="champ" id="password" v-model="state.newUserForm.password" type="password" required>
              </div>
              <div class="group">
                <label class="EUA" for="passwordConfirmation">Confirm your password :</label>
                <input class="champ" id="passwordConfirmation" v-model="state.newUserForm.passwordConfirmation" type="password" required>
              </div>
            </div>
          </div>
        </form>
        <div class="btn-MAJ">
          <button type="submit" class="MAJ">Add User</button>
        </div>
      </div>
      </div>
    </div>
</template>
  
  <script setup>
  import { ref, computed , reactive } from 'vue';
  
   const users = ref([
    { name: 'Neil Sims', email: 'neil.sims@flowbite.com', Role: 'student',  selected: false },
    { name: 'Roberta Casas', email: 'roberta.casas@flowbite.com', Role: 'teacher', selected: false },
    { name: 'Michael Gough', email: 'michael.gough@flowbite.com', Role: 'teacher', selected: false },
    { name: 'Jese Leos', email: 'jese.leos@flowbite.com', Role: 'student', selected: false },
    { name: 'Jese Leos', email: 'jese.leos@flowbite.com', Role: 'student', selected: false },
  ]); 
  const userType = ref('');
  const form = ref({
    firstname: '',
    lastname: '',
    cin: '',
    status:'',
    specialty: '',
    grade:'',
    number:'',
    email: '',
    password: '',
    passwordConfirmation: '',
    Role: ''
  });
  const searchQuery = ref('');
  
  const filteredUsers = computed(() => {
    return users.value.filter(user => 
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });
  const toggleAll = (event) => {
  const checked = event.target.checked;
  filteredUsers.value.forEach(user => {
    user.selected = checked;
  });
};
const deleteUser = (userToDelete) => {
  users.value = users.value.filter(user => user !== userToDelete);
};
const deleteSelectedUsers = () => {
  users.value = users.value.filter(user => !user.selected);
};

const isModalOpen = ref(false);

const openModal = (role) => {
  userType.value = role;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
}

const state = reactive({
  isAddUserModalOpen: false,
  newUserType: '',
  newUserForm: {
    firstname: '',
    lastname: '',
    grade: '',
    cin: '',
    status: '',
    specialty: '',
    phoneNumber: '',
    email: '',
    password: '',
    passwordConfirmation: ''
  }
});
function setUserType(type) {
        this.state.newUserType = type;
    }
function openAddUserModal() {
  state.isAddUserModalOpen = true;
  state.newUserType = 'teacher'; 
}

function closeAddUserModal() {
  state.isAddUserModalOpen = false;
}

  </script>
  
  <style scoped>
  .user-management {
    padding: 20px;
  }
  
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .actions {
    display: flex;
    gap: 10px; 
  }
  
  .add-user {
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .add-user {
    background-color: #658acb;
    color: white;
  }
  
  .search-bar {
    display: flex;
    margin: 20px 0;
    gap: 25px;
  }
  
  .search-bar input {
    display: flex;
    flex-direction: column;
    align-items: baseline;
    width: 200px;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
  }
  
  .user-table {
    width: 100%;
    border-collapse: collapse;
    
  }
  
  .user-table th, .user-table td {
    padding: 15px 38px;
    border-bottom: 1px solid #d1d5db;
    
  }
  
  .user-info {
    display: flex;
    align-items: center;
  }
  
  .user-name {
    font-weight: bold;
  }
  
  .user-email {
    font-size: 0.9em;
    color: #6b7280;
  }
  
  .edit-user, .delete-user {
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 5px;
  }
  
  .edit-user {
    background-color: #96ADD6;
    color: white;
  }
  
  .delete-user {
    background-color: #e85234;
    color: white;
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
  top: 50%;
  left: 50%;
  transform: translate(-30%, -50%);
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
.blur-background {
  filter: blur(5px);
}
.close {
  position: absolute;
  top: 10px;
  right: 10px;
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
.button-group {
    display: flex;
    
}

.userType {
    background-color: transparent;
    padding: 10px;
    cursor: pointer;
    border:none;
    gap: 15px;
    color: #2d343a;
   
}

.userType.active {
    font-weight: bold;
    border: none;   
    text-decoration:underline;
}

  </style>
  