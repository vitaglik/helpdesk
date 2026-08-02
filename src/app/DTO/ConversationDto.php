<?php

namespace App\DTO;

class ConversationDto
{
    public function __construct(public int $id, public string $body) {}
}