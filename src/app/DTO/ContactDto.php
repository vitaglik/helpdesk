<?php

namespace App\DTO;

class ContactDto
{
    public function __construct(public int $id, public string $name,  public string $email) {}
}