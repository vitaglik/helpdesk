<?php

namespace App\Mapper;

use App\DTO\AgentDto;
use App\DTO\CompanyDto;
use App\DTO\ContactDto;
use App\DTO\GroupDto;
use App\DTO\ConversationDto;

class EntityMapper
{
    public function mapAgent(array $data): AgentDto
    {
        return new AgentDto(
            id: $data['id'] ?? 0,
            name: $data['contact']['name'] ?? '',
            email: $data['contact']['email'] ?? ''
        );
    }

    public function mapContact(array $data): ContactDto
    {
        return new ContactDto(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            email: $data['email'] ?? ''
        );
    }

    public function mapGroup(array $data): GroupDto
    {
        return new GroupDto(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? ''
        );
    }

    public function mapCompany(array $data): CompanyDto
    {
        return new CompanyDto(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? ''
        );
    }

    public function mapConversations(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $result[] = new ConversationDto(
                id: $item['id'] ?? 0,
                body: $item['body'] ?? $item['body_text'] ?? ''
            );

        }
        return $result;
    }
}