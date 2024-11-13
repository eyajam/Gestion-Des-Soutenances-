 <template>
  <div class="calendar-container">
   <div class="calendar">
    <div class="calendar-header">
      <button @click="previousMonth" class="fleche"><i class="fi fi-rr-angle-small-left prev"></i></button>
      <h2>{{ currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}</h2>
      <button @click="nextMonth" class="fleche"><i class="fi fi-rr-angle-small-right prev"></i></button>
    </div>
    <div class="calendar-grid">
      <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" class="day-header" :key="day">{{ day }}</div>
      <div v-for="day in calendarDays" :key="day" 
        :class="['day', 
              { 'highlighted': getEventForDate(new Date(Date.UTC(currentDate.getFullYear(), currentDate.getMonth(), day)).toISOString().split('T')[0]), 
                'today': isToday(day) 
              }]"
        @click="selectDate(day)">
        {{ day }}
      </div>
    </div>
    <router-link class="Yaccount" :to="{ name: 'TeacherDashboard'}">⬅  Return to your account</router-link>
   </div>
   <div class="availibilities">
      <h3 class="YA">Your availabilities</h3>
       <div class="events">
        <p class="event" :class="{ highlighted: selectedEvent === 'Today' }" @click="highlight('Today')">Today</p>
        <p class="event" :class="{ highlighted: selectedEvent === 'Week' }" @click="highlight('Week')">Week</p>
        <p class="event" :class="{ highlighted: selectedEvent === 'Month' }" @click="highlight('Month')">Month</p>
      </div> 
      <div v-if="(showAddEventForm && eventsForSelectedDate.length === 0)||(showAddAV)" class="add-event-box">
        <div style="position: relative; left: 72px; color: #701600; cursor: pointer;" @click="closeForm">&#10006;</div>
        <h4 style="color: #b03d00b1; margin: 0 0 14px;"> {{ selectedDate }}</h4>
        <label for="event-type" style="color: #ffffffec; font-size: 14px;">Availibility :</label>
        <select id="event-type" v-model="eventType" class="AV-selector">
          <option value="available" style="color: black;">Available</option>
          <option value="unavailable" style="color: black;">Unavailable</option>
        </select>
        <label for="start-time" class="label">Start Time :</label>
        <div class="time">
          <input type="time" id="start-time" v-model="startTime">
          <i class="fi fi-tr-calendar-clock" style="color: white; font-size: 15px; position: relative; right: 8px;"></i>
        </div>
        <label for="end-time" class="label">End Time :</label>
        <div class="time">
          <input type="time" id="end-time" v-model="endTime">
          <i class="fi fi-tr-calendar-clock" style="color: white; font-size: 15px; position: relative; right: 8px;"></i>
        </div>
        <div class="addAV">
          <i class="fi fi-sr-floppy-disk-circle-arrow-right" v-if="currentEvent" @click="saveChanges"></i>
          <i class="fi fi-rr-add" v-else @click="addEvent"></i>
        </div>
      </div>
      <div v-else>
        <p v-if="eventsForSelectedDate.length === 0" style="color: #dfdfdfc8; position: relative; top: 60px;font-weight: bold;">No availability<br>has been added</p>
        <ul v-else >
          <h4 style="color: #b03d00b1; position: relative; right: 30px; bottom: 40px;">For {{ selectedDate }} 
            <i class="fi fi-sr-map-marker-plus icon" style="margin-left:10px; color: #F4F3E6;" @click="showAddAVForm"></i></h4>
          <li v-for="event in eventsForSelectedDate" :key="event.id" class="AVS">
            <div class="AV" >
            {{ event.status }} : <br>
            {{ formatTime(event.start_time) }} - {{ formatTime(event.end_time) }} </div>
            <div class="icon-container">
            <i class="fi fi-sr-pen-circle icon" style=" color: #e8e4c8;" @click="editEvent(event)"></i>
            <i class="fi fi-sr-cross-circle icon" style="color: #e85234;"@click="removeEvent(event)"></i></div>
          </li>
        </ul>
      </div>
   </div>
  </div>
  
</template> 

 <script setup>
import { ref, computed, watch ,onMounted} from 'vue';
import axios from 'axios';

const currentDate = ref(new Date());
const selectedDate = ref(null);
const eventType = ref('available');
const startTime = ref('');
const endTime = ref('');
const events = ref([]);
const showAddEventForm = ref(false);
const selectedEvent = ref('Today');
const showAddAV =ref(false);
const currentEvent = ref(null);
const authToken = localStorage.getItem('authToken');

const formatTime = (time) =>{
      return time.slice(0, 5); // Retirer les secondes
    };
const daysInMonth = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  return new Date(year, month + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  return new Date(year, month, 1).getDay();
});

const days = computed(() => {
  const daysArray = [];
  for (let i = 1; i <= daysInMonth.value; i++) {
    daysArray.push(i);
  }
  return daysArray;
});

const calendarDays = computed(() => {
  const firstDay = firstDayOfMonth.value;
  const daysArray = Array(firstDay).fill(null).concat(days.value);
  const totalDays = daysArray.length;
  const extraDays = totalDays % 7 === 0 ? 0 : 7 - (totalDays % 7);
  return daysArray.concat(Array(extraDays).fill(null));
});

const selectDate = (day) => {
  if (day !== null) {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const date = new Date(Date.UTC(year, month, day));
    selectedDate.value = date.toISOString().split('T')[0];
    showAddEventForm.value = true;
  }
};

const addEvent = async () => {
  try {
    // Vérifier si le nombre de disponibilités est inférieur à 4
    const response = await axios.post('http://localhost:8000/api/availabilities', {
      date: selectedDate.value,
      start_time: startTime.value,
      end_time: endTime.value,
      status: eventType.value,
    },{
      headers: { 'Authorization': `Bearer ${authToken}` } 
    });
    // Si la réponse est réussie, ajoutez l'événement et réinitialisez le formulaire
    events.value.push(response.data);
    resetForm();
  } catch (error) {
    // Si l'enseignant a atteint la limite de disponibilités, afficher un message d'erreur
    if (error.response && error.response.status === 403) {
      alert(error.response.data.message); // Message d'erreur pour les 4 disponibilités atteintes
    } else {
      console.error(error); // Afficher d'autres erreurs dans la console
    }
  }
};

const resetForm = () => {
  eventType.value = 'available';
  startTime.value = '';
  endTime.value = '';
  showAddAV.value=false;
  showAddEventForm.value=false;
};

const fetchAvailabilities = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/availabilities', {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    // Ajouter les disponibilités récupérées dans `events.value`
    events.value = response.data;
  } catch (error) {
    console.error("Error fetching availabilities:", error);
  }
};
const getEventForDate = (date) => {
  const event = events.value.find(event => event.date === date);
  return event ? true : false;
};

const previousMonth = () => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth() - 1;
  currentDate.value = new Date(year, month);
};

const nextMonth = () => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth() + 1;
  currentDate.value = new Date(year, month);
};
const highlight = (event) => {
  selectedEvent.value = event;
};
const isToday = (day) => {
  const today = new Date();
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  return (
    today.getFullYear() === year &&
    today.getMonth() === month &&
    today.getDate() === day
  );
};

watch(showAddEventForm, (newVal) => {
  if (!newVal) {
    const addEventBox = document.querySelector('.add-event-box');
    if (addEventBox) {
      addEventBox.classList.add('v-leave-active');
      setTimeout(() => {
        addEventBox.classList.remove('v-leave-active');
      }, 2000); 
    }
  }
});
const showAddAVForm = () => {
  showAddAV.value = true;
};
const closeForm = () => {
  showAddEventForm.value = false;
  showAddAV.value=false;
  currentEvent.value = null;
};
 const eventsForSelectedDate = computed(() => {
  if (selectedDate.value) {
    return events.value.filter(event => event.date === selectedDate.value);
  }
  return [];
}); 
const removeEvent = async (event) => {
  const index = events.value.findIndex(e => e.id === event.id);
  if (index !== -1) {
    try {
      await axios.delete(`http://localhost:8000/api/availabilities/${event.id}`, {
        headers: { 'Authorization': `Bearer ${authToken}` }
      });
      events.value.splice(index, 1); // Supprimer localement si la suppression est réussie côté serveur
      alert("deleted event successfully")
    } catch (error) {
      console.error("Failed to delete the event:", error);
    }
  }
};
const editEvent = (event) => {
  console.log("Editing event:", event); // Vérifiez les données de l'événement

  selectedDate.value = event.date;
  eventType.value = event.status;
  startTime.value = formatTime(event.start_time);
  endTime.value = formatTime(event.end_time);
  currentEvent.value = event; // Stocker l'événement en cours d'édition
  showAddAV.value = true;
};

const saveChanges = async () => {
  try {
    const response = await axios.put(`http://localhost:8000/api/availabilities/${currentEvent.value.id}`, {
      date: selectedDate.value,
      start_time: startTime.value,
      end_time: endTime.value,
      status: eventType.value
    }, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    alert("updated successfully");
    const index = events.value.findIndex(e => e.id === currentEvent.value.id);
    if (index !== -1) {
      events.value[index] = response.data; // Mettre à jour l'événement modifié
    }
    closeForm(); // Fermer le formulaire après l'édition
    
  } catch (error) {
    console.error("Failed to update the event:", error);
  }
};


onMounted(() => {
  fetchAvailabilities();
});
</script> 

<style scoped>
.calendar-container {
  display: flex;
  justify-content: center;
  gap: 60px;
  font-family: 'Lato';
  background-color: #eaac8be1 ;
  padding: 50px;
  padding-right: 90px;
  border-radius: 20px;
}
.calendar {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #ffffffec;
}
.calendar-header {
  display: flex;
  justify-content: space-between;
  width: 100%;
  max-width: 500px;
  padding-bottom: 30px;
  color: #ffffffec;
}
.availibilities{
  position: relative;
  left: 20px;
}
.YA{
   position: relative;
  right: 20px; 
  color: #ffffffec;
  overflow: hidden;
  letter-spacing: 2px;
}
.events{
  display: flex;
  gap: 30px;
  position: relative;
  bottom: 26px;
  font-size: 16px;
}
.event{
  color: #b03d00b1;
  cursor: pointer;
}
.highlighted {
    font-weight: bold;
    text-decoration: underline 2px solid;
}
.fleche{
  background-color: transparent;
  font-size: 20px;
  border: none;
  color: #fff;
}
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  row-gap: 5px;
  column-gap: 25px;
  max-width: 500px;
  width: 100%;
  overflow: hidden;
}
.day-header {
  font-weight: bold;
  text-align: center;
}
.day {
  padding: 10px;
  position: relative;
  min-height: 10px;
  cursor: pointer;
  text-align: center;
  border-style: none;
}
.highlight_underline {
  border-bottom: 3px solid #ceca90;
}
.AVS{
  color: #ffffffec; 
  position: relative; 
  right: 30px; 
  font-size: medium; 
  bottom: 50px; 
  background-color: #ffffff34; 
  list-style: none;
  border-radius: 15px; 
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 30px;
  padding: 0 8px 0 8px;
  margin-bottom: 5px;
}
.AV{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.today {
  background-color: transparent; 
  border: 3px solid #F4F3E6;  
  border-radius: 45%; 
  color: #fff; 
  font-weight: bold;
  text-align: center;
}
.addAV{
  position: relative;
  padding-top: 15px;
  font-size:x-large;
  color: rgba(255, 255, 255, 0.881);
  cursor: pointer;
}
.label{
  color: #ffffffec;
  font-size: 14px;
  
}
input[type="time"]::-webkit-calendar-picker-indicator {
  display: none;
}
input[type="time"]{
  width: 100px;
  margin: 5px 0; 
  border: none; 
  border-bottom: 1px solid #ffffffec; 
  background: transparent; 
  color: #ffffffec; 
  position: relative;
  left: 9px;
  outline: none; 
  appearance: none;
  -webkit-appearance: none; 
  -moz-appearance: textfield;
}
.AV-selector{
  width: 100px;
  margin: 10px 0; 
    border: none; 
    border-bottom: 1px solid #ffffffec; 
    background: transparent; 
    color: #ffffffec; 
    outline: none; 
}
.add-event-box {
  position: relative;
  bottom: 15px;
  padding: 8px;
  background-color: #ffffff29;
  height: 290px;
  border-radius: 20px; 
} 
@keyframes slideInFromLeft {
  0% {
    opacity: 0;
    transform: translateX(-50%);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}
@keyframes slideOutToLeft {
  0% {
    opacity: 1;
    transform: translateX(0);
  }
  100% {
    opacity: 0;
    transform: translateX(-100%);
  }
}
.add-event-box {
  animation: slideInFromLeft 1s ease-out;
}

.add-event-box.v-leave-active {
  animation: slideOutToLeft 1s ease-out;
}
.icon{
  font-size: 20px;
  position: relative;
  cursor: pointer;
}
.icon-container{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-top: 4px;
}
.Yaccount{
  position: relative;
      right: 160px;
      top: 35px;
      cursor: pointer;
      font-family: 'lato';
      color: #367298 ;
      font-size: 14px;
      margin: 0;
      padding: 0;
}
</style>


