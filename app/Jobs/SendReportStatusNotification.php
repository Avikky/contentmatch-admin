<?php

namespace App\Jobs;

use App\Mail\ReportStatusNotification;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReportStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId,
        public string $action
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Use appserver connection to fetch the report
        $report = Report::on('appserver')
            ->with(['reporter', 'reportable'])
            ->find($this->reportId);

        if (! $report || ! $report->reporter) {
            return;
        }

        // Send email to the reporter
        Mail::to($report->reporter->email)
            ->send(new ReportStatusNotification($report, $report->reporter, $this->action));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure
        Log::error('Failed to send report status notification', [
            'report_id' => $this->reportId,
            'action' => $this->action,
            'error' => $exception->getMessage(),
        ]);
    }
}
