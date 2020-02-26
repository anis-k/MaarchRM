<?php

class JsonTokenizer
{

    const CONTEXT_OBJECT = 1;
    const CONTEXT_ARRAY  = 2;

    const TOKEN_OBJECT_START   = 1;
    const TOKEN_OBJECT_END     = 2;
    const TOKEN_ARRAY_START    = 4;
    const TOKEN_ARRAY_END      = 8;
    const TOKEN_SCALAR         = 16;
    const TOKEN_KEY            = 32;
    const TOKEN_ITEM_SEPARATOR = 64;

    const EXPECTED_ANY         = 127;
    const EXPECTED_ARRAY_ITEM  = 29;  // Object Start, Array Start, Scalar, Array End
    const EXPECTED_OBJECT_ITEM = 23;  // Object Start, Array Start, Scalar, Object End
    const EXPECTED_SEPARATOR   = 64;
    const EXPECTED_KEY         = 32;
    const EXPECTED_ARRAY_END   = 8;
    const EXPECTED_OBJECT_END  = 2;

    /** @var resource */
    protected $stream;
    protected $threshold;

    /** @var array */
    protected $context  = array();
    protected $expected;

    /** @var array */
    protected $token = array();

    /** @var array */
    protected $buffered = array();

    /**
     * @param  resource                  $stream
     * @throws \InvalidArgumentException
     */
    public function __construct($stream, $threshold = PHP_INT_MAX)
    {
        if (!is_resource($stream) || get_resource_type($stream) != 'stream') {
            throw new \Exception("Argument is not a stream");
        }

        $this->stream = $stream;
        $this->threshold = $threshold;
        $this->expected = self::EXPECTED_ANY;
    }

    /**
     * @return array
     * @throws ReadingError
     */
    public function next()
    {
        $this->token = $this->fetch();

        if (!$this->token['token']) {
            return false;
        }

        if (!($this->token['token'] & $this->expected)) {
            throw new \Exception(sprintf("Read unexpected token %s/%s", $this->token['token'], $this->expected));
        }

        switch ($this->token['token']) {
            case self::TOKEN_ARRAY_START:
                $this->context[] = self::CONTEXT_ARRAY;
                $this->expected  = self::EXPECTED_ARRAY_ITEM;
                break;
            case self::TOKEN_OBJECT_START:
                $this->context[] = self::CONTEXT_OBJECT;
                $this->expected  = self::EXPECTED_OBJECT_ITEM;
                break;
            case self::TOKEN_OBJECT_END:
            case self::TOKEN_ARRAY_END:
                array_pop($this->context);
            // no break;
            case self::TOKEN_SCALAR:
                if ($this->context()) {
                    $this->expected  = self::EXPECTED_SEPARATOR;
                    $this->expected |= $this->context() == self::CONTEXT_ARRAY ?
                        self::EXPECTED_ARRAY_END :self::EXPECTED_OBJECT_END;
                } else {
                    $this->expected = 0;
                }
                break;
            case self::TOKEN_ITEM_SEPARATOR:
                $this->expected = $this->context() == self::CONTEXT_ARRAY ?
                    self::EXPECTED_ARRAY_ITEM : self::EXPECTED_OBJECT_ITEM;

                return $this->next();
        }

        return $this->token;
    }

    /**
     * @return array
     * @throws ReadingError
     */
    protected function fetch()
    {
        if ($this->context() == self::CONTEXT_OBJECT) {
            list($token, $key) = $this->readKey();
            if ($token != self::TOKEN_KEY) {
                return array(
                    'key'     => null,
                    'token'   => $token,
                    'content' => null
                );
            }
        } else {
            $key = null;
        }

        list($token, $content) = $this->readValue();

        return array(
            'key'     => $key,
            'token'   => $token,
            'content' => $content
        );
    }

    /**
     * @throws ReadingError
     */
    protected function readKey()
    {
        list($token, $key) = $this->readKeyToken();

        if ($token == self::TOKEN_KEY) {
            $char = $this->findSymbol();
            if ($char != ":") {
                throw new \Exception(sprintf("Expecting key-value separator, got \"%s\"", $char));
            }
        }

        return array($token, $key);
    }

    /**
     * @return array
     */
    protected function readKeyToken()
    {
        $char = $this->findSymbol();

        switch ($char) {
            case "}":
                return array(self::TOKEN_OBJECT_END,     null);
            case "]":
                return array(self::TOKEN_ARRAY_END,      null);
            case ",":
                return array(self::TOKEN_ITEM_SEPARATOR, null);
            case "\"":
                return array(self::TOKEN_KEY, $this->readString($char));
        }

        return array(null, null);
    }

    /**
     * @return array
     * @throws ReadingError
     */
    protected function readValue()
    {
        $char = $this->findSymbol();

        if ($char === "" || $char === false) {
            return array(null, null);
        }

        switch ($char) {
            case "{":
                return array(self::TOKEN_OBJECT_START,   null);
            case "}":
                return array(self::TOKEN_OBJECT_END,     null);
            case "[":
                return array(self::TOKEN_ARRAY_START,    null);
            case "]":
                return array(self::TOKEN_ARRAY_END,      null);
            case ",":
                return array(self::TOKEN_ITEM_SEPARATOR, null);
            case "\"":
                return array(self::TOKEN_SCALAR, $this->readString($char));
            default:
                return array(self::TOKEN_SCALAR, $this->readScalar($char));
        }
    }

    /**
     * @param $char
     * @throws Exception
     * @return string
     */
    protected function readString($char)
    {
        $quotes  = $char;
        $buffer  = "";

        $escaped = false;
        $size = 0;

        $buffer = fopen('php://temp', 'w+');

        do {
            $chunk = fread($this->stream, $this->threshold);
            $length = strlen($chunk);
            if (preg_match('#^[^"\\\\]*(?:\\\\.[^"\\\\]*)*"#', $chunk, $matches)) {
                $tail = substr($matches[0], 0, -1);
                
                $end = strlen($tail);
                $size += $end;
                fwrite($buffer, $tail);
                fseek($this->stream, (-$length+$end+1), SEEK_CUR);
                rewind($buffer);
                if ($size < $this->threshold) {
                    return stream_get_contents($buffer);
                }

                return $buffer;
            }

            fwrite($buffer, $chunk);
            $size += $this->threshold;
        } while ($chunk);

        return $buffer;

        while (true) {
            $char = fread($this->stream, 1);
            // Unterminated string (waiting for quotes)
            if ($char === false || $char === "") {
                throw new Exception("String not terminated correctly " . ftell($this->stream));
            }

            // Terminated string
            if ($quotes == $char && !$escaped) {
                if (is_resource($buffer)) {
                    return $buffer;
                } else {
                    return json_decode($quotes . $buffer . $quotes);
                }
            }

            // Continued
            if (is_string($buffer)) {
                $buffer .= $char;
                $size++;

                if (strlen($buffer) == $this->threshold) {
                    $tmp = fopen('php://temp', 'w+');
                    fwrite($tmp, $buffer);
                    $buffer = $tmp;
                }
            } else {
                fwrite($buffer, $char);
            }

            $escaped = !$escaped && $quotes === "\"" && $char == "\\";
        }
    }

    /**
     * @param $char
     * @return string
     * @throws Exception
     */
    protected function readScalar($char)
    {
        $buffer = $char;

        while (true) {
            $char = $this->readSymbol();
            if ($char === "" || $char === false || strpos(",}] \t\n\r", $char) !== false) {
                if ($char && strpos(",}]", $char) !== false) {
                    $this->buffered[] = $char;
                }
                break;
            }
            $buffer .= $char;
        }

        switch ($buffer) {
            case "true":
                return true;
            case "false":
                return false;
            case "null":
                return null;
        }

        if (!preg_match('/^-?(?:0|[1-9]\d*)?(?:\.\d+)?(?:[eE][+-]?\d+)?$/', $buffer)) {
            throw new Exception(sprintf("Scalar value \"%s\" is invalid", $buffer));
        }

        return floatval($buffer);
    }

    /**
     * @return string
     */
    protected function findSymbol()
    {
        while (($char = $this->readSymbol()) && strpos(" \n\r\t", $char) !== false);

        return $char;
    }

    /**
     * @return string
     */
    protected function readSymbol()
    {
        if ($this->buffered) {
            return array_pop($this->buffered);
        }

        return fread($this->stream, 1);
    }

    /**
     * @return mixed
     */
    public function context()
    {
        return end($this->context);
    }
}