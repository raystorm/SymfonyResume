<?php

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Tburton\ResumeBundle\Repository\ResumeDocumentDAO;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zend\Http\Header\ContentDisposition;
use MongoDBODMProxies\__CG__\Tburton\ResumeBundle\Document\ResumeDocument;

/**
 *  Controller to handle the serving of the actual Resume files in the various formats
 *  
 *  @author tfburton
 */
class FileController extends Controller
{
   
  public function docxAction() 
  { 
     $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
     return $this->getFile('docx', $type); 
  }

  public function htmlAction() { return $this->getFile('html', 'text/html'); }

  public function txtAction()  { return $this->getFile('txt', 'text/plain'); }

  public function pdfAction()  
  { return $this->getFile('pdf', 'application/pdf'); }

  /** Generate a Nice name for the downloaded resumes */
  public function buildFileName()
  {
     $twig = $this->container->get('twig');
     $globals = $twig->getGlobals();
     return $globals['fullName'].'-Resume.';
  }

  /**
   *  gets and serves the file from Mongo
   *  @param unknown $ext
   *  @param string $type content-type header
   */
  public function getFile($ext, $type)
  {
	 $dao = $this->getDAO();

     $resume = $dao->findByExt($ext);

     if ( !$resume ) { return $this->error($ext); }

     $response = new StreamedResponse();
     $d = $response->headers
                   ->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE,
                                     $this->buildFileName(). $ext);
     $response->headers->set('Content-Disposition', $d);
     $response->headers->set('Content-Type', $type);
     
     //$response->setContent($resume->getFile());
     
     //there has to be more elegant way to do this // brute force
     switch($ext)
     {
        case 'docx';
          $response->setCallback(function()
                                 {
                                    echo $this->getDAO()->findByExt('docx')
                                               ->getFile();
                                    flush();
                                 });
          break;

        case 'html':
          $response->setCallback(function()
                                 {
                                    echo $this->getDAO()->findByExt('html')
                                               ->getFile();
                                    flush();
                                 });
          break;

        case 'txt':
          $response->setCallback(function()
                                 {
                                    echo $this->getDAO()->findByExt('txt')
                                              ->getFile();
                                    flush();
                                 });
          break;

        case 'pdf':
          $response->setCallback(function()
                                 {
                                    echo $this->getDAO()->findByExt('pdf')
                                              ->getFile();
                                    flush();
                                 });
          break;

        default:
          return $this->error($ext);
     }
     
     $response->send();
  }

  /**
   *  get the repostory (DAO) for getting ResumeDocument objects
   *  @return ResumeDocumentDAO
   */
  private function getDAO()
  {
     return $this->get('doctrine_mongodb')->getManager()
                  ->getRepository('ResumeBundle:ResumeDocument');
  }

  /**
   *  @return string with the relative path to where the Resume files are stored
   */
  private function getFolder()
  { return $this->container->getParameter('resume.FolderLocation'); }

  /**
   * gets and serves the file from the FileSystem
   *
   * @param $ext -
   *         String file extension
   */
  public function getFSFile($ext)
  {
  	 $log = $this->get('logger');
     try
     {
     	$log->debug('Building resume file.');
     	$fileName = $this->getFolder().'\resume.'.$ext;
        //$resume = new BinaryFileResponse(new \SplFileInfo($this->getFolder().'\resume.'.$ext));
     	//$fs = new Filesystem();
     	
     	$fs = new Filesystem();
     	$exists = $fs->exists($fileName) ? 'true' : 'false';
     	$log->info("Filesystem reports: " . $exists );
     	$file = new File($fileName, false);
     	
     	if ( !$file->isFile() ) { $log->error("file not found!"); }     		
     	
     	$resume = new BinaryFileResponse($file->getPathname());
        //TODO: verify if we need to set file type headers
        /* 
        $d = $resume->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE,
                                               $this->buildFileName().$ext);
        $resume->headers->set('Content-Disposition', $d); e$log
        */
        $resume->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE,
                                       $this->buildFileName().$ext);
        $log->debug('Built file name header.');
        return $resume;
     }
     catch (FileNotFoundException $e) 
     { 
     	$log->error("Error opening/serving file: " . $e->getFile());
     	$log->error($e->getMessage());
     	$log->error($e);
     	return $this->error($ext); 
     }
  }

  public function error($ext)
  { return new Response('Error: ' . $ext . ' Resume File Not Found!', 404); }

}