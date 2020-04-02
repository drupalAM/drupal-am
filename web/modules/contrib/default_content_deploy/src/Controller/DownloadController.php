<?php

namespace Drupal\default_content_deploy\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\default_content_deploy\DeployManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Returns responses for config module routes.
 */
class DownloadController implements ContainerInjectionInterface {

  /**
   * The DCD manager.
   *
   * @var \Drupal\default_content_deploy\DeployManager
   */
  protected $deployManager;

  /**
   * DownloadController constructor.
   *
   * @param \Drupal\default_content_deploy\DeployManager $deploy_manager
   *   The DCD manager.
   */
  public function __construct(DeployManager $deploy_manager) {
    $this->deployManager = $deploy_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('default_content_deploy.manager')
    );
  }

  /**
   * Return binary archive file for download.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function downloadCompressedContent() {
    $this->deployManager->compressContent();
    $path = file_directory_temp() . '/dcd/content.tar.gz';

    $headers = [
      'Content-Type' => 'application/tar+gzip',
      'Content-Description' => 'File Download',
      'Content-Disposition' => 'attachment; filename=content.tar.gz'
    ];

    return new BinaryFileResponse($path, 200, $headers);
  }

}
