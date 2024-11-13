<!-- src/components/CircularChart.vue -->
<template>
    <div class="chart-container">
      <div v-if="!chartReady">Loading...</div>
      <canvas id="circularChart" width="200" height="200"></canvas>
      <div class="indicator">
      <p style="color:#e9d69c; font-size: small;">&#9632; without supervisor</p>
      <p style="color:#96ADD6e6; font-size: small;">&#9632; with supervisor</p></div>
      
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
import axios from 'axios';
 const authToken = localStorage.getItem('authToken');
// Refs pour les données du graphique et le statut de chargement
const chartReady = ref(false);
const chartData = ref({
  labels: ['', ''],
  values: []
});
const fixedColors = ['#96ADD6e6', '#ffebaeca']; // Couleurs fixes pour chaque segment

// Fonction pour dessiner le graphique
const drawChart = () => {
  const canvas = document.getElementById('circularChart');
  const ctx = canvas.getContext('2d');

  // Réinitialiser le canvas avant chaque rendu pour éviter les chevauchements
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  const total = chartData.value.values.reduce((acc, value) => acc + value, 0);
  let startAngle = 0;

  const centerX = canvas.width / 2;
  const centerY = canvas.height / 2;
  const radius = Math.min(centerX, centerY);

  chartData.value.values.forEach((value, index) => {
    const sliceAngle = (value / total) * 2 * Math.PI;
    const color = fixedColors[index % fixedColors.length];

    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
    ctx.closePath();
    ctx.fillStyle = color;
    ctx.fill();

    // Ajouter les labels sur chaque segment du graphique
    const labelX = centerX + (radius / 2) * Math.cos(startAngle + sliceAngle / 2);
    const labelY = centerY + (radius / 2) * Math.sin(startAngle + sliceAngle / 2);
    ctx.fillStyle = "#fff";
    ctx.font = "14px lato";
    ctx.fillText(chartData.value.labels[index], labelX, labelY);

    startAngle += sliceAngle;
  });
};

// Fonction pour récupérer les données via l'API
const fetchData = async () => {
  try {
    // Appel de l'API pour récupérer les données de la base de données
    const response = await axios.get('http://localhost:8000/api/statistics',
    {headers: {'Authorization': `Bearer ${authToken}`}});
    const data = response.data;

    // Remplir les données pour le graphique
    chartData.value.values = [data.with_supervisor_percentage, data.without_supervisor_percentage];
    drawChart();
    chartReady.value = true;
  } catch (error) {
    console.error('Erreur lors de la récupération des statistiques :', error);
  }
};

// Charger les données lorsque le composant est monté
onMounted(() => {
  fetchData();
});
  </script>
  
  <style scoped>
  .chart-container {
    position: relative;
    width: 330px;
    height: 200px;
    margin: 0 auto;
    display: flex;    
    align-items: center;
    justify-content: center;     
    gap: 20px;
  }
  
  
  </style>
  