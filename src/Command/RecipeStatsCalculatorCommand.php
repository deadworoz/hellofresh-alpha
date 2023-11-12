<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\RecipeSource\NaiveIterationRecipeSource;
use App\Domain\RecipeStatsCalculator;
use App\Domain\StatsCollector\BusiestPostcodeStatsCollector;
use App\Domain\StatsCollector\CountPerPostcodeAndTimeStatsCollector;
use App\Domain\StatsCollector\MatchByNameStatsCollector;
use App\Domain\StatsCollector\UniqueRecipeStatsCollector;
use App\Domain\TimeRange;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:recipe-stats-calculator',
    description: 'Calculate recipe statistics',
    hidden: false,
)]
class RecipeStatsCalculatorCommand extends Command
{
    private const RECIPE_FILE_OPTION = 'file';
    private const POSTCODE_OPTION = 'postcode';
    private const TIME_WINDOW_OPTION = 'time';
    private const RECIPE_OPTION = 'recipe';

    protected function configure()
    {
        $this->addOption(
            self::RECIPE_FILE_OPTION,
            'f',
            InputOption::VALUE_REQUIRED,
            'custom fixtures',
            'hf_test_calculation_fixtures.json',
        );

        $this->addOption(
            self::POSTCODE_OPTION,
            'p',
            InputOption::VALUE_REQUIRED,
            'custom postcode used to count the number of deliveries',
            '10120',
        );

        $this->addOption(
            self::TIME_WINDOW_OPTION,
            't',
            InputOption::VALUE_REQUIRED,
            'time window to count the number of deliveries to the postcode, e.g. 10AM-3PM',
            '10AM-3PM',
        );

        $this->addOption(
            self::RECIPE_OPTION,
            'r',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'List the recipe names (alphabetically ordered) that contain in their name one of the given words',
            ['Potato', 'Veggie', 'Mushroom'],
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = fopen($input->getOption(self::RECIPE_FILE_OPTION), 'rb');
        if ($file === false) {
            throw new RuntimeException('Unable to open a recipe file');
        }

        try {
            $recipeSource = new NaiveIterationRecipeSource($file);

            $statsCollectors = [
                new UniqueRecipeStatsCollector(),
                new BusiestPostcodeStatsCollector(),
                new CountPerPostcodeAndTimeStatsCollector(
                    postcode: $input->getOption(self::POSTCODE_OPTION),
                    timeRange: new TimeRange($input->getOption(self::TIME_WINDOW_OPTION)),
                ),
                new MatchByNameStatsCollector(
                    wordsToSearch: $input->getOption(self::RECIPE_OPTION),
                ),
            ];
            $calculator = new RecipeStatsCalculator(...$statsCollectors);;

            $stats = $calculator->calculate($recipeSource);
            $output->writeln(json_encode($stats, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } catch (Throwable $throwable) {
            $output->writeln($throwable->getMessage());
            return Command::FAILURE;
        } finally {
            fclose($file);
        }
    }
}
