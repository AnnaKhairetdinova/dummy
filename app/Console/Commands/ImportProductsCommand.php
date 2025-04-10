<?php

namespace App\Console\Commands;

use App\DTO\ProductData;
use App\Repositories\ProductRepository;
use App\Services\DummyJsonService;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products
                            {--chunk=50 : Размер чанка}
                            {--search=iPhone : Поисковый запрос}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт товаров из dummy';

    public function __construct(
        protected DummyJsonService $apiService,
        protected ProductRepository $repository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $chunkSize = (int) $this->option('chunk');
        $search = $this->option('search');

        $this->info('Начало импорта товаров из dummy');
        $this->info("Поисковый запрос: {$search}");
        $this->info("Размер чанка: {$chunkSize}");

        try {
            $res = $this->apiService->searchProducts($search, 1);
            $total = $res['total'] ?? 0;

            if ($total === 0) {
                $this->error("Не найдено товаров по запросу '{$search}'");
                return CommandAlias::FAILURE;
            }

            $this->info("Всего найдено товаров: {$total}");

            $progressBar = $this->output->createProgressBar($total);
            $progressBar->start();

            $totalImported = 0;
            $totalSkipped = 0;
            $totalErrors = 0;
            $hasMoreProducts = true;

            for ($skip = 0; $skip < $total && $hasMoreProducts; $skip += $chunkSize) {
                try {
                    $response = $this->apiService->searchProducts($search, $chunkSize, $skip);
                    $products = $response['products'] ?? [];

                    if (empty($products)) {
                        $this->newLine();
                        $this->warn("Продуктов не найдено");
                        $hasMoreProducts = false;
                        break;
                    }

                    $batchImported = 0;
                    $batchSkipped = 0;
                    $batchErrors = 0;

                    foreach ($products as $productData) {
                        try {
                            $existingProduct = $this->repository->findByExternalId($productData['id']);

                            if ($existingProduct) {
                                $totalSkipped++;
                                $batchSkipped++;
                                $progressBar->advance();
                                continue;
                            }

                            $dto = ProductData::fromArray($productData);
                            $this->repository->create($dto->toDB());

                            $totalImported++;
                            $batchImported++;
                            $progressBar->advance();
                        } catch (Exception $e) {
                            $totalErrors++;
                            $batchErrors++;
                            $progressBar->advance();
                        }
                    }

                    if ($chunkSize > 10) {
                        $this->newLine();
                        $this->info(sprintf(
                            "Обработано %d-%d из %d: импортировано %d, пропущено %d, ошибок %d",
                            $skip + 1,
                            min($skip + $chunkSize, $total),
                            $total,
                            $batchImported,
                            $batchSkipped,
                            $batchErrors
                        ));
                    }
                } catch (Exception $e) {
                    $this->newLine();
                    $this->error("Ошибка при получении товаров (skip={$skip}, limit={$chunkSize}): " . $e->getMessage());
                    $totalErrors += min($chunkSize, $total - ($totalImported + $totalSkipped + $totalErrors));
                    $progressBar->advance(min($chunkSize, $total - $progressBar->getProgress()));
                }
            }

            $progressBar->finish();

            $this->newLine(2);
            $this->info("Конец");
            $this->info("Всего: {$totalImported}");
            $this->info("Пропущено: {$totalSkipped}");
            $this->info("Ошибок: {$totalErrors}");

            return CommandAlias::SUCCESS;
        } catch (Exception $e) {
            $this->error("Произошла ошибка при выполнении команды: " . $e->getMessage());
            return CommandAlias::FAILURE;
        }
    }
}
