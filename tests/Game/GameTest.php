<?php

namespace App\Tests\Game;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GameTest extends ApiTestCase
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
    }

    private function createGame(string $playerName = ''): array
    {
        $payload = [];
        if($playerName){
            $payload['name'] = $playerName;
        }
        $response = $this->client->request('POST', '/api/games', [
            'json' => $payload
        ]);

        $game = json_decode($response->getContent(), true);

        $this->assertResponseStatusCodeSame(201);
        $this->assertNotEmpty($game['@id']);

        return $game;
    }

    private function joinGame(string $gameIri, string $playerName): array
    {
        $response = $this->client->request('POST', '/api/players', [
            'json' => [
                'name' => $playerName,
                'game' => $gameIri
            ]
        ]);

        $player = json_decode($response->getContent(), true);

        $this->assertResponseStatusCodeSame(201);
        $this->assertNotEmpty($player['@id']);

        return $player;
    }

    private function startRound(string $gameIri): array
    {
        $response = $this->client->request('POST', '/api/rounds', [
            'json' => [
                'game' => $gameIri
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        return json_decode($response->getContent(), true);
    }

    private function get(string $entityIri): array
    {
        $response = $this->client->request('GET', $entityIri);

        $this->assertResponseStatusCodeSame(200);

        return json_decode($response->getContent(), true);
    }

    private function getPlayer(string $playerIri): array
    {
        return $this->get($playerIri);
    }

    private function getGame(string $gameIri): array
    {
        return $this->get($gameIri);
    }

    private function playCard(string $roundIri, string $cardIri, string $playerIri)
    {
        $response = $this->client->request('POST', '/api/round_cards', [
            'json' => [
                'round' => $roundIri,
                'card' => $cardIri,
                'player' => $playerIri
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        return json_decode($response->getContent(), true);
    }

    private function finishRound(string $roundIri, string $playerIri)
    {
        $this->client->request('PUT', $roundIri, [
            'json' => [
                'winner' => $playerIri,
                'status' => 'finished'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testGame()
    {
        $questionCards = [];
        $answerCards = [];
        $playerName = 'Player1';
        $game = $this->createGame($playerName);

        $player2 = $this->joinGame($game['@id'], 'Player2');
        $player3 = $this->joinGame($game['@id'], 'Player3');

        $game = $this->getGame($game['@id']);

        $this->assertIsArray($game['players']);
        $this->assertCount(3, $game['players']);

        $player1 = $game['players'][0];
        $this->assertEquals($player1['name'], $playerName);

        $roundsToPlay = 20;
        echo PHP_EOL;
        for($i = 0; $i < $roundsToPlay; $i++){
            echo 'round: '.($i+1).PHP_EOL;
            $roundCards = [];
            $round = $this->startRound($game['@id']);

            $this->assertNotEmpty($round['questionCard']);
            echo 'question: '.$round['questionCard']['image'].PHP_EOL;

            $this->assertFalse(in_array($round['questionCard']['image'], $questionCards));

            $questionCards[] = $round['questionCard']['image'];

            foreach($game['players'] as $gamePlayer){
                $player = $this->getPlayer($gamePlayer['@id']);
                $this->assertCount(10, $player['cards']);

                $cards = $player['cards'];
                foreach($cards as $card){
                    $roundCards[] = $card['card']['@id'];
                }
//                echo 'cards count: '.count($roundCards).PHP_EOL;
                $roundCards = array_unique($roundCards);
//                echo 'unique cards count: '.count($roundCards).PHP_EOL;

                for($j = 0; $j < $round['questionCard']['answerCount']; $j++){
                    $card = array_shift($cards);
                    $playedCard = $this->playCard($round['@id'], $card['card']['@id'], $player['@id']);
                    $answerCards[] = $card['card']['@id'];
                }
            }

            $this->assertCount(30, $roundCards);
            $this->finishRound($round['@id'], $player1['@id']);
        }

        $game = $this->getGame($game['@id']);
        $player1Score = null;
        foreach($game['scores'] as $score){
            if($score['player'] == $playerName){
                $player1Score = $score['score'];
            }
        }
        $this->assertEquals($roundsToPlay, $player1Score);

        $questionCards = array_unique($questionCards);
        $this->assertCount($roundsToPlay, $questionCards);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager);
        unset($this->client);
    }
}