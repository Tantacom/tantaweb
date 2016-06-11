<?php
/**
 * @file SocialMediaLinksIconsetManagerTest.php.
 */

namespace Drupal\Tests\tantaweb\Unit;

use Drupal\Core\DependencyInjection\Container;
use Drupal\Core\DrupalKernel;
use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetFinderService;
use Drupal\social_media_links\IconsetInterface;
use Drupal\social_media_links\SocialMediaLinksIconsetManager;
use Drupal\system\Tests\Routing\ExceptionHandlingTest;
use Drupal\Tests\UnitTestCase;
use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;

class SocialMediaLinksIconsetManagerTest extends UnitTestCase {

  protected $mock;

  protected $class;

  protected $reflectedClass;

  protected $namespacesMock;

  protected $cacheBackendMock;

  protected $moduleHandlerMock;

  public function setUp() {
    $container = new Container();
    \Drupal::setContainer($container);
    $kernel_mock = $this->getMockBuilder('Drupal\Core\DrupalKernel')
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('kernel', $kernel_mock);

    $iconset_finder_service_mock = $this->getMockBuilder('Drupal\social_media_links\IconsetFinderService')
      ->getMock();
    $container->set('social_media_links.finder',
      $iconset_finder_service_mock);

    $this->namespacesMock = $this->getMock('\Traversable');

    $this->cacheBackendMock = $this->getMock('\Drupal\Core\Cache\CacheBackendInterface');

    $this->moduleHandlerMock = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->mock = $this->getMockBuilder(
      'Drupal\social_media_links\SocialMediaLinksIconsetManager'
    )
      ->setConstructorArgs([
        $this->namespacesMock,
        $this->cacheBackendMock,
        $this->moduleHandlerMock
      ])
      ->setMethods(['getDefinitions', 'createInstance'])
      ->getMock();
  }

  public function testGetIconsets() {

    $pluginConfig = [
      'id' => 'a_plugin_id',
      'name' => 'a plugin id',
      'publisher' => 'the publisher',
      'class' => 'DummyThemes'
    ];

    $this->mock->expects($this->any())
      ->method('getDefinitions')
      ->willReturn(
        [
          'a_plugin_id' => $pluginConfig
        ]
      );

    $this->mock->expects($this->once())
      ->method('createInstance')
      ->with('a_plugin_id')
      ->willReturn(new DummyThemes(
        [],
        'a_plugin_id',
        $pluginConfig
      ));


    $iconsets = $this->mock->getIconsets();
    $this->assertTrue(is_array($iconsets));
    $this->assertArrayHasKey('a_plugin_id', $iconsets);
  }
}

/**
 * Provides 'dummy' iconset.
 *
 * @Iconset(
 *   id = "dummy",
 *   name = "Dummy Themes Icons",
 *   publisher = "Dummy Themes",
 *   publisherUrl = "http://www.dummy.com/",
 *   downloadUrl = "http://www.dummy.com/dl",
 * )
 */
class DummyThemes extends IconsetBase implements IconsetInterface {

  public function getStyle() {
    return array(
      '32' => '32x32',
    );
  }

  public function getIconPath($iconName, $style) {
    return $this->path . '/PNG/' . $iconName . '.png';
  }

}
