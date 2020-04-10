<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={},
 *     itemOperations={
 *          "get"
 *     },
 *     normalizationContext={"groups"={"answer_cards:read"}},
 *     denormalizationContext={"groups"={"answer_cards:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AnswerCardRepository")
 */
class AnswerCard extends AbstractCard implements CardInterface
{

}
