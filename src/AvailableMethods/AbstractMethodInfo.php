<?php

namespace Pdffiller\LaravelSlack\AvailableMethods;

/**
 * Class AbstractMethodInfo
 *
 * @package Pdffiller\LaravelSlack\AvailableMethods
 */
abstract class AbstractMethodInfo
{
    const POST_METHOD = 'POST';
    const GET_METHOD = 'GET';
    const JSON_BODY_TYPE = 'json';
    const MULTIPART_BODY_TYPE = 'multipart';
    const QUERY_BODY_TYPE = 'query';
    
    abstract public function getName(): string ;
    abstract public function getMethod(): string ;
    abstract public function getUrl(): string ;
    abstract public function getHeaders(): array;
    abstract public function getBodyType(): string;
}
