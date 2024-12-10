<template>
    <div class="planning-container">
      <!-- Fixed Top Section -->
      <h2 style="letter-spacing:1px;">Generate Planning</h2>
      <div class="header">
        <div class="dropdown1"> 
          <label for="numberOfRooms"
           style="color: #355070; font-weight: 600; font-family: 'lato'; margin-right: 10px;letter-spacing:1px;">
           Number of Classrooms:</label>
          <select v-model="classrooms" id="classrooms">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="BIB1">BIB1</option>
          <option value="BIB2">BIB2</option>
          <option value="BIB3">BIB3</option>
          </select>
        </div>
       <div class="dropdown2">
        <label 
        style="color: #355070; font-weight: 600; font-family: 'lato'; margin-right: 10px;letter-spacing:1px;"
        >Block:</label>
        <select v-model="block" id="block">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="A">C</option>
          <option value="B">D</option>
        </select>
       </div>
      </div>
  
      <!-- Middle Section with Dropdown -->
    <div class="teacher-list">
      <table class="teachers">
        <tbody>
          <tr v-for="teacher in teachers" :key="teacher.user_id">
            <td class="teacher">
              <div>
                <div>{{ teacher.first_name }} {{ teacher.last_name }}</div>
                <div style="color: #69acc2;">{{ teacher.grade }}</div>
              </div>
            </td>
            <td style="background-color: white; border-radius: 20px;"> 
            <div class="role">

            <div style="display: inline-block; margin-right: 20px;">
              <div style="position: relative; bottom: 35px;margin-right: 30px;">
                <input type="checkbox" v-model="teacher.isPresident">Président</div>
              <div style=" display: ruby;position: relative;bottom: 30px;left: 10px;">
               <label for="president" style="color: #213547; margin-top: 0px;margin-right: 5px;"
                >Defenses:</label>
               <select v-model.number="teacher.DP" style="margin-right: 10px;">
                <option v-for="option in options" :key="option.value" :value="parseInt(option.value)">
                  {{ option.label }}
                </option>
               </select>
              </div></div>

              <div style="display: inline-block; margin-right: 20px;">
              <div style="position: relative; bottom: 35px;margin-right: 20px;">
                <input type="checkbox" v-model="teacher.isRapporteur">Rapporteur</div>
              <div style=" display: ruby;position: relative;bottom: 30px;left: 10px; ">
               <label for="rapporteur" style="color: #213547; margin-top: 0px;margin-right: 5px;"
                >Defenses:</label>
               <select v-model.number="teacher.DR" style="margin-right: 10px;">
                <option v-for="option in options" :key="option.value" :value="parseInt(option.value)">
                  {{ option.label }}
                </option>
               </select>
              </div></div>
  
              <div style=" display: ruby;position: relative;bottom: 30px;left: 10px; ">
               <select v-model="teacher.planningType" id="S" style="margin-right: 10px;">
                  <option value="successive">successive</option>
                  <option value="unsuccessive">unsuccessive</option>
               </select>
              </div>
              
            </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
      <!-- Bottom Section with Buttons -->
      
      <div class="button-section"> 
        <button class="btns" style="background-color: #ff6c1fca;color: white;"
         @click="generateAndDownloadFile('student')">Generate Student Planning</button>
        <button class="btns" style="background-color: #76b3d0ca;color: white;" 
        @click="generateAndDownloadFile('teacher')" >Generate Teacher Planning</button>
        <button class="btns" style="background-color: #c6da81;color: white;" 
        @click="generateAndDownloadFile('admin')">Generate Admin Planning</button>
      </div>
      <button class="old-plan" style="background-color: red; color: white;"@click="deleteSchedule()">
        <i class="fi fi-sr-trash" style="position: relative; top: 2px; margin-right: 5px;"></i>
         Old planning</button>
    </div>
  </template>
  
  <script setup>
  import { ref,onMounted } from 'vue';
  import axios from 'axios';
  
  const authToken = localStorage.getItem('authToken');
  const classrooms = ref(0);
  const block = ref('A');
  const options = [
  { value: '1', label: '1' },{ value: '1', label: '2' },{ value: '3', label: '3' },
  { value: '4', label: '4' },{ value: '5', label: '5' },{ value: '6', label: '6' },
  { value: '7', label: '7' }, { value: '8', label: '8' },{ value: '9', label: '9' },{ value: '10', label: '10' },
  { value: '11', label: '11' },{ value: '12', label: '12' },{ value: '13', label: '13' },{ value: '14', label: '14' },
  { value: '15', label: '15' },{ value: '16', label: '16' },{ value: '17', label: '17' },{ value: '18', label: '18' },
  { value: '19', label: '19' },{ value: '20', label: '20' }
];
  const teachers = ref([]);
  const fetchTeachers = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/teachers',
    {headers: { 'Authorization': `Bearer ${authToken}` },});
    teachers.value = response.data.map(teacher => ({ ...teacher, planningType: 'unsuccessive', DP: 0,DR: 0, 
      isPresident: false, 
      isRapporteur: false
     }
      ));
  } catch (error) {
    console.error('Error fetching teachers:', error);
  }
};

const sendTeachersToBackend = async () => {
  console.log("Sending teachers:", teachers.value);
  try {
    const response=await axios.post('http://localhost:8000/api/teachersPlanningType', 
    { teachers: teachers.value }, 
    { headers: { 'Authorization': `Bearer ${authToken}` } });
    console.log("Response from server:", response.data);
    return response;
  } catch (error) {
    console.error('Error sending teachers:', error);
  }
};


onMounted(() => {
  fetchTeachers();
});
  // Methods
  async function generatePlanning() {
    try {
      // Send teachers data to the backend
    const teachers = await sendTeachersToBackend();
      const response = await axios.post('http://localhost:8000/api/generatePlanning', {
        classrooms: classrooms.value ,
        block: block.value,
        rolesAndDefencesTeachers: teachers.data,
      },{ headers: { 'Authorization': `Bearer ${authToken}`}});
      const schedule = response.data.schedule;
      // Store the schedule in localStorage
      localStorage.setItem('schedule', JSON.stringify(schedule));
    } catch (error) {
      console.error("Error generating schedule:", error);
    }
}


/*   const generatePlanning = async (type) => {
  try {
    const teachers=await sendTeachersToBackend();
    const isExcel = type === 'admin';
    const response = await axios.post('http://localhost:8000/api/generatePDF', {
      classrooms: classrooms.value,
      block: block.value,
      rolesAndDefencesTeachers : teachers.data,
      type: type,
    }, 
    { headers: { 'Authorization': `Bearer ${authToken}`,
    'Accept': isExcel ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf'},
      responseType: 'blob'}
    );
    const fileType = isExcel ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';
    const url = window.URL.createObjectURL(new Blob([response.data], { type: fileType }));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 
    type === 'student' ? 'schedule_students.pdf' : 
    type === 'teacher' ? 'schedule_teachers.pdf' : 
    'schedule_admin.xlsx' // Default to 'admin' if the type is 'admin'
);  // Name the file
    document.body.appendChild(link);
    link.click();  // Programmatically trigger the download

    console.log("planning generated and downloaded:", response.data);
  } catch (error) {
    console.error("Error generating planning:", error);
  }
}; */
const generateFile = async (type,schedule) => { 
  try {
    if (!schedule) {
        console.error("No schedule found in localStorage. Please generate the schedule first.");
        return;
    }
    // Determine if Excel or PDF
    const isExcel = type === 'admin';
    
    // Make the request to generate and download the file
    const response = await axios.post('http://localhost:8000/api/generateFile', {
      schedule: schedule,
      type: type,
    }, 
    { 
      headers: { 
        'Authorization': `Bearer ${authToken}`,
        'Accept': isExcel ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf'
      },
      responseType: 'blob' // Important for file download
    });
    
    // Set file type for Blob
    const fileType = isExcel ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';
    
    // Create blob and URL for download
    const url = window.URL.createObjectURL(new Blob([response.data], { type: fileType }));
    const link = document.createElement('a');
    link.href = url;
    
    // Assign file name based on type
    link.setAttribute('download', 
      type === 'student' ? 'schedule_students.pdf' : 
      type === 'teacher' ? 'schedule_teachers.pdf' : 
      'schedule_admin.xlsx' // Default to 'admin' if the type is 'admin'
    );
    
    // Append the link, trigger download, and remove the link
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link); // Clean up

    console.log("Planning generated and downloaded:", response.data);
  } catch (error) {
    console.error("Error generating planning:", error);
  }
};
function generateAndDownloadFile(type) {
    // Check if a schedule is already in localStorage
    const schedule = JSON.parse(localStorage.getItem('schedule'));
    
    if (schedule) {
        // If a schedule exists, directly call generateFile with the existing schedule
        generateFile(type, schedule);
    } else {
        // If no schedule exists, generate a new one and store it in localStorage
        generatePlanning()
            .then(() => {
                const newSchedule = JSON.parse(localStorage.getItem('schedule'));
                if (newSchedule) {
                    generateFile(type, newSchedule);
                } else {
                    console.error("Failed to retrieve the newly generated schedule.");
                }
            })
            .catch(error => {
                console.error("Error in generating and downloading file:", error);
            });
    }
}
async function deleteDefenses() {
  if (confirm("Are you sure you want to delete all defenses? This action cannot be undone.")) {
    try {
      const response = await axios.delete('http://localhost:8000/api/defenses',
        { headers: { 
          'Authorization': `Bearer ${authToken}`,}}
      );
      alert(response.data.message); // Show success message
    } catch (error) {
      console.error(error);
      alert("Failed to delete defenses. Please try again.");
    }
  }}
function deleteSchedule() {
  localStorage.removeItem('schedule');
  deleteDefenses(); 
}


  
  /* const generateTeacherPlanning = async(type) => {
    try {
    const teachers=await sendTeachersToBackend();
    
    const response = await axios.post('http://localhost:8000/api/generate-student-planning', {
      classrooms: classrooms.value,
      block: block.value,
      rolesAndDefencesTeachers : teachers.data,
      type: type,
    }, 
    { headers: { 'Authorization': `Bearer ${authToken}`,'Accept': 'application/pdf' },
      responseType: 'blob'}
    );
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', type === 'student' ? 'schedule_students.pdf' : 'schedule_teachers.pdf');  // Name the file
    document.body.appendChild(link);
    link.click();  // Programmatically trigger the download

    console.log("Student planning generated and downloaded:", response.data);
  } catch (error) {
    console.error("Error generating student planning:", error);
  }
  };
  
  const generateAdminPlanning = async(type) => {
    try {
    const teachers=await sendTeachersToBackend();
    
    const response = await axios.post('http://localhost:8000/api/generate-student-planning', {
      classrooms: classrooms.value,
      block: block.value,
      rolesAndDefencesTeachers : teachers.data,
      type: type,
    }, 
    { headers: { 'Authorization': `Bearer ${authToken}`,'Accept': 'application/pdf' },
      responseType: 'blob'}
    );
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', type === 'student' ? 'schedule_students.pdf' : 'schedule_teachers.pdf');  // Name the file
    document.body.appendChild(link);
    link.click();  // Programmatically trigger the download

    console.log("Student planning generated and downloaded:", response.data);
  } catch (error) {
    console.error("Error generating student planning:", error);
  }
  }; */
  </script>
  
  <style scoped>
  .old-plan{
    position: relative;
    left: 400px;
    bottom: 460px;
    padding: 6px;
  }
  .planning-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
  }
  
  .header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    gap: 50px;
  }
  .dropdown1{
    width: 100%;
    display: ruby;
  }
  .dropdown2{
    width: 100%;
    display: ruby;
    
  }
  .teacher-list {
    max-height: 300px;
    overflow-y: auto;
    width: 100%;
  }
  .teachers{
    margin-bottom: 20px;
    width: 100%;
    border-collapse: separate;
    border-spacing: 10px 10px ;
  }
   .teacher{  
    background-color: white;
    border-radius: 20px;
    
  }

  .role{
    display: flex;
    justify-content: center;
    padding-top: 20px; 
    align-items: center;
    margin-top: 50px;
  }

  
  .button-section {
    display: flex;
    gap: 40px;
    width: 100%;
    position: relative;
    top: 28px;
  }
  
  .btns {
    padding: 10px 20px;
    cursor: pointer;
  }
  </style>
  