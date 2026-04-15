<?php

namespace App\View\Concerns;

/**
 * Methods Livewire adds to {@see \Illuminate\View\View} via macros at runtime.
 *
 * @see \Livewire\Macros\ViewMacros
 */
interface LivewireViewMacros
{
    /**
     * @param  class-string<\Illuminate\View\Component>|string  $view
     * @param  array<string, mixed>  $params
     * @return \Illuminate\View\View
     */
    public function layout($view, $params = []);
}
