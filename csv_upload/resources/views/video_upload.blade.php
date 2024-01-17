@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto mt-5">
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" id="fileInput">
            <button type="button" id="cancelButton" class="text-red-500">Cancel Upload</button>
            <button type="submit" id="uploadButton" class="text-indigo-500">Upload File</button>
        </form>

        <div id="progress" class="text-dark"></div>
        <div class="mt-6">
            <div x-data="{ open: false }">
                <button x-on:click="open = ! open">Toggle Dropdown</button>

                <div x-show.important="open">
                    Dropdown Contents...
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('uploadButton').addEventListener('click', function() {
            var controller = new AbortController();

            var formData = new FormData(document.getElementById('uploadForm'));
            var progressDiv = document.getElementById('progress');
            var cancelButton = document.getElementById('cancelButton');
            var uploadButton = document.getElementById('uploadButton');

            uploadButton.setAttribute('disabled', 'true');
            cancelButton.removeAttribute('disabled');
            cancelButton.addEventListener('click', function(e) {
                controller.abort();
                progressDiv.innerText = 'Upload Canceled!';
                document.getElementById('uploadForm').reset();
                uploadButton.removeAttribute('disabled');
                cancelButton.setAttribute('disabled', 'true');
            });

            // Set up the request with the signal
            axios.post('/video-upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    signal: controller.signal,
                    onUploadProgress: function(progressEvent) {
                        console.log('doprogress');
                        var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent
                            .total);
                        progressDiv.innerText = 'Upload Progress: ' + percentCompleted + '%';
                    },
                })
                .then(function(response) {
                    console.log(response.data.message);
                    progressDiv.innerText = response.data.message;
                })
                .catch(function(error) {
                    if (error.message === 'Request aborted') {
                        // Request was intentionally aborted
                        progressDiv.innerText = 'Upload Canceled!';
                    } else {
                        console.error('Error uploading file:', error);
                    }
                })
                .finally(function() {
                    // Enable upload button and disable cancel button
                    uploadButton.removeAttribute('disabled');
                    cancelButton.setAttribute('disabled', 'true');
                });
        });
    </script>
@endsection
