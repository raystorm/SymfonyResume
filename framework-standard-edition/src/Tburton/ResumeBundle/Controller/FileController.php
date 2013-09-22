<?php
/**
 *  Controller to handle the serving of the actual Resume files in the various formats
 *
 *  User: tfburton
 */

namespace Tburton\ResumeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController extends Controller
{
  public function docxAction() { return $this->getFile('docx'); }

  public function htmlAction() { return $this->getFile('html'); }

  public function txtAction()  { return $this->getFile('txt'); }

  public function pdfAction()  { return $this->getFile('pdf'); }

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
  { return $this->container->getParameter('reaume.FolderLocation'); }

  /**
   *  @param $ext - String file extension
   */
  public function getFile($ext)
  {
     try
     {
        $resume = new BinaryFileResponse(new \SplFileInfo($this->getFolder().'\resume.'.$ext));
        //TODO: verify if we need to set file type headers
        $d = $resume->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE,
                                               $this->buildFileName().$ext);
        $resume->headers->set('Content-Disposition', $d);
        return $resume;
     }
     catch (FileNotFoundException $e) { return $this->error(); }
  }

  public function error()
  { return new Response('Error:  Resume File Not Found!', 404); }

}