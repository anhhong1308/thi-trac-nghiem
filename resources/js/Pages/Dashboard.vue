<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Trang giảng viên" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Trang giảng viên
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h1 class="mb-3 text-2xl font-bold text-gray-800">
                        Xin chào, Giảng viên!
                    </h1>
                    <p class="text-gray-600">
                        Chào mừng bạn đến với hệ thống thi trắc nghiệm trực tuyến.
                    </p>
                </div>

                <div>
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        Chức năng giảng viên
                    </h3>

                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">

                        <Link
                            :href="route('teacher.chapters.index')"
                            class="rounded-xl bg-white p-6 shadow-sm hover:shadow-md"
                        >
                            <h4 class="mb-2 font-bold text-gray-800">
                                Quản lý chương
                            </h4>
                            <p class="text-sm text-gray-500">
                                Thêm, sửa, xóa và sắp xếp chương theo môn học.
                            </p>
                            <p class="mt-4 font-semibold text-blue-600">
                                Truy cập →
                            </p>
                        </Link>

                        <Link
                            :href="route('teacher.questions.index')"
                            class="rounded-xl bg-white p-6 shadow-sm hover:shadow-md"
                        >
                            <h4 class="mb-2 font-bold text-gray-800">
                                Ngân hàng câu hỏi
                            </h4>
                            <p class="text-sm text-gray-500">
                                Quản lý và phân loại câu hỏi trắc nghiệm.
                            </p>
                            <p class="mt-4 font-semibold text-green-600">
                                Truy cập →
                            </p>
                        </Link>

                        <Link
                            :href="route('teacher.questions.import')"
                            class="rounded-xl bg-white p-6 shadow-sm hover:shadow-md"
                        >
                            <h4 class="mb-2 font-bold text-gray-800">
                                Import câu hỏi
                            </h4>
                            <p class="text-sm text-gray-500">
                                Nhập câu hỏi từ file Excel.
                            </p>
                            <p class="mt-4 font-semibold text-purple-600">
                                Truy cập →
                            </p>
                        </Link>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>