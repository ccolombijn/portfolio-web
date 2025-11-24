<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\LatestProjectTool;
use Laravel\Mcp\Server;

class ProjectServer extends Server
{
    public string $serverName = 'Project Server';

    public string $serverVersion = '0.0.1';

    public string $instructions = 'This server provides project information.';

    public array $tools = [
        LatestProjectTool::class,
    ];

    public array $resources = [
        // ExampleResource::class,
    ];

    public array $prompts = [
        // ExamplePrompt::class,
    ];
}
