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

    use PHPUnit_Framework_TestCase,
        Symfony\Component\Config\FileLocator;

    class JSONTest extends PHPUnit_Framework_TestCase
    {
        private $loader;

        protected function setUp()
        {
            $this->loader = new JSON;

            $this->loader->setFileLocator(new FileLocator(sys_get_temp_dir()));
        }

        public function testGetMessage()
        {
            $this->assertEquals('Control character error, possibly incorrectly encoded', $this->loader->getMessage(JSON_ERROR_CTRL_CHAR));
            $this->assertEquals('The maximum stack depth has been exceeded', $this->loader->getMessage(JSON_ERROR_DEPTH));
            $this->assertEquals('Invalid or malformed JSON', $this->loader->getMessage(JSON_ERROR_STATE_MISMATCH));
            $this->assertEquals('Syntax error', $this->loader->getMessage(JSON_ERROR_SYNTAX));
            $this->assertEquals('Malformed UTF-8 characters, possibly incorrectly encoded', $this->loader->getMessage(JSON_ERROR_UTF8));
            $this->assertEquals('Unknown error code: 123', $this->loader->getMessage(123));
        }

        public function testLoad()
        {
            $data = array(
                'category' => array(
                    'test' => 'My value.',
                    'another' => array(
                        'One',
                        'Two',
                        'Three'
                    )
                ),
                'rand' => rand()
            );

            file_put_contents(
                $file = tempnam(sys_get_temp_dir(), 'js'),
                utf8_encode(json_encode($data))
            );

            $this->assertSame($data, $this->loader->load($file));

            unlink($file);
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to read file:
         */
        public function testLoadFail()
        {
            @ $this->loader->load('/fake/path');
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Syntax error
         */
        public function testLoadJSONFail()
        {
            file_put_contents(
                $file = tempnam(sys_get_temp_dir(), 'js'),
                '!'
            );

            $this->loader->load($file);
        }

        public function testSupports()
        {
            $this->assertFalse(($this->loader->supports('test.php')));
            $this->assertTrue($this->loader->supports('test.json'));
        }
    }