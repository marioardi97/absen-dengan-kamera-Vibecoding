@extends('layouts.app')

@section('title', 'Correction Requests')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Correction Requests</h1>
        <p class="mt-1 text-sm text-gray-600">Track your attendance correction requests and their status.</p>
    </div>

    <!-- Correction Requests -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Times</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $request->attendanceRecord->attendance_date->format('M j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $request->attendanceRecord->attendance_date->format('l') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->attendanceRecord->location->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->attendanceRecord->shift->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($request->requested_check_in_time)
                                            <div>In: {{ $request->requested_check_in_time->format('M j, H:i') }}</div>
                                        @endif
                                        @if($request->requested_check_out_time)
                                            <div>Out: {{ $request->requested_check_out_time->format('M j, H:i') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $request->status_badge_class }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('M j, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showRequestDetails({{ $request->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $requests->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No correction requests</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't submitted any attendance correction requests yet.</p>
                <div class="mt-6">
                    <a href="{{ route('employee.attendance.history') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        View Attendance History
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Request Details Modal -->
<div id="details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Correction Request Details</h3>
            <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="details-content" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
const requestsData = @json($requests->items());

function showRequestDetails(requestId) {
    const request = requestsData.find(r => r.id === requestId);
    if (!request) return;
    
    const content = `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Attendance Date</h4>
                    <p class="text-sm text-gray-900">${new Date(request.attendance_record.attendance_date).toLocaleDateString()}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Status</h4>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(request.status)}">
                        ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                    </span>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Location</h4>
                    <p class="text-sm text-gray-900">${request.attendance_record.location.name}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Shift</h4>
                    <p class="text-sm text-gray-900">${request.attendance_record.shift.name}</p>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Correction Type</h4>
                <p class="text-sm text-gray-900">${request.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
            </div>
            
            ${request.requested_check_in_time ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Requested Check-in Time</h4>
                    <p class="text-sm text-gray-900">${new Date(request.requested_check_in_time).toLocaleString()}</p>
                </div>
            ` : ''}
            
            ${request.requested_check_out_time ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Requested Check-out Time</h4>
                    <p class="text-sm text-gray-900">${new Date(request.requested_check_out_time).toLocaleString()}</p>
                </div>
            ` : ''}
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Reason</h4>
                <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">${request.reason}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Submitted</h4>
                    <p class="text-sm text-gray-900">${new Date(request.created_at).toLocaleString()}</p>
                </div>
                ${request.reviewed_at ? `
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Reviewed</h4>
                        <p class="text-sm text-gray-900">${new Date(request.reviewed_at).toLocaleString()}</p>
                    </div>
                ` : ''}
            </div>
            
            ${request.review_notes ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Review Notes</h4>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">${request.review_notes}</p>
                </div>
            ` : ''}
            
            ${request.reviewer ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Reviewed By</h4>
                    <p class="text-sm text-gray-900">${request.reviewer.name}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('details-content').innerHTML = content;
    document.getElementById('details-modal').style.display = 'flex';
}

function getStatusClass(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function closeDetailsModal() {
    document.getElementById('details-modal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('details-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailsModal();
    }
});
</script>
@endpush
@endsection