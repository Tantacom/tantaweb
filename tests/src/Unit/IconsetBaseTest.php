<?php

/**
 * @file IconsetBaseTest.php.
 */

namespace Drupal\Tests\tantaweb\Unit;

use Drupal\Core\DependencyInjection\Container;
use Drupal\social_media_links\IconsetBase;
use Drupal\Tests\UnitTestCase;

include_once __DIR__ . '/mock_namespace.php';

/**
 * Test the IconsetBase Class.
 *
 * @coversDefaultClass Drupal\social_media_links\IconsetBase
 * @covers Drupal\social_media_links\IconsetBase
 * @group tantaweb
 */
class IconsetBaseTest extends UnitTestCase {

  protected $mock;

  protected $iconBaseConfiguration = array();

  protected $iconBasePluginId = '';

  protected $iconBasePluginDefinition = array(
    'name' => 'a_name',
    'publisher' => 'a_publisher',
    'publisherUrl' => 'a_publisher_url',
    'downloadUrl' => 'a_download_url',
  );

  /**
   * Setup the test.
   */
  protected function setUp() {
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

    $iconset_finder_service_mock->expects($this->once())
      ->method('getPath')
      ->with($this->iconBasePluginId)
      ->willReturn('a_icon_path');

    // Get mock, without the constructor being called.
    $this->mock = $this->getMockBuilder('Drupal\social_media_links\IconsetBase')
      ->setConstructorArgs(array(
        $this->iconBaseConfiguration,
        $this->iconBasePluginId,
        $this->iconBasePluginDefinition,
      ))
      ->getMockForAbstractClass();
  }

  /**
   * Test for the get Name method.
   * @covers Drupal\social_media_links\IconsetBase::getName
   */
  public function testGetName() {
    $this->assertEquals('a_name', $this->mock->getName());
  }

  /**
   * Test for the getPublisher method.
   * @covers Drupal\social_media_links\IconsetBase::getPublisher
   */
  public function testGetPublisher() {
    $this->assertEquals('a_publisher', $this->mock->getPublisher());
  }

  /**
   * Test for the getPublisherUrl method.
   */
  public function testGetPublisherUrl() {
    $this->assertEquals('a_publisher_url', $this->mock->getPublisherUrl());
  }

  /**
   * Test for the getDownloadUrl method.
   */
  public function testGDownloadUrl() {
    $this->assertEquals('a_download_url', $this->mock->getDownloadUrl());
  }

  /**
   * Test for the getLibrary method.
   */
  public function testGetLibraryShouldReturnNull() {
    $this->assertNull($this->mock->getLibrary());
  }

  /**
   * Test for the getPath method.
   */
  public function testGetPath() {
    $this->assertEquals('a_icon_path', $this->mock->getPath());
  }

  /**
   * Test for the getIconElement method.
   */
  public function testGetIconElementShouldReturnAnArrayWithKeys() {

    $platform = $this->getMockBuilder('Drupal\social_media_links\PlatformBase')
      ->setConstructorArgs([[], 'platform', []])
      ->getMock();

    $platform->expects($this->once())
      ->method('getIconName')
      ->willReturn('a_icon_name');

    $this->mock->expects(
      $this->once()
    )
      ->method('getIconPath')
      ->willReturn('a_icon_path');

    $icon_element = $this->mock->getIconElement($platform, 'a_style');
    $this->assertArrayHasKey('#theme', $icon_element);
    $this->assertArrayHasKey('#uri', $icon_element);

    $this->assertEquals('image', $icon_element['#theme']);
    $this->assertEquals('a_icon_path', $icon_element['#uri']);
  }

  /**
   * Test for the explodeStyle method.
   */
  public function testExplodeStyle() {
    $style = "part_1:part_2";

    $exploded_style = $this->mock->explodeStyle($style);

    $this->assertArrayHasKey('iconset', $exploded_style);
    $this->assertArrayHasKey('style', $exploded_style);

    $this->assertEquals($exploded_style['iconset'], 'part_1');
    $this->assertEquals($exploded_style['style'], 'part_2');

    $style = 'part_1';

    $exploded_style = $this->mock->explodeStyle($style);

    $this->assertArrayHasKey('iconset', $exploded_style);
    $this->assertArrayHasKey('style', $exploded_style);
    $this->assertEquals($exploded_style['iconset'], 'part_1');
    $this->assertEmpty($exploded_style['style']);

    $style = ':part_2';

    $exploded_style = $this->mock->explodeStyle($style);

    $this->assertArrayHasKey('iconset', $exploded_style);
    $this->assertArrayHasKey('style', $exploded_style);
    $this->assertEquals($exploded_style['style'], 'part_2');
    $this->assertEmpty($exploded_style['iconset']);
  }
}
