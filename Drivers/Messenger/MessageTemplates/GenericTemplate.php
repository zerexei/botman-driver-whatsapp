<?php

namespace Drivers\Messenger\MessageTemplates;

class GenericTemplate implements \JsonSerializable
{
    protected array $elements = [];

    public static function create(): static
    {
        return new static();
    }

    public function __construct() {}

    public function addElement(Element $element): self
    {
        $this->elements[] = $element->toArray();
        return $this;
    }

    public function addElements(array $elements): self
    {
        foreach ($elements as $element) {
            if ($element instanceof Element) {
                $this->elements[] = $element->toArray();
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            "attachment" => [
                "type" => "template",
                "payload" => [
                    "template_type" => "generic",
                    "elements" => $this->elements
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
