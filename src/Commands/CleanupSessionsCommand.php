<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Models\UserSession;

class CleanupSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-manager:cleanup-sessions 
                            {--days=30 : Sessions older than this many days will be deleted}
                            {--inactive-only : Only delete inactive sessions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old user sessions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');
        $inactiveOnly = $this->option('inactive-only');

        $query = UserSession::where('last_activity', '<', now()->subDays($days));

        if ($inactiveOnly) {
            $query->where('is_active', false);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('No sessions to clean up.');
            return 0;
        }

        if ($this->confirm("This will delete {$count} sessions. Continue?")) {
            $deleted = $query->delete();
            $this->info("Deleted {$deleted} old sessions.");
        } else {
            $this->info('Operation cancelled.');
        }

        return 0;
    }
}