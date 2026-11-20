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
                        Xin chao, Giang vien!
                    </h1>
                    <p class="text-gray-600">
                        Chao mung ban den voi he thong thi trac nghiem truc tuyen.
                    </p>
                </div>

                <div>
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        Chuc nang giang vien
                    </h3>

                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">

                        <Link
                            :href="route('teacher.chapters.index')"
                            class="rounded-xl bg-white p-6 shadow-sm hover:shadow-md"
                        >
                            <h4 class="mb-2 font-bold text-gray-800">
                                Quan ly chuong
                            </h4>
                            <p class="text-sm text-gray-500">
                                Them, sua, xoa va sap xep chuong theo mon hoc.
                            </p>
                            <p class="mt-4 font-semibold text-blue-600">
                                Truy cap
                            </p>
                        </Link>

                        <Link
                            :href="route('teacher.questions.index')"
                            class="rounded-xl bg-white p-6 shadow-sm hover:shadow-md"
                        >
                            <h4 class="mb-2 font-bold text-gray-800">
                                Ngan hang cau hoi
                            </h4>
                            <p class="text-sm text-gray-500">
                                Quan ly va phan loai cau hoi trac nghiem.
                            </p>
                            <p class="mt-4 font-semibold text-green-600">
                                Truy cap
                            </p>
                        </Link>

                        <Link
                            :href="route('teacher.questions.import')"
                            class="rounded-xl bg-white p-6 shadow-sm hover:shadow-md"
                        >
                            <h4 class="mb-2 font-bold text-gray-800">
                                Import cau hoi
                            </h4>
                            <p class="text-sm text-gray-500">
                                Nhap cau hoi tu file Excel.
                            </p>
                            <p class="mt-4 font-semibold text-purple-600">
                                Truy cap
                            </p>
                        </Link>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
