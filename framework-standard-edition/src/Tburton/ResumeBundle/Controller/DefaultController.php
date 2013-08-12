<?php

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ResumeBundle:Default:index.html.twig',
                             array( "page_title" => "Tom Burton's Portfolio") );
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
