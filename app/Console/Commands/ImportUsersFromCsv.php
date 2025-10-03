<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class ImportUsersFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users-csv 
                          {--file=Results.csv : Path to the CSV file} 
                          {--batch-size=500 : Number of records to process in each batch}
                          {--resume : Resume from last successful import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from CSV file into users table with resume capability';

    /**
     * Progress tracking file
     */
    private $progressFile = 'import_users_progress.json';

    /**
     * CSV column mapping to database columns
     */
    private $columnMapping = [
        'User ID' => 'user_id',
        'Password' => 'password',
        'Name' => 'name',
        'Full Name' => 'full_name',
        'User Name' => 'user_name',
        'Email' => 'email',
        'Mobile No' => 'mobile_no',
        'Region Code' => 'region_code',
        'Region Name' => 'region_name',
        'Branch Code' => 'branch_code',
        'Branch Description' => 'branch_description',
        'P_I_N' => 'pin',
        'CompanyName' => 'company_name',
        'Activation Code' => 'activation_code',
        'Expiration Date' => 'expiration_date',
        'PASSDT_EXPIRE' => 'password_expire_date',
        'USER_LOCKED' => 'is_locked',
        'OldPassword' => 'old_password',
        'NewPassword1' => 'new_password_1',
        'NewPassword2' => 'new_password_2',
        'UndecriptedPassword' => 'unencrypted_password',
        'Created Date' => 'created_at'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CSV import process...');
        
        $filePath = base_path($this->option('file'));
        $batchSize = (int) $this->option('batch-size');
        $resume = $this->option('resume');

        // Validate file exists
        if (!File::exists($filePath)) {
            $this->error("CSV file not found: {$filePath}");
            return 1;
        }

        // Load progress if resuming
        $progress = $this->loadProgress();
        $startLine = $resume && $progress ? $progress['last_processed_line'] + 1 : 1;
        
        if ($resume && $progress) {
            $this->info("Resuming import from line {$startLine}");
            $this->info("Last successful import: {$progress['last_success_datetime']}");
            $this->info("Total processed so far: {$progress['total_processed']}");
        }

        try {
            $totalLines = $this->countCsvLines($filePath);
            $this->info("Total lines in CSV: " . ($totalLines - 1) . " (excluding header)");

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                throw new Exception("Cannot open CSV file");
            }

            // Read and validate header
            $header = fgetcsv($handle);
            if (!$this->validateHeader($header)) {
                fclose($handle);
                return 1;
            }

            // Skip to start line if resuming
            $currentLine = 1;
            while ($currentLine < $startLine && !feof($handle)) {
                fgetcsv($handle);
                $currentLine++;
            }

            $processedCount = $progress['total_processed'] ?? 0;
            $batchData = [];
            $batchCount = 0;
            
            $this->info("Processing records in batches of {$batchSize}...");
            
            // Create progress bar
            $progressBar = $this->output->createProgressBar($totalLines - $startLine);
            $progressBar->start();

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    $currentLine++;
                    continue;
                }

                $userData = $this->mapCsvRowToUserData($header, $row);
                
                if ($userData) {
                    $batchData[] = $userData;
                    $batchCount++;

                    // Process batch when batch size is reached
                    if ($batchCount >= $batchSize) {
                        $this->processBatch($batchData);
                        $processedCount += count($batchData);
                        
                        // Save progress
                        $this->saveProgress($currentLine, $processedCount);
                        
                        // Reset batch
                        $batchData = [];
                        $batchCount = 0;
                        
                        $progressBar->advance($batchSize);
                    }
                }
                
                $currentLine++;
            }

            // Process remaining records in the last batch
            if (!empty($batchData)) {
                $this->processBatch($batchData);
                $processedCount += count($batchData);
                $this->saveProgress($currentLine, $processedCount);
                $progressBar->advance(count($batchData));
            }

            fclose($handle);
            $progressBar->finish();
            
            $this->newLine(2);
            $this->info("Import completed successfully!");
            $this->info("Total records processed: {$processedCount}");
            
            // Clear progress file on successful completion
            $this->clearProgress();
            
            return 0;

        } catch (Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            Log::error("CSV Import Error", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Count total lines in CSV file
     */
    private function countCsvLines(string $filePath): int
    {
        $lines = 0;
        $handle = fopen($filePath, 'r');
        
        if ($handle) {
            while (fgets($handle) !== false) {
                $lines++;
            }
            fclose($handle);
        }
        
        return $lines;
    }

    /**
     * Validate CSV header
     */
    private function validateHeader(array $header): bool
    {
        $requiredColumns = ['User ID'];
        
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $header)) {
                $this->error("Required column '{$column}' not found in CSV header");
                return false;
            }
        }
        
        $this->info("CSV header validation passed");
        return true;
    }

    /**
     * Map CSV row to user data array
     */
    private function mapCsvRowToUserData(array $header, array $row): ?array
    {
        // Create associative array from header and row
        $csvData = array_combine($header, array_pad($row, count($header), null));
        
        // Skip if no User ID
        if (empty($csvData['User ID'])) {
            return null;
        }

        $userData = [];
        
        foreach ($this->columnMapping as $csvColumn => $dbColumn) {
            $value = $csvData[$csvColumn] ?? null;
            
            // Handle NULL values from CSV
            if ($value === 'NULL' || $value === '' || $value === null) {
                $userData[$dbColumn] = null;
            } else {
                $userData[$dbColumn] = $this->sanitizeValue($value, $dbColumn);
            }
        }

        // Set timestamps
        $now = Carbon::now();
        if (empty($userData['created_at'])) {
            $userData['created_at'] = $now;
        }
        $userData['updated_at'] = $now;

        return $userData;
    }

    /**
     * Sanitize and format values based on column type
     */
    private function sanitizeValue($value, string $column): mixed
    {
        if ($value === null || $value === 'NULL' || $value === '') {
            return null;
        }

        // Handle datetime fields
        if (in_array($column, ['expiration_date', 'password_expire_date', 'created_at', 'updated_at'])) {
            try {
                return Carbon::parse($value);
            } catch (Exception $e) {
                Log::warning("Invalid date format for column {$column}: {$value}");
                return null;
            }
        }

        // Handle boolean fields
        if ($column === 'is_locked') {
            return (int) filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // Handle string fields with length limits
        $stringLimits = [
            'user_id' => 255,
            'password' => 255,
            'name' => 100,
            'full_name' => 255,
            'user_name' => 255,
            'email' => 100,
            'mobile_no' => 15,
            'region_code' => 20,
            'region_name' => 50,
            'branch_code' => 20,
            'branch_description' => 50,
            'pin' => 255,
            'company_name' => 10,
            'activation_code' => 255,
            'old_password' => 30,
            'new_password_1' => 30,
            'new_password_2' => 30,
            'unencrypted_password' => 255,
            'remember_token' => 100
        ];

        if (isset($stringLimits[$column])) {
            return substr(trim($value), 0, $stringLimits[$column]);
        }

        return trim($value);
    }

    /**
     * Process a batch of user data using optimized bulk operations
     */
    private function processBatch(array $batchData): void
    {
        DB::beginTransaction();
        
        try {
            // Get existing user_ids in this batch
            $userIds = array_column($batchData, 'user_id');
            $existingUserIds = DB::table('users')
                ->whereIn('user_id', $userIds)
                ->pluck('user_id')
                ->toArray();

            $toUpdate = [];
            $toInsert = [];

            foreach ($batchData as $userData) {
                if (in_array($userData['user_id'], $existingUserIds)) {
                    $toUpdate[] = $userData;
                } else {
                    $toInsert[] = $userData;
                }
            }

            // Bulk insert new records
            if (!empty($toInsert)) {
                // Split into smaller chunks to avoid parameter limit
                $chunks = array_chunk($toInsert, 10);
                foreach ($chunks as $chunk) {
                    DB::table('users')->insert($chunk);
                }
            }

            // Update existing records one by one (SQL Server doesn't support bulk update easily)
            foreach ($toUpdate as $userData) {
                DB::table('users')
                    ->where('user_id', $userData['user_id'])
                    ->update(array_diff_key($userData, ['user_id' => null]));
            }
            
            DB::commit();
            
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Batch processing failed: " . $e->getMessage());
        }
    }

    /**
     * Load progress from file
     */
    private function loadProgress(): ?array
    {
        $progressPath = storage_path("app/{$this->progressFile}");
        
        if (File::exists($progressPath)) {
            $content = File::get($progressPath);
            return json_decode($content, true);
        }
        
        return null;
    }

    /**
     * Save progress to file
     */
    private function saveProgress(int $lastLine, int $totalProcessed): void
    {
        $progress = [
            'last_processed_line' => $lastLine,
            'total_processed' => $totalProcessed,
            'last_success_datetime' => Carbon::now()->toDateTimeString(),
            'file_name' => $this->option('file')
        ];
        
        $progressPath = storage_path("app/{$this->progressFile}");
        File::put($progressPath, json_encode($progress, JSON_PRETTY_PRINT));
    }

    /**
     * Clear progress file
     */
    private function clearProgress(): void
    {
        $progressPath = storage_path("app/{$this->progressFile}");
        
        if (File::exists($progressPath)) {
            File::delete($progressPath);
            $this->info("Progress tracking file cleared");
        }
    }
}
