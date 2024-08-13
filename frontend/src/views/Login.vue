<template>
    <NavBar />
    <section>
  <div class="outer-container">
    <div class="container">
      <div class="form1-container">
        <LoginForm />
      </div>
      <div class="form2-container">
          <SignUpForm /> 
      </div>  
    </div>
  </div>
</section>
  <new_footer style="bottom: -280px;" /> 
</template>

<style scoped>

.outer-container{
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding-top: 60px; 
}
.container {
    display: flex; 
    justify-content: center;
    align-items: center; 
  }
  .form1-container {
  position: absolute;
  left: 300px; 
  z-index: 2;
}
.form2-container {
  position: absolute;
  right: 300px;
  z-index: 1;
}
</style>

  <script setup >
  import LoginForm from '../components/LoginForm.vue';
  import SignUpForm from '../components/SignUpForm.vue';
  import NavBar from '../components/NavBar.vue';
  import new_footer from '../components/new_footer.vue';
  
  </script>
  
  
  