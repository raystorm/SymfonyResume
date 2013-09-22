<?php

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Alchemy\Zippy;

use ZendService\LiveDocx;
use ZendService\LiveDocx\MailMerge;

class AdminController extends Controller
{
   //TODO: move to a parameter
   //private $folder = '..\src\Tburton\ResumeBundle\Resources\views\Default\File';

   public function AdminAction(Request $request)
   {
      //$user = UserService::getCurrentUser();
      $user = "Error: Not Logged In!";
      //if ($user) { $name = $user->getNickname(); }


      $log = $this->get('logger');

      $form = $this->createFormBuilder()
                   ->add('resume', 'file')
                   ->add('Upload','submit')
                   ->getForm();

      $form->handleRequest($request);
      $result = "";
      if ($form->isValid() ) //handle file upload
      {
        $log->debug('handling the file upload.');

        $uploaded = $form['resume']->getData();

        $this->handleFileUpload($uploaded);
        //TODO: add a more regourous check // assume for now
        $result = "Resume upload successful.";
      }

      $attributes = array();
      /*
      $context = $request->getSession()->get(SecurityContext::LAST_USERNAME);
      if ( $context )
      {
        $token = $context->gettToken();
        if ($token) { $attributes=$token->getAttributes(); }
      }
      */

      $securityContext = $this->get('security.context');
      $user = $securityContext->getToken()->getUser();
      //$user - $this->getUser();
      $attributes = $securityContext->getToken()->getAttributes();

      return $this->render( 'ResumeBundle:Default:admin.html.twig',
                            array( "page_title" => "Tom Burton's Portfolio",
                                   "user"       => $user,
                                   "form"       => $form->createView(),
                                   "attributes" => $attributes,
                                   "result"     => $result));
   }

  /**
   * @return string with the relative path to where the Resume files are stored
   */
  private function getFolder()
   { return $this->container->getParameter('reaume.FolderLocation'); }

   private function handleFileUpload(UploadedFile $uploaded)
   {
      $log = $this->get('logger');

      if ( !$uploaded->isValid() )
      {
         $log->error('File Upload Failed');
         return;
      }

      $cont = $this->container;

      //ensure the  tmp files do not exist
      $fs = new Filesystem();

      $this->cleanupTmpFiles();

      $log->debug('checking the file extension, found: '.$uploaded->getExtension());
      $log->debug(' uploaded file type(best guess):'.$uploaded->getMimeType());
      //TODO: create routine to convert bytes to human readable.
      $log->debug( ' uploaded file size:'.$this->makeBytesReadable($uploaded->getSize()));

      //TODO move the file to a temporary area
      $uploaded->move($this->getFolder(),'resume-tmp.zip');

      $this->removeHeader();

      //rename the archive to .docx
      $fs->rename($this->getFolder().'/resume-tmp.zip',
          $this->getFolder().'/resume-tmp.docx');
      if ( $fs->exists($this->getFolder().'/resume-tmp.docx') )
      { $log->debug("file exists"); }

      //create the html & txt files  //liveDocx
      $mailMerge = new MailMerge();

      //TODO: move this to a seperate "Non-Committed Settings file before I commit changes.
      //TODO: sign up for a free account from:
      // https://packages.zendframework.com/docs/latest/manual/en/modules/zendservice.livedocx.html
      $log->debug('connecting to Live DocX to do the heavy lifting for resume conversion');
      //$container->getParameter('mailer.transport');
      $mailMerge->setUsername($cont->getParameter('liveDocx.username'))
                ->setPassword($cont->getParameter('liveDocx.password'))
                ->setService(MailMerge::SERVICE_FREE);

      $mailMerge->setLocalTemplate($this->getFolder().'/resume-tmp.docx');

      $mailMerge->assign(null);  // must be called as of phpLiveDocx 1.2

      $mailMerge->createDocument();

      $fs->copy($this->getFolder().'/resume-tmp.docx',
          $this->getFolder().'/resume.docx');

      $log->debug('generating the different file formats');
      $html = $mailMerge->retrieveDocument('html');
      $text = $mailMerge->retrieveDocument('txt');
      $pdf  = $mailMerge->retrieveDocument('pdf');

      //$fs->dumpFile('./File/resume.docx', //$archiveZip);
      $fs->dumpFile($this->getFolder().'/resume.html', $html);
      $fs->dumpFile($this->getFolder().'/resume.txt',  $text);
      $fs->dumpFile($this->getFolder().'/resume.pdf',  $pdf);

      $this->cleanupTmpFiles();
   }

   /**
    *  converts a given number in bytes into a more human friendly format with units.
    *  @param $bytes
    *  @return string
    */
   private function makeBytesReadable($bytes)
   {
      $count = 0;
      $readable = $bytes;
      while ( $readable > 1024 && 3 > $count )
      {
        $readable = $readable / 1024;
        ++$count;
      }

      $unit = ' ';
      switch($count)
      {
        case 0:
          $unit .= 'bytes';
          break;
        case 1:
          $unit .= 'KB';
          break;
        case 2:
          $unit .= 'MB';
          break;
        case 3:
          $unit .= 'GB';
          break;
      }

      return $readable . $unit;
   }

   /**
    * Cleanup temporary files used in the upload
    */
   private function cleanupTmpFiles()
   {
      $this->get('logger')->debug("cleaning up temp files.");

      $fs = new Filesystem();

      if ($fs->exists($this->getFolder().'/resume-tmp.zip'))
      { $fs->remove($this->getFolder().'/resume-tmp.zip'); }
      if ($fs->exists($this->getFolder().'/resume-tmp.docx'))
      { $fs->remove($this->getFolder().'/resume-tmp.docx'); }
   }

   /**
    * removes the header file from the word document
    */
   private function removeHeader()
  {
     $log = $this->get('logger');

     //TODO: unzip the file
     $log->debug('Opening the file as a .zip');

     $manager = new \ZipArchive();
     $manager->open($this->getFolder().'/resume-tmp.zip');

     //TODO: modify the archive
     //remove the header file
     $log->debug('removing the header');
     if ( !$manager->deleteName('word/header1.xml') )
     { //failure -- try emptying the file
       $manager->addFromString('word/header1.xml','');
     }

     //TODO: figure out a good temp directory for extraction
     //$archiveZip->extractMembers('/word/document.xml', '/tmp/');

     //TODO: do I need to close the archive?
     $manager->close();
  }

}
