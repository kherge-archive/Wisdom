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

use RuntimeException;

/**
 * Offers support for loading JSON files.
 *
 * @author Kevin Herrera <kherrera@codealchemy.com>
 */
class JSON extends Loader
{
    /**
     * Returns the error message for the JSON error code.
     *
     * @param integer $code The JSON error code.
     *
     * @return string The JSON error message.
     */
    public function getMessage($code)
    {
        switch ($code)
        {
            case JSON_ERROR_CTRL_CHAR:
                return 'Control character error, possibly incorrectly encoded';
            case JSON_ERROR_DEPTH:
                return 'The maximum stack depth has been exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Invalid or malformed JSON';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
        }

        return "Unknown error code: $code";
    }

    /** {@inheritDoc} */
    public function load($resource, $type = null)
    {
        if (false === ($data = file_get_contents($resource))) {
            throw new RuntimeException("Unable to read file: $resource");
        }

        $data = json_decode($this->doReplace($data), true);

        if (JSON_ERROR_NONE !== ($code = json_last_error())) {
            throw new RuntimeException(sprintf(
                'Unable to parse file: %s',
                $this->getMessage($code)
            ));
        }

        return $data;
    }

    /** {@inheritDoc} */
    public function supports($resource, $type = null)
    {
        return ('json' == pathinfo($resource, PATHINFO_EXTENSION));
    }
}

