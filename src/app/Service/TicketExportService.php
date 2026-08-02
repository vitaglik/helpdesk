<?php
/**
 * если будет много запросов можно добавить кеширование
 */

namespace App\Service;

use App\Api\FreshDeskClientInterface;
use App\Csv\CsvWriter;
use App\DTO\AgentDto;
use App\DTO\CompanyDto;
use App\DTO\ContactDto;
use App\DTO\GroupDto;
use App\Mapper\EntityMapper;
use App\Mapper\TicketMapper;
use App\Mapper\TicketCsvMapper;
use http\Exception\RuntimeException;

class TicketExportService
{
    private const int PER_PAGE = 100;

    public function __construct(
        private FreshDeskClientInterface $client,
        private TicketMapper             $ticketMapper,
        private EntityMapper             $entityMapper,
        private CsvWriter                $csvWriter,
        private TicketCsvMapper          $ticketCsvMapper
    )
    {

    }

    private function getAgent(?int $id): ?AgentDto
    {
        if ($id === null) {
            return null;
        }

        $data = $this->client->getAgent($id);

        return $data !== null ? $this->entityMapper->mapAgent($data) : null;
    }

    private function getContact(?int $id): ?ContactDto
    {
        if ($id === null) {
            return null;
        }

        $data = $this->client->getContact($id);

        return $data !== null ? $this->entityMapper->mapContact($data) : null;
    }

    private function getGroup(?int $id): ?GroupDto
    {
        if ($id === null) {
            return null;
        }

        $data = $this->client->getGroup($id);

        return $data !== null ? $this->entityMapper->mapGroup($data) : null;
    }

    private function getCompany(?int $id): ?CompanyDto
    {
        if ($id === null) {
            return null;
        }

        $data = $this->client->getCompany($id);

        return $data !== null ? $this->entityMapper->mapCompany($data) : null;
    }

    public function export(): int
    {
        $page = 1;
        $exportedCount = 0;

        try {
            $this->csvWriter->write(
                $this->ticketCsvMapper->getHeaders()
            );

            do {
                $tickets = $this->client->getTickets(
                    page: $page,
                    perPage: self::PER_PAGE,
                );

                foreach ($tickets as $ticketData) {
                    if (!is_array($ticketData)) {
                        continue;
                    }

                    $ticket = $this->ticketMapper->map($ticketData);

                    $agent = $this->getAgent($ticket->agentId);
                    $contact = $this->getContact($ticket->contactId);
                    $group = $this->getGroup($ticket->groupId);
                    $company = $this->getCompany($ticket->companyId);

                    $conversationData =
                        $this->client->getTicketConversations(
                            $ticket->id
                        );

                    $conversations =
                        $this->entityMapper->mapConversations(
                            $conversationData
                        );

                    $row = $this->ticketCsvMapper->map(
                        ticket: $ticket,
                        agent: $agent,
                        contact: $contact,
                        group: $group,
                        company: $company,
                        conversations: $conversations,
                    );

                    $this->csvWriter->write($row);

                    ++$exportedCount;
                }

                ++$page;
            } while (count($tickets) === self::PER_PAGE);
        } catch (RuntimeException $e){
            die($e->getMessage());
        }

        $this->csvWriter->close();

        return $exportedCount;
    }
}