<?php

namespace Pdffiller\LaravelSlack\AvailableMethods;

/**
 * Class FilesUpload
 *
 * @package Pdffiller\LaravelSlack\AvailableMethods
 */
class FilesInfo extends AbstractMethodInfo
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'files.info';
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return self::GET_METHOD;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return 'https://slack.com/api/files.info';
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Authorization' => "",
        ];
    }

    /**
     * @return string
     */
    public function getBodyType(): string
    {
        return self::QUERY_BODY_TYPE;
    }
}
