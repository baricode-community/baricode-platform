<?php

trait LogControllerCalls
{
    public function callAction($method, $parameters)
    {
        logger()->info(static::class . "->{$method} method called", $parameters);
        return parent::callAction($method, $parameters);
    }
}