<?php

namespace Maicol07\Instatus;

use Flarum\Bus\Dispatcher;
use Flarum\Discussion\Command\ReadDiscussion;
use Flarum\Discussion\Command\StartDiscussion;
use Flarum\Discussion\Discussion;
use Flarum\Post\Command\PostReply;
use Flarum\Post\Post;
use Flarum\User\User;
use JsonException;
use Laminas\Diactoros\Response\HtmlResponse;
use Maicol07\Instatus\Partials\Update;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class InstatusController implements RequestHandlerInterface
{
    public Dispatcher $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $body = json_decode($request->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
        $instatus = new Instatus($body);
        $incident = $instatus->incident();
        $ipAddress = $request->getAttribute('ipAddress');
        $actor = User::find(2);

        if (!Discussion::where('instatus_id', $incident->id())->exists()) {
            // Create discussion
            $update = $incident->updates()->pull(0);
            assert($update instanceof Update);

            $footer = $instatus->isMaintenance ? "This maintenance should last {$incident->duration()} minutes" : "This issue is categorized as {$incident->impact()}";

            $discussion = $this->bus->dispatch(
                new StartDiscussion($actor, [
                    'type' => 'discussion',
                    'attributes' => [
                        'title' => $incident->name(),
                        'content' => "{$update->body()}

Created on {$update->createdAt()->formatLocalized('%A %d %B %Y')}

Last edited on {$update->updatedAt()->formatLocalized('%A %d %B %Y')}

$footer

<b>Affected systems: {$incident->affectedComponents()->pluck('name')->join(', ')}</b>

<a href='https://status.maicol07.it/incident/{$incident->id()}'>Read more on Maicol07 Status</a>"
                    ],
                    'relationships' => [
                        'tags' => [
                            'data' => [
                                [
                                    'type' => 'tags',
                                    'id' => 3
                                ]
                            ]
                        ]
                    ]
                ], $ipAddress)
            );
            $discussion->instatus_id = $incident->id();
            $discussion->save();
        } else {
            $discussion = Discussion::whereFirst('instatus_id', $incident->id());
        }

        foreach ($incident->updates()->except(0) as $update) {
            assert($update instanceof Update);
            if (!Post::where('instatus_id', $update->id())->exists()) {
                $this->bus->dispatch(
                    new PostReply($discussion->id, $actor, [
                        'data' => [
                            'attributes' => [
                                'content' => "Current status: {$update->status()}
                            
                            {$update->body()}

                            Created on {$update->createdAt()->formatLocalized('%A %d %B %Y')}
                            Last edited on {$update->updatedAt()->formatLocalized('%A %d %B %Y')}

                            <b>Affected systems: {$incident->affectedComponents()->pluck('name')->join(', ')}</b>"
                            ]
                        ]
                    ], $ipAddress)
                );
            }
        }

        // After creating the discussion, we assume that the user has seen all
        // the posts in the discussion; thus, we will mark the discussion
        // as read if they are logged in.
        if ($actor->exists) {
            $this->bus->dispatch(
                new ReadDiscussion($discussion->id, $actor, $discussion->last_post_number)
            );
        }

        return new HtmlResponse('<h1>Hello, world!</h1>');
    }
}