<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class coachController extends Controller
{
    //
     //login
     public function login()
     {
         return view('auth.login');
     }
     public function register()
     {
         return view('auth.register');
     }
     public function registerEntrprise()
     {
         return view('auth.register-entreprise');
     }
     public function abonnements()
     {
         return view('auth.abonnements');
     }
     //forget password
     public function forgetPassword()
     {
         return view('auth.forget-password');
     }
     //reset password
     public function resetPassword()
     {
         return view('auth.reset');
     }
      //notifications
      public function notifications()
      {
          return view('coach.notification.index');
      }
     //dashboard
     public function dashboard()
     {
         return view('coach.dashboard');
     }
     //formation
     public function formation()
     {
         return view('coach.formation.index');
     }
     public function detailsFormation()
     {
         return view('coach.formation.details');
     }
     public function addFormation()
     {
         return view('coach.formation.add');
     }
     //ressources
     public function ressources()
     {
         return view('coach.ressources.index');
     }
     //forum
     public function forum()
     {
         return view('coach.forum.index');
     }
     //messagerie
     public function messagerie()
     {
         return view('coach.messagerie.index');
     }
     //calendrier
     public function calendrier()
     {
         return view('coach.calendrier.index');
     }
     //agentia
     public function agentia()
     {
         return view('coach.agent-ia.index');
     }
     public function addAgentia()
     {
         return view('coach.agent-ia.add');
     }
     public function detailsAgentia()
     {
         return view('coach.agent-ia.details');
     }
     //agent-ia-general
     public function agentIAgeneral()
     {
         return view('coach.agent-ia-general.index');
     }
     //setting
     public function setting()
     {
         return view('coach.setting.index');
     }
}
