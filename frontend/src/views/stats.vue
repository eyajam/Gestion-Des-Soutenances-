<template>
 <div class="content">
    <h3 class="AO">Analytic Overview &#9654;</h3>
      <div class="row">
        <div class="box"><strong>Filling Senior Project Form</strong>
          <div class="stat1"v-if="stats" >
          <div><i class="fi fi-ts-stats" style="font-size: 60px;"></i></div>
          <div style="font-size: 40px;position: relative; bottom: 10px;">{{ stats.filled_percentage.toFixed(1) }}%</div>
        </div>
        </div>
        <div class="box"><strong>End-Of-Studies Project in Pairs</strong>
          <div class="stat1" v-if="stats">
          <div><i class="fi fi-ts-stats" style="font-size: 60px;"></i></div>
          <div style="font-size: 40px;position: relative; bottom: 10px;">{{ stats.binomial_percentage.toFixed(1)}}%</div>
        </div>
        </div>
        <div class="box"><strong>Stud without End-Of-studies Project</strong>
          <div class="stat3" v-if="stats">
          <div><i class="fi fi-ts-stats" style="font-size: 60px;"></i></div>
          <div style="font-size: 40px;position: relative; bottom: 10px;">{{ stats.without_project_percentage.toFixed(1) }} %</div>
        </div>
        </div>
      </div>
      <div class="row2">
       <div class="four" style="background-color: #bbd6a9;"><strong>Deposit Check :</strong>
        <div class="deposit-group">
            <label class="deposit" for="deposit">Enter your xl file here :</label>
            <input type="file" id="deposit" @change="handleFileUpload">
            <span id="file-name" style="position: relative;bottom: 3px;color: #fff;font-size: 16px;font-weight: bold; ">{{ fileName }}
              <i @click="uploadFile" 
              class="fi fi-tr-file-upload" 
              style="margin-left:10px;position: relative; top: 2px; size: 20px; cursor: pointer;"></i>
            </span>
                <button v-if="downloadLink" @click="downloadTxtFile" class="download-deposit">Download File</button>
        </div>
      </div>
       <div class="four" style="background-color: #bbd6a9d8;"><strong>Number of stud for 1st and 2nd session</strong>
       <div class="session" v-if="stats">
        <div v-for="session in stats.sessions" :key="session.session" class="firstS" >
         <strong> {{ session.session }}</strong> Session: <strong> {{ session.total }} </strong>
        </div>
      </div>
       </div>
       <circularGraph />
      </div>
</div>
     
</template>
<script setup>
 import circularGraph from '../components/circularGraph.vue';
 import axios from 'axios';
 import { ref, onMounted } from 'vue';
 const file = ref(null);        // Stocke le fichier
 const fileName = ref('');
 const downloadLink = ref('');

 const authToken = localStorage.getItem('authToken');
 function  handleFileUpload(event) {
  const selectedFile = event.target.files[0];  // Récupère le fichier sélectionné
  if (selectedFile) {
    file.value = selectedFile;
    fileName.value = selectedFile.name; 
  };
 }
 const uploadFile = async () => {
  if (!file.value) {
    alert('Please select a file first.');
    return;
  }

  const formData = new FormData();
  formData.append('file', file.value);  // Ajoute le fichier au FormData

  try {
    const response = await axios.post('http://localhost:8000/api/upload', formData, {
      headers: {
         'Authorization': `Bearer ${authToken}`,
      },
      responseType: 'blob'
    });
    const url = window.URL.createObjectURL(new Blob([response.data])); 
    downloadLink.value = url;
    console.log(response.data);  // Traitez la réponse ici
  } catch (error) {
    console.error('File upload failed:', error);
  }
};
const downloadTxtFile = () => {

  const link = document.createElement('a');
  link.href = downloadLink.value;
  link.setAttribute('download', 'missing_reports.txt'); // Nom du fichier
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link); // Supprime le lien après le clic
};
  const stats = ref({
  filled_percentage: 0,
  binomial_percentage: 0,
  without_project_percentage: 0,
  with_supervisor_percentage: 0,
  without_supervisor_percentage: 0,
  sessions: []
    
  });

// Function to load statistics from the API
const loadStatistics = async () => {
  try {
    console.log("Before API call:", stats.value);
    const response = await axios.get('http://localhost:8000/api/statistics',
    {headers: { 'Authorization': `Bearer ${authToken}`}}
  );
    console.log("API Response:", response.data);
    stats.value = response.data;
    console.log("After setting stats:", stats.value);
  } catch (error) {
    console.error('There was an error fetching the statistics:', error);
  }
}

// Load statistics when the component mounts
onMounted( () => {
   loadStatistics(); // Ensure stats are loaded before any dependent logic runs
});
</script>
<style scoped>
.firstS{
  margin-bottom:10px ;
}
.session{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-top: 20px;
}
.stat1{
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 50px;
}
.stat3{
  margin-top: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 50px;
}
.AO{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex: 1;
}
.row {
  display: flex;
  gap: 70px;
}
.row2{
    margin-top: 40px;
    display: flex;
    gap:50px;
}
.box {
  width: 210px;
  height: 130px;
  border-radius: 15px;
  flex: 1;
  background-color: #bbd6a9d8;
  border: none;
  padding: 20px;
  text-align: center;
  color: rgb(255, 255, 255);
}
.four{
  height: 150px;
  width: 220px;
  border-radius: 15px;
  border: none;
  padding: 20px;
  text-align: center;
  color: rgb(255, 255, 255);
  
}
.deposit-group{
   display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.deposit{
  font-weight:500;
  text-decoration: underline; 
  border-radius: 20px;
  cursor: pointer;
}
.download-deposit{
  background-color: rgba(255, 255, 255, 0.687);
  border-radius: 10px;
  color: #618648;
  font-family: 'lato';
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  position: relative;
  top: 12px;
}
</style>