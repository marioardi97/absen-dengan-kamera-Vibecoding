@extends('layouts.app')

@section('title', 'Check In')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Check In</h1>
        <p class="mt-1 text-sm text-gray-600">Take a selfie and confirm your location to check in.</p>
    </div>

    @if($todayAttendance)
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 9V11H14V9H12M12 17H14V13H12V17M12 2L13.09 8.26L22 12L13.09 15.74L12 22L10.91 15.74L2 12L10.91 8.26L12 2Z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-800">
                        <strong>Already checked in today.</strong> You have already completed your check-in for today.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Camera Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Take Your Selfie</h3>
                <p class="mt-1 text-sm text-gray-600">A live photo is required for attendance verification.</p>
            </div>
            
            <div class="p-6">
                <!-- Camera Preview -->
                <div class="relative mb-4">
                    <video id="video" 
                           class="w-full h-64 bg-gray-100 rounded-lg object-cover" 
                           style="display: none;" 
                           autoplay 
                           muted 
                           playsinline></video>
                    
                    <canvas id="canvas" 
                            class="w-full h-64 bg-gray-100 rounded-lg" 
                            style="display: none;"></canvas>
                    
                    <div id="camera-placeholder" class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Camera Access Required</h3>
                            <p class="mt-1 text-sm text-gray-500">Click "Start Camera" to begin</p>
                        </div>
                    </div>
                    
                    <!-- Camera Controls Overlay -->
                    <div id="camera-controls" class="absolute bottom-4 left-1/2 transform -translate-x-1/2" style="display: none;">
                        <button id="capture-btn" 
                                class="bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-4 py-2 rounded-full shadow-lg transition-all duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4,4H7L9,2H15L17,4H20A2,2 0 0,1 22,6V18A2,2 0 0,1 20,20H4A2,2 0 0,1 2,18V6A2,2 0 0,1 4,4M12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17A5,5 0 0,0 17,12A5,5 0 0,0 12,7Z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Camera Action Buttons -->
                <div class="flex space-x-3">
                    <button id="start-camera" 
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V17A1,1 0 0,0 4,18H16A1,1 0 0,0 17,17V13.5L21,17.5V6.5L17,10.5Z"/>
                        </svg>
                        Start Camera
                    </button>
                    
                    <button id="retake-photo" 
                            class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200" 
                            style="display: none;">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
                        </svg>
                        Retake
                    </button>
                </div>
            </div>
        </div>

        <!-- Check-in Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Check-in Details</h3>
                <p class="mt-1 text-sm text-gray-600">Select your location and shift for today.</p>
            </div>

            <form id="checkin-form" class="p-6">
                @csrf
                
                <!-- Location & Shift Selection -->
                @if($userLocations->count() > 0)
                    <div class="space-y-4 mb-6">
                        @foreach($userLocations as $userLocation)
                            <div class="relative">
                                <input type="radio" 
                                       id="location_{{ $userLocation->id }}" 
                                       name="location_shift" 
                                       value="{{ $userLocation->location_id }}_{{ $userLocation->shift_id }}"
                                       class="sr-only peer"
                                       data-location-id="{{ $userLocation->location_id }}"
                                       data-shift-id="{{ $userLocation->shift_id }}">
                                
                                <label for="location_{{ $userLocation->id }}" 
                                       class="flex items-center p-4 bg-gray-50 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $userLocation->location->name }}</h4>
                                            <span class="text-xs text-gray-500">{{ $userLocation->shift->name }}</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-600">{{ $userLocation->location->address }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $userLocation->shift->formatted_time }}</p>
                                        <div class="mt-2 flex items-center text-xs text-gray-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                            </svg>
                                            <span>Within {{ $userLocation->location->geofence_radius }}m radius</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-colors duration-200 flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No locations assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">Contact your administrator to get assigned to work locations.</p>
                    </div>
                @endif

                <!-- GPS Status -->
                <div id="location-status" class="mb-6 p-4 rounded-lg border" style="display: none;">
                    <div class="flex items-center">
                        <svg id="location-icon" class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M3.05,13H1V11H3.05C3.5,6.83 6.83,3.5 11,3.05V1H13V3.05C17.17,3.5 20.5,6.83 20.95,11H23V13H20.95C20.5,17.17 17.17,20.5 13,20.95V23H11V20.95C6.83,20.5 3.5,17.17 3.05,13M12,5A7,7 0 0,0 5,12A7,7 0 0,0 12,19A7,7 0 0,0 19,12A7,7 0 0,0 12,5Z"/>
                        </svg>
                        <div>
                            <p id="location-message" class="text-sm font-medium"></p>
                            <p id="location-details" class="text-xs text-gray-500 mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        id="checkin-submit"
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.1 3.89 23 5 23H19C20.1 23 21 22.1 21 21V9ZM19 9H14V4H19V9Z"/>
                    </svg>
                    <span id="submit-text">Check In</span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
class CheckInManager {
    constructor() {
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        this.startCameraBtn = document.getElementById('start-camera');
        this.captureBtn = document.getElementById('capture-btn');
        this.retakeBtn = document.getElementById('retake-photo');
        this.form = document.getElementById('checkin-form');
        this.submitBtn = document.getElementById('checkin-submit');
        
        this.stream = null;
        this.photoTaken = false;
        this.locationValid = false;
        this.currentLocation = null;
        
        this.init();
    }
    
    init() {
        this.startCameraBtn.addEventListener('click', () => this.startCamera());
        this.captureBtn.addEventListener('click', () => this.capturePhoto());
        this.retakeBtn.addEventListener('click', () => this.retakePhoto());
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Listen for location/shift selection changes
        document.querySelectorAll('input[name="location_shift"]').forEach(input => {
            input.addEventListener('change', () => this.validateLocation());
        });
        
        this.updateSubmitButton();
    }
    
    async startCamera() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                } 
            });
            
            this.video.srcObject = this.stream;
            this.video.style.display = 'block';
            document.getElementById('camera-placeholder').style.display = 'none';
            document.getElementById('camera-controls').style.display = 'block';
            this.startCameraBtn.style.display = 'none';
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            showToast('Unable to access camera. Please check permissions.', 'error');
        }
    }
    
    capturePhoto() {
        const context = this.canvas.getContext('2d');
        this.canvas.width = this.video.videoWidth;
        this.canvas.height = this.video.videoHeight;
        
        context.drawImage(this.video, 0, 0);
        
        this.video.style.display = 'none';
        this.canvas.style.display = 'block';
        document.getElementById('camera-controls').style.display = 'none';
        this.retakeBtn.style.display = 'block';
        
        this.photoTaken = true;
        this.updateSubmitButton();
        
        showToast('Photo captured successfully!', 'success');
    }
    
    retakePhoto() {
        this.video.style.display = 'block';
        this.canvas.style.display = 'none';
        document.getElementById('camera-controls').style.display = 'block';
        this.retakeBtn.style.display = 'none';
        
        this.photoTaken = false;
        this.updateSubmitButton();
    }
    
    async validateLocation() {
        const selectedInput = document.querySelector('input[name="location_shift"]:checked');
        if (!selectedInput) {
            this.locationValid = false;
            this.updateSubmitButton();
            return;
        }
        
        const locationId = selectedInput.dataset.locationId;
        
        try {
            const position = await this.getCurrentPosition();
            const response = await fetch('/employee/attendance/validate-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    location_id: locationId,
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                })
            });
            
            const data = await response.json();
            this.currentLocation = position.coords;
            this.locationValid = data.valid;
            
            this.showLocationStatus(data);
            this.updateSubmitButton();
            
        } catch (error) {
            console.error('Location validation error:', error);
            this.showLocationError();
            this.locationValid = false;
            this.updateSubmitButton();
        }
    }
    
    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported'));
                return;
            }
            
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        });
    }
    
    showLocationStatus(data) {
        const statusDiv = document.getElementById('location-status');
        const icon = document.getElementById('location-icon');
        const message = document.getElementById('location-message');
        const details = document.getElementById('location-details');
        
        statusDiv.style.display = 'block';
        
        if (data.valid) {
            statusDiv.className = 'mb-6 p-4 rounded-lg border bg-green-50 border-green-200 text-green-800';
            message.textContent = 'Location verified';
            details.textContent = `You are ${data.distance}m from the location (within ${data.allowed_radius}m radius)`;
        } else {
            statusDiv.className = 'mb-6 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800';
            message.textContent = 'Outside allowed area';
            details.textContent = `You are ${data.distance}m from the location (allowed: ${data.allowed_radius}m radius)`;
        }
    }
    
    showLocationError() {
        const statusDiv = document.getElementById('location-status');
        const message = document.getElementById('location-message');
        const details = document.getElementById('location-details');
        
        statusDiv.style.display = 'block';
        statusDiv.className = 'mb-6 p-4 rounded-lg border bg-yellow-50 border-yellow-200 text-yellow-800';
        message.textContent = 'Location access required';
        details.textContent = 'Please enable location services and try again';
    }
    
    updateSubmitButton() {
        const canSubmit = this.photoTaken && this.locationValid;
        this.submitBtn.disabled = !canSubmit;
        
        if (canSubmit) {
            this.submitBtn.className = 'w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200';
        } else {
            this.submitBtn.className = 'w-full bg-gray-400 text-white py-3 px-4 rounded-md cursor-not-allowed';
        }
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.photoTaken || !this.locationValid || !this.currentLocation) {
            showToast('Please complete all requirements before checking in.', 'error');
            return;
        }
        
        const selectedInput = document.querySelector('input[name="location_shift"]:checked');
        if (!selectedInput) {
            showToast('Please select a location and shift.', 'error');
            return;
        }
        
        this.submitBtn.disabled = true;
        document.getElementById('submit-text').textContent = 'Processing...';
        
        try {
            const photoData = this.canvas.toDataURL('image/jpeg', 0.8);
            
            const formData = {
                location_id: selectedInput.dataset.locationId,
                shift_id: selectedInput.dataset.shiftId,
                latitude: this.currentLocation.latitude,
                longitude: this.currentLocation.longitude,
                accuracy: this.currentLocation.accuracy || 0,
                photo: photoData
            };
            
            const response = await fetch('/employee/attendance/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Check-in successful!', 'success');
                setTimeout(() => {
                    window.location.href = '/employee/dashboard';
                }, 1500);
            } else {
                throw new Error(result.message || 'Check-in failed');
            }
            
        } catch (error) {
            console.error('Check-in error:', error);
            showToast(error.message || 'Check-in failed. Please try again.', 'error');
            
            this.submitBtn.disabled = false;
            document.getElementById('submit-text').textContent = 'Check In';
        }
    }
    
    destroy() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.checkInManager = new CheckInManager();
});

// Cleanup when leaving the page
window.addEventListener('beforeunload', function() {
    if (window.checkInManager) {
        window.checkInManager.destroy();
    }
});
</script>
@endpush
@endsection