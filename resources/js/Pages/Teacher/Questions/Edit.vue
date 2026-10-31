
<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    question: {
        type: Object,
        required: true,
    },
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

const form = useForm({
    subject_id: String(props.question.subject_id ?? ''),
    chapter_id: String(props.question.chapter_id ?? ''),
    bloom_level_id: String(props.question.bloom_level_id ?? ''),
    content: props.question.content ?? '',
    type: props.question.type ?? 'single_choice',
    explanation: props.question.explanation ?? '',
    options: (props.question.options ?? [])
        .sort((a, b) => (a.position ?? a.id) - (b.position ?? b.id))
        .map(option => ({
            content: option.content ?? '',
            is_correct: Boolean(option.is_correct),
        })),
});

const filteredChapters = computed(() => {
    return props.chapters.filter(
        chapter => String(chapter.subject_id) === String(form.subject_id)
    );
});

const optionErrors = computed(() => {
    return Object.entries(form.errors)
        .filter(([key]) => key.startsWith('options'))
        .map(([key, message]) => ({ key, message }));
});

const addOption = () => {
    form.options.push({
        content: '',
        is_correct: false,
    });
};

const removeOption = (index) => {
    if (form.options.length <= 2) {
        alert('Câu hỏi phải có ít nhất 2 phương án.');
        return;
    }

    form.options.splice(index, 1);
};

const selectType = (type) => {
    form.type = type;

    if (type === 'single_choice') {
        const firstCorrect = form.options.findIndex(
            option => option.is_correct
        );

        form.options.forEach((option, index) => {
            option.is_correct = index === firstCorrect;
        });
    }
};

const toggleCorrect = (index) => {
    if (form.type === 'single_choice') {
        form.options.forEach((option, i) => {
            option.is_correct = i === index;
        });
    } else {
        form.options[index].is_correct =
            !form.options[index].is_correct;
    }
};

const submit = () => {
    form.put(route('teacher.questions.update', props.question.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Chỉnh sửa câu hỏi
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Cập nhật thông tin câu hỏi trong ngân hàng.
                    </p>
                </div>

                <Link
                    :href="route('teacher.questions.index')"
                    class="rounded-lg border bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Quay lại
                </Link>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-6 rounded-xl bg-white p-6 shadow-sm"
            >
                <!-- Môn học -->
                <div>
                    <label class="mb-2 block font-medium text-gray-700">
                        Môn học
                    </label>

                    <select
                        v-model="form.subject_id"
                        @change="form.chapter_id = ''"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="">-- Chọn môn học --</option>
                        <option
                            v-for="subject in subjects"
                            :key="subject.id"
                            :value="String(subject.id)"
                        >
                            {{ subject.name }}
                        </option>
                    </select>

                    <p v-if="form.errors.subject_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.subject_id }}
                    </p>
                </div>

                <!-- Chương -->
                <div>
                    <label class="mb-2 block font-medium text-gray-700">
                        Chương
                    </label>

                    <select
                        v-model="form.chapter_id"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="">-- Chọn chương --</option>
                        <option
                            v-for="chapter in filteredChapters"
                            :key="chapter.id"
                            :value="String(chapter.id)"
                        >
                            {{ chapter.name }}
                        </option>
                    </select>

                    <p v-if="form.errors.chapter_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.chapter_id }}
                    </p>
                </div>

                <!-- Bloom -->
                <div>
                    <label class="mb-2 block font-medium text-gray-700">
                        Mức độ Bloom
                    </label>

                    <select
                        v-model="form.bloom_level_id"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="">-- Chọn mức độ --</option>
                        <option
                            v-for="level in bloomLevels"
                            :key="level.id"
                            :value="String(level.id)"
                        >
                            {{ level.name }}
                        </option>
                    </select>

                    <p v-if="form.errors.bloom_level_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.bloom_level_id }}
                    </p>
                </div>

                <!-- Nội dung -->
                <div>
                    <label class="mb-2 block font-medium text-gray-700">
                        Nội dung câu hỏi
                    </label>

                    <textarea
                        v-model="form.content"
                        rows="4"
                        class="w-full rounded-lg border-gray-300"
                        placeholder="Nhập nội dung câu hỏi..."
                    ></textarea>

                    <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                        {{ form.errors.content }}
                    </p>
                </div>

                <!-- Loại câu hỏi -->
                <div>
                    <label class="mb-2 block font-medium text-gray-700">
                        Loại câu hỏi
                    </label>

                    <select
                        :value="form.type"
                        @change="selectType($event.target.value)"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="single_choice">Một đáp án đúng</option>
                        <option value="multiple_choice">Nhiều đáp án đúng</option>
                    </select>
                </div>

                <!-- Phương án -->
                <div class="border-t pt-5">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-gray-800">
                                Danh sách phương án
                            </h2>
                            <p class="text-sm text-gray-500">
                                Chọn phương án đúng bằng cách tích vào ô bên cạnh.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="addOption"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            + Thêm phương án
                        </button>
                    </div>

                    <div
                        v-for="(option, index) in form.options"
                        :key="index"
                        class="mb-3 flex items-start gap-3"
                    >
                        <div class="flex flex-1 items-center gap-3">
                            <span class="mt-3 font-semibold text-gray-500">
                                {{ String.fromCharCode(65 + index) }}.
                            </span>

                            <div class="flex-1">
                                <input
                                    v-model="option.content"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300"
                                    :placeholder="`Nội dung phương án ${index + 1}`"
                                />
                            </div>
                        </div>

                        <label class="mt-2 flex items-center gap-2 whitespace-nowrap">
                            <input
                                type="checkbox"
                                :checked="option.is_correct"
                                @change="toggleCorrect(index)"
                                class="rounded border-gray-300 text-blue-600"
                            />
                            <span class="text-sm text-gray-700">Đúng</span>
                        </label>

                        <button
                            type="button"
                            @click="removeOption(index)"
                            class="mt-1 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50"
                        >
                            Xóa
                        </button>
                    </div>

                    <p
                        v-for="item in optionErrors"
                        :key="item.key"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ item.message }}
                    </p>

                    <p class="mt-3 text-sm text-gray-500">
                        {{
                            form.type === 'single_choice'
                                ? 'Câu hỏi phải có chính xác một phương án đúng.'
                                : 'Câu hỏi phải có ít nhất một phương án đúng.'
                        }}
                    </p>
                </div>

                <!-- Giải thích -->
                <div>
                    <label class="mb-2 block font-medium text-gray-700">
                        Giải thích đáp án (không bắt buộc)
                    </label>

                    <textarea
                        v-model="form.explanation"
                        rows="3"
                        class="w-full rounded-lg border-gray-300"
                        placeholder="Nhập giải thích nếu có..."
                    ></textarea>
                </div>

                <!-- Nút cập nhật -->
                <div class="flex justify-end gap-3 border-t pt-5">
                    <Link
                        :href="route('teacher.questions.index')"
                        class="rounded-lg border px-5 py-2.5 text-gray-700 hover:bg-gray-50"
                    >
                        Hủy
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? 'Đang cập nhật...'
                                : 'Cập nhật câu hỏi'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
