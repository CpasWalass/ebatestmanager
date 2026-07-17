<?php

namespace App\Jobs;

use App\Models\AiGenerationRequest;
use App\Services\GeminiTestCaseGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateTestCasesWithAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 150;

    public function __construct(public AiGenerationRequest $generationRequest)
    {
    }

    public function handle(GeminiTestCaseGenerator $generator): void
    {
        $this->generationRequest->update(['status' => 'processing']);

        try {
            $cases = $generator->generate(
                $this->generationRequest->extracted_text,
                $this->generationRequest->template
            );

            $this->generationRequest->update([
                'status' => 'completed',
                'proposed_cases' => $cases,
            ]);
        } catch (Throwable $e) {
            $this->generationRequest->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
