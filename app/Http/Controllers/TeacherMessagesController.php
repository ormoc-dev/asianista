<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Lesson;
use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherMessagesController extends MessageInboxController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->role === 'teacher', 403);

            return $next($request);
        });
    }

    /**
     * Students this teacher registered (same scope as registration / reports lists).
     *
     * @return Collection<int, int>
     */
    protected function reportAlignedStudentIdsForMessaging(): Collection
    {
        $teacherId = Auth::id();

        static $cache = [];

        if (isset($cache[$teacherId])) {
            return $cache[$teacherId];
        }

        return $cache[$teacherId] = User::query()
            ->where('role', 'student')
            ->where('registered_by_teacher_id', $teacherId)
            ->pluck('id');
    }

    /**
     * Students tied to this teacher: targeted grade/section on your content, or took your quiz/quest.
     * Used for "My class" badges and filter only.
     *
     * @return Collection<int, int>
     */
    protected function classAssociatedStudentIdsForTeacher(int $teacherId): Collection
    {
        static $cache = [];

        if (isset($cache[$teacherId])) {
            return $cache[$teacherId];
        }

        $rawIds = collect();

        $pairs = collect();
        foreach ([
            Lesson::query()->where('teacher_id', $teacherId)->get(['grade_id', 'section_id']),
            Quiz::query()->where('teacher_id', $teacherId)->get(['grade_id', 'section_id']),
            Quest::query()->where('teacher_id', $teacherId)->get(['grade_id', 'section_id']),
        ] as $rows) {
            foreach ($rows as $row) {
                if ($row->grade_id !== null && $row->section_id !== null) {
                    $pairs->push([(int) $row->grade_id, (int) $row->section_id]);
                }
            }
        }
        $pairs = $pairs->unique(fn ($p) => $p[0].'-'.$p[1])->values();

        if ($pairs->isNotEmpty()) {
            $rawIds = $rawIds->merge(
                User::query()
                    ->where('role', 'student')
                    ->where(function ($q) use ($pairs) {
                        foreach ($pairs as [$gradeId, $sectionId]) {
                            $q->orWhere(function ($q2) use ($gradeId, $sectionId) {
                                $q2->where('grade_id', $gradeId)->where('section_id', $sectionId);
                            });
                        }
                    })
                    ->pluck('id')
            );
        }

        $rawIds = $rawIds->merge(
            QuestAttempt::query()
                ->whereHas('quest', fn ($q) => $q->where('teacher_id', $teacherId))
                ->pluck('user_id')
        );

        $rawIds = $rawIds->merge(
            QuizAttempt::query()
                ->whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))
                ->pluck('student_id')
        );

        $merged = $rawIds->filter()->unique()->values();

        if ($merged->isEmpty()) {
            return $cache[$teacherId] = collect();
        }

        return $cache[$teacherId] = User::query()
            ->where('role', 'student')
            ->where('registered_by_teacher_id', $teacherId)
            ->whereIn('id', $merged)
            ->pluck('id');
    }

    /**
     * Teachers may message registered students or other teachers only.
     */
    public function start(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $otherUserId = (int) $request->input('user_id');

        if ($otherUserId === $user->id) {
            return back();
        }

        $other = User::query()->findOrFail($otherUserId);

        if ($other->role === 'student') {
            abort_unless(
                $this->reportAlignedStudentIdsForMessaging()->contains($otherUserId),
                403,
                'You can only message students you registered.'
            );
        } elseif ($other->role !== 'teacher') {
            abort(403);
        }

        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->whereHas('participants', function ($q) use ($otherUserId) {
                $q->where('users.id', $otherUserId);
            })
            ->first();

        if (! $conversation) {
            $conversation = DB::transaction(function () use ($user, $otherUserId) {
                $conv = Conversation::create([
                    'title' => null,
                    'is_group' => false,
                ]);

                $conv->participants()->attach([$user->id, $otherUserId]);

                return $conv;
            });
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'conversation_id' => $conversation->id,
            ]);
        }

        return redirect()->to($this->indexRoute([
            'conversation' => $conversation->id,
        ]));
    }

    protected function filterConversationsForUser(Collection $conversations, User $user): Collection
    {
        $allowedStudents = $this->reportAlignedStudentIdsForMessaging()->flip();

        return $conversations->filter(function (Conversation $conversation) use ($user, $allowedStudents) {
            $pivot = $conversation->participants
                ->firstWhere('id', $user->id)
                ?->pivot;

            if (! $pivot) {
                return false;
            }

            $visible = false;
            if (is_null($pivot->deleted_at)) {
                $visible = true;
            } else {
                $last = $conversation->lastMessage;
                if ($last && $last->created_at->gt($pivot->deleted_at)) {
                    $visible = true;
                }
            }

            if (! $visible) {
                return false;
            }

            $other = $conversation->participants->firstWhere('id', '!=', $user->id);
            if ($other && $other->role === 'student' && ! $allowedStudents->has($other->id)) {
                return false;
            }

            return true;
        })->values();
    }

    protected function loadContacts(User $user, string $search): Collection
    {
        $messageableIds = $this->reportAlignedStudentIdsForMessaging();

        $contactsQuery = User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($q) use ($messageableIds) {
                $q->where(function ($q2) use ($messageableIds) {
                    $q2->where('role', 'student')
                        ->whereIn('id', $messageableIds);
                })->orWhere('role', 'teacher');
            });

        if ($search !== '') {
            $contactsQuery->where('name', 'like', '%'.$search.'%');
        }

        return $contactsQuery->orderBy('name')->limit(300)->get();
    }

    protected function extraInboxViewData(User $user): array
    {
        $messageable = $this->reportAlignedStudentIdsForMessaging();
        $classLinked = $this->classAssociatedStudentIdsForTeacher($user->id);

        return [
            'myStudentIds' => $messageable->values()->all(),
            'myClassStudentIds' => $classLinked->values()->all(),
            'myStudentsCount' => $messageable->count(),
            'myClassCount' => $classLinked->count(),
        ];
    }

    protected function decorateConversationJson(array $payload, Conversation $conversation, User $viewer): array
    {
        $classLinked = $this->classAssociatedStudentIdsForTeacher($viewer->id);
        $otherId = $payload['other']['id'] ?? null;
        $role = $payload['other']['role'] ?? '';

        $payload['is_my_class_student'] = $role === 'student'
            && $otherId
            && $classLinked->contains((int) $otherId);

        return $payload;
    }

    protected function inboxView(): string
    {
        return 'teacher.messages.index';
    }

    protected function messageRouteGroup(): string
    {
        return 'teacher.messages';
    }

    protected function indexRoute(array $query = []): string
    {
        return route('teacher.messages', $query);
    }

    protected function pollUrl(): string
    {
        return route('teacher.messages.poll');
    }

    protected function sendUrlJsTemplate(): string
    {
        return str_replace('999999999', '__CONV__', route('teacher.messages.send', ['conversation' => 999999999]));
    }
}
