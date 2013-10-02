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

     $securityContext = $this->get('security.context');
     $user = $securityContext->getToken()->getUser();

     return $this->render('ResumeBundle:Default:navBar.html.twig',
                          array( "user" => $user, //UserService::getCurrentUser(),
                                 "page_title" => $page_title ));
   }
}