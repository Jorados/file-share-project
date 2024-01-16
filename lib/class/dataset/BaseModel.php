<?php

namespace dataset;

abstract class BaseModel
{
    public function __construct($data = null)
    {
        $this->bindData($data);
    }

    public function getObjectVars()
    {
        return get_object_vars($this);
    }

    protected function bindData($data)
    {
        if (empty($data)) return;

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->getObjectVars())) continue;

            $this->{$key} = $value;
        }
    }
}
