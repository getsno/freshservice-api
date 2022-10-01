<?php

namespace Gets\Freshservice\Entities;

use Gets\Freshservice\Exceptions\ConversationException;

class Conversation
{
    private $id;
    private $body;
    private $bodyText;
    private $createdAt;
    private $updatedAt;
    private $userId;
    private $fromEmail;
    private $attachments = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getBodyText(): string
    {
        return $this->bodyText;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setBodyText(string $bodyText): self
    {
        $this->bodyText = $bodyText;

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

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setFromEmail(?string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function toArray(): array
    {
        $conversation = [
            'id'         => $this->getId(),
            'body'       => $this->getBody(),
            'body_text'  => $this->getBodyText(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'user_id'    => $this->getUserId(),
            'from_email' => $this->getFromEmail(),
        ];

        $conversation['attachments'] = [];
        foreach ($this->getAttachments() as $attachment) {
            $conversation['attachments'][] = $attachment->toArray();
        }

        return $conversation;
    }

    /**
     * @throws ConversationException
     */
    public static function fillFromObject($object): self
    {
        try {
            $conversation = new self();
            $conversation->setId($object->id)
                ->setBody($object->body)
                ->setBodyText($object->body_text)
                ->setCreatedAt($object->created_at)
                ->setUpdatedAt($object->updated_at)
                ->setUserId($object->user_id)
                ->setFromEmail($object->from_email);

            if (!empty($object->attachments)) {
                $attachments = [];
                foreach ($object->attachments as $attachment) {
                    $attachments[] = Attachment::fillFromObject($attachment);
                }
                $conversation->setAttachments($attachments);
            }

            return $conversation;
        } catch (\Exception $e) {
            throw new ConversationException($e->getMessage());
        }
    }
}
