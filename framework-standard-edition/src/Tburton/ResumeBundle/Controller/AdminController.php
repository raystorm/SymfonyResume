<?php

namespace Tburton\ResumeBundle\Controller;

use Alchemy\Zippy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use ZendService\LiveDocx\MailMerge;
use ZendService\LiveDocx;
use Tburton\ResumeBundle\Document\ResumeDocument;
use Tburton\ResumeBundle\Repository\ResumeDocumentDAO;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 *  Controller to handle the Admin Page and resume file upload/conversion
 *
 *  @author tfburton
 */
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
        $log->debug("Resume upload completed.");
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

      return $this->render('ResumeBundle:Default:admin.html.twig',
                            array("page_title"  => "Tom Burton's Portfolio",
                                   "user"       => $user,
                                   "userDump"   => print_r($user, true),
                                   "form"       => $form->createView(),
                                   "attributes" => print_r($attributes, true),
                                   "result"     => $result));
   }

   /**
    * @return string with the relative path to where the Resume files are stored
    */
   private function getFolder()
   { return $this->container->getParameter('resume.FolderLocation'); }

   /**
    * processes the uploaded file
    *   - converts to all the formats
    *   - saves the file
    * @param UploadedFile $uploaded
    */
   private function handleFileUpload(UploadedFile $uploaded)
   {
      $log = $this->get('logger');

      if ( !$uploaded->isValid() )
      {
         $log->error('File Upload Failed');
         return;
      }

      $cont = $this->container;

      //ensure the tmp files do not exist
      $fs = new Filesystem();

      $this->cleanupTmpFiles();

      $log->debug('checking the file extension, found: '
                  .$uploaded->getExtension());
      $log->debug('uploaded file type(best guess):'.$uploaded->getMimeType());
      $log->debug('uploaded file size:'
                 .$this->makeBytesReadable($uploaded->getSize()));
      $uploaded->move($this->getFolder(), 'resume-tmp.zip');
      $log->debug('file moved to temporary work area,');

      $this->removeHeader();

      //rename the archive to .docx
      $fs->rename($this->getFolder() . '/resume-tmp.zip',
           		  $this->getFolder() . '/resume-tmp.docx');
      if ( $fs->exists($this->getFolder() . '/resume-tmp.docx') )
      { $log->debug("resume tmp file successfully created."); }

      //create the html & txt files //liveDocx
      $mailMerge = new MailMerge();

      //TODO: move this to a seperate "Non-Committed Settings file before I commit changes.
      //uses a free account from:
      // https://packages.zendframework.com/docs/latest/manual/en/modules/zendservice.livedocx.html
      $log->debug('connecting to Live DocX to do the heavy lifting for resume conversion');
      //$container->getParameter('mailer.transport');
      $mailMerge->setUsername($cont->getParameter('liveDocx.username'))
                ->setPassword($cont->getParameter('liveDocx.password'))
                ->setService(MailMerge::SERVICE_FREE);

      $mailMerge->setLocalTemplate($this->getFolder().'/resume-tmp.docx');

      $mailMerge->assign(null); // must be called as of phpLiveDocx 1.2

      $mailMerge->createDocument();

      $fs->copy($this->getFolder().'/resume-tmp.docx',
          	    $this->getFolder().'/resume.docx');
      
      $log->debug('generating the different file formats');
      
      $docx = file_get_contents($this->getFolder() . '/resume.docx');
      $html = $mailMerge->retrieveDocument('html');
      $text = $mailMerge->retrieveDocument('txt');
      $pdf  = $mailMerge->retrieveDocument('pdf');

      $fs->dumpFile($this->getFolder().'/resume.html', $html);
      $fs->dumpFile($this->getFolder().'/resume.txt',  $text);
      $fs->dumpFile($this->getFolder().'/resume.pdf',  $pdf);

      //verify file creation
      if ( $fs->exists($this->getFolder().'/resume.docx') )
      { $log->debug("resume docx file successfully created."); }
      if ( $fs->exists($this->getFolder().'/resume.html') )
      { $log->debug("resume html file successfully created."); }
      if ( $fs->exists($this->getFolder().'/resume.txt') )
      { $log->debug("resume txt file successfully created."); }
      if ( $fs->exists($this->getFolder().'/resume.pdf') )
      { $log->debug("resume pdf file successfully created."); }

      $this->cleanupTmpFiles();      
      
      //ResumeDocument Marshallers
      $dao     = $this->getDAO();
      $manager = $this->getDocumentManager();
      
      $dao->removeAllByExt('docx');
      $dao->removeAllByExt('html');
      $dao->removeAllByExt('txt');
      $dao->removeAllByExt('pdf');
      
      //persist (save) files to mongo
      $manager->persist(ResumeDocument::factory('docx', $docx));
      $manager->persist(ResumeDocument::factory('html', $html));
      $manager->persist(ResumeDocument::factory('txt',  $txt));
      $manager->persist(ResumeDocument::factory('pdf',  $pdf));
      $manager->flush();
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

      $archiver = new \ZipArchive();
      $archiver->open($this->getFolder().'/resume-tmp.zip');

      //TODO: modify the archive, remove the header file
      $log->debug('removing the header');
      if ( !$archiver->deleteName('word/header1.xml') )
      { //failure -- try emptying the file
        $archiver->addFromString('word/header1.xml','');
   	  }

      //TODO: figure out a good temp directory for extraction
      //$archiveZip->extractMembers('/word/document.xml', '/tmp/');

      //TODO: do I need to close the archive?
      $archiver->close();
   }

   /** @return DocumentManager */
   private function getDocumentManager()
   { return $this->get('doctrine_mongodb')->getManager(); }
   
   /**
    *  get the repostory (DAO) for getting ResumeDocument objects
    *  @return ResumeDocumentDAO
    */
   private function getDAO()
   {
      return $this->get('doctrine_mongodb')->getManager()
                   ->getRepository('ResumeBundle:ResumeDocument');
   }
    
}
