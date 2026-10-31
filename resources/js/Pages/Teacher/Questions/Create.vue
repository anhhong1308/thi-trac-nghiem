
<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    subjects: {
        type: Array,
        default: () => [],
    },
    chapters: {
        type: Array,
        default: () => [],
    },
    bloomLevels: {
        type: Array,
        default: () => [],
    },
});

// Khởi tạo biểu mẫu
const form = useForm({
    subject_id: '',
    chapter_id: '',
    bloom_level_id: '',
    content: '',
    type: 'single_choice',
    explanation: '',
    options: [
        { content: '', is_correct: false },
        { content: '', is_correct: false },
        { content: '', is_correct: false },
        { content: '', is_correct: false },
    ],
});

// Lọc chương theo môn học
const filteredChapters = computed(() => {
    return props.chapters.filter(
        chapter => String(chapter.subject_id) === String(form.subject_id)
    );
});

// Lọc lỗi của các phương án trả lời
// Sửa lỗi startsWith gây trắng trang
const optionErrors = computed(() => {
    return Object.entries(form.errors)
        .filter(([key]) => typeof key === 'string' && key.startsWith('options.'))
        .map(([key, message]) => ({
            key,
            message,
        }));
});

// Chọn một đáp án đúng
const selectSingleCorrect = (selectedIndex) => {
    form.options.forEach((option, index) => {
        option.is_correct = index === selectedIndex;
    });
};

// Chọn nhiều đáp án đúng
const selectMultipleCorrect = (selectedIndex, checked) => {
    form.options[selectedIndex].is_correct = checked;
};

// Khi thay đổi loại câu hỏi
const changeType = () => {
    form.options.forEach(option => {
        option.is_correct = false;
    });
};

// Thêm phương án
const addOption = () => {
    form.options.push({
        content: '',
        is_correct: false,
    });
};

// Xóa phương án
const removeOption = (index) => {
    if (form.options.length <= 2) {
        return;
    }

    form.options.splice(index, 1);
};

// Gửi biểu mẫu
const submit = () => {
    if (!form.subject_id) {
        alert('Vui lòng chọn môn học!');
        return;
    }

    if (!form.chapter_id) {
        alert('Vui lòng chọn chương học!');
        return;
    }

    if (!form.bloom_level_id) {
        alert('Vui lòng chọn mức độ Bloom!');
        return;
    }

    if (!form.content.trim()) {
        alert('Vui lòng nhập nội dung câu hỏi!');
        return;
    }

    if (form.options.length < 2) {
        alert('Câu hỏi phải có ít nhất 2 phương án!');
        return;
    }

    const hasEmptyOption = form.options.some(
        option => !option.content.trim()
    );

    if (hasEmptyOption) {
        alert('Vui lòng nhập đầy đủ nội dung các phương án!');
        return;
    }

    const correctCount = form.options.filter(
        option => option.is_correct === true
    ).length;

    if (form.type === 'single_choice' && correctCount !== 1) {
        alert('Câu hỏi một đáp án phải có chính xác 1 đáp án đúng!');
        return;
    }

    if (form.type === 'multiple_choice' && correctCount < 1) {
        alert('Câu hỏi nhiều đáp án phải có ít nhất 1 đáp án đúng!');
        return;
    }

    // Gửi dữ liệu lên Laravel
    form.transform(data => ({
        ...data,
        options: data.options.map(option => ({
            content: option.content.trim(),
            is_correct: Boolean(option.is_correct),
        })),
    }));

    form.post(route('teacher.questions.store'), {
        preserveScroll: true,

        onSuccess: () => {
            console.log('Lưu câu hỏi thành công.');
        },

        onError: (errors) => {
            console.error('Lỗi khi lưu câu hỏi:', errors);
        },

        onFinish: () => {
            form.transform(data => data);
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="mx-auto max-w-4xl">

            <!-- Tiêu đề -->
            <div class="mb-6">
                <a
                    :href="route('teacher.questions.index')"
                    class="text-sm text-blue-600 hover:underline"
                >
                    ← Quay lại ngân hàng câu hỏi
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-800">
                    Thêm câu hỏi mới
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Nhập nội dung và thiết lập đáp án cho câu hỏi.
                </p>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-6 rounded-xl bg-white p-6 shadow"
            >

                <!-- Môn học -->
                <div>
                    <label class="mb-2 block font-medium">
                        Môn học <span class="text-red-500">*</span>
                    </label>

                    <select
                        v-model="form.subject_id"
                        @change="form.chapter_id = ''"
                        class="w-full rounded-lg border-gray-300"
                        required
                    >
                        <option value="">-- Chọn môn học --</option>

                        <option
                            v-for="subject in props.subjects"
                            :key="subject.id"
                            :value="subject.id"
                        >
                            {{ subject.name }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.subject_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.subject_id }}
                    </p>
                </div>

                <!-- Chương học -->
                <div>
                    <label class="mb-2 block font-medium">
                        Chương học <span class="text-red-500">*</span>
                    </label>

                    <select
                        v-model="form.chapter_id"
                        class="w-full rounded-lg border-gray-300"
                        required
                    >
                        <option value="">-- Chọn chương --</option>

                        <option
                            v-for="chapter in filteredChapters"
                            :key="chapter.id"
                            :value="chapter.id"
                        >
                            {{ chapter.name }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.chapter_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.chapter_id }}
                    </p>
                </div>

                <!-- Mức độ Bloom -->
                <div>
                    <label class="mb-2 block font-medium">
                        Mức độ Bloom <span class="text-red-500">*</span>
                    </label>

                    <select
                        v-model="form.bloom_level_id"
                        class="w-full rounded-lg border-gray-300"
                        required
                    >
                        <option value="">-- Chọn mức độ --</option>

                        <option
                            v-for="level in props.bloomLevels"
                            :key="level.id"
                            :value="level.id"
                        >
                            {{ level.name }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.bloom_level_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.bloom_level_id }}
                    </p>
                </div>

                <!-- Nội dung câu hỏi -->
                <div>
                    <label class="mb-2 block font-medium">
                        Nội dung câu hỏi <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        v-model="form.content"
                        rows="4"
                        class="w-full rounded-lg border-gray-300"
                        placeholder="Nhập nội dung câu hỏi..."
                        required
                    ></textarea>

                    <p
                        v-if="form.errors.content"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.content }}
                    </p>
                </div>

                <!-- Loại câu hỏi -->
                <div>
                    <label class="mb-2 block font-medium">
                        Loại câu hỏi
                    </label>

                    <select
                        v-model="form.type"
                        @change="changeType"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="single_choice">
                            Một đáp án đúng
                        </option>

                        <option value="multiple_choice">
                            Nhiều đáp án đúng
                        </option>
                    </select>
                </div>

                <!-- Các phương án trả lời -->
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <label class="font-medium">
                            Các phương án trả lời
                            <span class="text-red-500">*</span>
                        </label>

                        <button
                            type="button"
                            @click="addOption"
                            class="text-sm font-medium text-blue-600 hover:underline"
                        >
                            + Thêm phương án
                        </button>
                    </div>

                    <div
                        v-for="(option, index) in form.options"
                        :key="index"
                        class="mb-3 flex items-center gap-3 rounded-lg border p-3 transition"
                        :class="option.is_correct
                            ? 'border-green-500 bg-green-50'
                            : 'border-gray-200 bg-white'"
                    >
                        <!-- Radio hoặc checkbox -->
                        <label
                            :for="`correct-option-${index}`"
                            class="flex shrink-0 cursor-pointer items-center gap-2"
                        >
                            <input
                                v-if="form.type === 'single_choice'"
                                :id="`correct-option-${index}`"
                                type="radio"
                                name="correct_option"
                                :checked="option.is_correct"
                                @change="selectSingleCorrect(index)"
                                class="h-5 w-5 cursor-pointer accent-green-600"
                            />

                            <input
                                v-else
                                :id="`correct-option-${index}`"
                                type="checkbox"
                                :checked="option.is_correct"
                                @change="selectMultipleCorrect(index, $event.target.checked)"
                                class="h-5 w-5 cursor-pointer accent-green-600"
                            />

                            <span class="font-semibold text-gray-700">
                                {{ String.fromCharCode(65 + index) }}.
                            </span>
                        </label>

                        <!-- Nội dung phương án -->
                        <input
                            v-model="option.content"
                            type="text"
                            class="min-w-0 flex-1 rounded-lg border-gray-300"
                            :placeholder="`Nhập phương án ${String.fromCharCode(65 + index)}...`"
                            required
                        />

                        <!-- Trạng thái đáp án -->
                        <span
                            v-if="option.is_correct"
                            class="shrink-0 text-sm font-semibold text-green-700"
                        >
                            Đáp án đúng
                        </span>

                        <!-- Xóa phương án -->
                        <button
                            type="button"
                            @click="removeOption(index)"
                            :disabled="form.options.length <= 2"
                            class="shrink-0 text-sm text-red-600 hover:underline disabled:cursor-not-allowed disabled:opacity-30"
                        >
                            Xóa
                        </button>
                    </div>

                    <p class="mt-2 text-sm text-gray-500">
                        Chọn nút tròn nếu chỉ có một đáp án đúng.
                        Chọn ô vuông nếu có nhiều đáp án đúng.
                    </p>

                    <!-- Lỗi phương án -->
                    <p
                        v-if="form.errors.options"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.options }}
                    </p>

                    <p
                        v-for="item in optionErrors"
                        :key="item.key"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ item.message }}
                    </p>
                </div>

                <!-- Giải thích -->
                <div>
                    <label class="mb-2 block font-medium">
                        Giải thích đáp án
                    </label>

                    <textarea
                        v-model="form.explanation"
                        rows="3"
                        class="w-full rounded-lg border-gray-300"
                        placeholder="Nhập giải thích đáp án (không bắt buộc)..."
                    ></textarea>
                </div>

                <!-- Lỗi tổng quát -->
                <div
                    v-if="form.hasErrors"
                    class="rounded-lg bg-red-50 p-3 text-sm text-red-700"
                >
                    Có lỗi khi lưu câu hỏi. Vui lòng kiểm tra lại thông tin.
                </div>

                <!-- Nút thao tác -->
                <div class="flex justify-end gap-3 border-t pt-5">
                    <a
                        :href="route('teacher.questions.index')"
                        class="rounded-lg border px-5 py-2 text-gray-600 hover:bg-gray-50"
                    >
                        Hủy
                    </a>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Đang lưu...' : 'Lưu câu hỏi' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
