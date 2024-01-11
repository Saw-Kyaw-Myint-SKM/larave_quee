@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto mt-3">
        <div x-data="PROGRESS_DATA" x-init="checkIfIdPresent">
            <h1 x-text="progress"></h1>
            <div class="w-full h-[2px]  bg-gray-500"></div>
            <h5 x-text="pageTitle" class="mb-2"></h5>
            <!-- TW Elements is free under AGPL, with commercial license required for specific uses. See more details: https://tw-elements.com/license/ and contact us for queries at tailwind@mdbootstrap.com -->
            <div class="w-full bg-purple-200 border dark:bg-neutral-600">
                <div class="bg-blue-300 p-0.5 text-center text-xs font-medium leading-none text-primary-100"
                    style="width: 25%">
                    25%
                </div>
            </div>
            <h2></h2>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('PROGRESS_DATA', () => ({
                progress: 'Welcome to progress page',
                pageTitle: 'project Of Uploads',
                progressPercentage: 0,
                params: {
                    id: null
                },
                init() {
                    checkIfIdPresent();
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
                getUploadProgress(){
                    let self=this;
                    checkIfIdPresent();
                    let  progressResponse=setInterval(() => {
                        axios.get('progress/data',{
                          params:{
                            id: self.params.id ?? "{{ session()->get('lastBatchId') }}"
                          }
                        }).then(function(response){
                           console.log(response);
                        })
                    }, 1000);
                }
                
            }))
        })
    </script>
@endsection
