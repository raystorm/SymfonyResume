<?php
/**
 * Controller to display and handle login across the pages
 *
 * User: tfburton
 *
 */

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NavBarController extends Controller
{
   public function NavBarAction(Request $request, $page_title)
   {
     $log = $this->get('logger');
     $form = $this->createFormBuilder()->getForm();

//     $form->handleRequest($request);
//     $log->debug('about to check the login form!');
//     //if ($form->isValid() )
//     if ( $form->isSubmitted() )
//     { //TODO: is there a way to get the login page address programatically
//       $log->info("redirecting to the Login page.");
//       return $this->redirect('/login_openid');
//     }

     $securityContext = $this->get('security.context');
     $user = $securityContext->getToken()->getUser();

     if ( !$user ) { $user = true; }

     return $this->render('ResumeBundle:Default:navBar.html.twig',
                          array("form" => $form->createView(),
                                "user" => $user, //UserService::getCurrentUser(),
                                "page_title" => $page_title));
   }
}