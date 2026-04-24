<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AIAssistantController extends Controller
{
    /**
     * Generate quest content based on a topic.
     */
    public function generateQuest(Request $request)
    {
        $modelKeys = array_keys(config('services.quest_ai.models', []));

        $request->validate([
            'topic' => 'required|string|max:255',
            'difficulty' => 'nullable|string',
            'total_levels' => 'nullable|integer|min:1|max:30',
            'ai_model' => ['nullable', 'string', Rule::in($modelKeys)],
        ]);

        $topic = $request->input('topic');
        $difficulty = $request->input('difficulty', 'medium');
        $totalLevels = (int) $request->input('total_levels', 3);
        $modelKey = $request->input('ai_model', config('services.quest_ai.default'));

        if (! isset(config('services.quest_ai.models')[$modelKey])) {
            $modelKey = config('services.quest_ai.default');
        }

        try {
            $this->assertQuestAiProviderConfigured($modelKey);
            $generatedData = $this->generateQuestWithLlm($modelKey, $topic, $difficulty, $totalLevels);

            return response()->json([
                'status' => 'success',
                'data' => $generatedData,
            ]);
        } catch (\RuntimeException $e) {
            Log::warning('Quest AI configuration: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        } catch (\Exception $e) {
            Log::error('Quest AI Error: '.$e->getMessage(), [
                'exception' => $e::class,
            ]);
            $message = trim($e->getMessage());
            if ($message === '') {
                $message = 'The Neural Link was interrupted.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 500);
        }
    }

    private function assertQuestAiProviderConfigured(string $modelKey): void
    {
        $entry = config('services.quest_ai.models')[$modelKey] ?? null;
        if (! $entry) {
            throw new \RuntimeException('Invalid AI model configuration.');
        }
        if ($entry['provider'] === 'groq' && empty(env('GROQ_API_KEY'))) {
            throw new \RuntimeException('Groq is not configured. Add GROQ_API_KEY to your environment, or choose an OpenRouter model.');
        }
        if ($entry['provider'] === 'openrouter' && empty(config('services.openrouter.api_key'))) {
            throw new \RuntimeException('OpenRouter is not configured. Add OPENROUTER_API_KEY to your environment.');
        }
    }

    private function generateQuestWithLlm(string $modelKey, string $topic, string $difficulty, int $totalLevels): array
    {
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
        4. Rewards: Numeric values for XP (100-300), AP (20-60) in ab_reward, GP (10-40) based on complexity.
        
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

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $maxTokens = (int) min(4096, max(1024, 400 + 180 * $totalLevels));
        $decoded = $this->chatCompletionJsonForQuestForge($modelKey, $messages, $maxTokens);

        if (! $decoded) {
            throw new \Exception('Failed to decode AI response.');
        }

        return $decoded;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonFromLlmText(string $text): ?array
    {
        $text = trim($text);
        if (preg_match('/^```(?:json)?\s*([\s\S]*?)\s*```/m', $text, $m)) {
            $text = trim($m[1]);
        }
        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice = substr($text, $start, $end - $start + 1);
            $decoded = json_decode($slice, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Human-readable errors for LLM HTTP failures (especially OpenRouter free-tier 429s).
     */
    private function friendlyLlmHttpMessage(int $status, string $detail): string
    {
        $extra = ($detail !== '' && stripos($detail, 'Provider returned error') === false)
            ? ' ('.$detail.')'
            : '';

        switch ($status) {
            case 429:
                return 'The AI provider is rate-limiting requests (HTTP 429). are often strict: wait 1–2 minutes, try a different model, or add credits at openrouter.ai. '.$extra;
            case 401:
                return 'The API key was rejected (HTTP 401). Check OPENROUTER_API_KEY or GROQ_API_KEY in your .env file.'.$extra;
            case 402:
                return 'This model or account needs credits (HTTP 402). Add balance on OpenRouter or switch provider.'.$extra;
            case 403:
                return 'Access denied by the AI provider (HTTP 403). Check the model name and account permissions.'.$extra;
            case 502:
            case 503:
                return 'The AI provider is temporarily overloaded (HTTP '.$status.'). Try again in a few minutes.'.$extra;
            default:
                if ($detail !== '') {
                    return 'AI request failed (HTTP '.$status.'): '.$detail;
                }

                return 'AI request failed (HTTP '.$status.').';
        }
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array<string, mixed>|null
     */
    private function chatCompletionJsonForQuestForge(string $modelKey, array $messages, int $maxTokens): ?array
    {
        $models = config('services.quest_ai.models', []);
        if (! isset($models[$modelKey])) {
            throw new \InvalidArgumentException('Invalid quest AI model.');
        }

        $entry = $models[$modelKey];
        $provider = $entry['provider'];
        $model = $entry['model'];
        $useJsonObject = (bool) ($entry['json_object'] ?? true);

        if ($provider === 'groq') {
            $apiKey = (string) env('GROQ_API_KEY');
            $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
            $headers = [
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ];
            $body = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => $maxTokens,
                'stream' => false,
            ];
            if ($useJsonObject) {
                $body['response_format'] = ['type' => 'json_object'];
            }
        } elseif ($provider === 'openrouter') {
            $apiKey = (string) config('services.openrouter.api_key');
            $endpoint = config('services.openrouter.base_url').'/chat/completions';
            $headers = array_filter([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('services.openrouter.http_referer') ?: null,
                'X-Title' => config('services.openrouter.app_title') ?: null,
            ]);
            $body = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => $maxTokens,
            ];
            if ($useJsonObject) {
                $body['response_format'] = ['type' => 'json_object'];
            }
        } else {
            throw new \RuntimeException('Unknown AI provider.');
        }

        $client = Http::timeout(120)->withHeaders($headers);

        $response = $client->post($endpoint, $body);

        if ($response->failed() && $provider === 'openrouter' && isset($body['response_format'])) {
            Log::warning('OpenRouter chat failed with JSON mode; retrying without response_format.', [
                'status' => $response->status(),
                'snippet' => mb_substr($response->body(), 0, 500),
            ]);
            unset($body['response_format']);
            $response = $client->post($endpoint, $body);
        }

        if ($response->failed()) {
            $status = $response->status();
            $errBody = $response->json();
            $detail = is_array($errBody) && isset($errBody['error']['message'])
                ? trim((string) $errBody['error']['message'])
                : mb_substr($response->body(), 0, 800);

            throw new \Exception($this->friendlyLlmHttpMessage($status, $detail));
        }

        $result = $response->json();
        $textResponse = (string) ($result['choices'][0]['message']['content'] ?? '');

        return $this->decodeJsonFromLlmText($textResponse);
    }

    /**
     * Generate a single quest question based on a topic and type.
     */
    public function generateQuestion(Request $request)
    {
        $modelKeys = array_keys(config('services.quest_ai.models', []));

        $request->validate([
            'topic' => 'required|string|max:255',
            'type' => 'required|string|in:multiple_choice,identification',
            'difficulty' => 'nullable|string',
            'ai_model' => ['nullable', 'string', Rule::in($modelKeys)],
        ]);

        $topic = $request->input('topic');
        $type = $request->input('type');
        $difficulty = $request->input('difficulty', 'medium');
        $modelKey = $request->input('ai_model', config('services.quest_ai.default'));

        if (! isset(config('services.quest_ai.models')[$modelKey])) {
            $modelKey = config('services.quest_ai.default');
        }

        try {
            $this->assertQuestAiProviderConfigured($modelKey);
            $questionData = $this->generateSingleQuestionWithLlm($modelKey, $topic, $type, $difficulty);

            return response()->json([
                'status' => 'success',
                'data' => $questionData,
            ]);
        } catch (\RuntimeException $e) {
            Log::warning('Quest question AI configuration: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        } catch (\Exception $e) {
            Log::error('Quest question AI Error: '.$e->getMessage(), [
                'exception' => $e::class,
            ]);
            $message = trim($e->getMessage());
            if ($message === '') {
                $message = 'The Neural Realm is unresponsive.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 500);
        }
    }

    private function generateSingleQuestionWithLlm(string $modelKey, string $topic, string $type, string $difficulty): array
    {
        $prompt = "You are the 'Neural Quest Forge' in the High Fantasy RPG world of ASIANISTA. 
        Your task is to generate ONE quest question (challenge) based on the following:
        
        Topic: {$topic}
        Type: {$type}
        Difficulty: {$difficulty}
        
        Requirements:
        1. Text: Creative RPG-style question/challenge text.
        2. Points: Suggested points (10-50 based on difficulty).
        3. For 'multiple_choice': Return an array of 4 options and the correct answer string.
        4. For 'identification': Return the correct answer string (options can be null or omitted).
        
        JSON structure:
        {
            \"text\": \"...\",
            \"type\": \"{$type}\",
            \"level\": 1,
            \"points\": 10,
            \"options\": [\"...\", \"...\", \"...\", \"...\"],
            \"answer\": \"...\"
        }
        
        Return ONLY valid JSON. No conversational text.";

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $decoded = $this->chatCompletionJsonForQuestForge($modelKey, $messages, 768);

        if (! $decoded) {
            throw new \Exception('Failed to decode AI response.');
        }

        return $decoded;
    }

    /**
     * Generate a classroom random event (for admin or teacher forms).
     */
    public function generateRandomEvent(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'event_type' => 'nullable|string|in:positive,negative,neutral,challenge,auto',
        ]);

        if (empty(env('GROQ_API_KEY'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI is not configured. Add GROQ_API_KEY to your environment.',
            ], 503);
        }

        $topic = $request->input('topic');
        $eventTypeHint = $request->input('event_type', 'auto');

        try {
            $raw = $this->callGroqForRandomEvent($topic, $eventTypeHint);
            $data = $this->normalizeRandomEventPayload($raw);

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Groq Random Event API Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Could not generate an event. Try a different topic.',
            ], 500);
        }
    }

    private function callGroqForRandomEvent(string $topic, string $eventTypeHint): array
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        $typeInstruction = $eventTypeHint === 'auto'
            ? 'Choose the most fitting event_type: positive, negative, neutral, or challenge.'
            : "Use event_type \"{$eventTypeHint}\".";

        $prompt = "You design short 'random encounter' events for the RPG-themed learning platform ASIANISTA.
Topic or situation from the teacher: {$topic}

{$typeInstruction}

Return a JSON object with:
- title: short catchy title (max 80 chars)
- description: 1-2 sentences of flavor for the class
- effect: clear sentence(s) explaining what the teacher should do or what students experience
- xp_reward: non-negative integer (0-200). Use greater than 0 mainly for positive/challenge rewards.
- xp_penalty: non-negative integer (0-120). Use greater than 0 mainly for negative events. Usually 0 for positive/neutral.
- target_type: one of: single, all, pair, random
- event_type: one of: positive, negative, neutral, challenge

Rules: For positive, xp_reward should be positive and xp_penalty 0. For negative, xp_penalty greater than 0 and xp_reward 0. For neutral, both can be 0. For challenge, moderate xp_reward and xp_penalty 0 unless you describe a risk.

Return ONLY valid JSON with exactly these keys: title, description, effect, xp_reward, xp_penalty, target_type, event_type.";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'system', 'content' => 'You output only valid JSON objects. No markdown.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.75,
            'max_tokens' => 700,
            'stream' => false,
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq Request Failed: ' . $response->body());
        }

        $result = $response->json();
        $textResponse = $result['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode($textResponse, true);

        if (! is_array($decoded)) {
            throw new \Exception('Failed to decode AI response: ' . $textResponse);
        }

        return $decoded;
    }

    private function normalizeRandomEventPayload(array $raw): array
    {
        $allowedTypes = ['positive', 'negative', 'neutral', 'challenge'];
        $allowedTargets = ['single', 'all', 'pair', 'random'];

        $eventType = $raw['event_type'] ?? 'neutral';
        if (! in_array($eventType, $allowedTypes, true)) {
            $eventType = 'neutral';
        }

        $targetType = $raw['target_type'] ?? 'single';
        if (! in_array($targetType, $allowedTargets, true)) {
            $targetType = 'single';
        }

        $xpReward = max(0, min(500, (int) ($raw['xp_reward'] ?? 0)));
        $xpPenalty = max(0, min(500, (int) ($raw['xp_penalty'] ?? 0)));

        if ($eventType === 'positive' || $eventType === 'challenge') {
            $xpPenalty = 0;
            if ($xpReward < 5) {
                $xpReward = 40;
            }
        } elseif ($eventType === 'negative') {
            $xpReward = 0;
            if ($xpPenalty < 5) {
                $xpPenalty = 25;
            }
        } elseif ($eventType === 'neutral') {
            $xpReward = min($xpReward, 40);
            $xpPenalty = min($xpPenalty, 20);
        }

        return [
            'title' => mb_substr(strip_tags((string) ($raw['title'] ?? 'Mystery Event')), 0, 255),
            'description' => strip_tags((string) ($raw['description'] ?? '')),
            'effect' => strip_tags((string) ($raw['effect'] ?? '')),
            'xp_reward' => $xpReward,
            'xp_penalty' => $xpPenalty,
            'target_type' => $targetType,
            'event_type' => $eventType,
        ];
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
            Log::error('Student AI Support Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'The Neural Connection was lost.'
            ], 500);
        }
    }

    /**
     * Handle teacher support chat requests.
     */
    public function teacherChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array'
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);

        try {
            $aiResponse = $this->callGroqForTeacherSupport($message, $history);
            return response()->json([
                'status' => 'success',
                'reply' => $aiResponse
            ]);
        } catch (\Exception $e) {
            Log::error('Teacher AI Support Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'The Neural Connection was lost.'
            ], 500);
        }
    }

    private function callGroqForTeacherSupport($message, $history)
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = "https://api.groq.com/openai/v1/chat/completions";

        $systemPrompt = "You are the 'Arcane Advisor' in the High Fantasy RPG world of ASIANISTA. 
        Your role is to assist teachers with educational strategies, lesson planning, student engagement, 
        and gamification techniques. Be professional yet immersive, using light RPG-themed language. 
        Provide practical, actionable advice for educators. Keep responses concise but helpful.";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
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
        return $result['choices'][0]['message']['content'] ?? 'The Advisor is meditating... (No response received)';
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

    /**
     * Generate lesson content using AI.
     */
    public function generateLessonContent(Request $request)
    {
        $modelKeys = array_keys(config('services.quest_ai.models', []));

        $request->validate([
            'topic' => 'required|string|max:255',
            'grade_level' => 'nullable|string',
            'lesson_type' => 'nullable|string',
            'ai_model' => ['nullable', 'string', Rule::in($modelKeys)],
        ]);

        $topic = $request->input('topic');
        $gradeLevel = $request->input('grade_level', 'general');
        $lessonType = $request->input('lesson_type', 'lecture');
        $modelKey = $request->input('ai_model', config('services.quest_ai.default'));

        if (! isset(config('services.quest_ai.models')[$modelKey])) {
            $modelKey = config('services.quest_ai.default');
        }

        try {
            $this->assertQuestAiProviderConfigured($modelKey);
            $content = $this->generateLessonWithLlm($modelKey, $topic, $gradeLevel, $lessonType);

            return response()->json([
                'status' => 'success',
                'data' => $content,
            ]);
        } catch (\RuntimeException $e) {
            Log::warning('Lesson AI configuration: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 503);
        } catch (\Exception $e) {
            Log::error('AI Lesson Generation Error: '.$e->getMessage(), [
                'exception' => $e::class,
            ]);
            $message = trim($e->getMessage());
            if ($message === '') {
                $message = 'Failed to generate lesson content.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 500);
        }
    }

    private function generateLessonWithLlm(string $modelKey, string $topic, string $gradeLevel, string $lessonType): array
    {
        $prompt = "You are an educational content generator for the ASIANISTA learning platform.
        Create a comprehensive lesson on the following topic:

        Topic: {$topic}
        Grade Level: {$gradeLevel}
        Lesson Type: {$lessonType}

        Generate a JSON object with the following structure:
        {
            \"title\": \"A clear, engaging lesson title\",
            \"objectives\": [\"Learning objective 1\", \"Learning objective 2\", \"Learning objective 3\"],
            \"introduction\": \"A brief introduction to the topic (2-3 paragraphs)\",
            \"main_content\": \"The main lesson content with key concepts, explanations, and examples\",
            \"key_points\": [\"Key point 1\", \"Key point 2\", \"Key point 3\", \"Key point 4\"],
            \"activities\": [\"Suggested activity 1\", \"Suggested activity 2\"],
            \"summary\": \"A concise summary of the lesson\",
            \"assessment_questions\": [
                {\"question\": \"Question text\", \"answer\": \"Expected answer\"}
            ]
        }

        Return ONLY valid JSON. No conversational text.";

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $decoded = $this->chatCompletionJsonForQuestForge($modelKey, $messages, 2048);

        if (! $decoded) {
            throw new \Exception('Failed to decode AI response.');
        }

        return $decoded;
    }

    /**
     * Generate quiz questions using AI.
     */
    public function generateQuizQuestions(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'num_questions' => 'nullable|integer|min:1|max:20',
            'question_types' => 'nullable|array',
            'difficulty' => 'nullable|string|in:easy,medium,hard,mixed'
        ]);

        $topic = $request->input('topic');
        $numQuestions = $request->input('num_questions', 5);
        $questionTypes = $request->input('question_types', ['multiple_choice']);
        $difficulty = $request->input('difficulty', 'medium');

        try {
            $questions = $this->callGroqForQuiz($topic, $numQuestions, $questionTypes, $difficulty);
            return response()->json([
                'status' => 'success',
                'data' => $questions
            ]);
        } catch (\Exception $e) {
            Log::error('AI Quiz Generation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate quiz questions.'
            ], 500);
        }
    }

    private function callGroqForQuiz($topic, $numQuestions, $questionTypes, $difficulty)
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = "https://api.groq.com/openai/v1/chat/completions";

        $typesStr = implode(', ', $questionTypes);

        $prompt = "You are an educational quiz generator for the ASIANISTA learning platform.
        Create {$numQuestions} quiz questions on the following topic:

        Topic: {$topic}
        Question Types: {$typesStr}
        Difficulty: {$difficulty}

        Generate a JSON object with the following structure:
        {
            \"title\": \"A suitable quiz title\",
            \"description\": \"A brief description of the quiz\",
            \"questions\": [
                {
                    \"question\": \"The question text\",
                    \"type\": \"multiple_choice\",
                    \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
                    \"answer\": \"The correct answer\",
                    \"points\": 10
                },
                {
                    \"question\": \"The question text\",
                    \"type\": \"identification\",
                    \"answer\": \"The correct answer\",
                    \"points\": 10
                }
            ]
        }

        Make sure questions are educational, clear, and appropriate for the topic.
        For identification questions, do not include options.
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
            'max_tokens' => 2048,
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
}
