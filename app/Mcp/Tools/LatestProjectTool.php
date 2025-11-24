<?php

namespace App\Mcp\Tools;

use Generator;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\ToolResult;
use Laravel\Mcp\Server\Tools\ToolInputSchema;
use App\Contracts\ProjectRepositoryInterface;

class LatestProjectTool extends Tool
{

    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
    ) {}
    /**
     * Get the tool's description.
     */
    public function description(): string
    {
        return 'Fetches the latest projects, optionally filtered by a limit.';
    }

    /**
     * Handle the tool request.
     * @return ToolResult|Generator<ToolNotification|ToolResult>
     */
    public function handle(array $arguments): ToolResult|Generator
    {
        return ToolResult::json([
            'projects' => $this->projectRepository->all(),
        ]);
    }

    /**
     * Get the tool's input schema.
     * @return ToolInputSchema
     */
    public function schema(ToolInputSchema $schema): ToolInputSchema
    {
        return $schema;
    }
}
