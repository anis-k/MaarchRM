<?php
class JsonParser
{
    function parse($stream, $threshold = 2097152)
    {
        $tokenizer = new JsonTokenizer($stream, $threshold);

        $stack = [];
        $keys = [];
        $result = null;
        while ($token = $tokenizer->next()) {
            switch ($token['token']) {
                case JsonTokenizer::TOKEN_OBJECT_START:
                    if (isset($token['key'])) {
                        $keys[] = $token['key'];
                    }
                    $stack[] = new stdClass();
                    break;

                case JsonTokenizer::TOKEN_OBJECT_END:
                    $obj = array_pop($stack);
                    if (empty($stack)) {
                        $result = $obj;
                    } else {
                        $item = array_pop($stack);
                        if (is_object($item)) {
                            $key = array_pop($keys);
                            $item->{$key} = $obj;
                        } elseif (is_array($item)) {
                            $item[] = $obj;
                        }

                        $stack[] = $item;
                    }
                    break;

                case JsonTokenizer::TOKEN_ARRAY_START:
                    if (isset($token['key'])) {
                        $keys[] = $token['key'];
                    }
                    $stack[] = [];
                    break;

                case JsonTokenizer::TOKEN_ARRAY_END:
                    $arr = array_pop($stack);
                    if (empty($stack)) {
                        $result = $arr;
                    } else {
                        $item = array_pop($stack);
                        if (is_object($item)) {
                            $key = array_pop($keys);
                            $item->{$key} = $arr;
                        } elseif (is_array($item)) {
                            $item[] = $arr;
                        }

                        $stack[] = $item;
                    }
                    break;

                case JsonTokenizer::TOKEN_SCALAR:
                    $item = array_pop($stack);
                    if (is_object($item)) {
                        $item->{$token['key']} = $token['content'];
                    } elseif (is_array($item)) {
                        $item[] = $token['content'];
                    }

                    $stack[] = $item;
                    break;

                case JsonTokenizer::TOKEN_KEY:
                    $keys[] = $token;
                    break;

                case JsonTokenizer::TOKEN_ITEM_SEPARATOR:
                    break;
            }
        }

        return $result;
    }
}
