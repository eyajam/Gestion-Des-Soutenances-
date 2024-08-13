<!-- src/components/CircularChart.vue -->
<template>
    <div class="chart-container">
      <canvas id="circularChart" width="200" height="200"></canvas>
      <div class="indicator">
      <p style="color:#ffa462ca; font-size: small;">&#9632; without supervisor</p>
      <p style="color:#96ADD6e6; font-size: small;">&#9632; with supervisor</p></div>
      <div v-if="!chartReady">Loading...</div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'CircularChart',
    data() {
      return {
        chartReady: false,
        chartData: {
          labels: [],
          values: []
        },
        fixedColors: ['#96ADD6e6', '#ffa462ca'] 
      };
    },
    mounted() {
      this.fetchData();
    },
    methods: {
      fetchData() {
        // Simuler un appel API ici. Remplacer par votre appel API réel.
        const response = {
          data: {
            labels: ['with', 'without'],
            values: [10, 20]
          }
        };
  
        console.log('API Response:', response);
  
        if (response.data && response.data.labels && response.data.values) {
          this.chartData.labels = response.data.labels;
          this.chartData.values = response.data.values;
          this.drawChart();
          this.chartReady = true;
        } else {
          console.error("La réponse de l'API n'a pas le format attendu.");
        }
      },
      drawChart() {
        const canvas = document.getElementById('circularChart');
        const ctx = canvas.getContext('2d');
  
        const total = this.chartData.values.reduce((acc, value) => acc + value, 0);
        let startAngle = 0;
  
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = Math.min(centerX, centerY);
  
        this.chartData.values.forEach((value, index) => {
          const sliceAngle = (value / total) * 2 * Math.PI;
          const color = this.fixedColors[index % this.fixedColors.length];
  
          ctx.beginPath();
          ctx.moveTo(centerX, centerY);
          ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
          ctx.closePath();
          ctx.fillStyle = color;
          ctx.fill();
  
          // Add labels
          const labelX = centerX + (radius / 2) * Math.cos(startAngle + sliceAngle / 2);
          const labelY = centerY + (radius / 2) * Math.sin(startAngle + sliceAngle / 2);
          ctx.fillStyle = "#fff";
          ctx.font = "14px lato";
          ctx.fillText(this.chartData.labels[index], labelX, labelY);
  
          startAngle += sliceAngle;
        });
      },
      getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
          color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
      }
    }
  };
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
  