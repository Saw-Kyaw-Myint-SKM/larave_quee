@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto mt-3">
        <div x-data="PROGRESS_DATA">
            <h1 x-text="progress"></h1>
            <div class="w-full h-[2px]  bg-gray-500"></div>
            <h5 x-text="pageTitle" class="mb-2"></h5>
            <!-- TW Elements is free under AGPL, with commercial license required for specific uses. See more details: https://tw-elements.com/license/ and contact us for queries at tailwind@mdbootstrap.com -->
            <div class="w-full bg-purple-200 border dark:bg-neutral-600">
                <div class="bg-blue-400 p-0.5 text-center text-xs font-medium leading-none text-primary-100"
                    x-bind:style="`width:${progressPercentage}%`" x-text="progressPercentage + '%'">
                </div>
            </div>
            <h2></h2>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"
        integrity="sha512-TjBzDQIDnc6pWyeM1bhMnDxtWH0QpOXMcVooglXrali/Tj7W569/wd4E8EDjk1CwOAOPSJon1VfcEt1BI4xIrA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('PROGRESS_DATA', () => ({
                progress: 'Welcome to progress page',
                pageTitle: 'project Of Uploads',
                progressPercentage: 0,
                params: {
                    id: null
                },
                checkIfIdPresent() {
                    const params = Object.fromEntries(
                        new URLSearchParams(window.location.search)
                    )
                    // console.log(params)
                    if (params.id) {
                        this.params.id = params.id;
                    }
                },
                getUploadProgress() {
                    let self = this;
                    this.checkIfIdPresent();
                    let progressResponse = setInterval(() => {
                        axios.get('progress/data', {
                            params: {
                                id: self.params.id ??
                                    "{{ session()->get('lastBatchId') }}"
                            }
                        }).then(function(response) {
                            if (response.data) {
                                let totalJobs = parseInt(response.data.total_jobs);
                                let pendingJobs = parseInt(response.data.pending_jobs)
                                let completedJobs = totalJobs - pendingJobs;
                                if (pendingJobs == 0) {
                                    self.progressPercentage = 100;
                                } else {
                                    self.progressPercentage = parseInt(completedJobs /
                                        totalJobs * 100).toFixed(0);
                                }
                                if (parseInt(self.progressPercentage) >= 100) {
                                    clearInterval(progressResponse);
                                }
                            }
                        })
                    }, 1000);
                },
                init() {
                    this.$nextTick(() => {
                        this.getUploadProgress();
                    })
                },
            }))
        })
    </script>
@endsection
