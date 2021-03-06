<?php

namespace Gets\Freshservice\Entities;

use Gets\Freshservice\Exceptions\DepartmentException;

class Department
{
    private $id;
    private $name;
    private $description;
    private $headUserId;
    private $primeUserId;
    private $domains;
    private $customFields;
    private $createdAt;
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getHeadUserId(): ?int
    {
        return $this->headUserId;
    }

    public function getPrimeUserId(): ?int
    {
        return $this->primeUserId;
    }

    public function getDomains(): ?array
    {
        return $this->domains;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setHeadUserId(?int $headUserId): self
    {
        $this->headUserId = $headUserId;

        return $this;
    }

    public function setPrimeUserId(?int $primeUserId): self
    {
        $this->primeUserId = $primeUserId;

        return $this;
    }

    public function setDomains(?array $domains): self
    {
        $this->domains = $domains;

        return $this;
    }

    public function setCustomFields(?array $customFields): self
    {
        $this->customFields = $customFields;

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

    public function toArray(): array
    {
        return [
            'id'           => $this->getId(),
            'name'         => $this->getName(),
            'description'  => $this->getDescription(),
            'headUserId'   => $this->getHeadUserId(),
            'primeUserId'  => $this->getPrimeUserId(),
            'domains'      => $this->getDomains(),
            'customFields' => $this->getCustomFields(),
            'createdAt'    => $this->getCreatedAt(),
            'updatedAt'    => $this->getUpdatedAt()
        ];
    }

    /**
     * @throws DepartmentException
     */
    public static function fillFromObject(object $object): self
    {
        try {
            $ticket = new self();
            $ticket->setId($object->id)
                ->setName($object->name)
                ->setDescription($object->description)
                ->setHeadUserId($object->head_user_id ?? null)
                ->setPrimeUserId($object->primary_user_id ?? null)
                ->setDomains((array) $object->domains)
                ->setCreatedAt($object->created_at)
                ->setUpdatedAt($object->updated_at)
                ->setCustomFields((array)$object->custom_fields);
            return $ticket;
        } catch (\Exception $e) {
            throw new DepartmentException($e->getMessage());
        }
    }
}
