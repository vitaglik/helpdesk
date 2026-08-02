<?php

namespace App\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use http\Exception\InvalidArgumentException;
use JsonException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class FreshdeskClient implements FreshDeskClientInterface
{
    private const string API_VERSION_PATH = "https://davopa.freshdesk.com/api/v2";
    private const int MAX_PER_PAGE = 200;
    private const int MAX_RETRIES = 3;
    private readonly ClientInterface $httpClient;

    public function __construct(string $apiKey, ?ClientInterface $httpClient = null)
    {

        if (trim($apiKey) == "") {
            throw new InvalidArgumentException('apiKey can\'t be empty');
        }

        $this->httpClient = $httpClient ?? new HttpClient([
            'base_uri' => self::API_VERSION_PATH,
            'auth' => [$apiKey, 'X'],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'connect_timeout' => 10,
            'timeout' => 30,
            'http_errors' => true
        ]);
    }

    private function request(string $method, string $url, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $url, $options);
            $body = $response->getBody();
            if ($body === '') {
                return [];
            }
            $data = json_decode($body, true, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw new RuntimeException('Freshdesk returned unexpected response for ' . $method . ' ' . $url);
            }
            return $data;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();
            throw new RuntimeException('Freshdesk request failed with code ' . $statusCode . ': ' . $responseBody);
        } catch (JsonException $e) {
            throw new RuntimeException('Freshdesk request returned invalid JSON: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new RuntimeException('Freshdesk request failed: ' . $e->getMessage());
        }
    }

    public function getTickets(int $page, int $perPage): array
    {

        if ($page < 1) {
            throw new InvalidArgumentException('page must be greater than 0');
        }

        if ($perPage < 1 && $perPage > self::MAX_PER_PAGE) {
            throw new InvalidArgumentException('perPage must be between 1 and ' . self::MAX_PER_PAGE);
        }

        return $this->request('GET', 'tickets', [
            'query' => [
                'page' => $page,
                'per_page' => $perPage
            ]
        ]);
    }

    public function getAgent(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }

        return $this->request('GET', 'agents/' . $id);
    }

    public function getContact(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }

        return $this->request('GET', 'contacts/' . $id);
    }

    public function getGroup(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }

        return $this->request('GET', 'groups/' . $id);
    }

    public function getCompany(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }

        return $this->request('GET', 'companies/' . $id);
    }

    public function getTicketConversations(int $ticketId): ?array
    {
        if ($ticketId < 1) {
            return null;
        }

        try {
            return $this->request(
                'GET',
                "tickets/{$ticketId}/conversations"
            );
        } catch (\RuntimeException $exception) {
            if ($exception->getCode() === 404) {
                return [];
            }

            throw $exception;
        }
    }
}