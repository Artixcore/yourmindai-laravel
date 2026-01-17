<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class WebhookController extends Controller
{
    /**
     * Handle deployment webhook from GitHub/GitLab
     * 
     * POST /api/webhook/deploy
     */
    public function deploy(Request $request)
    {
        $webhookSecret = env('WEBHOOK_SECRET');
        
        // Verify webhook secret if configured
        if ($webhookSecret) {
            $signature = $request->header('X-Hub-Signature-256') ?? $request->header('X-Hub-Signature');
            
            if (!$signature) {
                Log::warning('Webhook called without signature');
                return response()->json(['error' => 'Missing signature'], 401);
            }
            
            // For GitHub: X-Hub-Signature-256 uses sha256
            if ($request->header('X-Hub-Signature-256')) {
                $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $webhookSecret);
                if (!hash_equals($expectedSignature, $signature)) {
                    Log::warning('Webhook signature verification failed');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            } else {
                // For GitLab or older GitHub: X-Hub-Signature uses sha1
                $expectedSignature = 'sha1=' . hash_hmac('sha1', $request->getContent(), $webhookSecret);
                if (!hash_equals($expectedSignature, $signature)) {
                    Log::warning('Webhook signature verification failed');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }
        }
        
        // Check if this is a push event
        $event = $request->header('X-GitHub-Event') ?? $request->header('X-Gitlab-Event');
        $payload = $request->all();
        
        // For GitHub
        if ($event === 'push' || ($request->header('X-GitHub-Event') === 'push')) {
            $ref = $payload['ref'] ?? '';
            // Only deploy on push to main/master branch
            if (!str_ends_with($ref, '/main') && !str_ends_with($ref, '/master')) {
                Log::info('Webhook received for non-main branch, skipping deployment', ['ref' => $ref]);
                return response()->json(['message' => 'Skipped: not main/master branch'], 200);
            }
        }
        
        // For GitLab
        if ($event === 'Push Hook' || ($request->header('X-Gitlab-Event') === 'Push Hook')) {
            $ref = $payload['ref'] ?? '';
            if ($ref !== 'refs/heads/main' && $ref !== 'refs/heads/master') {
                Log::info('Webhook received for non-main branch, skipping deployment', ['ref' => $ref]);
                return response()->json(['message' => 'Skipped: not main/master branch'], 200);
            }
        }
        
        Log::info('Webhook triggered deployment', [
            'event' => $event,
            'ref' => $ref ?? 'unknown',
            'commit' => $payload['head_commit']['id'] ?? $payload['commits'][0]['id'] ?? 'unknown'
        ]);
        
        // Execute deployment script in background
        $deployScript = base_path('deploy.sh');
        
        if (!file_exists($deployScript)) {
            Log::error('Deployment script not found', ['path' => $deployScript]);
            return response()->json(['error' => 'Deployment script not found'], 500);
        }
        
        // Make script executable
        chmod($deployScript, 0755);
        
        // Run deployment in background
        $command = "bash {$deployScript} >> " . storage_path('logs/webhook-deploy.log') . " 2>&1 &";
        exec($command);
        
        return response()->json([
            'message' => 'Deployment triggered',
            'status' => 'processing'
        ], 202);
    }
}
