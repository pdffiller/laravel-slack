<?php

namespace Pdffiller\LaravelSlack\AvailableMethods;

/**
 * Class ChatPostEphemeral
 *
 * @package Pdffiller\LaravelSlack\AvailableMethods
 */
class ChatPostEphemeral extends AbstractMethodInfo
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'chat.postEphemeral';
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return self::POST_METHOD;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return 'https://slack.com/api/chat.postEphemeral';
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Content-type'  => 'application/json; charset=utf-8',
            'Authorization' => "",
        ];
    }

    /**
     * @return string
     */
    public function getBodyType(): string
    {
        return self::JSON_BODY_TYPE;
    }
}
