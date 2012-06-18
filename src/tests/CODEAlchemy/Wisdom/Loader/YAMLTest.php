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
        Symfony\Component\Config\FileLocator,
        Symfony\Component\Yaml\Yaml as _Yaml;

    class YAMLTest extends PHPUnit_Framework_TestCase
    {
        private $loader;

        protected function setUp()
        {
            $this->loader = new YAML;

            $this->loader->setFileLocator(new FileLocator(sys_get_temp_dir()));
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
                $file = tempnam(sys_get_temp_dir(), 'yaml'),
                _Yaml::dump($data)
            );

            $this->assertSame($data, $this->loader->load($file));

            unlink($file);
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to read file:
         */
        public function testLoadReadFail()
        {
            @ $this->loader->load('/fake/file');
        }

        /**
         * @expectedException Symfony\Component\Yaml\Exception\ParseException
         */
        public function testLoadFail()
        {
            file_put_contents(
                $file = tempnam(sys_get_temp_dir(), 'yaml'),
                "\t"
            );

            $this->loader->load($file);
        }

        public function testSupports()
        {
            $this->assertFalse(($this->loader->supports('test.php')));
            $this->assertTrue($this->loader->supports('test.yml'));
        }
    }