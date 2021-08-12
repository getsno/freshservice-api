<?php

namespace Gets\Freshservice\Requests;


use Gets\Freshservice\Exceptions\DepartmentRequestException;

class DepartmentRequest
{
    private $id;
    private $name;
    private $description;
    private $headUserId;
    private $primeUserId;
    private $domains;
    private $customFields;

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

    /**
     * @throws DepartmentRequestException
     */
    public function checkRequest(): void
    {
        if (empty($this->name)) {
            throw new DepartmentRequestException('Department name is required');
        }
    }

    /**
     * @throws DepartmentRequestException
     */
    public function toArray(): array
    {
        $this->checkRequest();

        $department =  [
            'name' => $this->getName()
        ];

        if ($this->getId() !== null) {
            $department['id'] = $this->getId();
        }

        if ($this->getDescription() !== null) {
            $department['description'] = $this->getDescription();
        }

        if ($this->getHeadUserId() !== null) {
            $department['head_user_id'] = $this->getHeadUserId();
        }

        if ($this->getPrimeUserId() !== null) {
            $department['prime_user_id'] = $this->getPrimeUserId();
        }

        if ($this->getDomains() !== null) {
            $department['domains'] = $this->getDomains();
        }

        if ($this->getCustomFields() !== null) {
            $department['custom_fields'] = $this->getCustomFields();
        }

        return $department;
    }
}
