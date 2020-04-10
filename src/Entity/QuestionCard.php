<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionCardRepository")
 */
class QuestionCard extends AbstractCard implements CardInterface
{
    /**
     * @ORM\Column(type="integer")
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
