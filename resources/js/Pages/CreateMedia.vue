<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import axios from 'axios';
    import { Head } from '@inertiajs/vue3';
    export default {

        components:{
            AuthenticatedLayout,
            Head
        },

        data() {
            return {
                dragging:false,
                media : [],
            };
        },


        methods:{
            onDroppedFiles($event){
                this.dragging = false;
                let files = [...$event.dataTransfer.items]
                .filter(item => item.kind === 'file')
                .map(item => item.getAsFile());

               this.uploadFiles(files);

            },

            onSelectedFiles($event){
                let files = [...$event.target.files];

                this.uploadFiles(files);

                this.$refs.files.value = null;

            },

            uploadFiles(files){

                files.forEach(file => {
                    this.media.unshift({
                        file : file,
                        progress:0,
                        error:null,
                        uploaded:false,
                        preview_url:null,
                        id:null
                    })
                });

                this.media.filter(media => !media.uploaded).forEach(media => {
                    let form = new FormData;

                    form.append('file',media.file);
                    // Post Request
                    axios.post(this.route('media.store'),form,{
                        onUploadProgress: (event) => {
                            media.progress = Math.round(event.loaded * 100 / event.total);
                        }
                    })
                    // Response
                    .then(({data}) => {
                        media.uploaded    = true;
                        media.id          = data.id;
                        media.preview_url = data.preview_url;
                    })
                    .catch(error => {
                        // Error Control
                        media.error = `Upload Failed!`;

                        if(error?.response.status === 422){
                            media.error = error.response.data.errors.file[0];
                        }

                    })
                })
            }


        }
    }
</script>


<template>
    <Head title="New Media" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                New Media
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                @drop.prevent="onDroppedFiles"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                :class="[dragging ? 'border-indigo-500' : 'border-gray-400','flex flex-col border-2 px-6 py-12 items-center border-dashed rounded-md']"
                >
                <svg class="w-12 h-12 text-gray-500 "
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
                </svg>
                <p class="text-xl text-gray-700">Drop File Upload</p>
                <p class="mb-2 text-gray-700">or</p>
                <label class="bg-white px-4 h-9 inline-flex items-center rounded border border-gray-300 shadow-sm text-sm font-medium text-gray-700 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                    Select Files
                    <input ref="files" @input="onSelectedFiles" type="file" name="files" multiple class="sr-only">
                </label>
                <p class="text-xs text-gray-700 mt-5">Max File Upload Size : 512 MB</p>
                </div>
            </div>

            <ul class="my-6 bg-white rounded divide-y divide-gray-200 shadow">
                <li v-for="(item,index) in media" :key="index"
                class="p-3 flex items-center space-x-2">

                    <div class="w-9 h-9 bg-gray-300 flex-shrink-0">
                        <img :src="item.preview_url" class="h-full w-full rounded" :alt="item.file.name" />
                    </div>
                    <div class="text-sm text-gray-700 flex-1">{{  item.file.name }}</div>
                    <!-- Thumbnail Div -->
                    <div v-if="!item.uploaded && !item.error"
                    class="w-40 bg-gray-200 rounded-full h-5 shadow-inner overflow-hidden relative flex items-center justify-center">
                        <div class="inline-block h-full bg-indigo-600 absolute top-0 left-0" :style="`width: ${item.progress}%`"></div>
                        <div class="relative z-10 text-xs font-semibold text-center text-white drop-shadow text-shadow">{{ item.progress }}%</div>
                    </div>
                    <!-- #Thumbnail Div -->

                    <!-- Error -->
                    <div v-if="item.error" class="text-sm text-red-600">{{ item.error }}</div>
                    <!-- #Error -->

                    <inertia-link href="#" v-if="item.uploaded" class="text-sm text-indigo-600 underline">Edit</inertia-link>

                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>





