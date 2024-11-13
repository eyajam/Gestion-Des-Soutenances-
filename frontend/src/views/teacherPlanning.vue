<template>
    <div class="msgContainer"> 
    <div v-if="planning && planning.length > 0" class="schedule-container">
    <p style="color: #acbcdb;font-weight: bold; font-family: 'lato';font-size: 20px;">Your upcoming defense schedule:</p>
    <table class="schedule-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Heure</th>
          <th>Salle</th>
          <th>Etudiants</th>
          <th>Etudiants-emails</th>
          <th>Titre</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in planning" :key="index">
          <td>{{ formatDate(item.date) }}</td>
          <td>{{ formatTime(item.time) }}</td>
          <td>{{ item.classroom }}</td>
          <td>{{ item.students }}</td>
          <td>{{ item.emails }}</td>
          <td>{{ item.title }}</td>
          <!-- <td>{{ item.specialty }}</td> -->
          <td>
            <!-- Check the role of the teacher for this defense -->
            <span v-if="isEncadrant(item)">Encadrant</span>
            <span v-if="isPresident(item)">President</span>
            <span v-if="isRapporteur(item)">Rapporteur</span>
          </td>
        </tr>
      </tbody>
    </table>
        <i class="fi fi-sr-left icon2" @click="goToAccount"></i>
        <button class="print-btn" @click="printSchedule">Print Schedule</button>
    </div> 
      <div v-else class="msg">
        <i class="fi fi-sr-left icon1" @click="goToAccount"></i>     
         Once the oral examination calendar is finalised, we will provide you with your schedule.
        </div>
    </div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const planning = ref(null);

const email = localStorage.getItem('email');
const goToAccount = () => {
      router.push({ name: 'TeacherDashboard' });
    }

// Function to handle printing
const printSchedule = () => {
  window.print();  // Trigger the browser's print dialog
};

// Fetch the teacher's planning once the component is mounted
onMounted(async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/teacher-planning',{email: email}, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('authToken')}` }
    });
    planning.value = response.data;
  } catch (error) {
    console.error('Error fetching teacher planning:', error);
  }
});
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB',{ day: 'numeric',
        month: 'numeric',
        year: '2-digit',});  // Change the locale if needed
};

const formatTime = (timeString) => {
    const time = new Date(timeString);
    return time.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
};
const isEncadrant = (item) => {
    return item.email_encadrant === email;
};

const isPresident = (item) => {
    return item.email_president === email;
};

const isRapporteur = (item) => {
    return item.email_rapporteur === email;
};
</script>

<style scoped>
.msgContainer{
    display: flex;
    align-items: center;
    justify-content: center;
}
.msg{
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffffec;
    width: 800px;
    height: 100px;
    border-radius: 42px;
background: linear-gradient(145deg, #fab895, #d39b7d);
box-shadow:  11px 11px 22px #966e59,
             -11px -11px 22px #fecaad;
}
.icon1{
    font-size:larger;
    position: relative; 
    font-size: large; 
    color: white;
    right: 30px;
    top: 4px;
    cursor: pointer;
}
.icon2{
    font-size:larger;
    position: relative; 
    font-size: large; 
    color: #acbcdb;
    right: 525px;
    top: 4px;
    cursor: pointer;
}
.print-btn {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
    color: white;
    background-color: #527bcdd4;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.print-btn:hover {
    background-color: #527bcd;
}
.plan{
  display: contents;
}
@media print {
  .schedule-table {
    width: 100%;
    table-layout: auto; /* Allow table to auto-adjust its layout */
    margin: auto; /* Center the table horizontally */
    
  }

  .schedule-table th, .schedule-table td {
    word-wrap: break-word;
    white-space: normal;
    font-size: 12px; /* Adjust font size for better fit */
    border: 1px solid black;
     
  }

  /* Specifically set a minimum width for the Role column */
  .schedule-table td:last-child {
    min-width: 50px; /* Adjust this value to ensure the role fits */
  }

  /* Optional: Center the text in the role column */
  .schedule-table td:last-child {
    text-align: center;
  }

  /* Hide elements like buttons during print */
  .print-btn {
    display: none;
  }

  body {
     /* Set page margins to ensure good print layout */
    font-size: 10px;
  }
}

.schedule-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  margin-top: 10px;
}

.schedule-table th, .schedule-table td {
  padding: 10px 13px;
  border: 1px solid #ddd;
}

.schedule-table th {
  background-color: #527bcd;
  color: white;
}

.schedule-table tr:nth-child(even) {
  background-color: #f2f2f2;
}

.schedule-table tr:hover {
  background-color: #f1f1f1;
}

.schedule-table td {
  font-size: 14px;
  color: #333;
}


</style>