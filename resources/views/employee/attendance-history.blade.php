@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Attendance History</h1>
        <p class="mt-1 text-sm text-gray-600">View your complete attendance records and request corrections.</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('employee.attendance.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select id="location_id" 
                            name="location_id" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select id="shift_id" 
                            name="shift_id" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Shifts</option>
                        @foreach($userLocations->unique('shift_id') as $userLocation)
                            <option value="{{ $userLocation->shift->id }}" {{ request('shift_id') == $userLocation->shift->id ? 'selected' : '' }}>
                                {{ $userLocation->shift->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-4 flex justify-end space-x-3">
                    <a href="{{ route('employee.attendance.history') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        @if($attendance->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendance as $record)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $record->attendance_date->format('M j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $record->attendance_date->format('l') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $record->location->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $record->location->address }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $record->shift->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $record->shift->formatted_time }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->check_in_time)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $record->check_in_time->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $record->check_in_time->format('M j') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->check_out_time)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $record->check_out_time->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $record->check_out_time->format('M j') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->worked_hours ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'present' => 'bg-green-100 text-green-800',
                                            'late' => 'bg-yellow-100 text-yellow-800',
                                            'early_departure' => 'bg-orange-100 text-orange-800',
                                            'absent' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($record->check_in_photo)
                                            <button onclick="showPhoto('{{ $record->check_in_photo_url }}', 'Check-in Photo')" 
                                                    class="text-blue-600 hover:text-blue-900 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4,4H7L9,2H15L17,4H20A2,2 0 0,1 22,6V18A2,2 0 0,1 20,20H4A2,2 0 0,1 2,18V6A2,2 0 0,1 4,4M12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17A5,5 0 0,0 17,12A5,5 0 0,0 12,7Z"/>
                                                </svg>
                                            </button>
                                        @endif
                                        
                                        @if($record->check_out_photo)
                                            <button onclick="showPhoto('{{ $record->check_out_photo_url }}', 'Check-out Photo')" 
                                                    class="text-orange-600 hover:text-orange-900 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4,4H7L9,2H15L17,4H20A2,2 0 0,1 22,6V18A2,2 0 0,1 20,20H4A2,2 0 0,1 2,18V6A2,2 0 0,1 4,4M12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17A5,5 0 0,0 17,12A5,5 0 0,0 12,7Z"/>
                                                </svg>
                                            </button>
                                        @endif
                                        
                                        <button onclick="requestCorrection({{ $record->id }})" 
                                                class="text-purple-600 hover:text-purple-900 transition-colors"
                                                title="Request Correction">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $attendance->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
            </div>
        @endif
    </div>
</div>

<!-- Photo Modal -->
<div id="photo-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 id="photo-modal-title" class="text-lg font-medium text-gray-900">Photo</h3>
            <button onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <img id="photo-modal-image" src="" alt="" class="w-full rounded-lg">
        </div>
    </div>
</div>

<!-- Correction Request Modal -->
<div id="correction-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <form id="correction-form">
            @csrf
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Request Attendance Correction</h3>
                <button type="button" onclick="closeCorrectionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" id="attendance_record_id" name="attendance_record_id">
                
                <div>
                    <label for="correction_type" class="block text-sm font-medium text-gray-700 mb-1">Correction Type</label>
                    <select id="correction_type" name="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select correction type</option>
                        <option value="check_in">Check-in Time</option>
                        <option value="check_out">Check-out Time</option>
                        <option value="both">Both Times</option>
                    </select>
                </div>
                
                <div id="check_in_time_field" style="display: none;">
                    <label for="requested_check_in_time" class="block text-sm font-medium text-gray-700 mb-1">Requested Check-in Time</label>
                    <input type="datetime-local" id="requested_check_in_time" name="requested_check_in_time" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div id="check_out_time_field" style="display: none;">
                    <label for="requested_check_out_time" class="block text-sm font-medium text-gray-700 mb-1">Requested Check-out Time</label>
                    <input type="datetime-local" id="requested_check_out_time" name="requested_check_out_time" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="correction_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Correction</label>
                    <textarea id="correction_reason" name="reason" rows="3" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Please explain why this correction is needed..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                <button type="button" onclick="closeCorrectionModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Photo modal functions
function showPhoto(url, title) {
    document.getElementById('photo-modal-image').src = url;
    document.getElementById('photo-modal-title').textContent = title;
    document.getElementById('photo-modal').style.display = 'flex';
}

function closePhotoModal() {
    document.getElementById('photo-modal').style.display = 'none';
}

// Correction request functions
function requestCorrection(recordId) {
    document.getElementById('attendance_record_id').value = recordId;
    document.getElementById('correction-modal').style.display = 'flex';
}

function closeCorrectionModal() {
    document.getElementById('correction-modal').style.display = 'none';
    document.getElementById('correction-form').reset();
    hideTimeFields();
}

function hideTimeFields() {
    document.getElementById('check_in_time_field').style.display = 'none';
    document.getElementById('check_out_time_field').style.display = 'none';
}

// Handle correction type change
document.getElementById('correction_type').addEventListener('change', function() {
    const type = this.value;
    hideTimeFields();
    
    if (type === 'check_in' || type === 'both') {
        document.getElementById('check_in_time_field').style.display = 'block';
    }
    
    if (type === 'check_out' || type === 'both') {
        document.getElementById('check_out_time_field').style.display = 'block';
    }
});

// Handle correction form submission
document.getElementById('correction-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    try {
        const response = await fetch('/employee/attendance/correction-request', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Correction request submitted successfully!', 'success');
            closeCorrectionModal();
        } else {
            throw new Error(result.message || 'Failed to submit correction request');
        }
    } catch (error) {
        console.error('Correction request error:', error);
        showToast(error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Request';
    }
});

// Close modals when clicking outside
document.getElementById('photo-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhotoModal();
    }
});

document.getElementById('correction-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCorrectionModal();
    }
});
</script>
@endpush
@endsection