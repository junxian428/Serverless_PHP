<?php declare(strict_types=1);

namespace Bref\Event\ApiGateway;

use Bref\Event\InvalidLambdaEvent;
use Bref\Event\LambdaEvent;

/**
 * Represents a Lambda event when Lambda is invoked by ApiGateway websocket route.
 *
 * @final
 */
class WebsocketEvent implements LambdaEvent
{
    private array $event;
    private string $routeKey;
    private string | null $eventType = null;
    private mixed $body = null;
    private string $connectionId;
    private string $domainName;
    private string $apiId;
    private string $stage;

    /**
     * @throws InvalidLambdaEvent
     */
    public function __construct(mixed $event)
    {
        if (
            ! is_array($event) ||
            ! isset($event['requestContext']['routeKey']) ||
            ! isset($event['requestContext']['eventType']) ||
            ! isset($event['requestContext']['connectionId']) ||
            ! isset($event['requestContext']['domainName']) ||
            ! isset($event['requestContext']['apiId']) ||
            ! isset($event['requestContext']['stage']) ||
            ! in_array(
                $event['requestContext']['eventType'],
                [
                    'CONNECT',
                    'DISCONNECT',
                    'MESSAGE',
                ],
                true
            )
        ) {
            throw new InvalidLambdaEvent('Websocket', $event);
        }

        $this->domainName = $event['requestContext']['domainName'];
        $this->connectionId = $event['requestContext']['connectionId'];
        $this->routeKey = $event['requestContext']['routeKey'];
        $this->apiId = $event['requestContext']['apiId'];
        $this->stage = $event['requestContext']['stage'];
        $this->event = $event;

        if (isset($event['requestContext']['eventType'])) {
            $this->eventType = $event['requestContext']['eventType'];
        }

        if (isset($event['body'])) {
            $this->body = $event['body'];
        }
    }

    public function toArray(): array
    {
        return $this->event;
    }

    public function getRouteKey(): string
    {
        return $this->routeKey;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function getApiId(): string
    {
        return $this->apiId;
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function getRegion(): string
    {
        return getenv('AWS_REGION');
    }
}
