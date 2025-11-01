<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\Transcriptions;
use Illuminate\Support\Facades\Log;

class ProcessTranscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The transcription instance.
     * 
     * @var \App\Models\Transcription
     */
    protected $transcription;

    /**
     * Create a new job instance.
     * 
     * @param Transcription $transcription
     * @return void
     */
    public function __construct(Transcriptions $transcription)
    {
        $this->transcription = $transcription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to Processing
            $this->transcription->update([
                'status' => 'processing'
            ]);

            // Simulate API call to transcription service
            $response = $this->callTranscriptionApi($this->transcription->audio_file_path);

            // Update transcription with results 
            $this->transcription->update([
                'transcription' => $response['transcript'],
                'status' => 'completed',
            ]);

            
            
        } catch (\Exception $e) {
            Log::error('Transcription Processing failed: ' . $e->getMessage(), [
                'transcription_id' => $this->transcription->id,
                'error' => $e->getTraceAsString()
            ]); 
            
            $this->transcription->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }


    /**
     *Simulate a transcription API call
    *
    * @param string $audioFilePath
    * @return array
    * @throws \Exception
    */
    public function callTranscriptionApi(string $audioFilePath): array
    {
        // This is a dummy implementation that simulates an API call
        // In a real application, you would make an actual HTTP request to a 
        // For example, using OpenAI's whisper API. Google Speech-to-Text, etc.
        
        // Simulate API processing time
        sleep(2);

        // Simulate a 10% chance of failure for testing purposes
        if (rand(1, 10) === 1) {
            throw new \Exception('Failed to transcribe audio file');
        }

        // Return a dummy response
        return [
            'transcript' => 'This is a dummy transcription',
            'language' => 'en',
            'confidence' => 0.95,
        ];  
    }

}
