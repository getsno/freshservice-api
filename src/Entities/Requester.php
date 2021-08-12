<?php

namespace Gets\Freshservice\Entities;

use Gets\Freshservice\Exceptions\RequesterException;

class Requester
{
    private $id;
    private $firstName;
    private $lastName;
    private $jobTitle;
    private $primaryEmail;
    private $workPhoneNumber;
    private $mobilePhoneNumber;
    private $departmentIds = [];

    public function getId(): int
    {
        return $this->id;
    }

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

    public function getPrimaryEmail(): string
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

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function setPrimaryEmail(string $primaryEmail): self
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

    public function toArray(): array
    {
        return [
            'id'                  => $this->getId(),
            'first_name'          => $this->getFirstName(),
            'last_name'           => $this->getLastName(),
            'job_title'           => $this->getJobTitle(),
            'primary_email'       => $this->getPrimaryEmail(),
            'work_phone_number'   => $this->getWorkPhoneNumber(),
            'mobile_phone_number' => $this->getMobilePhoneNumber(),
            'department_ids'      => $this->getDepartmentIds()
        ];
    }

    /**
     * @throws RequesterException
     */
    public static function fillFromObject($object): self
    {
        try {
            $requester = new self();
            $requester->setId($object->id)
                ->setFirstName($object->first_name)
                ->setLastName($object->last_name)
                ->setPrimaryEmail($object->primary_email)
                ->setWorkPhoneNumber($object->work_phone_number)
                ->setMobilePhoneNumber($object->mobile_phone_number)
                ->setDepartmentIds($object->department_ids);

            return $requester;
        } catch (\Exception $e) {
            throw new RequesterException($e->getMessage());
        }
    }
}
