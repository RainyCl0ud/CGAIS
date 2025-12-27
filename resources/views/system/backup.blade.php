<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">System Backup</h1>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('system.backup.create') }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Create Manual Backup
                                </button>
                            </form>
                            <a href="{{ route('system.backup.download') }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Download On-the-Fly Backup
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Automatic Backup Status -->
                    <div class="bg-green-50 p-6 rounded-lg mb-6 border border-green-200">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-green-900">🔄 Automatic Backup Status</h2>
                            <span class="px-3 py-1 bg-green-600 text-white text-sm font-semibold rounded-full">Active</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><strong>Schedule:</strong> Daily at 12:00 AM (Philippine Time)</p>
                            <p><strong>Retention:</strong> Last 30 days of backups</p>
                            <p><strong>Last Automatic Backup:</strong> 
                                <span class="font-semibold">
                                    {{ $backupInfo['backup_stats']['last_backup'] ?? 'Never' }}
                                </span>
                            </p>
                            <p><strong>Total Automatic Backups:</strong> {{ $backupInfo['backup_stats']['automatic_backups'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-blue-900 mb-4">Database Information</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium text-gray-700">Database Size:</span>
                                    <p class="text-gray-900">{{ $backupInfo['database_size'] }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Last Backup:</span>
                                    <p class="text-gray-900">{{ $backupInfo['backup_stats']['last_backup'] ?? 'Never' }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">System Uptime:</span>
                                    <p class="text-gray-900">{{ $backupInfo['system_uptime'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-green-900 mb-4">Backup Statistics</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium text-gray-700">Total Backups:</span>
                                    <p class="text-gray-900">{{ number_format($backupInfo['backup_stats']['total_backups'] ?? 0) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Total Backup Size:</span>
                                    <p class="text-gray-900">{{ number_format($backupInfo['backup_stats']['total_size_mb'] ?? 0, 2) }} MB</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Manual Backups:</span>
                                    <p class="text-gray-900">{{ number_format($backupInfo['backup_stats']['manual_backups'] ?? 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Statistics -->
                    <div class="bg-purple-50 p-6 rounded-lg mb-8">
                        <h2 class="text-xl font-semibold text-purple-900 mb-4">Data Statistics</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="font-medium text-gray-700">Total Users:</span>
                                <p class="text-gray-900 text-lg font-semibold">{{ number_format($backupInfo['total_users']) }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Total Appointments:</span>
                                <p class="text-gray-900 text-lg font-semibold">{{ number_format($backupInfo['total_appointments']) }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Activity Logs:</span>
                                <p class="text-gray-900 text-lg font-semibold">{{ number_format($backupInfo['total_activity_logs']) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Backups -->
                    @if(isset($backupInfo['recent_backups']) && $backupInfo['recent_backups']->count() > 0)
                    <div class="bg-gray-50 p-6 rounded-lg mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Backups</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($backupInfo['recent_backups'] as $backup)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $backup->filename }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $backup->type === 'automatic' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ ucfirst($backup->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($backup->size / 1024 / 1024, 2) }} MB</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $backup->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($backup->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($backup->created_at)->format('Y-m-d H:i:s') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            @if($backup->status === 'completed' && $backup->path)
                                                <a href="{{ route('system.backup.download-file', $backup->filename) }}" 
                                                   class="text-blue-600 hover:text-blue-900">Download</a>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Backup Instructions -->
                    <div class="bg-yellow-50 p-6 rounded-lg mb-8">
                        <h2 class="text-xl font-semibold text-yellow-900 mb-4">Backup Information</h2>
                        <div class="space-y-3 text-sm text-gray-700">
                            <p><strong>What's included in the backup:</strong></p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>All user accounts and profiles</li>
                                <li>Complete appointment history</li>
                                <li>Schedule information</li>
                                <li>Notification records</li>
                                <li>Activity and audit logs</li>
                                <li>Personal data sheets</li>
                                <li>Feedback forms</li>
                            </ul>
                            <p class="mt-4"><strong>Note:</strong> The backup is generated in JSON format and contains all system data as of the current time.</p>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="bg-red-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-red-900 mb-4">Security Notice</h2>
                        <div class="space-y-3 text-sm text-gray-700">
                            <p><strong>Important:</strong> This backup contains sensitive information including:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>Personal information of students and faculty</li>
                                <li>Counseling session details</li>
                                <li>System activity logs</li>
                                <li>User authentication data</li>
                            </ul>
                            <p class="mt-4"><strong>Please ensure:</strong></p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>Store the backup file securely</li>
                                <li>Limit access to authorized personnel only</li>
                                <li>Follow your institution's data protection policies</li>
                                <li>Delete the file when no longer needed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
