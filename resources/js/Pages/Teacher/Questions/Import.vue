
<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    preview: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    file: null,
});

const uploadFile = () => {
    form.post(route('teacher.questions.import.preview'), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const confirmImport = () => {
    if (!confirm('Bạn có chắc chắn muốn nhập các câu hỏi này vào ngân hàng câu hỏi?')) {
        return;
    }

    form.post(route('teacher.questions.import.confirm'), {
        preserveScroll: true,
    });
};

const questionType = (type) => {
    return type === 'single_choice'
        ? 'Một đáp án'
        : 'Nhiều đáp án';
};
</script>

<template>
    <div class="p-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Import câu hỏi từ Excel
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Tải file Excel lên để kiểm tra và nhập câu hỏi vào ngân hàng.
                </p>
            </div>

            <Link
                :href="route('teacher.questions.index')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
            >
                Quay lại ngân hàng câu hỏi
            </Link>
        </div>

        <!-- Tải file mẫu -->
        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">
                1. Tải file Excel mẫu
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Sử dụng file mẫu để nhập câu hỏi đúng định dạng hệ thống.
            </p>

            <a
                :href="route('teacher.questions.import.template')"
                class="mt-4 inline-flex rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
            >
                Tải file Excel mẫu
            </a>
        </div>

        <!-- Chọn file -->
        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">
                2. Tải file Excel lên
            </h2>

            <form @submit.prevent="uploadFile" class="mt-4 space-y-4">
                <input
                    type="file"
                    accept=".xlsx,.xls"
                    @change="form.file = $event.target.files[0]"
                    class="block w-full rounded-lg border border-gray-300 p-3 text-sm"
                />

                <p class="text-xs text-gray-500">
                    Hỗ trợ file .xlsx, .xls. Dung lượng tối đa 5MB.
                </p>

                <p
                    v-if="form.errors.file"
                    class="text-sm text-red-600"
                >
                    {{ form.errors.file }}
                </p>

                <button
                    type="submit"
                    :disabled="!form.file || form.processing"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ form.processing ? 'Đang kiểm tra...' : 'Tải lên và xem trước' }}
                </button>
            </form>
        </div>

        <!-- Kết quả xem trước -->
        <div
            v-if="preview"
            class="space-y-4 rounded-xl border bg-white p-5 shadow-sm"
        >
            <h2 class="text-lg font-semibold text-gray-800">
                3. Kết quả kiểm tra dữ liệu
            </h2>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-gray-100 p-4">
                    <p class="text-sm text-gray-600">Tổng số câu hỏi</p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ preview.total }}
                    </p>
                </div>

                <div class="rounded-lg bg-green-50 p-4">
                    <p class="text-sm text-green-700">Hợp lệ</p>
                    <p class="mt-1 text-2xl font-bold text-green-700">
                        {{ preview.valid }}
                    </p>
                </div>

                <div class="rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-red-700">Không hợp lệ</p>
                    <p class="mt-1 text-2xl font-bold text-red-700">
                        {{ preview.invalid }}
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3">Dòng</th>
                            <th class="border p-3">Nội dung câu hỏi</th>
                            <th class="border p-3">Loại câu hỏi</th>
                            <th class="border p-3">Đáp án đúng</th>
                            <th class="border p-3">Trạng thái</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="row in preview.rows"
                            :key="row.row_number"
                            class="align-top"
                        >
                            <td class="border p-3">
                                {{ row.row_number }}
                            </td>

                            <td class="border p-3">
                                <div class="min-w-48 whitespace-pre-wrap">
                                    {{ row.content }}
                                </div>

                                <ul
                                    v-if="row.errors.length"
                                    class="mt-2 list-disc space-y-1 pl-5 text-red-600"
                                >
                                    <li
                                        v-for="(error, index) in row.errors"
                                        :key="index"
                                    >
                                        {{ error }}
                                    </li>
                                </ul>
                            </td>

                            <td class="border p-3">
                                {{ questionType(row.type) }}
                            </td>

                            <td class="border p-3">
                                {{ row.correct_answers }}
                            </td>

                            <td class="border p-3">
                                <span
                                    v-if="row.valid"
                                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700"
                                >
                                    Hợp lệ
                                </span>

                                <span
                                    v-else
                                    class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700"
                                >
                                    Lỗi dữ liệu
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="preview.invalid > 0"
                class="rounded-lg bg-red-50 p-4 text-sm text-red-700"
            >
                File còn {{ preview.invalid }} dòng không hợp lệ.
                Vui lòng sửa file Excel và tải lên lại trước khi nhập.
            </div>

            <div
                v-else
                class="rounded-lg bg-green-50 p-4 text-sm text-green-700"
            >
                Tất cả câu hỏi đều hợp lệ. Bạn có thể xác nhận nhập vào hệ thống.
            </div>

            <button
                type="button"
                @click="confirmImport"
                :disabled="preview.invalid > 0 || form.processing"
                class="rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                Xác nhận nhập {{ preview.valid }} câu hỏi
            </button>
        </div>
    </div>
</template>
