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

    class INITest extends PHPUnit_Framework_TestCase
    {
        private $loader;

        protected function setUp()
        {
            $this->loader = new INI (new FileLocator(sys_get_temp_dir()));
        }

        public function testLoad()
        {
            file_put_contents($file = tempnam(sys_get_temp_dir(), 'ini'), <<<INI
[category]
test = "My value."
another[] = "One"
another[] = "Two"
another[] = "Three"
INI
            );

            $this->assertSame(
                array(
                    'category' => array(
                        'test' => 'My value.',
                        'another' => array(
                            'One',
                            'Two',
                            'Three'
                        )
                    )
                ),
                $this->loader->load($file)
            );

            unlink($file);
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to parse file:
         */
        public function testLoadFail()
        {
            @ $this->loader->load('/fake/path');
        }

        public function testSupports()
        {
            $this->assertFalse(($this->loader->supports('test.php')));
            $this->assertTrue($this->loader->supports('test.ini'));
        }
    }