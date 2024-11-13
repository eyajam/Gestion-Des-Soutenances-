<template>
    <div class="complaint-Container">
      <h2 :class="{ 'blur-background': showInfo || showMail }" style="position: relative;bottom: 30px;">Complaints &#9660;</h2>
      <div class="claims" :class="{ 'blur-background': showInfo || showMail }">
        <div class="studComplaints">
          <div v-for="complaint in complaints" :key="complaint.id" class="studs">
            <div class="stud">
              <i class="fi fi-ts-user-graduate icon-left"></i>
              {{ complaint.student.first_name }}  {{ complaint.student.last_name }}  {{ complaint.student.specialty }}
            </div>
            <div class="session-icon">
              <i class="fi fi-ss-comment-info" @click="showInfoDiv(complaint)" style="color: #004479;"></i>
            </div>
          </div>
        </div>
        
        <div class="teacherComplaints">
          <div v-for="teacherComplaint in teacherComplaints" :key="teacherComplaint.id" class="studs">
            <div class="stud">
              <i class="fi fi-tr-chalkboard-user icon-left"></i>
              <span v-if="teacherComplaint.teacher_complaint">
              {{ teacherComplaint.teacher_complaint.first_name }} {{ teacherComplaint.teacher_complaint.last_name }}
              </span>
              <span v-else>
                Teacher information unavailable
              </span>
            </div>
            <div class="session-icon">
              <i class="fi fi-ss-circle-envelope" @click="showMailDiv(teacherComplaint)" style="color: #ffead4;"></i>
              <i class="fi fi-ss-comment-info" style="margin-left: 10px;color: #004479;" @click="showInfoDiv(teacherComplaint)"></i>
            </div>
          </div>
        </div>

      </div>
    <div v-if="showInfo" class="info-popup">
      <div class="info-content">
        <span @click="hideInfoDiv" class="close-btn">&times;</span>
        <div class="infoClaim">
          <div v-if="currentItem">
            {{ currentItem.student ? currentItem.student.first_name : currentItem.teacher_complaint.first_name }} 
            {{ currentItem.student ? currentItem.student.last_name : currentItem.teacher_complaint.last_name }} 
            <span v-if="currentItem.student && currentItem.student.specialty">
              <strong>{{ currentItem.student.specialty }}</strong>
            </span>
          </div>
          <div><strong>student_email : </strong>{{ currentItem.student ? currentItem.student.email : currentItem.student_email }}</div>
          <div><strong>Object : </strong>{{ currentItem.object }}</div>
          <div><strong>Message : </strong>{{ currentItem.message }}</div>
          <div v-if="currentItem.teacher_complaint && currentItem.student.session === 'first'" class="SS">
            <div class="SS1" @click="updateSession(currentItem.student.email)">2 Session</div>
          </div>
          <div v-else-if="currentItem.teacher_complaint && currentItem.student.session === 'second'">
            <div style="font-family: 'lato'; color: red;">Second Session</div>
          </div>
          <div class="VRBtn" v-if="currentItem.complaint_type==='editForm' && currentItem.status !== 'approved' && currentItem.status !== 'disapproved'">
            <div class="validateBTN" style="background-color: rgba(145, 183, 89, 0.941);" value="approved" @click="updateStatus('approved')">validate</div>
            <div class="validateBTN" style="background-color: rgba(1216, 50, 50, 0.941); margin-left: 10px;" value="disapproved" @click="updateStatus('disapproved')">refuse</div>
          </div>
          <div v-else>
            <span v-if="currentItem.status === 'approved'" style="color: rgba(145, 183, 89, 0.941); font-weight: bold;">Complaint approved</span>
            <span v-if="currentItem.status === 'disapproved'" style="color: rgba(1216, 50, 50, 0.941); font-weight: bold;">Complaint disapproved</span>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showMail" class="mail-popup">
  <div class="mail-content">
    <span @click="hideMailDiv" class="close-btn">&times;</span>
    <div class="mailClaim">
      <div v-if="currentItem"><strong>To: {{ currentItem.teacher_email }}</strong></div>
      <textarea v-model="mailMessage" placeholder="Write your message here..." class="mail-textarea"></textarea>
      <button @click="sendMail" class="send-button"><i class="fi fi-rs-paper-plane-launch"></i></button>
    </div>
  </div>
</div>
    </div>
  </template>
  
  <script setup>
  import { ref,onMounted } from 'vue';
  import axios from 'axios';

  const complaints = ref([]);
  const teacherComplaints = ref([]);

  const showInfo = ref(false);
  const currentItem = ref(null);
  const mailMessage = ref('');
  const showMail = ref(false);

const showInfoDiv = (item) => {
  currentItem.value = item;
  showInfo.value = true;
};

const hideInfoDiv = () => {
  showInfo.value = false;
  currentItem.value = null;
};
const showMailDiv = (item) => {
  currentItem.value = item;
  showMail.value = true;
  showInfo.value = false;
};
const hideMailDiv = () => {
  showMail.value = false;
  currentItem.value = null;
  mailMessage.value = '';
};
const authToken = localStorage.getItem('authToken');
const fetchTeacherComplaints = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/teacherComplaints',{ headers: { 'Authorization': `Bearer ${authToken}` }});
    teacherComplaints.value = response.data;
  } catch (error) {
    console.error('Error fetching complaints:', error);
  }
};
const fetchComplaints = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/complaints',{ headers: { 'Authorization': `Bearer ${authToken}` }});
    complaints.value = response.data;
  } catch (error) {
    console.error('Error fetching complaints:', error);
  }
};
const updateStatus = async (status) => {
  if (currentItem.value.status === 'approved' || currentItem.value.status === 'disapproved') {
    alert('This complaint has already been processed.');
    return; 
  }
  try {
    const response = await axios.put(`http://localhost:8000/api/updateStatus/${currentItem.value.id}/status`, {
      status: status,
    }, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    currentItem.value.status = response.data.complaint.status;
    alert('Status updated to ' + status);
  } catch (error) {
    console.error('Error updating status:', error);
  }
};
const updateSession = async (email) => {
  try {
    const response = await axios.put(`http://localhost:8000/api/students/session`, {
      email: email,
    }, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    // Met à jour le champ session dans currentItem pour afficher la nouvelle session
    if (currentItem.value.student) {
      currentItem.value.student.session = 'second';
    }
    alert(response.data.message);
  } catch (error) {
    console.error('Error updating session:', error);
  }
};

onMounted(() => {
  fetchComplaints();
  fetchTeacherComplaints();
});
  </script>
  
  <style scoped>
  .claims-container {
  position: relative;
}
  .claims {
    display: flex;
    justify-content:center;
    align-items: center;
    margin: 20px;
    gap: 70px;
  }
  .infoClaim{
    color: #355070;
    display: flex;
    flex-direction: column;
    align-items: baseline;
    row-gap: 12px;
    width: 100%;
  }

  .studComplaints, .teacherComplaints {
    flex: 1;
    max-height: 315px; 
    width: 360px;
    overflow-y: auto;
  }
  
  .studs {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 15px;
    background-color: #96add6e4;
    padding: 15px;
    border-radius: 5px;
    width: 300px;
    color: white;
    box-shadow: 9px 9px 18px rgba(35, 76, 106, 0.36);
  }
  
  .stud {
    display: flex;
    align-items: center;
    flex: 1;

  }
  
  .icon-left {
    margin-right: 15px;
  }
  
  .session-icon {
    font-size: 20px;
    cursor: pointer;
  }
  .studComplaints::-webkit-scrollbar, .teacherComplaints::-webkit-scrollbar {
  width: 6px;
}

.studComplaints::-webkit-scrollbar-track, .teacherComplaints::-webkit-scrollbar-track {
  background: transparent; 
}

.studComplaints::-webkit-scrollbar-thumb, .teacherComplaints::-webkit-scrollbar-thumb {
  background-color: white; 
  border-radius: 10px; 
}
.info-popup {
  
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

.info-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 100%;
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 22px;
  font-size: 24px;
  cursor: pointer;
}

.blur-background {
  filter: blur(5px);
}
.SS{
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}
.SS1{
  background-color: #355070;
  padding: 5px 10px;
  border-radius: 10px;
  color: #fff;
  font-weight: bold;
  font-family: 'lato';
  cursor: pointer;
  margin-top: 12px;
  height: 25px;
  width: 150px;
}
.mail-popup {
  position: absolute;
  top: 50%;
  left: 58%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 10px;
}
.mail-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  max-width: 700px;
  max-height: 400px;
  width: 100%;
}
.mailClaim {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.mail-textarea {
  width: 400px;
  height: 100px;
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  resize: none;
}
.send-button {
  align-self: flex-end;
  background-color: #fff;
  border: none;
  font-size: 24px;
  color: #355070;
  cursor: pointer;
  padding: 0;
  margin-right: 8px;
}
.validateBTN{
  border-radius: 10px;
  border: 1px solid transparent;
  padding: 3px 5px;
  color: #fff;
  position: relative;
  top: 7px;
  height: 25px;
  width: 150px;
  font-family: 'lato';
  cursor: pointer;
}
.VRBtn{
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

  </style>
  