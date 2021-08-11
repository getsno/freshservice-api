<?php

namespace Gets\Freshservice\Requests;

use Gets\Freshservice\Exceptions\RequesterRequestExceptions;

class RequesterRequest
{
    private $firstName;
    private $lastName;
    private $jobTitle;
    private $primaryEmail;
    private $workPhoneNumber;
    private $mobilePhoneNumber;
    private $departmentIds = [];

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function getPrimaryEmail(): ?string
    {
        return $this->primaryEmail;
    }

    public function getWorkPhoneNumber(): ?int
    {
        return $this->workPhoneNumber;
    }

    public function getDepartmentIds(): array
    {
        return $this->departmentIds;
    }

    public function getMobilePhoneNumber(): ?int
    {
        return $this->mobilePhoneNumber;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function setPrimaryEmail(?string $primaryEmail): self
    {
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    public function setWorkPhoneNumber(?int $workPhoneNumber): self
    {
        $this->workPhoneNumber = $workPhoneNumber;

        return $this;
    }

    public function setMobilePhoneNumber(?int $mobilePhoneNumber): self
    {
        $this->mobilePhoneNumber = $mobilePhoneNumber;

        return $this;
    }

    public function setDepartmentIds(array $departmentIds): self
    {
        $this->departmentIds = $departmentIds;

        return $this;
    }

    /**
     * @throws RequesterRequestExceptions
     */
    public function checkRequest(): void
    {
        if (empty($this->primaryEmail) && (empty($this->workPhoneNumber) && empty($this->mobilePhoneNumber))) {
            throw new RequesterRequestExceptions('Requester fields error');
        }

        if (empty($this->firstName)) {
            throw new RequesterRequestExceptions('First name is required');
        }
    }

    /**
     * @throws RequesterRequestExceptions
     */
    public function toArray(): array
    {
        $this->checkRequest();

        $requester = [
            'first_name'          => $this->getFirstName(),
            'job_title'           => $this->getJobTitle(),
            'primary_email'       => $this->getPrimaryEmail(),
            'work_phone_number'   => $this->getWorkPhoneNumber(),
            'mobile_phone_number' => $this->getMobilePhoneNumber()
        ];

        if ($this->getLastName() !== null) {
            $requester['last_name'] = $this->getLastName();
        }

        if (!empty($this->getDepartmentIds())) {
            $requester['department_ids'] = $this->getDepartmentIds();
        }

        return $requester;
    }

    /**
     * @throws RequesterRequestExceptions
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(),  JSON_THROW_ON_ERROR);
    }
}
