<div class="registration-container">
    <style>
        .registration-container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .registration-container h2 {
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .code-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(0, 35, 102, 0.05);
            border-radius: 12px;
        }
        .code-display {
            font-size: 1.1rem;
            color: var(--text-dark);
        }
        .code-display strong {
            color: var(--primary);
            background: var(--accent);
            padding: 4px 12px;
            border-radius: 6px;
            margin-left: 8px;
        }
        .btn-generate {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-generate:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .students-table th, .students-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .students-table th {
            background: var(--primary);
            color: white;
        }
        .status {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status.active { background: #dcfce7; color: #166534; }
        .status.inactive { background: #fee2e2; color: #991b1b; }
        .alert-success {
            padding: 12px;
            background: #dcfce7;
            color: #166534;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>

    <h2><i class="fas fa-id-card"></i> Student Registrations</h2>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="code-section">
        <button wire:click="generateCode" class="btn-generate">
            <i class="fas fa-key"></i> Generate Registration Code
        </button>

        @if($registrationCode)
            <div class="code-display">
                Current Code: <strong>{{ $registrationCode }}</strong>
            </div>
        @endif
    </div>

    <div class="students-table-container">
        <h3>Students List</h3>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            <span class="status {{ strtolower($student->status ?? 'active') }}">
                                {{ ucfirst($student->status ?? 'Active') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px; color: #666;">No students registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
