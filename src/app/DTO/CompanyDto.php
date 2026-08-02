<?php

namespace App\DTO;

class CompanyDto
{
    public function __construct(public int $id, public string $name) {}
}