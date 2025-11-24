<?php

use App\Mcp\Servers\ProjectServer;
use Laravel\Mcp\Server\Facades\Mcp;

// Mcp::web('demo', \App\Mcp\Servers\PublicServer::class); // Available at /mcp/demo
// Mcp::local('demo', \App\Mcp\Servers\LocalServer::class); // Start with ./artisan mcp:start demo
Mcp::web('project', ProjectServer::class); // Available at /mcp/project
