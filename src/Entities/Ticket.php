<?php

namespace Gets\Freshservice\Entities;

use Gets\Freshservice\Exceptions\TicketException;

class Ticket
{
    public const SOURCE_TYPE_EMAIL = 1;
    public const SOURCE_TYPE_PORTAL = 2;
    public const SOURCE_TYPE_PHONE = 3;
    public const SOURCE_TYPE_CHAT = 4;
    public const SOURCE_TYPE_FEEDBACK_WIDGET = 5;
    public const SOURCE_TYPE_YAMMER = 6;
    public const SOURCE_TYPE_AWS_CLOUDWATCH = 7;
    public const SOURCE_TYPE_PAGERDUTY = 8;
    public const SOURCE_TYPE_WALKUP = 9;
    public const SOURCE_TYPE_SLACK = 10;

    public static function getAvailableSourceTypes(): array
    {
        return [
            self::SOURCE_TYPE_EMAIL           => 'Email',
            self::SOURCE_TYPE_PORTAL          => 'Portal',
            self::SOURCE_TYPE_PHONE           => 'Phone',
            self::SOURCE_TYPE_CHAT            => 'Chat',
            self::SOURCE_TYPE_FEEDBACK_WIDGET => 'Feedback widget',
            self::SOURCE_TYPE_YAMMER          => 'Yammer',
            self::SOURCE_TYPE_AWS_CLOUDWATCH  => 'AWS Cloudwatch',
            self::SOURCE_TYPE_PAGERDUTY       => 'Pagerduty',
            self::SOURCE_TYPE_WALKUP          => 'Walkup',
            self::SOURCE_TYPE_SLACK           => 'Slack',
        ];
    }

    public const STATUS_OPEN = 2;
    public const STATUS_PENDING = 3;
    public const STATUS_RESOLVED = 4;
    public const STATUS_CLOSED = 5;

    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_OPEN     => 'Open',
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED   => 'Closed',
        ];
    }

    public const PRIORITY_LOW = 1;
    public const PRIORITY_MEDIUM = 2;
    public const PRIORITY_HIGH = 3;
    public const PRIORITY_URGENT = 4;

    public static function getAvailablePriorities(): array
    {
        return [
            self::PRIORITY_LOW    => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH   => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    // Unique ID of the ticket.
    private $id;

    /*
     * User ID of the requester.
     * For existing contacts, the requester_id can be passed instead of the requester's email.
     */
    private $requesterId;

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

    /*
     * Helps categorize the ticket according to the different kinds of issues your support team deals with.
     * The default Value is incident. * As of now, API v2 supports only type ‘incident’
     */
    private $type;

    // Subject of the ticket. The default value is null.
    private $subject;

    // HTML content of the ticket.
    private $description;

    // Content of the ticket in plain text.
    private $descriptionText;

    // Department ID of the requester.
    private $departmentId;

    //Key value pairs containing the names and values of custom fields.
    private $customFields = [];

    /*
     * Set to true if the ticket has been deleted/trashed.
     * Deleted tickets will not be displayed in any views except the "deleted" filter.
     */
    private $deleted = false;

    // Ticket creation timestamp.
    private $createdAt;

    // Ticket updated timestamp.
    private $updatedAt;

    // Ticket attachments. The total size of these attachments cannot exceed 15 MB
    private $attachments = [];

    private $conversations = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getRequesterId(): int
    {
        return $this->requesterId;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDescriptionText(): ?string
    {
        return $this->descriptionText;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function getConversations(): array
    {
        return $this->conversations;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function setRequesterId(int $requesterId): self
    {
        $this->requesterId = $requesterId;
        
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
     * @throws TicketException
     */
    public function setStatus(int $status): self
    {
        if (!array_key_exists($status, self::getAvailableStatuses())) {
            throw new TicketException('Unsupported status');
        }

        $this->status = $status;
        
        return $this;
    }

    /**
     * @throws TicketException
     */
    public function setPriority(int $priority): self
    {
        if (!array_key_exists($priority, self::getAvailablePriorities())) {
            throw new TicketException('Unsupported priority');
        }

        $this->priority = $priority;
        
        return $this;
    }

    /**
     * @throws TicketException
     */
    public function setSource(int $source): self
    {
        if (!array_key_exists($source, self::getAvailableSourceTypes())) {
            throw new TicketException('Unsupported source type');
        }

        $this->source = $source;
        
        return $this;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        
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

    public function setDescriptionText(?string $descriptionText): self
    {
        $this->descriptionText = $descriptionText;
        
        return $this;
    }

    public function setDepartmentId(?int $departmentId): self
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    public function setCustomFields(array $customFields): self
    {
        $this->customFields = $customFields;

        return $this;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setUpdatedAt($updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function setConversations(array $conversations): self
    {
        $this->conversations = $conversations;

        return $this;
    }

    public function toArray(): array
    {
        $ticket = [
            'id'               => $this->getId(),
            'requester_id'     => $this->getRequesterId(),
            'email'            => $this->getEmail(),
            'phone'            => $this->getPhone(),
            'status'           => $this->getStatus(),
            'priority'         => $this->getPriority(),
            'source'           => $this->getSource(),
            'type'             => $this->getType(),
            'subject'          => $this->getSubject(),
            'description'      => $this->getDescription(),
            'description_text' => $this->getDescriptionText(),
            'department_id'    => $this->getDepartmentId(),
            'deleted'          => $this->isDeleted(),
            'created_at'       => $this->getCreatedAt(),
            'updated_at'       => $this->getUpdatedAt(),
        ];

        $ticket['attachments'] = [];
        foreach ($this->getAttachments() as $attachment) {
            $ticket['attachments'][] = $attachment->toArray();
        }

        $ticket['conversations'] = [];
        foreach ($this->getConversations() as $conversation) {
            $ticket['conversations'][] = $conversation->toArray();
        }

        return $ticket;
    }

    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(),  JSON_THROW_ON_ERROR);
    }

    /**
     * @throws TicketException
     */
    public static function fillFromObject($object): self
    {
        try {
            $ticket = new self();
            $ticket->setId($object->id)
                ->setRequesterId($object->requester_id)
                ->setStatus($object->status)
                ->setPriority($object->priority)
                ->setSource($object->source)
                ->setSubject($object->subject)
                ->setDescription($object->description)
                ->setDescriptionText($object->description_text)
                ->setDepartmentId($object->department_id)
                ->setCreatedAt($object->created_at)
                ->setUpdatedAt($object->updated_at);

            if (!empty($object->attachments)) {
                $attachments = [];
                foreach ($object->attachments as $attachment) {
                    $attachments[] = Attachment::fillFromObject($attachment);
                }
                $ticket->setAttachments($attachments);
            }

            if (!empty($object->conversations)) {
                $conversations = [];
                foreach ($object->conversations as $conversation) {
                    $conversations[] = Conversation::fillFromObject($conversation);
                }
                $ticket->setConversations($conversations);
            }

            return $ticket;
        } catch (\Exception $e) {
            throw new TicketException($e->getMessage());
        }
    }
}
