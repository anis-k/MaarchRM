<?php

class JsonSerializer
{
    protected $salt;
    protected $resources = [];

    public function serialize($data, $options)
    {
        $data = $this->getRefs($data);

        $json = json_encode($data, $options);

        $parts = preg_split('/(Resource id #\d+)/', $json, 0, PREG_SPLIT_DELIM_CAPTURE);

        $stream = fopen('php://temp', 'w+');
        foreach ($parts as $part) {
            if (isset($this->resources[$part])) {
                stream_copy_to_stream($this->resources[$part], $stream);
            } else {
                fwrite($stream, $part);
            }
        }

        rewind($stream);

        return $stream;
    }

    /**
     * Converts resources to refs "Resource id #i"
     */
    protected function getRefs($data)
    {
        if (is_scalar($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->getRefs($value);
            }

            return $data;
        }

        if (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->{$key} = $this->getRefs($value);
            }

            return $data;
        }

        if (is_resource($data)) {
            $ref = (string) $data;
            $this->resources[$ref] = $data;
            
            return $ref;
        }
    }
}
