<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionCardRepository")
 */
class QuestionCard extends AbstractCard implements CardInterface
{
    /**
     * @ORM\Column(type="integer")
     * @Groups({"rounds:read", "players:read", "games:read"})
     */
    private $answerCount = 1;

    public function getAnswerCount(): ?int
    {
        return $this->answerCount;
    }

    public function setAnswerCount(int $answerCount): self
    {
        $this->answerCount = $answerCount;

        return $this;
    }
}
