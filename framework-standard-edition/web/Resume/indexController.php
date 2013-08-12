<?php
/**
 * Controller file for displaying the .twig pages
 */

namespace TBurton\ResumeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class indexController
{

   public function indexAction()
   {
      $content = $this->renderView('Resume:index.html.twig',
                                   array( "page_title" => "Tom Burton's Portfolio",
                                          "fullName"   => "Tom Burton" ) );

      return new Response($content);
   }

}