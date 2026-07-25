<?php

namespace Database\Seeders;

use App\Domain\Marketplace\Models\Plugin;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    public function run(): void
    {
        $plugins = [
            [
                'name' => 'GitHub Integration',
                'slug' => 'github-integration',
                'description' => 'Sync PRs, issues, and commits with your workspace.',
                'version' => '2.1.0',
                'author' => 'Forge AI',
                'category' => 'tool',
                'icon' => 'git-pull-request',
                'is_official' => true,
                'tags' => ['source-control', 'github', 'devops'],
            ],
            [
                'name' => 'Slack Notifier',
                'slug' => 'slack-notifier',
                'description' => 'Receive agent session updates and alerts in Slack.',
                'version' => '1.3.0',
                'author' => 'Forge AI',
                'category' => 'extension',
                'icon' => 'message-square',
                'is_official' => true,
                'tags' => ['notifications', 'slack', 'messaging'],
            ],
            [
                'name' => 'Web Scraper Tool',
                'slug' => 'web-scraper',
                'description' => 'Extract and index web page content for knowledge base.',
                'version' => '1.0.0',
                'author' => 'Forge AI',
                'category' => 'tool',
                'icon' => 'globe',
                'is_official' => true,
                'tags' => ['scraping', 'web', 'knowledge'],
            ],
            [
                'name' => 'Code Analyzer',
                'slug' => 'code-analyzer',
                'description' => 'Automated code review and static analysis for PRs.',
                'version' => '1.2.0',
                'author' => 'Community',
                'category' => 'tool',
                'icon' => 'code',
                'is_official' => false,
                'tags' => ['code-review', 'analysis', 'static-analysis'],
            ],
            [
                'name' => 'Custom MCP Server',
                'slug' => 'custom-mcp-server',
                'description' => 'Extend agents with custom Model Context Protocol tools.',
                'version' => '1.0.0',
                'author' => 'Forge AI',
                'category' => 'extension',
                'icon' => 'puzzle',
                'is_official' => true,
                'tags' => ['mcp', 'tools', 'protocol'],
            ],
            [
                'name' => 'Prompt Templates',
                'slug' => 'prompt-templates',
                'description' => 'Community-curated prompt templates for common tasks.',
                'version' => '1.1.0',
                'author' => 'Community',
                'category' => 'template',
                'icon' => 'file-text',
                'is_official' => false,
                'tags' => ['prompts', 'templates', 'community'],
            ],
        ];

        foreach ($plugins as $plugin) {
            Plugin::query()->firstOrCreate(
                ['slug' => $plugin['slug']],
                $plugin,
            );
        }
    }
}
