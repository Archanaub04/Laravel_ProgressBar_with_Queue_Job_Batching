@extends('layouts.index')
@section('content')
    <div class="container-content mx-auto mt-6 p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-xl text-center mb-5">Choose a Excel file to Upload</h1>

        <form id="upload-form" class="upload-form">
            @csrf
            <input type="file" name="mycsv" id="mycsv" required>
            <button type="submit" id="submit-btn" class="btn">Upload</button>
        </form>

        <div id="error-container" class="error-message" style="display: none;"></div>

        <div id="progress-container" style="display: none;">
            <div class="status-message">
                <p id="progress-text">Upload is in progress (0% complete)</p>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill"></div>
            </div>
        </div>

        <div id="completion-message" class="status-message" style="display: none;">
            <p>Upload Completed!</p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let batchId = null;
            let progressInterval = null;

            // Setup AJAX CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function updateProgress(id) {
                if (progressInterval) return;

                progressInterval = setInterval(() => {
                    $.get(`/batch/${id}`)
                        .done(function(data) {
                            const progress = data.progress || 0;
                            $('.progress-bar-fill').css('width', `${progress}%`);
                            $('#progress-text').text(`Upload is in progress (${progress}% complete)`);

                            if (progress >= 100) {
                                clearInterval(progressInterval);
                                progressInterval = null;
                                $('#progress-container').hide();
                                $('#completion-message').show();
                            }
                        })
                        .fail(function() {
                            clearInterval(progressInterval);
                            $('#error-container').text('Failed to fetch progress').show();
                        });
                }, 2000);
            }

            $('#upload-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $('#submit-btn');

                // Reset UI
                $('#error-container').hide();
                $('#completion-message').hide();
                $('.progress-bar-fill').css('width', '0%');
                $('#progress-container').show();
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: '/upload',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        batchId = response.id;
                        updateProgress(batchId);
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.errors ?
                            Object.values(xhr.responseJSON.errors).flat().join(', ') :
                            'An error occurred during upload';
                        $('#error-container').text(errorMsg).show();
                        $('#progress-container').hide();
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            // Check for in-progress batch on page load
            $.get('/batch-in-progress')
                .done(function(data) {
                    if (data?.id) {
                        batchId = data.id;
                        $('#progress-container').show();
                        updateProgress(batchId);
                    }
                });
        });
    </script>
@endsection
