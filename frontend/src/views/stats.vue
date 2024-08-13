<template>
 <div class="content">
    <h3 class="AO">Analytic Overview &#9654;</h3>
      <div class="row">
        <div class="box"><strong>Filling Senior Project Form</strong>
          <div class="stat1">
          <div><i class="fi fi-ts-stats" style="font-size: 60px;"></i></div>
          <div style="font-size: 40px;position: relative; bottom: 10px;">60%</div>
        </div>
        </div>
        <div class="box"><strong>End-Of-Studies Project in Pairs</strong>
          <div class="stat1">
          <div><i class="fi fi-ts-stats" style="font-size: 60px;"></i></div>
          <div style="font-size: 40px;position: relative; bottom: 10px;">60%</div>
        </div>
        </div>
        <div class="box"><strong>Stud without End-Of-studies Project</strong>
          <div class="stat3">
          <div><i class="fi fi-ts-stats" style="font-size: 60px;"></i></div>
          <div style="font-size: 40px;position: relative; bottom: 10px;">60%</div>
        </div>
        </div>
      </div>
      <div class="row2">
       <div class="four" style="background-color: #bbd6a9;"><strong>Deposit Check :</strong>
        <div class="deposit-group">
            <label class="deposit" for="deposit">Enter your xl file here<i class="fi fi-tr-file-upload" style="margin-left:10px;position: relative; top: 2px;"></i></label>
            <input type="file" id="deposit" @change="handleFileUpload">
            <span id="file-name" style="position: relative;bottom: 12px;color: #585858;"></span>
        </div >
      </div>
       <div class="four" style="background-color: #bbd6a9d8;"><strong>Number of stud for 1st and 2nd session</strong>
        <div class="session">
          <div class="firstS">1st session :</div>
          <div>2nd session :</div>
        </div>
       </div>
       <circularGraph />
      </div>
</div>
     
</template>
<script setup>
 import circularGraph from '../components/circularGraph.vue';
 function  handleFileUpload(event) {
    const fileInput = event.target;
    const fileName = fileInput.files[0] ? fileInput.files[0].name : '';
    document.getElementById('file-name').textContent = fileName;
  };
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
  gap: 60px;
}
.stat3{
  margin-top: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 60px;
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
</style>