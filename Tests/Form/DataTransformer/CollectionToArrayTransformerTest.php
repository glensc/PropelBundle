<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\PropelBundle\Tests\Form\Form\DataTransformer;

use Propel\Runtime\Collection\ObjectCollection;
use Propel\PropelBundle\Tests\TestCase;
use Propel\PropelBundle\Form\DataTransformer\CollectionToArrayTransformer;

class CollectionToArrayTransformerTest extends TestCase
{
    private $transformer;

    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Form\Form')) {
            $this->markTestSkipped('The "Form" component is not available');
        }

        parent::setUp();

        $this->transformer = new CollectionToArrayTransformer();
    }

    public function testTransform()
    {
        $result = $this->transformer->transform(new ObjectCollection());

        $this->assertTrue(is_array($result));
        $this->assertEquals(0, count($result));
    }

    public function testTransformWithNull()
    {
        $result = $this->transformer->transform(null);

        $this->assertTrue(is_array($result));
        $this->assertEquals(0, count($result));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testTransformThrowsExceptionIfNotObjectCollection()
    {
        $this->transformer->transform(new DummyObject());
    }

    public function testTransformWithData()
    {
        $coll = new ObjectCollection();
        $coll->setData(array('foo', 'bar'));

        $result = $this->transformer->transform($coll);

        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));
        $this->assertEquals('foo', $result[0]);
        $this->assertEquals('bar', $result[1]);
    }

    public function testReverseTransformWithNull()
    {
        $result = $this->transformer->reverseTransform(null);

        $this->assertInstanceOf('\Propel\Runtime\Collection\ObjectCollection', $result);
        $this->assertEquals(0, count($result->getData()));
    }

    public function testReverseTransformWithEmptyString()
    {
        $result = $this->transformer->reverseTransform('');

        $this->assertInstanceOf('\Propel\Runtime\Collection\ObjectCollection', $result);
        $this->assertEquals(0, count($result->getData()));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testReverseTransformThrowsExceptionIfNotArray()
    {
        $this->transformer->reverseTransform(new DummyObject());
    }

    public function testReverseTransformWithData()
    {
        $inputData  = array('foo', 'bar');

        $result     = $this->transformer->reverseTransform($inputData);
        $data       = $result->getData();

        $this->assertInstanceOf('\Propel\Runtime\Collection\ObjectCollection', $result);

        $this->assertTrue(is_array($data));
        $this->assertEquals(2, count($data));
        $this->assertEquals('foo', $data[0]);
        $this->assertEquals('bar', $data[1]);
        $this->assertsame($inputData, $data);
    }
}

class DummyObject {}
