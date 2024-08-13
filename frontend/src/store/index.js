import { createStore } from "vuex";


const store = createStore({
   state : {
      // Le rôle de l'utilisateur, par exemple 'student', 'teacher', ou 'admin'
      role: null,
   },
   getters: {
      // Un getter pour vérifier si l'utilisateur est un étudiant ou un enseignant
      isStudentOrTeacher: (state) => {
         return state.role === 'student' || state.role === 'teacher';}
   },
   actions: {
      // Action pour définir le rôle de l'utilisateur
    setRole(role) {
      this.role = role;
    },
   },
   mutations: {},



})
export default store