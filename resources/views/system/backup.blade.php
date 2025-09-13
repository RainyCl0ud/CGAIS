<x-app-layout>
    <div class="flex flex-col items-center justify-start py-8 px-4">
            <div class="w-full max-w-4xl mx-auto">
                <div class="bg-white/90 rounded-2xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-900">System Backup</h1>
                        <a href="{{ route('system.backup.download') }}" 
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Download Backup
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

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
                                    <p class="text-gray-900">{{ $backupInfo['last_backup'] }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">System Uptime:</span>
                                    <p class="text-gray-900">{{ $backupInfo['system_uptime'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-green-900 mb-4">Data Statistics</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium text-gray-700">Total Users:</span>
                                    <p class="text-gray-900">{{ number_format($backupInfo['total_users']) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Total Appointments:</span>
                                    <p class="text-gray-900">{{ number_format($backupInfo['total_appointments']) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Activity Logs:</span>
                                    <p class="text-gray-900">{{ number_format($backupInfo['total_activity_logs']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

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
