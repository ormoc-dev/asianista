<?php

namespace App\Http\Livewire\Teacher;

use Livewire\Component;
use App\Models\User;
use App\Models\RegistrationCode;

class Registration extends Component
{
    public $registrationCode;

    public function mount()
    {
        $this->registrationCode = RegistrationCode::latest()->first()?->code;
    }

    public function generateCode()
    {
        $code = strtoupper(bin2hex(random_bytes(3)));
        RegistrationCode::create(['code' => $code]);
        $this->registrationCode = $code;
        session()->flash('success', "New code generated: $code");
    }

    public function render()
    {
        $students = User::where('role', 'student')->get();
        return view('livewire.teacher.registration', [
            'students' => $students
        ])->layout('livewire.teacher.app-layout');
    }
}
