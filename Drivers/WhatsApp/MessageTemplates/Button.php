<?php

namespace Drivers\WhatsApp\MessageTemplates;

/**
 * Represents a single row in a WhatsApp interactive list message.
 *
 * @see https://developers.facebook.com/docs/whatsapp/cloud-api/messages/interactive-list-messages
 */
class Button implements \JsonSerializable
{
    public static function create(string|int $id, string $text): static
    {
        return new static($id, $text);
    }

    public function __construct(
        protected string|int $id,
        protected string $text,
    ) {}

    public function toArray(): array
    {
        return [
            'id'   => (string) $this->id,
            'text' => $this->text,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
