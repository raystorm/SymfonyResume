<?php

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 *  Controller used when special actions aren't required
 *
 *  @author tfburton
 */
class DefaultController extends Controller
{
    //TODO: add template actions

    public function indexAction()
    {
       $name = "Not Logged In";

       $securityContext = $this->get('security.context');
       $token = $securityContext->getToken();

       if ( $token && $securityContext->isGranted('IS_AUTHENTICATED_OPENID') )
       { $name = $token->getUser()->getUserName(); }

       return $this->render('ResumeBundle:Default:index.html.twig',
                             array("page_title"  => "Tom Burton's Portfolio",
                                    "userName"    => $token->getUser(),
                                    "welcomeName" => $name));
    }

    public function aboutAction()
    {
       return $this->render('ResumeBundle:Default:about.html.twig',
                             array("page_title" => "About") );
    }

    public function contactAction()
    {
       return $this->render('ResumeBundle:Default:contact.html.twig',
                             array("page_title" => "Contact") );
    }

    public function dump($var) { return print_r($var, true); }
}
