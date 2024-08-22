import {createRouter, createWebHistory} from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Login from "../views/Login.vue";
import SignUp from "../views/SignUp.vue";
import StudentDashboard from "../views/StudentDashboard.vue";
import '@flaticon/flaticon-uicons/css/all/all.css';
import ProjectForm from "../views/ProjectForm.vue";
import complaint from "../views/complaint.vue";
import EditProjectForm from "../views/EditProjectForm.vue";
import TeacherDashboard from "../views/TeacherDashboard.vue";
import TeacherCalendar from "../views/TeacherCalendar.vue";
import teacherComplaint from "../views/teacherComplaint.vue";
import complaintT from "../views/complaintT.vue";
import teacherPlanning from "../views/teacherPlanning.vue";
import forgetPassword from "../views/forgetPassword.vue";
import stats from "../views/stats.vue";
import complaints from "../views/complaints.vue";
import users from "../views/users.vue";
import superviseAssign from "../views/superviseAssign.vue";
const routes = [
   {
      path: '/',
      component: Dashboard,
      redirect: '/stats',
      children: [
        {
          path: 'stats',
          name: 'stats',
          component: stats
        },
         {
          path: 'complaints',
          name: 'complaints',
          component: complaints
        },
        {
         path: 'users',
         name: 'users',
         component: users
       },
        {
          path: 'SuperviseAssign',
          name: 'superviseAssign',
          component: superviseAssign
        },
       /* {
          path: 'generate-planning',
          name: 'GeneratePlanning',
          component: () => import('../views/GeneratePlanning.vue') // Si vous avez un autre composant pour cela
        } */
      ]
   },  
{
   path: '/login',
   name: 'login',
   component: Login
},
{
   path: '/SignUp/:userType',
   component: SignUp ,
   props: true ,
   name: 'SignUp'
},
{
   path: '/StudentDashboard/',
   component: StudentDashboard ,
   name: 'StudentDashboard',
},
{
path: '/TeacherDashboard/',
component: TeacherDashboard ,
name: 'TeacherDashboard',

},
{
   path: '/ProjectForm/',
   component: ProjectForm ,
   name: 'ProjectForm'
},
{
   path: '/complaint/',
   component: complaint ,
   name: 'complaint'
},
{
   path: '/EditProjectForm/',
   component: EditProjectForm ,
   name: 'EditProjectForm'
},
{
   path: '/TeacherCalendar/',
   component: TeacherCalendar ,
   name: 'TeacherCalendar'
},
{
   path: '/teacherComplaint/',
   component: teacherComplaint,
   name: 'teacherComplaint'
},
{
   path: '/complaintT/:username/:specialty',
   name: 'complaintT',
   component: complaintT,
   props: true
 },
 {
   path: '/teacherPlanning/',
   component: teacherPlanning,
   name: 'teacherPlanning'
},
{
   path: '/forgetPassword/',
   component: forgetPassword,
   name: 'forgetPassword'
},

];

const router = createRouter({
   history: createWebHistory(),
   routes

})
router.beforeEach((to, from, next) => {
   const authToken = localStorage.getItem('authToken');
   const userRole = localStorage.getItem('userRole');
   if (to.name === 'StudentDashboard') {
      if (!authToken || userRole !== 'student') {
        next({ name: 'login' });
      } else {
        next();
      }
    } else if (to.name === 'TeacherDashboard') {
      if (!authToken || userRole !== 'teacher') {
        next({ name: 'login' });
      } else {
        next();
      }
    } else {
      next();
    }
 }); 

export default router