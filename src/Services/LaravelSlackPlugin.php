<?php

namespace Pdffiller\LaravelSlack\Services;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Pdffiller\LaravelSlack\AvailableMethods\AbstractMethodInfo;
use Pdffiller\LaravelSlack\AvailableMethods\FilesInfo;
use Pdffiller\LaravelSlack\AvailableMethods\FilesUpload;
use Pdffiller\LaravelSlack\RequestBody\BaseRequestBody;
use Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject;
use Pdffiller\LaravelSlack\RequestBody\Multipart\FileItemObject;
use Pdffiller\LaravelSlack\Services\SlackApi;

/**
 * Class SlackApi
 *
 * @package App\Services
 */
class LaravelSlackPlugin
{
    /**
     * @var AbstractMethodInfo
     */
    private $method;

    /**
     * @var \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    private $message;

    /**
     * @var Model;
     */
    private $model;

    /**
     * @var mixed
     */
    private $options;

    /**
     * @var \Pdffiller\LaravelSlack\Services\SlackApi
     */
    private $slackApi;

    /**
     * Plugin constructor.
     *
     * @param \Pdffiller\LaravelSlack\Services\SlackApi $slackApi
     */
    public function __construct(SlackApi $slackApi)
    {
        $this->slackApi = $slackApi;
    }

    /**
     * @param \Pdffiller\LaravelSlack\AvailableMethods\AbstractMethodInfo $method
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param null $options
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\BaseRequestBody
     */
    public function buildMessage(AbstractMethodInfo $method, Model $model = null, $options = null): BaseRequestBody
    {
        $this->model = $model;
        $this->options = $options;

        if ($method instanceof FilesUpload) {
            return $this->buildMultipartMessage($method);
        }

        return $this->buildJsonMessage($method);
    }

    /**
     * @param \Pdffiller\LaravelSlack\RequestBody\BaseRequestBody|null $message
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendMessage(BaseRequestBody $message = null): array
    {
        return $this->slackApi->post($this->method, $message ? $message->toArray() : $this->message->toArray(),
            $this->model, $this->options);
    }

    /**
     * @return \Pdffiller\LaravelSlack\RequestBody\Json\JsonBodyObject
     */
    private function buildJsonMessage(AbstractMethodInfo $method): JsonBodyObject
    {
        $this->method = $method;
        $this->message = new JsonBodyObject();

        return $this->message;
    }

    /**
     * @param \Pdffiller\LaravelSlack\AvailableMethods\AbstractMethodInfo $method
     *
     * @return \Pdffiller\LaravelSlack\RequestBody\Multipart\FileItemObject
     */
    private function buildMultipartMessage(AbstractMethodInfo $method): FileItemObject
    {
        $this->method = $method;
        $this->message = new FileItemObject();

        return $this->message;
    }

    public function getFileInfo(string $fileId)
    {
        $method = new FilesInfo();

        return $this->slackApi->request($method, ['file' => $fileId]);
    }

    public function downloadFile(array $fileInfo)
    {
        $validator = Validator::make(
            ['url' => Arr::get($fileInfo, 'file.url_private_download')],
            ['url' => 'regex:/^https:\/\/files\.slack\.com\/.*$/']
        );

        if ($validator->fails()) {
            throw new \Exception('File not found');
        }

        return $this->slackApi->downloadFile($fileInfo);

    }
}
