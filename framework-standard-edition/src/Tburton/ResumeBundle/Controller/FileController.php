<?php

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 *  Controller to handle the serving of the actual Resume files in the various formats
 *  
 *  @author tfburton
 */
class FileController extends Controller
{
  public function docxAction() { return $this->getFile('docx'); }

  public function htmlAction() { return $this->getFile('html'); }

  public function txtAction()  { return $this->getFile('txt');  }

  public function pdfAction()  { return $this->getFile('pdf');  }

  /** Generate a Nice name for the downloaded resumes */
  public function buildFileName()
  {
     $twig = $this->container->get('twig');
     $globals = $twig->getGlobals();
     return $globals['fullName'].'-Resume.';
  }

  /**
   * @return string with the relative path to where the Resume files are stored
   */
  private function getFolder()
  { return $this->container->getParameter('resume.FolderLocation'); }

  /**
   *  @param $ext - String file extension
   */
  public function getFile($ext)
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