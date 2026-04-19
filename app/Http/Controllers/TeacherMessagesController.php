<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Lesson;
use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

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
     * All student accounts — matches teacher reports (`TeacherReportsController@scores`),
     * which list every student (any status) when browsing by grade/section.
     *
     * @return Collection<int, int>
     */
    protected function reportAlignedStudentIdsForMessaging(): Collection
    {
        static $cache;

        if ($cache !== null) {
            return $cache;
        }

        return $cache = User::query()
            ->where('role', 'student')
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
            ->whereIn('id', $merged)
            ->pluck('id');
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
