@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto mt-5">
        <form id="uploadForm" enctype="multipart/form-data"
            class="p-5 shadow flex items-center justify-between rounded bg-white">
            @csrf
            <input type="file" name="file" id="fileInput"
                class="py-2 pl-3 text-gray-500 rounded mt-2 outline-none focus:border-indigo-500 focus:ring-indigo-400 text-sm file:mr-3 file:py-0.5 file:px-4 file:rounded-md file:border-indigo-500/10 file:text-sm file:bg-indigo-500/20 file:text-indigo-500">
            <div>
                <button type="button" id="cancelButton"
                    class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:focus:ring-yellow-900">Cancel
                    Upload</button>
                <button type="submit" id="uploadButton"
                    class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Upload
                    File</button>
            </div>
        </form>
        <!-- component -->
        <div class="w-full mt-5 p-3 shadow shadow-gray-200 bg-white">
            <div class="flex items-center py-3">
                <span class="w-10 h-10 shrink-0 mr-4 rounded-full bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-500" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M6 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M18 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M4 17h-2v-11a1 1 0 0 1 1 -1h14a5 7 0 0 1 5 7v5h-2m-4 0h-8"></path>
                        <path d="M16 5l1.5 7l4.5 0"></path>
                        <path d="M2 10l15 0"></path>
                        <path d="M7 5l0 5"></path>
                        <path d="M12 5l0 5"></path>
                    </svg>
                </span>
                <div class="space-y-3 flex-1">
                    <div class="flex items-center">
                        <h4 class="font-medium text-sm mr-auto text-gray-700 flex items-center">
                            Public Transport
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 shrink-0 w-5 h-5 text-gray-500"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M12 9h.01"></path>
                                <path d="M11 12h1v4h1"></path>
                            </svg>
                        </h4>
                        <div><span id="progress" class="text-dark font-weight-bold mr-3"></span></div>
                    </div>
                    <div class="overflow-hidden bg-blue-50 h-1.5 rounded-full w-full">
                        <span class="h-full bg-blue-500 w-full block rounded-full text-black transition duration-150"
                            id="progressbar" style="width: 0%"></span>
                    </div>
                </div>
            </div>
        </div>
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
        let progressDiv = document.getElementById('progress');
        let progressBar = document.getElementById('progressbar');
        let controller = new AbortController();
        let cancelButton = document.getElementById('cancelButton');
        let uploadButton = document.getElementById('uploadButton');
        document.getElementById('uploadButton').addEventListener('click', function(e) {
            e.preventDefault();
            if (controller.signal.aborted) {
                controller = new AbortController();
            }
            let formData = new FormData(document.getElementById('uploadForm'));
            console.log(formData.get('file'));
            if (formData.get('file').name.length < 1) {
                console.log(formData.get('file').name)
                alert('no data');
                return;
            }

            // Set up the request with the signal
            axios.post('/video-upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    signal: controller.signal,
                    onUploadProgress: function(progressEvent) {
                        var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent
                            .total);
                        progressDiv.innerText = 'Upload Progress: ' + percentCompleted + '%';
                        progressBar.style.width = percentCompleted + '%';
                    },
                })
                .then(function(response) {
                    progressDiv.innerText = response.data.message;
                })
                .catch(function(error) {
                    if (error.message === 'Request aborted') {
                        progressDiv.innerText = 'Upload Canceled!';
                        progressBar.style.width = '0%'
                    } else {
                        console.error('Error uploading file:', error);
                    }
                })
                .finally(function() {
                    // Enable upload button and disable cancel button
                });
        });

        cancelButton.addEventListener('click', function(e) {
            controller.abort();
            progressDiv.innerText = 'Upload Canceled!';
            progressBar.style.width = '0%';
            document.getElementById('uploadForm').reset();
        });
    </script>
@endsection
