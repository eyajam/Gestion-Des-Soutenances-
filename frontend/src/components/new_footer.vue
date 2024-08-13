<template>
    <footer class="footer">
  <div class="footer-content">
    <div><img src="/images/ISG.png" alt="ISG Logo" class="footer-logo-section"></div>
      <div class="footer-contact">
        <h4 style="color: #eaac8b;">Contact us</h4>
        <div class="decorative-line"></div>
          <ul>
            <li>41, Rue de la Liberté, Cité Bouchoucha<br> 2000 Le Bardo,Tunis -TUNISIE</li>
            <li>direction.isg@isg.rnu.tn</li>
            <li>Université de Tunis</li>
            <li>+216 71 588 553</li>
          </ul>
        </div>
      <div class="footer-social">
         <h4 style="color: #eaac8b;">Join us</h4>
         <div class="decorative-line"></div>
         <div class="social-links">
          <a href="https://www.facebook.com/isgtunis.officiel/"><i class="fi fi-brands-facebook"></i></a>
          <a href="https://www.linkedin.com/school/institutsup%C3%A9rieurdegestiondetunis/?originalSubdomain=fr"><i class="fi fi-brands-linkedin"></i></a>
          <a href="mailto:Direction.isg@isg.rnu.tn"><i class="fi fi-sr-envelope"></i></a>
        </div>
      </div>
  </div>
</footer>

</template>
<script setup>

</script>
<style scoped>
.footer {
  width: 100vw;
  background-color: #355070;
  padding: 20px 0;
  position:absolute ;
  left: 0; 
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
} 

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  width: 100%;
  max-width: 1200px;
  padding: 0 10px;
  font-family:'Lato' ;
}

.footer-logo-section {
  display: flex;
  position: relative;
  height: 120px;
  top: 70px;
  left: 65px;
}

.footer-contact {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  color: white;
}

.footer-contact .footer-social h4 {
  margin-bottom: 10px;
}
.decorative-line {
  width: 50px;
  height: 3px;
  background-color: #ffffff;
  margin: 0;
}

.footer-contact ul {
  list-style: none;
  padding: 0;
}

.footer-contact li {
  margin-bottom: 5px;
}

.footer-social {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  color: white;
  position: relative;
  right: 100px;
}

.footer-social .social-links {
  display: flex;
  gap: 50px;
  margin-top: 20px;
  
}

.footer-social a {
  color: white;
  font-size: 24px;
  display: inline-flex;
  align-items: center;
  margin: 0;
  padding: 0;
}
</style>