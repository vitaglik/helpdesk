<?php

namespace App\Api;

interface FreshDeskClientInterface
{
    public function getTickets(int $page, int $perPage): array;
    public function getAgent(int $id): ?array;
    public function getContact(int $id): ?array;
    public function getGroup(int $id): ?array;
    public function getCompany(int $id): ?array;
    public function getTicketConversations(int $ticketId): ?array;

}