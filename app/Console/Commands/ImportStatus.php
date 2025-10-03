<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:status 
                          {--clear : Clear the progress tracking file}
                          {--stats : Show database statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check import status and manage progress tracking';

    /**
     * Progress tracking file
     */
    private $progressFile = 'import_users_progress.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            $this->clearProgress();
            return 0;
        }

        if ($this->option('stats')) {
            $this->showDatabaseStats();
            return 0;
        }

        $this->showImportStatus();
        return 0;
    }

    /**
     * Show current import status
     */
    private function showImportStatus(): void
    {
        $progressPath = storage_path("app/{$this->progressFile}");
        
        if (!File::exists($progressPath)) {
            $this->info("No import progress found.");
            $this->info("Use 'php artisan import:users-csv' to start a new import.");
            return;
        }

        $progress = json_decode(File::get($progressPath), true);
        
        $this->info("=== Import Status ===");
        $this->info("File: {$progress['file_name']}");
        $this->info("Last processed line: {$progress['last_processed_line']}");
        $this->info("Total processed: {$progress['total_processed']}");
        $this->info("Last success: {$progress['last_success_datetime']}");
        
        // Calculate time since last update
        $lastUpdate = Carbon::parse($progress['last_success_datetime']);
        $timeDiff = $lastUpdate->diffForHumans();
        $this->info("Time since last update: {$timeDiff}");
        
        $this->newLine();
        $this->info("To resume import, run: php artisan import:users-csv --resume");
        $this->info("To start fresh, run: php artisan import:status --clear");
    }

    /**
     * Show database statistics
     */
    private function showDatabaseStats(): void
    {
        try {
            $totalUsers = DB::table('users')->count();
            $usersToday = DB::table('users')
                ->whereDate('created_at', Carbon::today())
                ->count();
            
            $recentUsers = DB::table('users')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['user_id', 'email', 'created_at']);

            $this->info("=== Database Statistics ===");
            $this->info("Total users in database: {$totalUsers}");
            $this->info("Users created today: {$usersToday}");
            
            if ($recentUsers->count() > 0) {
                $this->newLine();
                $this->info("Recent users:");
                $this->table(
                    ['User ID', 'Email', 'Created At'],
                    $recentUsers->map(function ($user) {
                        return [
                            $user->user_id,
                            $user->email ?: 'N/A',
                            $user->created_at
                        ];
                    })->toArray()
                );
            }

        } catch (\Exception $e) {
            $this->error("Failed to get database statistics: " . $e->getMessage());
        }
    }

    /**
     * Clear progress tracking file
     */
    private function clearProgress(): void
    {
        $progressPath = storage_path("app/{$this->progressFile}");
        
        if (File::exists($progressPath)) {
            File::delete($progressPath);
            $this->info("Progress tracking file cleared successfully.");
        } else {
            $this->info("No progress file found to clear.");
        }
    }
}