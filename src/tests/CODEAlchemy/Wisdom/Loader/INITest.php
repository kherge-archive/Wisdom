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

    use PHPUnit_Framework_TestCase;

    define('INI_TEST_CONSTANT', 20);

    class INITest extends PHPUnit_Framework_TestCase
    {
        public function testLoad()
        {
            file_put_contents($tmp = tempnam(sys_get_temp_dir(), 'wis'), <<<INPUT
[category]
constant = #INI_TEST_CONSTANT#
variable = %rand%
unchanged = "unchanged"
INPUT
            );

            $loader = new INI;

            $loader->setValues(array('rand' => $rand = rand()));

            $this->assertSame(
                array(
                    'category' => array(
                        'constant' => (string) INI_TEST_CONSTANT,
                        'variable' => "$rand",
                        'unchanged' => 'unchanged'
                    )
                ),
                $loader->load($tmp)
            );
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to read file:
         */
        public function testLoadReadFail()
        {
            $loader = new INI;

            @ $loader->load('/path/to/nowhere');
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to parse file:
         */
        public function testLoadParseFail()
        {
            file_put_contents($tmp = tempnam(sys_get_temp_dir(), 'wis'), <<<INPUT
[category
INPUT
            );

            $loader = new INI;

            @ $loader->load($tmp);
        }

        public function testSupports()
        {
            $loader = new INI;

            $this->assertTrue($loader->supports('test/file.ini'));
        }
    }