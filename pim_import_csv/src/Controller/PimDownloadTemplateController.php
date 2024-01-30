<?php

namespace Drupal\pim_import_csv\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class PIMImportCSVController.
 */
class PimDownloadTemplateController {

  /**
   * Download template file.
   */
  public function downloadTemplate() {
    // Create the response object for file download.
    $response = $this->createFileResponse('public://pim.csv', 'pim.csv');
    return $response;
  }

  /**
   * Helper method to create the file response.
   */
  private function createFileResponse($file_path, $file_name) {
    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $file_name . '"');
    $response->setContent(file_get_contents($file_path));

    return $response;
  }

}
