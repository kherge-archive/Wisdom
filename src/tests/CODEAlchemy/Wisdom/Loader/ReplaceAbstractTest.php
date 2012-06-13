<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom\Loader;

    use CODEAlchemy\Wisdom\Config\FileLocator,
        PHPUnit_Framework_TestCase;

    class ReplaceAbstractTest extends PHPUnit_Framework_TestCase
    {
        private $abstract;

        protected function setUp()
        {
            $this->abstract = new _ReplaceAbstract(new FileLocator);
        }

        public function testAncestry()
        {
            $this->assertInstanceOf(
                'Symfony\Component\Config\Loader\FileLoader',
                $this->abstract
            );

            $this->assertInstanceOf(
                'CODEAlchemy\Wisdom\Loader\ReplaceInterface',
                $this->abstract
            );
        }

        public function testReplacement()
        {
            $this->abstract->setReplacementValues(array(
                'alpha' => 'beta',
                'delta' => 'gamma'
            ));

            $data = <<<TEST
brokenTest:
    brokenPart1: %alpha
    brokenPart2: another%
    brokenPart3: #beta
    brokenPart4: antler#

workingTests:
    variable1: %alpha%
    variable2: %delta%
    variable3: %gamma%
    constant1: #PDO::ERRMODE_EXCEPTION#
    constant2: #LOCK_EX#
    constant3: #CONSTANT_SHOULD_NOT_BE_DEFINED#
TEST
            ;

            $a = 'beta';
            $b = 'gamma';
            $c = \PDO::ERRMODE_EXCEPTION;
            $d = LOCK_EX;
            $expected = <<<TEST
brokenTest:
    brokenPart1: %alpha
    brokenPart2: another%
    brokenPart3: #beta
    brokenPart4: antler#

workingTests:
    variable1: $a
    variable2: $b
    variable3: %gamma%
    constant1: $c
    constant2: $d
    constant3: #CONSTANT_SHOULD_NOT_BE_DEFINED#
TEST
            ;

            $this->assertEquals(
                $expected,
                $this->abstract->doReplacements($data)
            );
        }

        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage The $values argument is not an array or implements ArrayAccess.
         */
        public function testSetInvalid()
        {
            $this->abstract->setReplacementValues('test');
        }
    }

    class _ReplaceAbstract extends ReplaceAbstract
    {
        public function load($resource, $type = null)
        {
        }

        public function supports($resource, $type = null)
        {
        }
    }