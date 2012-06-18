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

    define('JSON_TEST_CONSTANT', 20);

    class JSONTest extends PHPUnit_Framework_TestCase
    {
        public function testGetMessage()
        {
            $loader = new JSON;

            $this->assertEquals('Control character error, possibly incorrectly encoded', $loader->getMessage(JSON_ERROR_CTRL_CHAR));
            $this->assertEquals('The maximum stack depth has been exceeded', $loader->getMessage(JSON_ERROR_DEPTH));
            $this->assertEquals('Invalid or malformed JSON', $loader->getMessage(JSON_ERROR_STATE_MISMATCH));
            $this->assertEquals('Syntax error', $loader->getMessage(JSON_ERROR_SYNTAX));
            $this->assertEquals('Malformed UTF-8 characters, possibly incorrectly encoded', $loader->getMessage(JSON_ERROR_UTF8));
            $this->assertEquals('Unknown error code: 123', $loader->getMessage(123));
        }

        public function testLoad()
        {
            file_put_contents($tmp = tempnam(sys_get_temp_dir(), 'wis'), <<<INPUT
{"category":{"constant": #INI_TEST_CONSTANT#, "variable": %rand%, "unchanged": "unchanged"}}
INPUT
            );

            $loader = new JSON;

            $loader->setValues(array('rand' => $rand = rand()));

            $this->assertSame(
                array(
                    'category' => array(
                        'constant' => INI_TEST_CONSTANT,
                        'variable' => $rand,
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
            $loader = new JSON;

            @ $loader->load('/path/to/nowhere');
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to parse file:
         */
        public function testLoadParseFail()
        {
            file_put_contents($tmp = tempnam(sys_get_temp_dir(), 'wis'), '{');

            $loader = new JSON;

            @ $loader->load($tmp);
        }

        public function testSupports()
        {
            $loader = new JSON;

            $this->assertTrue($loader->supports('test/file.json'));
        }
    }