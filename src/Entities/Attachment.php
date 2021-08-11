<?php

namespace Gets\Freshservice\Entities;

use Gets\Freshservice\Exceptions\AttachmentException;

class Attachment
{
    private $id;
    private $contentType;
    private $size;
    private $name;
    private $attachmentUrl;
    private $createdAt;
    private $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttachmentUrl(): string
    {
        return $this->attachmentUrl;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setAttachmentUrl(string $attachmentUrl): self
    {
        $this->attachmentUrl = $attachmentUrl;

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
            'id'            => $this->getId(),
            'contentType'   => $this->getContentType(),
            'size'          => $this->getSize(),
            'name'          => $this->getName(),
            'attachmentUrl' => $this->getAttachmentUrl(),
            'createdAt'     => $this->getCreatedAt(),
            'updatedAt'     => $this->getUpdatedAt()
        ];
    }

    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(),  JSON_THROW_ON_ERROR);
    }

    /**
     * @throws AttachmentException
     */
    public static function fillFromObject($object): self
    {
        try {
            $attachment = new self();
            $attachment->setId($object->id)
                ->setName($object->name)
                ->setSize($object->size)
                ->setContentType($object->content_type)
                ->setAttachmentUrl($object->attachment_url)
                ->setCreatedAt($object->created_at)
                ->setUpdatedAt($object->updated_at);
            return $attachment;
        } catch (\Exception $e) {
            throw new AttachmentException($e->getMessage());
        }
    }
}
