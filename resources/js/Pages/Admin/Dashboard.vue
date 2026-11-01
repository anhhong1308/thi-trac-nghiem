
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
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
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6 text-gray-900">

                        <h1 class="mb-4 text-2xl font-bold">
                            Xin chào, Quản trị viên!
                        </h1>

                        <p class="mb-6 text-gray-600">
                            Chào mừng bạn đến với hệ thống thi trắc nghiệm trực tuyến.
                        </p>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                            <!-- Quản lý tài khoản -->
                            <Link
                                :href="route('admin.users.index')"
                                class="block rounded-lg bg-blue-50 p-6 transition hover:bg-blue-100 hover:shadow-md"
                            >
                                <h3 class="text-lg font-semibold text-blue-800">
                                    Quản lý tài khoản
                                </h3>

                                <p class="mt-2 text-sm text-gray-600">
                                    Quản lý Admin, Giảng viên và Sinh viên.
                                </p>

                                <p class="mt-4 font-medium text-blue-700">
                                    Truy cập ngay →
                                </p>
                            </Link>

                            <!-- Quản lý môn học -->
                            <Link
                                :href="route('admin.subjects.index')"
                                class="block rounded-lg bg-green-50 p-6 transition hover:bg-green-100 hover:shadow-md"
                            >
                                <h3 class="text-lg font-semibold text-green-800">
                                    Quản lý môn học
                                </h3>

                                <p class="mt-2 text-sm text-gray-600">
                                    Thêm, sửa, xóa môn học và quản lý chương học.
                                </p>

                                <p class="mt-4 font-medium text-green-700">
                                    Truy cập ngay →
                                </p>
                            </Link>

                            <!-- Quản lý hệ thống -->
                            <div class="rounded-lg bg-purple-50 p-6">
                                <h3 class="text-lg font-semibold text-purple-800">
                                    Quản lý hệ thống
                                </h3>

                                <p class="mt-2 text-sm text-gray-600">
                                    Theo dõi và quản lý hoạt động hệ thống.
                                </p>

                                <p class="mt-4 text-sm text-gray-500">
                                    Đang phát triển
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
