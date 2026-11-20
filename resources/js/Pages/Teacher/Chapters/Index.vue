<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    chapters: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    subjects: {
        type: Array,
        default: () => [],
    },
});

const showForm = ref(false);
const editingChapter = ref(null);
const search = ref('');
const filterSubject = ref('');

const form = useForm({
    subject_id: '',
    name: '',
    description: '',
    order: 1,
    is_active: true,
});

const filteredChapters = computed(() => {
    return (props.chapters.data || []).filter(chapter => {
        const matchSearch =
            chapter.name.toLowerCase().includes(search.value.toLowerCase());

        const matchSubject =
            !filterSubject.value ||
            String(chapter.subject_id) === String(filterSubject.value);

        return matchSearch && matchSubject;
    });
});

function openCreate() {
    editingChapter.value = null;
    form.reset();
    form.subject_id = props.subjects[0]?.id || '';
    form.order = 1;
    form.is_active = true;
    showForm.value = true;
}

function openEdit(chapter) {
    editingChapter.value = chapter;

    form.subject_id = chapter.subject_id;
    form.name = chapter.name;
    form.description = chapter.description || '';
    form.order = chapter.order;
    form.is_active = chapter.is_active;

    showForm.value = true;
}

function submit() {
    if (editingChapter.value) {
        form.put(route('teacher.chapters.update', editingChapter.value.id), {
            onSuccess: () => {
                showForm.value = false;
                form.reset();
                editingChapter.value = null;
            },
        });
    } else {
        form.post(route('teacher.chapters.store'), {
            onSuccess: () => {
                showForm.value = false;
                form.reset();
            },
        });
    }
}

function deleteChapter(chapter) {
    if (!confirm(`Bạn có chắc muốn xóa chương "${chapter.name}" không?`)) {
        return;
    }

    router.delete(route('teacher.chapters.destroy', chapter.id));
}
</script>

<template>
    <Head title="Quản lý chương" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">
                    Quản lý chương
                </h2>

                <button
                    @click="openCreate"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Thêm chương
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">
                        Danh sách chương
                    </h3>

                    <p class="mb-5 text-sm text-gray-500">
                        Quản lý chương theo từng môn học để phục vụ việc xây
                        dựng ngân hàng câu hỏi.
                    </p>

                    <div class="mb-5 grid gap-3 md:grid-cols-2">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Tìm kiếm tên chương..."
                            class="rounded-lg border-gray-300"
                        />

                        <select
                            v-model="filterSubject"
                            class="rounded-lg border-gray-300"
                        >
                            <option value="">Tất cả môn học</option>
                            <option
                                v-for="subject in subjects"
                                :key="subject.id"
                                :value="subject.id"
                            >
                                {{ subject.name }}
                            </option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">STT</th>
                                    <th class="px-4 py-3">Tên chương</th>
                                    <th class="px-4 py-3">Môn học</th>
                                    <th class="px-4 py-3">Thứ tự</th>
                                    <th class="px-4 py-3">Trạng thái</th>
                                    <th class="px-4 py-3 text-center">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                <tr
                                    v-for="(chapter, index) in filteredChapters"
                                    :key="chapter.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3">
                                        {{ index + 1 }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">
                                            {{ chapter.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ chapter.description }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ chapter.subject?.name || '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ chapter.order }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            :class="chapter.is_active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-100 text-gray-600'"
                                            class="rounded-full px-3 py-1 text-xs font-medium"
                                        >
                                            {{ chapter.is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-3">
                                            <button
                                                @click="openEdit(chapter)"
                                                class="font-medium text-indigo-600 hover:underline"
                                            >
                                                Sửa
                                            </button>

                                            <button
                                                @click="deleteChapter(chapter)"
                                                class="font-medium text-red-600 hover:underline"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="filteredChapters.length === 0">
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        Chưa có chương nào.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="chapters.links?.length > 3"
                        class="mt-5 flex flex-wrap gap-2"
                    >
                        <button
                            v-for="(link, index) in chapters.links"
                            :key="index"
                            @click="link.url && router.visit(link.url)"
                            :disabled="!link.url"
                            v-html="link.label"
                            :class="[
                                'rounded border px-3 py-2 text-sm',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700',
                                !link.url ? 'cursor-not-allowed opacity-50' : ''
                            ]"
                        />
                    </div>
                </div>

                <!-- Form thêm / sửa chương -->
                <div
                    v-if="showForm"
                    class="rounded-xl bg-white p-6 shadow-sm"
                >
                    <h3 class="mb-5 text-lg font-semibold text-gray-800">
                        {{ editingChapter ? 'Cập nhật chương' : 'Thêm chương mới' }}
                    </h3>

                    <form @submit.prevent="submit" class="space-y-4">

                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Môn học
                            </label>

                            <select
                                v-model="form.subject_id"
                                class="w-full rounded-lg border-gray-300"
                                required
                            >
                                <option value="" disabled>
                                    -- Chọn môn học --
                                </option>

                                <option
                                    v-for="subject in subjects"
                                    :key="subject.id"
                                    :value="subject.id"
                                >
                                    {{ subject.name }} ({{ subject.code }})
                                </option>
                            </select>

                            <div v-if="form.errors.subject_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.subject_id }}
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Tên chương
                            </label>

                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border-gray-300"
                                placeholder="Ví dụ: Chương 1 - Tổng quan"
                                required
                            />

                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Mô tả
                            </label>

                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-lg border-gray-300"
                                placeholder="Mô tả nội dung chương..."
                            />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Thứ tự chương
                            </label>

                            <input
                                v-model.number="form.order"
                                type="number"
                                min="1"
                                class="w-full rounded-lg border-gray-300"
                                required
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600"
                            />

                            <label for="is_active" class="text-sm">
                                Chương đang hoạt động
                            </label>
                        </div>

                        <div class="flex gap-3 pt-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Đang lưu...' : 'Lưu chương' }}
                            </button>

                            <button
                                type="button"
                                @click="showForm = false"
                                class="rounded-lg border px-5 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Hủy
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
