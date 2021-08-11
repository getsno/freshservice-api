<?php

namespace Gets\Freshservice\Requests;

use Gets\Freshservice\Entities\Ticket;
use Gets\Freshservice\Exceptions\TicketRequestException;

class TicketRequest
{
    /*
     * User ID of the requester.
     * For existing contacts, the requester_id can be passed instead of the requester's email.
     */
    private $requesterId;

    // Name of the requester
    private $name;

    /*
     * Email address of the requester.
     * If no contact exists with this email address in Freshservice, it will be added as a new contact.
     */
    private $email;

    /*
     * Phone number of the requester.
     * If no contact exists with this phone number in Freshservice, it will be added as a new contact.
     * If the phone number is set and the email address is not, then the name attribute is mandatory.
     */
    private $phone;

    // Status of the ticket.
    private $status;

    // Priority of the ticket.
    private $priority;

    // The channel through which the ticket was created. The default value is 2.
    private $source;

    // Subject of the ticket. The default value is null.
    private $subject;

    // HTML content of the ticket.
    private $description;

    // Department ID of the requester.
    private $departmentId;

    // Ticket attachments. The total size of these attachments cannot exceed 15 MB.
    private $attachments = [];

    public function getRequesterId(): ?int
    {
        return $this->requesterId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getSource(): int
    {
        return $this->source;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setRequesterId(?int $requesterId): self
    {
        $this->requesterId = $requesterId;

        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @throws TicketRequestException
     */
    public function setStatus(int $status): self
    {
        if (!array_key_exists($status, Ticket::getAvailableStatuses())) {
            throw new TicketRequestException('Unsupported status');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @throws TicketRequestException
     */
    public function setPriority(int $priority): self
    {
        if (!array_key_exists($priority, Ticket::getAvailablePriorities())) {
            throw new TicketRequestException('Unsupported priority');
        }

        $this->priority = $priority;

        return $this;
    }

    /**
     * @throws TicketRequestException
     */
    public function setSource(int $source): self
    {
        if (!array_key_exists($source, Ticket::getAvailableSourceTypes())) {
            throw new TicketRequestException('Unsupported source type');
        }

        $this->source = $source;

        return $this;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setDepartmentId(?int $departmentId): self
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function hasAttachments(): bool
    {
        return count($this->attachments) > 0;
    }

    /**
     * @throws TicketRequestException
     */
    public function checkRequest(): void
    {
        if (empty($this->requesterId) && empty($this->email) && (empty($this->phone) && empty($this->name))) {
            throw new TicketRequestException('Requester fields error');
        }

        if (empty($this->status) || empty($this->priority) || empty($this->source)) {
            throw new TicketRequestException('mandatory fields error');
        }
    }

    /**
     * @throws TicketRequestException
     */
    public function toArray(): array
    {
        $this->checkRequest();

        $requester = [
            'name'          => $this->getName(),
            'email'         => $this->getEmail(),
            'phone'         => $this->getPhone(),
            'status'        => $this->getStatus(),
            'priority'      => $this->getPriority(),
            'source'        => $this->getSource(),
            'subject'       => $this->getSubject(),
            'description'   => $this->getDescription(),
        ];

        if ($this->getRequesterId() !== null) {
            $requester['requester_id'] = $this->getRequesterId();
        }

        if ($this->getDepartmentId() !== null) {
            $requester['department_id'] = $this->getDepartmentId();
        }

        return $requester;
    }

    /**
     * @throws \JsonException
     * @throws TicketRequestException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(),  JSON_THROW_ON_ERROR);
    }
}
