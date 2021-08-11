<?php

namespace Gets\Freshservice;

use Gets\Freshservice\Entities\Department;
use Gets\Freshservice\Entities\Requester;
use Gets\Freshservice\Entities\Ticket;
use Gets\Freshservice\Exceptions\DepartmentException;
use Gets\Freshservice\Exceptions\DepartmentRequestException;
use Gets\Freshservice\Exceptions\FreshserviceException;
use Gets\Freshservice\Exceptions\RequesterException;
use Gets\Freshservice\Exceptions\RequesterRequestExceptions;
use Gets\Freshservice\Exceptions\TicketException;
use Gets\Freshservice\Exceptions\TicketRequestException;
use Gets\Freshservice\Requests\DepartmentRequest;
use Gets\Freshservice\Requests\RequesterRequest;
use Gets\Freshservice\Requests\TicketRequest;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

class Freshservice
{
    private $apiKey;

    /* @var GuzzleHttpClient $httpClient */
    private $httpClient;

    public function __construct(string $domain, string $apiKey)
    {
        $this->apiKey = $apiKey;

        $this->httpClient = new GuzzleHttpClient([
            'base_uri' => $domain
        ]);
    }

    /**
     * @throws FreshserviceException
     */
    public function createTicket(TicketRequest $ticket): Ticket
    {
        try {
            if ($ticket->hasAttachments()) {
                $multipart = [];
                foreach ($ticket->toArray() as $key => $value) {
                    $multipart[] = [
                        'name'     => $key,
                        'contents' => $value,
                    ];
                }
                foreach ($ticket->getAttachments() as $attachment) {
                    $multipart[] = [
                        'name'     => 'attachments[]',
                        'filename' => $attachment['name'],
                        'contents' => file_get_contents($attachment['tmp_name']),
                    ];
                }
                $response = $this->httpClient->post('/api/v2/tickets', [
                    'auth'      => [$this->apiKey, 'X'],
                    //'body' => $ticket->toJson(),
                    'headers'   => [
                        'Accept' => 'application/json',
                    ],
                    'multipart' => $multipart,
                ])->getBody()->getContents();
            } else {
                $response = $this->httpClient->post('/api/v2/tickets', [
                    'auth'    => [$this->apiKey, 'X'],
                    'body'    => $ticket->toJson(),
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ],
                ])->getBody()->getContents();
            }

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->ticket)) {
                throw new FreshserviceException('Wrong json response');
            }

            return Ticket::fillFromObject($convertedResponse->ticket);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (TicketException | TicketRequestException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getTicketById(int $ticketId): ?Ticket
    {
        try {
            $response = $this->httpClient->get("/api/v2/tickets/$ticketId?include=conversations,requester", [
                'auth'    => [$this->apiKey, 'X'],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->ticket)) {
                throw new FreshserviceException('Wrong json response');
            }

            return Ticket::fillFromObject($convertedResponse->ticket);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                $response = $e->getResponse();
                $responseBody = $response->getBody()->getContents();
                throw new FreshserviceException($responseBody);
            }

            return null;
        } catch (TicketException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getTicketsByEmail(string $email): array
    {
        try {
            $response = $this->httpClient->get("/api/v2/tickets?email=$email", [
                'auth'    => [$this->apiKey, 'X'],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->tickets)) {
                throw new FreshserviceException('Wrong json response');
            }

            $tickets = [];
            foreach ($convertedResponse->tickets as $ticket) {
                $tickets[] = Ticket::fillFromObject($ticket);
            }

            return $tickets;
        } catch (RequestException $e) {
            if ($e->getCode() !== 400) {
                throw new FreshserviceException($e->getMessage());
            }

            return [];
        } catch (TicketException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function createDepartment(DepartmentRequest $department): Department
    {
        try {
            $response = $this->httpClient->post('/api/v2/departments', [
                'auth'    => [$this->apiKey, 'X'],
                'body'    => $department->toJson(),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->department)) {
                throw new FreshserviceException('Wrong json response');
            }

            return Department::fillFromObject($convertedResponse->department);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (DepartmentRequestException | DepartmentException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getDepartments(int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("/api/v2/departments?page=$page", [
                'auth'    => [$this->apiKey, 'X'],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->departments)) {
                throw new FreshserviceException('Wrong json response');
            }

            $departments = [];
            foreach ($convertedResponse->departments as $department) {
                $departments[] = Department::fillFromObject($department);
            }

            return $departments;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return [];
        } catch (DepartmentException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getDepartmentById(int $departmentId): ?Department
    {
        try {
            $response = $this->httpClient->get("/api/v2/departments/$departmentId", [
                'auth'    => [$this->apiKey, 'X'],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->department)) {
                throw new FreshserviceException('Wrong json response');
            }

            return Department::fillFromObject($convertedResponse->department);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return null;
        } catch (DepartmentException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getDepartmentByName(string $departmentName): ?Department
    {
        try {
            $response = $this->httpClient->get("/api/v2/departments?query=\"name:'$departmentName'\"", [
                'auth'    => [$this->apiKey, 'X'],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->departments)) {
                throw new FreshserviceException('Wrong json response');
            }

            if (empty($convertedResponse->departments[0])) {
                throw new FreshserviceException('Department not found');
            }

            return Department::fillFromObject($convertedResponse->departments[0]);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return null;
        } catch (DepartmentException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function createRequester(RequesterRequest $requester): Requester
    {
        try {
            $response = $this->httpClient->post('/api/v2/requesters', [
                'auth'    => [$this->apiKey, 'X'],
                'body'    => $requester->toJson(),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->requester)) {
                throw new FreshserviceException('Wrong json response');
            }

            return Requester::fillFromObject($convertedResponse->requester);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (RequesterRequestExceptions | RequesterException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function updateRequester(int $requesterId, RequesterRequest $requester): Requester
    {
        try {
            $response = $this->httpClient->put("/api/v2/requesters/$requesterId", [
                'auth'    => [$this->apiKey, 'X'],
                'body'    => $requester->toJson(),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->requester)) {
                throw new FreshserviceException('Wrong json response');
            }

            return Requester::fillFromObject($convertedResponse->requester);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (RequesterRequestExceptions | RequesterException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getRequesterByEmail(string $requesterEmail): ?Requester
    {
        try {
            $response = $this->httpClient->get("/api/v2/requesters?query=\"primary_email:'$requesterEmail'\"", [
                'auth'    => [$this->apiKey, 'X'],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ])->getBody()->getContents();

            $convertedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($convertedResponse->requesters)) {
                throw new FreshserviceException('Wrong json response');
            }

            if (empty($convertedResponse->requesters[0])) {
                throw new FreshserviceException('Requester not found');
            }

            return Requester::fillFromObject($convertedResponse->requesters[0]);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return null;
        } catch (RequesterException | \JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }
}
