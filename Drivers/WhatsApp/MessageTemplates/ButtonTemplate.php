<?php

namespace Drivers\WhatsApp\MessageTemplates;

/**
 * Builds a WhatsApp interactive list message.
 *
 * A list message presents a set of options in a scrollable menu. It requires
 * a body text and at least one Button (row) grouped in a section.
 *
 * Usage:
 *   $template = ButtonTemplate::create('Choose a category')
 *       ->addButton(Button::create('1', 'Support'))
 *       ->addButton(Button::create('2', 'Sales'));
 *
 * @see https://developers.facebook.com/docs/whatsapp/cloud-api/messages/interactive-list-messages
 */
class ButtonTemplate implements \JsonSerializable
{
    protected array $buttons = [];

    public static function create(string $text, string $actionLabel = 'Options'): static
    {
        return new static($text, $actionLabel);
    }

    public function __construct(
        protected string $text,
        protected string $actionLabel = 'Options',
    ) {}

    public function addButton(Button $button): self
    {
        $this->buttons[] = $button->toArray();
        return $this;
    }

    public function addButtons(array $buttons): self
    {
        foreach ($buttons as $button) {
            if ($button instanceof Button) {
                $this->buttons[] = $button->toArray();
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type'        => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => [
                    'text' => $this->text,
                ],
                'action' => [
                    'button'   => $this->actionLabel,
                    'sections' => [
                        [
                            'title' => $this->actionLabel,
                            'rows'  => $this->buttons,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
