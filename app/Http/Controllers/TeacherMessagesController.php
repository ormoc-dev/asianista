<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;

class TeacherMessagesController extends MessageInboxController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->role === 'teacher', 403);

            return $next($request);
        });
    }

    protected function scopeInboxContacts(Builder $query): Builder
    {
        return $query->whereIn('role', ['student', 'teacher']);
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
