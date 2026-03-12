<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIAssistantController extends Controller
{
    /**
     * Generate quest content based on a topic.
     * For demonstration, this uses a template-based mock AI.
     */
    public function generateQuest(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'difficulty' => 'nullable|string'
        ]);

        $topic = $request->input('topic');
        $difficulty = $request->input('difficulty', 'medium');
        $totalLevels = $request->input('total_levels', 3);

        try {
            $generatedData = $this->callGroqAPI($topic, $difficulty, $totalLevels);
            return response()->json([
                'status' => 'success',
                'data' => $generatedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Groq API Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'The Neural Link was interrupted.'
            ], 500);
        }
    }

    private function callGroqAPI($topic, $difficulty, $totalLevels)
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = "https://api.groq.com/openai/v1/chat/completions";

        $prompt = "You are the 'Neural Quest Forge' in the High Fantasy RPG world of ASIANISTA. 
        Your task is to generate a JSON object for a teacher creating a quest.
        
        Topic: {$topic}
        Difficulty: {$difficulty}
        Total Stages: {$totalLevels}
        
        Requirements:
        1. Title: Creative RPG-style quest name.
        2. Description: Immersive narrative (2-3 sentences) turning the topic into a fantasy mission.
        3. Challenges: Generate exactly one challenge for each stage from 1 to {$totalLevels}.
           - For each level, provide either a Multiple Choice or Identification question.
           - Ensure levels are sequential (1, 2, 3...).
        4. Rewards: Numeric values for XP (100-300), AB (20-60), GP (10-40) based on complexity.

        JSON structure:
        {
            \"title\": \"...\",
            \"description\": \"...\",
            \"xp_reward\": 0,
            \"ab_reward\": 0,
            \"gp_reward\": 0,
            \"challenges\": [
                {
                    \"text\": \"...\",
                    \"type\": \"multiple_choice\",
                    \"level\": 1,
                    \"points\": 10,
                    \"options\": [\"...\", \"...\", \"...\", \"...\"],
                    \"answer\": \"...\"
                }
            ]
        }
        
        Return ONLY valid JSON. No conversational text.";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'system', 'content' => "You are a helpful assistant that outputs only JSON."],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
            'max_tokens' => 1024,
            'stream' => false
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq Request Failed: ' . $response->body());
        }

        $result = $response->json();
        $textResponse = $result['choices'][0]['message']['content'] ?? '';
        
        $decoded = json_decode($textResponse, true);

        if (!$decoded) {
            throw new \Exception('Failed to decode AI response: ' . $textResponse);
        }

        return $decoded;
    }
    /**
     * Generate a single quest question based on a topic and type.
     */
    public function generateQuestion(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'type' => 'required|string|in:multiple_choice,identification',
            'difficulty' => 'nullable|string'
        ]);

        $topic = $request->input('topic');
        $type = $request->input('type');
        $difficulty = $request->input('difficulty', 'medium');

        try {
            $questionData = $this->callGroqForQuestion($topic, $type, $difficulty);
            return response()->json([
                'status' => 'success',
                'data' => $questionData
            ]);
        } catch (\Exception $e) {
            \Log::error('Groq Question API Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'The Neural Realm is unresponsive.'
            ], 500);
        }
    }

    private function callGroqForQuestion($topic, $type, $difficulty)
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = "https://api.groq.com/openai/v1/chat/completions";

        $prompt = "You are the 'Neural Quest Forge' in the High Fantasy RPG world of ASIANISTA. 
        Your task is to generate ONE quest question (challenge) based on the following:
        
        Topic: {$topic}
        Type: {$type}
        Difficulty: {$difficulty}
        
        Requirements:
        1. Text: Creative RPG-style question/challenge text.
        2. Points: Suggested points (10-50 based on difficulty).
        3. For 'multiple_choice': Return an array of 4 options and the correct answer string.
        4. For 'identification': Return the correct answer string.

        JSON structure:
        {
            \"text\": \"...\",
            \"type\": \"{$type}\",
            \"level\": 1,
            \"points\": 10,
            \"options\": [\"...\", \"...\", \"...\", \"...\"], // Only for multiple_choice
            \"answer\": \"...\"
        }
        
        Return ONLY valid JSON. No conversational text.";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'system', 'content' => "You are a helpful assistant that outputs only JSON."],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
            'max_tokens' => 512,
            'stream' => false
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq Request Failed: ' . $response->body());
        }

        $result = $response->json();
        $textResponse = $result['choices'][0]['message']['content'] ?? '';
        
        $decoded = json_decode($textResponse, true);

        if (!$decoded) {
            throw new \Exception('Failed to decode AI response: ' . $textResponse);
        }

        return $decoded;
    }

    /**
     * Handle student support chat requests.
     */
    public function studentChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array'
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);

        try {
            $aiResponse = $this->callGroqForStudentSupport($message, $history);
            return response()->json([
                'status' => 'success',
                'reply' => $aiResponse
            ]);
        } catch (\Exception $e) {
            \Log::error('Student AI Support Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'The Neural Connection was lost.'
            ], 500);
        }
    }

    private function callGroqForStudentSupport($message, $history)
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = "https://api.groq.com/openai/v1/chat/completions";

        $systemPrompt = "You are the 'Neural Sage' in the High Fantasy RPG world of ASIANISTA. 
        Your role is to support students in their learning journey. 
        Be encouraging, use light RPG-themed language (e.g., calling them 'heroes' or 'explorers'), 
        and provide clear, helpful educational support. 
        Keep your responses concise but immersive.";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        // Add history for context (limit to last 5 exchanges to save tokens)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $chat) {
            $messages[] = ['role' => $chat['role'], 'content' => $chat['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 800,
            'stream' => false
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq Request Failed: ' . $response->body());
        }

        $result = $response->json();
        return $result['choices'][0]['message']['content'] ?? 'The Sage is deep in meditation... (No response received)';
    }
}
