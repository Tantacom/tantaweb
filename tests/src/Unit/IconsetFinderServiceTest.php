<?php
/**
 * Created by PhpStorm.
 * User: carlosrevillo
 * Date: 8/06/16
 * Time: 16:01
 */

/**
 * @file IconsetFinderServiceTest.php
 */

namespace Drupal\social_media_links {

  use Drupal\Core\Site\Settings;

  /**
   * Mock function.
   *
   * @return bool
   *   TRUE
   */
  function drupal_get_profile() {
    global $install_state;

    if (drupal_installation_attempted()) {
      // If the profile has been selected return it.
      if (isset($install_state['parameters']['profile'])) {
        $profile = $install_state['parameters']['profile'];
      }
      else {
        $profile = NULL;
      }
    }
    else {
      // Fall back to NULL, if there is no 'install_profile' setting.
      $profile = Settings::get('install_profile');
    }

    return $profile;
  }

  /**
   * Mock function.
   *
   * @return bool
   *   TRUE
   */
  function drupal_get_path() {
    return TRUE;
  }

  /**
   * Returns TRUE if a Drupal installation is currently being attempted.
   */
  function drupal_installation_attempted() {
    // This cannot rely on the MAINTENANCE_MODE constant, since that would prevent
    // tests from using the non-interactive installer, in which case Drupal
    // only happens to be installed within the same request, but subsequently
    // executed code does not involve the installer at all.
    // @see install_drupal()
    return isset($GLOBALS['install_state']) && empty($GLOBALS['install_state']['installation_finished']);
  }
}

namespace Drupal\Tests\tantaweb\Unit {

  use Drupal\Core\DependencyInjection\Container;
  use Drupal\Tests\UnitTestCase;

  /**
   * Class IconsetFinderServiceTest.
   * @package Drupal\social_media_links
   *
   * @coversDefault Drupal\social_media_links\IconsetFinderService
   * @group tantaweb
   */
  class IconsetFinderServiceTest extends UnitTestCase {

    protected $mock;

    protected $class;

    public function setUp() {
      $container = new Container();
      \Drupal::setContainer($container);

      $kernel_mock = $this->getMockBuilder('Drupal\Core\DrupalKernel')
        ->disableOriginalConstructor()
        ->getMock();
      $container->set('kernel', $kernel_mock);

      $this->mock = $this->getMockBuilder('Drupal\social_media_links\IconsetFinderService')
        ->getMock();
      $this->mock->__construct();
    }

    public function testProperties() {
      var_dump($this->mock->getIconsets());
    }
  }
}
