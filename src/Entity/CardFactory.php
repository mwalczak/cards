<?php


namespace App\Entity;


use App\Exception\BadCardTypeException;

class CardFactory
{
    public static function create(string $type): AbstractCard
    {
        switch($type){
            case 'answers':
                return new AnswerCard();
            case 'questions':
                return new QuestionCard();
        }

        throw new BadCardTypeException($type);
    }
}