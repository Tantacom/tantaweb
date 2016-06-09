<?php
/**
 * @file PlatformBaseTest.php.
 */

namespace Drupal\social_media_links;

use Drupal\Core\DependencyInjection\Container;
use Drupal\Core\Utility\UnroutedUrlAssembler;
use Drupal\Tests\UnitTestCase;

class PlatformBaseTest extends UnitTestCase {

  protected $class;

  /**
   * Setup the test.
   */
  public function setUp() {
    $this->class = new PlatformBase(
      [],
      'platform_base_plugin_id',
      [
        'id' => 'the plugin definition id',
        'iconName' => 'icon name',
        'name' => 'platform base plugin definition name',
        'urlPrefix' => 'http://',
        'urlSuffix' => 'a url suffix',
      ]
    );
  }

  /**
   * Test for the getValue method.
   */
  public function testValueWillBeEscaped() {
    $this->class->setValue("a dummy' non esca'ped string");
    $this->assertEquals("a dummy&#039; non esca&#039;ped string", $this->class->getValue());

    $this->class->setValue("<a href='test'>Test</a>");
    $this->assertEquals("&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;", $this->class->getValue());
  }

  /**
   * Tests for the getIconName method.
   */
  public function testGetIconNameShouldReturnIconNameIfDefined() {
    $this->assertEquals('icon name', $this->class->getIconName());

    $another_instance = new PlatformBase(
      [],
      'platform_base_plugin_id',
      [
        'id' => 'the plugin definition id',
        'name' => 'platform base plugin definition name',
        'urlPrefix' => 'http://',
        'urlSuffix' => 'a url suffix',
      ]
    );

    $this->assertEquals('the plugin definition id', $another_instance->getIconName());
  }

  /**
   * Tests for the getName, getUrlPrefix and getUrlSuffix class.
   */
  public function testGetterMethodsShouldReturnExpectedValues() {
    $this->assertEquals('platform base plugin definition name', $this->class->getName());
    $this->assertEquals('http://', $this->class->getUrlPrefix());
    $this->assertEquals('a url suffix', $this->class->getUrlSuffix());
  }

  /**
   * Test for the getUrl method.
   */
  public function testGetUrlWillReturnAnUrl() {
    $container = new Container();
    \Drupal::setContainer($container);

    $unrouted_url_assembler = $this->getMockBuilder('Drupal\Core\Utility\UnroutedUrlAssembler')
      ->disableOriginalConstructor()
      ->setMethods(array('assemble'))
      ->getMock();

    $container->set('unrouted_url_assembler', $unrouted_url_assembler);

    $this->assertInstanceOf('Drupal\Core\Url', $this->class->getUrl());
  }

  /**
   * Test for the generateUrl method.
   */
  public function testGenerateUrl() {
    $container = new Container();
    \Drupal::setContainer($container);

    $unrouted_url_assembler = $this->getMockBuilder('Drupal\Core\Utility\UnroutedUrlAssembler')
      ->disableOriginalConstructor()
      ->setMethods(array('assemble'))
      ->getMock();

    $unrouted_url_assembler->expects($this->once())
      ->method('assemble')
      ->willReturn('http://a url suffix');

    $container->set('unrouted_url_assembler', $unrouted_url_assembler);

    $generated_url = $this->class->generateUrl($this->class->getUrl());

    $this->assertTrue(is_string($generated_url));
    $this->assertEquals('http://a url suffix', $generated_url);
  }
}
