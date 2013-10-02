<?php

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    //TODO: add template actions

    public function indexAction()
    {
       $name = "Not Logged In";

       $securityContext = $this->get('security.context');
       $user = $securityContext->getToken()->getUser();
       if ( $user ) { $name = $user; }

       return $this->render( 'ResumeBundle:Default:index.html.twig',
                             array( "page_title" => "Tom Burton's Portfolio",
                                    "userName"   => $name));
    }

    public function aboutAction()
    {
       return $this->render('ResumeBundle:Default:about.html.twig',
                            array( "page_title" => "About") );
    }

    public function contactAction()
    {
       return $this->render('ResumeBundle:Default:contact.html.twig',
                            array( "page_title" => "Contact") );
    }
}
