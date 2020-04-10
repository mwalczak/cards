<?php


namespace App\Entity;


use App\Exception\BadCardTypeException;

class CardFactory
{
    public static function create(string $type): CardInterface
    {
        switch($type){
            case 'white':
                return new AnswerCard();
            case 'black':
                return new QuestionCard();
        }

        throw new BadCardTypeException($type);
    }
}