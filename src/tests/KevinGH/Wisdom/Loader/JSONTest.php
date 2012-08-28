<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom\Loader;

use PHPUnit_Framework_TestCase;

define('JSON_TEST_CONSTANT', 20);

class JSONTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMessages
     */
    public function testGetMessage($message, $code)
    {
        $loader = new JSON;

        $this->assertEquals($message, $loader->getMessage($code));
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

    public function getMessages()
    {
        return array(
            array('Control character error, possibly incorrectly encoded', JSON_ERROR_CTRL_CHAR),
            array('The maximum stack depth has been exceeded', JSON_ERROR_DEPTH),
            array('Invalid or malformed JSON', JSON_ERROR_STATE_MISMATCH),
            array('Syntax error', JSON_ERROR_SYNTAX),
            array('Malformed UTF-8 characters, possibly incorrectly encoded', JSON_ERROR_UTF8),
            array('Unknown error code: 123', 123)
        );
    }
}

