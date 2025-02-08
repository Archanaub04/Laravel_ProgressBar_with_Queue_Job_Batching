@extends('layouts.index')
@section('content')
    <div class="container-content mx-auto mt-6 p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-xl text-center mb-5">Choose a file to Upload</h1>

        <form id="upload-form" class="upload-form">
            @csrf
            <input type="file" name="fileInput" id="fileInput" required>
            <button type="submit" id="submit-btn" class="btn" onclick="uploadFile()">Upload</button>
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
            const chunkSize = 2 * 1024 * 1024; // 2MB per chunk
            let fileInput = $('#fileInput');

            $('#upload-form').submit(function(e) {
                e.preventDefault();
                uploadFile();
            });

            function uploadFile() {
                let file = fileInput[0].files[0];
                if (!file) {
                    alert("Please select a file.");
                    return;
                }

                let totalChunks = Math.ceil(file.size / chunkSize);
                let fileName = file.name;
                let chunkIndex = 0;

                $('#error-container').hide();
                $('#completion-message').hide();
                $('.progress-bar-fill').css('width', '0%');
                $('#progress-container').show();
                $('#submit-btn').prop('disabled', true).text('Uploading...');

                function uploadChunk(start) {
                    let end = Math.min(start + chunkSize, file.size);
                    let chunk = file.slice(start, end);
                    let formData = new FormData();
                    formData.append("file", chunk);
                    formData.append("fileName", fileName);
                    formData.append("chunkIndex", chunkIndex);
                    formData.append("totalChunks", totalChunks);
                    formData.append("_token", "{{ csrf_token() }}");

                    $.ajax({
                        url: "{{ route('upload.chunk') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function() {
                            chunkIndex++;
                            let progress = Math.round((chunkIndex / totalChunks) * 100);
                            $('.progress-bar-fill').css('width', progress + '%').text(progress + '%');
                            $('#progress-text').text('Upload is in progress (' + progress +
                                '% complete)');

                            if (chunkIndex < totalChunks) {
                                uploadChunk(end);
                            } else {
                                $('#progress-container').hide();
                                $('#completion-message').show();
                                $('#submit-btn').prop('disabled', false).text('Upload');
                            }
                        },
                        error: function(jqXHR) {
                            $('#error-container').text('Error: ' + jqXHR.responseText).show();
                            $('#submit-btn').prop('disabled', false).text('Upload');
                        }
                    });
                }

                uploadChunk(0);
            }
        });
    </script>
@endsection
