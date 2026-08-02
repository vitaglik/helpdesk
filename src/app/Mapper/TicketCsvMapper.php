<?php

namespace App\Mapper;

use App\DTO\AgentDto;
use App\DTO\CompanyDto;
use App\DTO\ContactDto;
use App\DTO\GroupDto;
use App\DTO\TicketDto;
use App\Mapper\EntityMapper;
use App\Mapper\TicketMapper;

class TicketCsvMapper
{
    public function map(
        TicketDto $ticket,
        ?AgentDto $agent,
        ?CompanyDto $company,
        ?GroupDto $group,
        ?ContactDto $contact,
        array $conversations
    ): array
    {
        return [
            $ticket->id,
            $ticket->description,
            $ticket->status,
            $ticket->priority,

            $agent?->id ?? '',
            $agent?->name ?? '',
            $agent?->email ?? '',

            $contact?->id ?? '',
            $contact?->name ?? '',
            $contact?->email ?? '',

            $group?->id ?? '',
            $group?->name ?? '',

            $company?->id ?? '',
            $company?->name ?? '',

            $this->mapConversations($conversations)
        ];
    }

    public function getHeaders(): array
    {
        return [
            'Ticket ID',
            'Description',
            'Status',
            'Priority',
            'Agent ID',
            'Agent Name',
            'Agent Email',
            'Contact ID',
            'Contact Name',
            'Contact Email',
            'Group ID',
            'Group Name',
            'Company ID',
            'Company Name',
            'Comments'
        ];
    }

    /**
     * можно сделать что бы передавались не цифры, а название статуса
     */
//    private function mapStatus(int $status): string
//    {
//        return match ($status) {
//            2 => 'Open',
//            3 => 'Pending',
//            4 => 'Resolved',
//            5 => 'Closed',
//            default => $status
//        };
//    }
//
//    private function mapPriority(int $priority): string
//    {
//        return match ($priority) {
//            1 => 'Low',
//            2 => 'Medium',
//            3 => 'High',
//            4 => 'Urgent',
//            default => $priority
//        };
//    }

    private function mapConversations(array $conversations): string
    {
        $bodies = [];

        foreach ($conversations as $conversation) {
            $body = trim($conversation->body);

            if ($body === '') {
                continue;
            }

            $bodies[] = $body;
        }

        return implode(' | ', $bodies);
    }
}