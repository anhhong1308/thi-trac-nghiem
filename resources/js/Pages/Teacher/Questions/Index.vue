
<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
    questions: Object,
});

const deleteQuestion = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này không?')) {
        router.delete(route('teacher.questions.destroy', id));
    }
};

const questionType = (type) => {
    return type === 'single_choice'
        ? 'Một đáp án đúng'
        : 'Nhiều đáp án đúng';
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="mx-auto max-w-7xl">

            <!-- Tiêu đề và các nút chức năng -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Ngân hàng câu hỏi
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Quản lý và phân loại câu hỏi trắc nghiệm
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <!-- Nút Import Excel -->
                    <Link
                        :href="route('teacher.questions.import')"
                        class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                    >
                        ↑ Import Excel
                    </Link>

                    <!-- Nút thêm câu hỏi -->
                    <Link
                        :href="route('teacher.questions.create')"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        + Thêm câu hỏi
                    </Link>
                </div>
            </div>

            <!-- Bảng danh sách câu hỏi -->
            <div class="overflow-hidden rounded-xl bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-5 py-4">STT</th>
                                <th class="px-5 py-4">Nội dung câu hỏi</th>
                                <th class="px-5 py-4">Môn học</th>
                                <th class="px-5 py-4">Chương</th>
                                <th class="px-5 py-4">Mức độ Bloom</th>
                                <th class="px-5 py-4">Loại câu hỏi</th>
                                <th class="px-5 py-4">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="(question, index) in questions.data"
                                :key="question.id"
                                class="border-t hover:bg-gray-50"
                            >
                                <td class="px-5 py-4">
                                    {{ questions.from + index }}
                                </td>

                                <td class="max-w-md px-5 py-4">
                                    {{ question.content }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ question.subject?.name }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ question.chapter?.name }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ question.bloom_level?.name }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ questionType(question.type) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <Link
                                        :href="route('teacher.questions.edit', question.id)"
                                        class="mr-3 text-blue-600 hover:underline"
                                    >
                                        Sửa
                                    </Link>

                                    <button
                                        @click="deleteQuestion(question.id)"
                                        class="text-red-600 hover:underline"
                                    >
                                        Xóa
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="questions.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-5 py-12 text-center text-gray-500"
                                >
                                    Chưa có câu hỏi nào trong ngân hàng.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="flex flex-wrap items-center justify-between gap-3 border-t px-5 py-4">
                    <span class="text-sm text-gray-500">
                        Tổng số: {{ questions.total }} câu hỏi
                    </span>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in questions.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="rounded border px-3 py-1 text-sm"
                            :class="{
                                'bg-blue-600 text-white': link.active,
                                'pointer-events-none opacity-40': !link.url
                            }"
                        />
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
