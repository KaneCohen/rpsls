<?php
namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayCommand extends Command
{
    const ROCK     = 0;
    const PAPER    = 1;
    const SCISSORS = 2;
    const LIZARD   = 3;
    const SPOCK    = 4;

    /**
     * Moves used in validation.
     *
     * @var array
     */
    private $moves = [
        self::ROCK     => 'rock',
        self::PAPER    => 'paper',
        self::SCISSORS => 'scissors',
        self::LIZARD   => 'lizard',
        self::SPOCK    => 'spock'
    ];

    /**
     * Hard coded winners and win scenarios.
     *
     * @var array
     */
    private $winners = [
        self::ROCK     => [
            self::SCISSORS => 'Rock crushes scissors',
            self::LIZARD   => 'Rock crushes lizard'
        ],
        self::PAPER    => [
            self::ROCK  => 'Paper covers rock',
            self::SPOCK => 'Paper disproves Spock'
        ],
        self::SCISSORS => [
            self::PAPER  => 'Scissors cut paper',
            self::LIZARD => 'Scissors decapitate lizard'
        ],
        self::LIZARD   => [
            self::SPOCK => 'Lizard poisons Spock',
            self::PAPER => 'Lizard eats paper'
        ],
        self::SPOCK    => [
            self::SCISSORS => 'Spock smashes scissors',
            self::ROCK     => 'Spock vaporizes rock'
        ]
    ];

    /**
     * Command configuration.
     */
    protected function configure()
    {
        $this->setName('play')
            ->setDescription('Play a game of Rock-Paper-Scissors-Lizard-Spock');
    }

    /**
     * Command execution.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initial messages.
        $output->writeln('<info>Game begins!</info>');
        $output->writeln('');
        $output->writeln('<comment>Possible moves: Rock, Paper, Scissors, Lizard, Spock</comment>');

        // Validation
        $helper = $this->getHelper('question');
        $question = new Question('Your move: ');
        $question->setMaxAttempts(3);
        $question->setValidator(function($answer) use ($output) {
            $move = array_search(strtolower(trim($answer)), $this->moves);
            if ($move === false) {
                $output->writeln('<error></error>');
                throw new \RuntimeException('Wrong move, man. Try again.');
            }
            return $move;
        });

        // Get a move as an integer.
        $move = $helper->ask($input, $output, $question);
        $gameMove = $this->pickMove($move);

        $output->writeln('I played: ' . $this->moveToString($gameMove));
        $output->writeln('Winner: ' . $this->determineWinner($move, $gameMove));
    }

    /**
     * Converts move integer to string.
     *
     * @param int $move
     * @return string
     */
    protected function moveToString($move)
    {
        return ucfirst($this->moves[$move]);
    }

    /**
     * Returns random integer as one of the valid move.
     *
     * @param $move
     * @return int
     */
    protected function pickMove($move)
    {
        return array_rand($this->moves);
    }

    /**
     * Returns string marking outcome of the game.
     *
     * @param $playerMove
     * @param $gameMove
     * @return string
     */
    protected function determineWinner($playerMove, $gameMove)
    {
        $userWon = isset($this->winners[$playerMove][$gameMove]);
        if ($userWon) return $this->winners[$playerMove][$gameMove] . ', <info>you win</info>!';

        $gameWon = isset($this->winners[$gameMove][$playerMove]);
        if ($gameWon) return $this->winners[$gameMove][$playerMove] . ', <error>you lost</error>!';

        return 'Tie!';
    }
}