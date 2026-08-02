<?php

namespace App\DTO;

class AgentDto
{
    public function __construct(public int $id, public string $name,  public string $email) {}
}