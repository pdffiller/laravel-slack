<?php

namespace Pdffiller\LaravelSlack\RequestBody\Multipart;

use Pdffiller\LaravelSlack\RequestBody\BaseRequestBody;

/**
 * Multipart form-data body for uploading files
 * Class FileItemObject
 *
 * @package Pdffiller\LaravelSlack\RequestBody\Multipart
 */
class FileItemObject extends BaseRequestBody
{
    /**
     * @var string
     */
    private ?string $fileName = null;

    /**
     * @var string
     */
    private ?string $filePath = null;

    /**
     * @var string
     */
    private ?string $fileType = null;

    /**
     * @var string
     */
    private ?string $initialComment = null;

    /**
     * @var string
     */
    private ?string $channels = null;
    private ?string $threadTs = null;
    /**
     * @param string $fileName
     *
     * @return FileItemObject
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param string $filePath
     *
     * @return FileItemObject
     */
    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @param string $fileType
     *
     * @return FileItemObject
     */
    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * @param string $initialComment
     *
     * @return FileItemObject
     */
    public function setInitialComment(string $initialComment): self
    {
        $this->initialComment = $initialComment;

        return $this;
    }

    /**
     * @param string $channels
     *
     * @return FileItemObject
     */
    public function setChannels(string $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    /**
     * @param string $threadTs
     *
     * @return FileItemObject
     */
    public function setThreadTs(string $threadTs): self
    {
        $this->threadTs = $threadTs;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            [
                'name'     => 'channels',
                'contents' => $this->channels,
            ],
            [
                'name'     => 'filename',
                'contents' => $this->fileName,
            ],
            [
                'name'     => 'thread_ts',
                'contents' => $this->threadTs,
            ],
            [
                'name'     => 'file',
                'contents' => fopen($this->filePath, 'rb'),
            ],
            [
                'name'     => 'filetype',
                'contents' => $this->fileType,
            ],
            [
                'name'     => 'initial_comment',
                'contents' => $this->initialComment,
            ],
        ];
    }
}
