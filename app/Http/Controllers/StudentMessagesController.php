<?php

namespace App\Http\Controllers;

class StudentMessagesController extends MessageInboxController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->role === 'student', 403);

            return $next($request);
        });
    }

    protected function inboxView(): string
    {
        return 'student.messages.index';
    }

    protected function messageRouteGroup(): string
    {
        return 'student.messages';
    }

    protected function indexRoute(array $query = []): string
    {
        return route('student.messages', $query);
    }

    protected function pollUrl(): string
    {
        return route('student.messages.poll');
    }

    protected function sendUrlJsTemplate(): string
    {
        return str_replace('999999999', '__CONV__', route('student.messages.send', ['conversation' => 999999999]));
    }
}
