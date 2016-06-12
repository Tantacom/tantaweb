<?php
/**
 * @file SocialMediaLinksIconsetManagerTest.php.
 */

namespace Drupal\Tests\tantaweb\Unit;

use Drupal\Core\DependencyInjection\Container;
use Drupal\Tests\UnitTestCase;

/**
 * Class SocialMediaLinksIconsetManagerTest.
 *
 * @package Drupal\Tests\tantaweb\Unit
 * @coversDefaultClass Drupal\social_media_links\SocialMediaLinksIconsetManager
 * @group tantaweb
 */
class SocialMediaLinksIconsetManagerTest extends UnitTestCase {

  protected $mock;

  protected $namespacesMock;

  protected $cacheBackendMock;

  protected $moduleHandlerMock;

  protected $definitions;

  /**
   * Setup the test.
   */
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
      ->setMethods(['getDefinitions', 'createInstance', 'getIconsets'])
      ->getMock();

    $iconset_mock = $this->getMockBuilder('Drupal\social_media_links\IconsetInterface');

    $this->definitions = [
      'a_plugin_id' => [
        'id' => 'a_plugin_id',
        'name' => 'a plugin id',
        'publisher' => 'the publisher',
        'class' => get_class($iconset_mock)
      ],
      'another_plugin_id' => [
        'id' => 'another_plugin_id',
        'name' => 'another plugin id',
        'publisher' => 'the other publisher',
        'class' => get_class($iconset_mock)
      ]
    ];
  }

  /**
   * Test for the getIconsets method.
   */
  public function testGetIconsets() {
    $mock = $this->getMockBuilder(
      'Drupal\social_media_links\SocialMediaLinksIconsetManager'
    )
      ->setConstructorArgs([
        $this->namespacesMock,
        $this->cacheBackendMock,
        $this->moduleHandlerMock
      ])
      ->setMethods(['getDefinitions', 'createInstance'])
      ->getMock();

    $iconset_mock1 = $this->getMockBuilder('Drupal\social_media_links\IconsetInterface')
      ->setConstructorArgs([
        [],
        'a_plugin_id',
        $this->definitions['a_plugin_id']
      ])
      ->getMock();

    $iconset_mock2 = $this->getMockBuilder('Drupal\social_media_links\IconsetInterface')
      ->setConstructorArgs([
        [],
        'another_plugin_id',
        $this->definitions['another_plugin_id']
      ])
      ->getMock();

    $mock->expects($this->once())
      ->method('getDefinitions')
      ->willReturn($this->definitions);

    $mock->expects($this->exactly(2))
      ->method('createInstance')
      ->withConsecutive(['a_plugin_id'], ['another_plugin_id'])
      ->willReturnonConsecutiveCalls($iconset_mock1, $iconset_mock2);

    $iconsets = $mock->getIconsets();
    $this->assertTrue(is_array($iconsets));

    foreach ($iconsets as $id => $iconset) {
      $this->assertArrayHasKey($id, $iconsets);
      $this->assertInstanceOf(
        'Drupal\social_media_links\IconsetInterface',
        $iconset['instance']
      );
    }
  }

  /**
   * Test for the getStyles method.
   */
  public function testGetStyles() {
    $iconsets = $this->definitions;

    $instances = [
      'a_plugin_id' => $this->getMockBuilder('Drupal\social_media_links\IconsetInterface')
        ->setConstructorArgs([
          [],
          'a_plugin_id',
          $this->definitions['a_plugin_id']
        ])
        ->setMethods(['getPath', 'getStyle'])
        ->getMockForAbstractClass(),
      'another_plugin_id' => $this->getMockBuilder('Drupal\social_media_links\IconsetInterface')
        ->setConstructorArgs([
          [],
          'another_plugin_id',
          $this->definitions['another_plugin_id']
        ])
        ->setMethods(['getPath', 'getStyle'])
        ->getMockForAbstractClass()
    ];
    foreach ($this->definitions as $plugin_id => $definition) {
      $iconsets[$plugin_id]['instance'] = $instances[$plugin_id];
    }

    $this->mock->expects($this->any())
      ->method('getIconsets')
      ->willReturn($iconsets);

    foreach ($iconsets as $plugin_id => $iconset) {
      $iconset['instance']->expects($this->once())
        ->method('getPath')
        ->willReturn($plugin_id . '_path');

      $iconset['instance']->expects($this->once())
        ->method('getStyle')
        ->willReturn([
          'key1' => 'style1',
          'key2' => 'style2',
        ]);
    }

    $styles = $this->mock->getStyles();

    $this->assertTrue(is_array($styles));
    $this->assertArrayHasKey('a_plugin_id', $styles);
    $this->assertArrayHasKey('another_plugin_id', $styles);

    foreach($styles as $plugin_id => $style) {
      $this->assertTrue(is_array($style));
      $this->assertArrayHasKey($plugin_id . ':key1', $styles[$plugin_id]);
      $this->assertArrayHasKey($plugin_id . ':key2', $styles[$plugin_id]);
      $this->assertEquals('style1', $styles[$plugin_id][$plugin_id.':key1']);
      $this->assertEquals('style2', $styles[$plugin_id][$plugin_id.':key2']);
    }
  }
}
