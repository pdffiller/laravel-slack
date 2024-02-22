<?php

namespace Pdffiller\LaravelSlack\RequestBody\Json;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Pdffiller\LaravelSlack\RequestBody\BaseRequestBody;

/**
 * Class JsonBodyObject
 *
 * @package Pdffiller\LaravelSlack\RequestBody\Json
 */
class JsonBodyObject extends BaseRequestBody implements Arrayable
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $triggerId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $iconUrl;

    /**
     * @var boolean
     */
    private $replaceOriginal;

    /**
     * @var string
     */
    private $ts;

    /**
     * @var string
     */
    private $threadTs;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $attachments;

    /**
     * @var \Pdffiller\LaravelSlack\RequestBody\Json\Dialog
     */
    private $dialog;

    private $user;

    /**
     * JsonBody constructor.
     */
    public function __construct()
    {
        $this->attachments = new Collection();
    }

    /**
     * @param string $channel
     *
     * @return JsonBodyObject
     */
    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @param string $triggerId
     *
     * @return JsonBodyObject
     */
    public function setTriggerId(string $triggerId): self
    {
        $this->triggerId = $triggerId;

        return $this;
    }

    /**
     * @param bool $replaceOriginal
     *
     * @return JsonBodyObject
     */
    public function setReplaceOriginal(bool $replaceOriginal): self
    {
        $this->replaceOriginal = $replaceOriginal;

        return $this;
    }

    /**
     * @param string $ts
     *
     * @return JsonBodyObject
     */
    public function setTs(string $ts): self
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * @param string $threadTs
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function setThreadTs(string $threadTs): self
    {
        $this->threadTs = $threadTs;

        return $this;
    }

    /**
     * @param string $username
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $iconUrl
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function setIconUrl(string $iconUrl): self
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    /**
     * @param string $text
     *
     * @return JsonBodyObject
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param \Pdffiller\LaravelSlack\RequestBody\Json\Dialog $dialog
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function setDialog(Dialog $dialog): self
    {
        $this->dialog = $dialog;

        return $this;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param \Pdffiller\LaravelSlack\RequestBody\Json\Attachment $attachment
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments->push($attachment);

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    /**
     * @param \Illuminate\Support\Collection $attachments
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function setAttachments(Collection $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param \Illuminate\Support\Collection $attachments
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    public function addAttachments(Collection $attachments): self
    {
        collect($attachments)->each(function ($attachment, $key) {
            if ($attachment instanceof Attachment) {
                $this->addAttachment($attachment);
            }
        });

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'attachments'      => $this->attachments->toArray(),
            'channel'          => $this->channel,
            'dialog'           => $this->dialog ? $this->dialog->toArray() : null,
            'icon_url'         => $this->iconUrl,
            'replace_original' => $this->replaceOriginal,
            'text'             => $this->text,
            'thread_ts'        => $this->threadTs,
            'trigger_id'       => $this->triggerId,
            'username'         => $this->username,
            'user'             => $this->user,
            'ts'               => $this->ts,
        ];

        return array_filter($array, function ($value) {
            return !is_null($value) && $value !== '';
        });
    }
}
