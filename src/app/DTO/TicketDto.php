<?php

namespace App\DTO;

class TicketDto
{
    public function __construct(
        public int $id,
        public string $description,
        public int $status,
        public int $priority,
        public int $agentId,
        public int $contactId,
        public int $groupId,
        public int $companyId,
    ) {

    }
}