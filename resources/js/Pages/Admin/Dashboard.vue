```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Trang quản trị hệ thống
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h1 class="mb-4 text-2xl font-bold">
                            Xin chào, Quản trị viên!
                        </h1>

                        <p class="mb-6 text-gray-600">
                            Chào mừng bạn đến với hệ thống thi trắc nghiệm trực tuyến.
                        </p>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="rounded-lg bg-blue-50 p-6">
                                <h3 class="text-lg font-semibold text-blue-800">
                                    Quản lý tài khoản
                                </h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Quản lý Admin, Giảng viên và Sinh viên.
                                </p>
                            </div>

                            <div class="rounded-lg bg-green-50 p-6">
                                <h3 class="text-lg font-semibold text-green-800">
                                    Quản lý môn học
                                </h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Quản lý môn học và chương học.
                                </p>
                            </div>

                            <div class="rounded-lg bg-purple-50 p-6">
                                <h3 class="text-lg font-semibold text-purple-800">
                                    Quản lý hệ thống
                                </h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Theo dõi và quản lý hoạt động hệ thống.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
```