<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Api\FreshdeskClient;
use App\Csv\CsvWriter;
use App\Mapper\EntityMapper;
use App\Mapper\TicketCsvMapper;
use App\Mapper\TicketMapper;
use App\Service\TicketExportService;

$apiKey = 'PZJOshPRVvHmNMDINvan';

$client = new FreshdeskClient($apiKey);

$csvWriter = new CsvWriter(
    __DIR__ . '/storage/tickets.csv'
);

$exportService = new TicketExportService(
    client: $client,
    ticketMapper: new TicketMapper(),
    entityMapper: new EntityMapper(),
    ticketCsvMapper: new TicketCsvMapper(),
    csvWriter: $csvWriter,
);

try {
    $exportedCount = $exportService->export();

    echo sprintf(
        'Экспорт завершён. Выгружено тикетов: %d',
        $exportedCount
    );
} catch (Throwable $exception) {
    echo '<pre>';
    echo $exception->getMessage() . PHP_EOL . PHP_EOL;
    echo $exception->getTraceAsString();
}