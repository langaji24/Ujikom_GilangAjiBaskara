<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Illuminate\Support\Str;
use Spatie\ErrorSolutions\Contracts\BaseSolution;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Support\Laravel\LaravelVersion;
use Throwable;

class MissingViteManifestSolutionProvider implements HasSolutionsForThrowable
{
    /** @var array<string, string> */
    protected array $links = [];

    public function __construct()
    {
        $this->links = [
            'Asset bundling with Vite' => 'https://laravel.com/docs/'.LaravelVersion::major().'.x/vite#running-vite',
        ];
    }

    public function canSolve(Throwable $throwable): bool
    {
        return Str::startsWith($throwable->getMessage(), 'Vite manifest not found');
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            $this->getSolution(),
        ];
    }

    public function getSolution(): Solution
    {
        /** @var string */
        $baseCommand = collect([
            'pnpm-lock.yaml' => 'pnpm',
            'yarn.lock' => 'yarn',
        ])->first(fn ($_, $lockfile) => file_exists(base_path($lockfile)), 'npm run');

        return app()->environment('local')
            ? $this->getLocalSolution($baseCommand)
            : $this->getProductionSolution($baseCommand);
    }

    protected function getLocalSolution(string $baseCommand): Solution
    {
        return BaseSolution::create('Start the development server')
            ->setSolutionDescription("Run `{$baseCommand} dev` in your terminal and refresh the page.")
            ->setDocumentationLinks($this->links);
    }

    protected function getProductionSolution(string $baseCommand): Solution
    {
        return BaseSolution::create('Build the production assets')
            ->setSolutionDescription("Run `{$baseCommand} build` in your deployment script.")
            ->setDocumentationLinks($this->links);
    }
}
