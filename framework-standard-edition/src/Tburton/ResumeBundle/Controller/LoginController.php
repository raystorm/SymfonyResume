<?php

namespace Tburton\ResumeBundle\Controller;

use Fp\OpenIdBundle\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

/**
 *  Controller to display the custom Login page to display Urls for known
 *  OpenId Providers
 *
 *  User: tfburton
 */
class LoginController extends Controller
{
   public function loginAction()
   {
      $request = $this->container->get('request');
      /* @var $request \Symfony\Component\HttpFoundation\Request */
      $session = $request->getSession();
      /* @var $session \Symfony\Component\HttpFoundation\Session */

      // get the error if any (works with forward and redirect -- see below)
      if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
      { $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);}
      elseif (null !== $session
             && $session->has(SecurityContext::AUTHENTICATION_ERROR))
      {
         $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
         $session->remove(SecurityContext::AUTHENTICATION_ERROR);
      }
      else { $error = ''; }

      if ($error)
      { // TODO: this is a potential security risk
        //       (see http://trac.symfony-project.org/ticket/9523)
        $error = $error->getMessage();
      }
      //TODO: convert to "normal" template render method
      /*
      return $this->container->get('templating')
                  ->renderResponse('FpOpenIdBundle:Security:login.html.'
                                   .$this->container->getParameter('fp_openid.template.engine'),
                                   array('error' => $error));
      */
      return $this->render('ResumeBundle:Default:login.html.twig',
                           array('page_title' => 'Login', 'error' => $error));
   }

   public function logoutAction()
   {
      throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
   }
}