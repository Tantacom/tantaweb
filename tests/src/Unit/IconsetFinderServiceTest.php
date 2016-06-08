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

namespace Drupal\Tests\tantaweb\Unit;

use Drupal\Core\DependencyInjection\Container;
use Drupal\social_media_links\IconsetFinderService;
use Drupal\Tests\UnitTestCase;

include_once __DIR__ . '/mock_namespace.php';

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

    $this->class = new IconsetFinderService();
  }

  /**
   * Test constructor calls setters.
   */
  public function testConstructorShouldSetDirsAndIconsSets() {
    $classname = 'Drupal\social_media_links\IconsetFinderService';

    $mock = $this->getMockBuilder($classname)
      ->setMethods(['setSearchDirs', 'setIconsets'])
      ->getMock();

    $mock->expects($this->once())
      ->method('setSearchDirs');

    $mock->expects($this->once())
      ->method('setIconsets');

    $reflectedClass = new \ReflectionClass($classname);
    $constructor = $reflectedClass->getConstructor();
    $constructor->invoke($mock, 4);
  }

  /**
   * Tests if properties are set by checking if they are arrays.
   *
   * @todo fix when social_media_links fix bug with getIconsets.
   */
  public function testPropertiesAreSet() {
    $this->assertTrue(is_array($this->class->getSearchDirs()));
    $this->assertNull($this->class->getIconsets());
  }

  public function testGetPath() {
    $reflection = new \ReflectionClass($this->class);
    $reflection_property = $reflection->getProperty('iconsets');
    $reflection_property->setAccessible(TRUE);

    $reflection_property->setValue($this->class,
      array('iconset_id' => 'iconset_1'));

    $this->assertEquals('iconset_1', $this->class->getPath('iconset_id'));
    $this->assertNull($this->class->getPath('non_existent_iconset'));
  }
}

