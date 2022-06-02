# Send slack messages and handle interactions with it

## Description

This package is used for sending Slack messages and handling interactions with slack messages: clicking on messages buttons and interacting with users through dialogs.

## Installition

Install the package via composer:

``` bash
composer require meilleursbiens/laravel-slack
```

Install service provider:

```php
// config/app.php
'providers' => [
    ...
    Pdffiller\LaravelSlack\LaravelSlackServiceProvider::class,
];
```

Publish config and database migration file:

```bash
php artisan vendor:publish --provider="Pdffiller\LaravelSlack\LaravelSlackServiceProvider"
```

Run migrations:

```bash
php artisan migrate
```

This is the contents of the published `laravel-slack-plugin.php` config file:

```php
return [
    /*
     * Bot User OAuth Access from Slack App
     */
    'bot-token' => env('SLACK_BOT_TOKEN', null),

    /*
     * Verification token from Slack App
     */
    'verification_token' => env('SLACK_VERIFICATION_TOKEN', null),

    /*
     * Handlers are processed by controller
     */
    'handlers' => [
    ],

    /*
     * Endpoint URL
     */
    'endpoint-url' => 'slack/handle'
];
```

## Usage

### Sending messages

Plugin supports this methods from slack api:
- [chat.postMessage](https://api.slack.com/methods/chat.postMessage)
- [chat.update](https://api.slack.com/methods/chat.update)
- [dialog.open](https://api.slack.com/methods/dialog.open)
- [files.upload](https://api.slack.com/methods/files.upload)

You can inject service `Pdffiller\LaravelSlack\Services\LaravelSlackPlugin` into your method.

Service has 2 methods:
- `buildMessage((AbstractMethodInfo $method), [Model $model], [string $options])`
- `sendMessage([$message])`

Instances of these classes can be used as a `$method` parameter:
- `Pdffiller\LaravelSlack\AvailableMethods\ChatPostMessage`
- `Pdffiller\LaravelSlack\AvailableMethods\ChatUpdate`
- `Pdffiller\LaravelSlack\AvailableMethods\DialogOpen`
- `Pdffiller\LaravelSlack\AvailableMethods\FilesUpload`

Eloquent models can be used as a not obligatory `$body` parameter.

All sent slack messages via `chat.postMessage` method are saved in the database in the `laravel_slack_message` table.
Slack message `ts` and `channel` are saved for every message.
If eloquent model is sent as the `$body` parameter to the `buildMessage` method, model's primary key and model's path are also saved.
If json options are sent as a $options parameter to the `buildMessage` method, it will be also saved in the db.
You can then use `ts` and `channel` parameters in `chat.update` method in order to update existing slack message.

#### Sending plain text message
```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
$slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\ChatPostMessage())
           ->setChannel("#ABCDEF") //channel id from web app
           ->setText("asdsadsad");
$slack->sendMessage();
```

#### Update slack message
```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\ChatUpdate())
           ->setChannel("#ABCDEF")
           ->setTs('1405894322.002768')
           ->setText("updated text");
$slack->sendMessage();
```

### Building messages with attachments

For building messages with [attachments](https://api.slack.com/docs/message-attachments) plugin has this classes:
- `Pdffiller\LaravelSlack\RequestBody\Json\Attachment`
- `Pdffiller\LaravelSlack\RequestBody\Json\AttachmentField`
- `Pdffiller\LaravelSlack\RequestBody\Json\AttachmentAction`

#### Sending message with text attachment
```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
$slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\ChatPostMessage())
                   ->setChannel("#ABCDEF")
                   ->addAttachment(\Pdffiller\LaravelSlack\RequestBody\Json\Attachment::create()
                                         ->setText("this is text")
                                         ->setColor('#36a64f')); // default color is #D3D3D3
$slack->sendMessage();
```


#### Sending message with attachment including fields
```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
$slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\ChatPostMessage())
           ->setChannel("#ABCDEF")
           ->addAttachment(\Pdffiller\LaravelSlack\RequestBody\Json\Attachment::create()
                         ->addFields([                  
                                 [                           
                                     'title' => 'User Id',
                                     'value' => 10
                                     /*, 'short'=true/false*/  // short is true by default, it means that next field is shown on the same line
                                 ],
                                 \Pdffiller\LaravelSlack\RequestBody\Json\AttachmentField::create('Team Id', 10),
                             ]
                         ));
$slack->sendMessage();
```

#### Sending message with attachment including actions
```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
$slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\ChatPostMessage())
               ->setChannel("#ABCDEF")
               ->addAttachment(\Pdffiller\LaravelSlack\RequestBody\Json\Attachment::create()
                   ->setCallbackId('callback-id-is-used-in-handler')
                   ->setFallback('Fallback text') // Required plain-text summary of the attachment
                   ->setColor('#36a64f')
                   ->addActions([
                           [
                               'name' => 'button-name',
                               'text' => 'Accept',
                               'value' => 1,
                               /*'style' => 'primary/danger',
                               'type'  => 'button'*/
                           ],
                           \Pdffiller\LaravelSlack\RequestBody\Json\AttachmentAction::create('button-name', 'Decline', 0)
                       ]
                   ));
$slack->sendMessage();
```

### Sending message with [file](https://api.slack.com/methods/files.upload)
```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
$slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\FilesUpload())
                ->setChannel("#ABCDEF")
                ->setFilePath("/uploads/one.txt") //path to file in your project or web
                ->setFileName("one.txt); 
$slack->sendMessage();
```

### Open [dialogs](https://api.slack.com/methods/dialog.open) after messages interaction

Dialog can be opened after interaction with message, for example clicking on a button. After interaction with message, `trigger_id` is sent in the request payload. This parameter is used in the `dialog.open` method.

For building dialog plugin has this classes:
- `Pdffiller\LaravelSlack\RequestBody\Json\Dialog`
- `Pdffiller\LaravelSlack\RequestBody\Json\DialogElement`

```php
$slack = resolve(\Pdffiller\LaravelSlack\Services\LaravelSlackPlugin::class);
$slack->buildMessage(new \Pdffiller\LaravelSlack\AvailableMethods\DialogOpen())
                   ->setTriggerId('trigger_id')
                   ->setDialog(\Pdffiller\LaravelSlack\RequestBody\Json\Dialog::create()
                           ->setCallbackId('will-be-used-in-handler')
                           ->setTitle('Title')
                           ->setSubmitLabel('Save')
                           //->setState(json_encode([])) // you can save some data between opening dialog and handling                                                                   it's interaction in the state parameter
                           ->addElement(\Pdffiller\LaravelSlack\RequestBody\Json\DialogElement::create()
                                             ->setName('reason')
                                             ->setLabel('...')
                                             ->setType(DialogElement::TEXTAREA_TYPE))
                   );
$slack->sendMessage();
```

## Handling Interactive Components

Plugin supports handling interacrtions with buttons and dialogs. All requests from slack are sent to the plugin's endpoint. It's url can be changed in the generated `laravel-slack-plugin.php` config in the `endpoint-url` section.  This URL should be added to the `Interactive Components` section in your Slack app. OAuth Access Token and Verification token from Slack App should be added to your config file.

Plugin has convenient system of handlers for processing all interactions.
Both Attachment and Dialog have `callback_id` parameter. After every interaction with button or dialog slack sends request to the `/slack/handle` endpoint. `callback_id` parameter is included in the request payload, so all requests can be distinguished by this parameter.
So, one handler should be implemented for the requests of one `callback_id` type.

Plugin has `Pdffiller\LaravelSlack\Handlers\BaseHandler` class which has this methods:
- `shouldBeHandled()`
- `handle()`

In your project you should implement your own handlers on top of plugin's `BaseHandler` class.

### Handler example 

```php
use Pdffiller\LaravelSlack\Handlers\BaseHandler;

class CustomHandler extends BaseHandler
{    
    // check if this handler should be executed
    public function shouldBeHandled()
    {
        $payload = json_decode($this->request->get('payload'), true);
        $callBackId = Arr::get($payload, 'callback_id');

        return $callBackId === 'some-callback-id';
    }
    
    public function handle()
    {
        $payload = json_decode($this->request->get('payload'), true);
        // ...
    }
}
```

Handler should be added to the `handlers` section in the generated `laravel-slack-plugin.php` config file in your project.

```php
'handlers' => [
        \App\Slack\Handlers\CustomHandler::class,
        ...
]
```


