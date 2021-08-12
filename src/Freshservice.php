<?php

namespace Gets\Freshservice;

use JsonException;
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
    /* @var GuzzleHttpClient $httpClient */
    private $httpClient;

    public function __construct(string $domain, string $apiKey)
    {
        $this->httpClient = new GuzzleHttpClient([
            'base_uri' => $domain,
            'auth'     => [$apiKey, 'X'],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
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

                $responseJson = $this->httpClient->post('/api/v2/tickets', [
                    'multipart' => $multipart,
                ])->getBody()->getContents();
            } else {
                $responseJson = $this->httpClient->post('/api/v2/tickets', [
                    'json' => $ticket->toArray(),
                ])->getBody()->getContents();
            }

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);
            if (!isset($response->ticket)) {
                throw new FreshserviceException('Ticket is missing in response');
            }

            return Ticket::fillFromObject($response->ticket);
        } catch (TicketException | TicketRequestException $e) {
            throw new FreshserviceException($e->getMessage());
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();

            throw new FreshserviceException($responseBody);
        } catch (JsonException $e) {
            throw new FreshserviceException('Failed to parse JSON response');
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getTicketById(int $ticketId): ?Ticket
    {
        try {
            $responseJson = $this->httpClient->get("/api/v2/tickets/$ticketId?include=conversations,requester")
                ->getBody()
                ->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);
            if (!isset($response->ticket)) {
                throw new FreshserviceException('Wrong JSON response');
            }

            return Ticket::fillFromObject($response->ticket);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                $response = $e->getResponse();
                $responseBody = $response->getBody()->getContents();
                throw new FreshserviceException($responseBody);
            }

            return null;
        } catch (TicketException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getTicketsByEmail(string $email): array
    {
        try {
            $responseJson = $this->httpClient->get("/api/v2/tickets?email=$email")
                ->getBody()
                ->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);
            if (!isset($response->tickets)) {
                throw new FreshserviceException('Tickets are missing in response');
            }

            $tickets = [];
            foreach ($response->tickets as $ticket) {
                $tickets[] = Ticket::fillFromObject($ticket);
            }

            return $tickets;
        } catch (RequestException $e) {
            if ($e->getCode() !== 400) {
                throw new FreshserviceException($e->getMessage());
            }

            return [];
        } catch (TicketException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function createDepartment(DepartmentRequest $department): Department
    {
        try {
            $responseJson = $this->httpClient->post('/api/v2/departments', [
                'json' => $department->toArray(),
            ])->getBody()->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);
            if (!isset($response->department)) {
                throw new FreshserviceException('Department is missing in response');
            }

            return Department::fillFromObject($response->department);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (DepartmentRequestException | DepartmentException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getDepartments(int $page = 1): array
    {
        try {
            $responseJson = $this->httpClient->get("/api/v2/departments?page=$page")
                ->getBody()
                ->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($response->departments)) {
                throw new FreshserviceException('Departments are missing in response');
            }

            $departments = [];
            foreach ($response->departments as $department) {
                $departments[] = Department::fillFromObject($department);
            }

            return $departments;
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return [];
        } catch (DepartmentException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getDepartmentById(int $departmentId): ?Department
    {
        try {
            $responseJson = $this->httpClient->get("/api/v2/departments/$departmentId")
                ->getBody()
                ->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($response->department)) {
                throw new FreshserviceException('Department is missing in response');
            }

            return Department::fillFromObject($response->department);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return null;
        } catch (DepartmentException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getDepartmentByName(string $departmentName): ?Department
    {
        try {
            $responseJson = $this->httpClient->get("/api/v2/departments?query=\"name:'$departmentName'\"")
                ->getBody()
                ->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);
            if (!isset($response->departments)) {
                throw new FreshserviceException('Departments are missing in response');
            }
            if (empty($response->departments[0])) {
                throw new FreshserviceException('Department not found');
            }

            return Department::fillFromObject($response->departments[0]);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return null;
        } catch (DepartmentException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function createRequester(RequesterRequest $requester): Requester
    {
        try {
            $responseJson = $this->httpClient->post('/api/v2/requesters', [
                'json' => $requester->toArray(),
            ])->getBody()->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($response->requester)) {
                throw new FreshserviceException('Requester is missing in response');
            }

            return Requester::fillFromObject($response->requester);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (RequesterRequestExceptions | RequesterException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function updateRequester(int $requesterId, RequesterRequest $requester): Requester
    {
        try {
            $responseJson = $this->httpClient->put("/api/v2/requesters/$requesterId", [
                'json' => $requester->toArray(),
            ])->getBody()->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($response->requester)) {
                throw new FreshserviceException('Requester is missing in response');
            }

            return Requester::fillFromObject($response->requester);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            throw new FreshserviceException($responseBody);
        } catch (RequesterRequestExceptions | RequesterException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }

    /**
     * @throws FreshserviceException
     */
    public function getRequesterByEmail(string $requesterEmail): ?Requester
    {
        try {
            $responseJson = $this->httpClient->get("/api/v2/requesters?query=\"primary_email:'$requesterEmail'\"")
                ->getBody()
                ->getContents();

            $response = json_decode($responseJson, false, 512, JSON_THROW_ON_ERROR);

            if (!isset($response->requesters)) {
                throw new FreshserviceException('Wrong JSON response');
            }

            if (empty($response->requesters[0])) {
                throw new FreshserviceException('Requester not found');
            }

            return Requester::fillFromObject($response->requesters[0]);
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                throw new FreshserviceException($e->getMessage());
            }

            return null;
        } catch (RequesterException | JsonException $e) {
            throw new FreshserviceException($e->getMessage());
        }
    }
}
