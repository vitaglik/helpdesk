<?php

namespace App\Mapper;

use App\DTO\TicketDto;
use http\Exception\InvalidArgumentException;

class TicketMapper
{
    public function map(array $data): TicketDTO
    {
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('Ticket id id missing');
        }

        return new TicketDTO(
            id: $data['id'],
            description: $data['description'] ?? $data['description_text'] ?? '',
            status: $data['status'],
            priority: $data['priority'],
            agentId: $data['responder_id'],
            contactId: $data['requester_id'],
            groupId: $data['group_id'] ?? 0,
            companyId: $data['company_id']
        );
    }
}