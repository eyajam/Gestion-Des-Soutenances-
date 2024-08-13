<template>
    <div class="complaint-Container">
      <h2 :class="{ 'blur-background': showInfo || showMail }" style="position: relative;bottom: 30px;">Complaints &#9660;</h2>
      <div class="claims" :class="{ 'blur-background': showInfo || showMail }">
        <div class="studComplaints">
          <div v-for="stud in students" :key="stud.id" class="studs">
            <div class="stud">
              <i class="fi fi-ts-user-graduate icon-left"></i>
              {{ stud.username }} " {{ stud.specialty }} "
            </div>
            <div class="session-icon">
              <i class="fi fi-ss-comment-info" @click="showInfoDiv(stud)" style="color: #004479;"></i>
            </div>
          </div>
        </div>
        <div class="teacherComplaints">
          <div v-for="teacher in teachers" :key="teacher.id" class="studs">
            <div class="stud">
              <i class="fi fi-tr-chalkboard-user icon-left"></i>
              {{ teacher.username }}
              
            </div>
            <div class="session-icon">
              <i class="fi fi-ss-circle-envelope" @click="showMailDiv(teacher)" style="color: #ffead4;"></i>
              <i class="fi fi-ss-comment-info" style="margin-left: 10px;color: #004479;" @click="showInfoDiv(teacher)"></i>
            </div>
          </div>
        </div>
      </div>
      <div v-if="showInfo" class="info-popup">
      <div class="info-content">
        <span @click="hideInfoDiv" class="close-btn">&times;</span>
        <div class="infoClaim">
          <div v-if="currentItem"><strong>{{ currentItem.username }}</strong> <span v-if="currentItem.specialty">"<strong>{{ currentItem.specialty }} </strong>"</span></div>
          <div><strong>Object :</strong> Sans encadrant académique</div>
          <div><strong>Message :</strong> ca fais plus que 2 semaines que j'ai fait une réclamation et j'ai toujours pas de réponse ni d'encadrant.</div>
          <div v-if="!currentItem.specialty" class="SS">
            <i class="fi fi-ss-circle-2" style="font-size:large ; margin-right: 8px; margin-top: 6px;"></i>Session</div>
        </div>
      </div>
    </div>
    <div v-if="showMail" class="mail-popup">
  <div class="mail-content">
    <span @click="hideMailDiv" class="close-btn">&times;</span>
    <div class="mailClaim">
      <div v-if="currentItem"><strong>To: {{ currentItem.username }}</strong></div>
      <textarea v-model="mailMessage" placeholder="Write your message here..." class="mail-textarea"></textarea>
      <button @click="sendMail" class="send-button"><i class="fi fi-rs-paper-plane-launch"></i></button>
    </div>
  </div>
</div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const students = ref([
    { id: 1, username: 'eya jammoussi', specialty: 'BIS' },
    { id: 2, username: 'ines ben rabeh', specialty: 'BIS' },
    { id: 3, username: 'malek seghair', specialty: 'BI' },
    { id: 4, username: 'Omar ben salem', specialty: 'BIS' },
    { id: 5, username: 'shirine sarraj ', specialty: 'BIS' },
    { id: 6, username: 'Omaima kiassa', specialty: 'BIS' }, 
  ]);
  
  const teachers = ref([
    { id: 1, username: 'Anouer Bennajeh' },
    { id: 2, username: 'chaouki bayoudhi' },
    { id: 3, username: 'moez hammemi' },
    { id: 4, username: 'hadhemi' },
    { id: 5, username: 'Baati' },
    { id: 6, username: 'hafedh' },
  ]);
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
    row-gap: 15px;
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
  background-color: #ffffff;
  border: 1px solid #355070;
  padding: 5px 10px;
  border-radius: 20px;
  color: #355070;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  margin-top: 12px;
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


  </style>
  