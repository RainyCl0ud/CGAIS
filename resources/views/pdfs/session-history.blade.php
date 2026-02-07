<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session History Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6px;
            line-height: 1.0;
            color: #333;
            margin: 0;
            padding: 3px;
            margin-top: 120px;
            margin-bottom: 50px;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 1000;
            text-align: center;
            padding: 6px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 10px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            color: #666;
            margin: 1px 0;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 5px;
            background: #f8fafc;
            padding: 3px;
            border-radius: 2px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 8px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 5px;
            color: #666;
            margin-top: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 1px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 0px 1px;
            border-radius: 1px;
            font-size: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-no-show { background: #fef3c7; color: #92400e; }
        .badge-failed { background: #fee2e2; color: #7f1d1d; }
        .notes-section {
            margin-top: 0px;
        }
        .notes-label {
            font-weight: bold;
            font-size: 5px;
            color: #374151;
        }
        .notes-content {
            margin-top: 0px;
            font-size: 5px;
            color: #6b7280;
        }
        .footer {
            margin-top: 5px;
            text-align: center;
            font-size: 5px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 3px;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body { margin: 0; }
        }
        .fixed-footer { position: fixed; bottom: 0; left: 0; right: 0; background: white; z-index: 1000; padding: 3px; text-align: center; border-top: 1px solid #000; }
        .page-number::after { content: "Page " counter(page) " of " counter(pages); }
    </style>
</head>
<body>
    <div class="header">
        <!-- Control Number - Top Left -->
        <div style="position:absolute; left:0; top:0; width:120px;">
            <div style="border:1px solid #000; padding:4px; font-size:8px; text-align:center; background:#fff;">
                <div style="font-weight:700; margin-bottom:2px;">Control No.</div>
                <div style="border-bottom:1px solid #000; height:16px;"></div>
            </div>
        </div>

        <!-- Centered Content: Logo + University Info -->
        <div style="display:flex; justify-content:center; align-items:flex-start; gap:15px;">
            <!-- Logo -->
            <div style="flex-shrink:0;">
                @if(!empty($logos['logo']))
                    <img class="logo" src="{{ $logos['logo'] }}" alt="logo" style="width:50px; height:auto;">
                @endif
            </div>

            <!-- University Info -->
            <div style="text-align:center;">
                <div class="title" style="font-weight:700; font-size:10px; text-transform:uppercase; font-family: serif;">University of Science and Technology of Southern Philippines</div>
                <div class="subtitle" style="font-size:6px; text-transform:uppercase; margin-top:2px;">
                    Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva
                </div>
                <div class="subtitle" style="font-weight:700; margin-top:3px; font-size:8px;">GUIDANCE AND COUNSELING SERVICES</div>
            </div>
        </div>

        <!-- Document Code - Top Right -->
        <div style="position:absolute; right:0; top:0; width:100px;">
            <div style="border:1px solid #000; padding:1px; font-size:5px; text-align:center; background:#fff;">
                <div style="background:#1b2a6b; color:#fff; font-weight:700; padding:1px;">Document Code No.</div>
                <div style="font-weight:700; font-size:6px; padding:2px 0;">FM-USTP-GCS-03</div>
                <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                    <div style="flex:1; border-right:1px solid #000; padding:1px;">Rev. No.</div>
                    <div style="flex:1; padding:1px;">Effective Date</div>
                </div>
                <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                    <div style="flex:1; border-right:1px solid #000; padding:1px;">00</div>
                    <div style="flex:1; padding:1px;">01.01.26</div>
                </div>
                <div style="border-top:1px solid #000; padding:2px; font-size:6px; font-weight:700;"></div>
            </div>
        </div>

        <h1 style="margin-top:10px;">Session History Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
        @if($user->isCounselor() || $user->isAssistant())
            <p>All Sessions System-wide</p>
        @else
            <p>{{ $user->full_name }}'s Sessions</p>
        @endif
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_sessions'] }}</div>
            <div class="stat-label">Total Sessions</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['completed_sessions'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    @if($appointments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Session Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>
                            <div style="font-weight: bold;">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                            <div style="color: #6b7280; font-size: 8px;">{{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time->format('g:i A') }}</div>
                        </td>
                        <td>{{ $appointment->user->full_name }}</td>
                        <td>
                            <span class="badge {{ $appointment->getTypeBadgeClass() }}">
                                {{ ucfirst($appointment->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $appointment->getCounselingCategoryBadgeClass() }}">
                                {{ $appointment->getCounselingCategoryLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower(str_replace(' ', '-', $appointment->status)) }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td>
                            @if($appointment->type === 'urgent')
                                <div class="notes-section">
                                    <div class="notes-label">Reason for urgency:</div>
                                    <div class="notes-content">{{ $appointment->reason ?: 'Not specified' }}</div>
                                </div>
                            @endif
                            <div class="notes-section">
                                <div class="notes-label">Purpose/Concern:</div>
                                <div class="notes-content">{{ $appointment->notes ?: 'Not specified' }}</div>
                            </div>
                            @if($appointment->counselor_notes)
                                <div class="notes-section">
                                    <div class="notes-label">Counselor Notes:</div>
                                    <div class="notes-content">{{ $appointment->counselor_notes }}</div>
                                </div>
                            @endif
                            @if($appointment->reschedule_reason)
                                <div class="notes-section">
                                    <div class="notes-label">Reschedule Reason:</div>
                                    <div class="notes-content">{{ $appointment->reschedule_reason }}</div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <div style="font-size: 16px; margin-bottom: 10px;">No sessions found</div>
            <div style="font-size: 12px;">Try adjusting your filters or search criteria.</div>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated by the Counseling Appointment System</p>
        <p>Confidential - For authorized personnel only</p>
    </div>

    <div class="fixed-footer">
        <p style="font-size:5px; color:#666;">Confidential - For authorized personnel only</p>
    </div>
</body>
</html>
