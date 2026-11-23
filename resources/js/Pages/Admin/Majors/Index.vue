<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    majors: Object,
    subjects: Array,
});

const search = ref('');
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    code: '',
    description: '',
    is_active: true,
    subject_ids: [],
});

const filteredMajors = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return props.majors.data;
    }

    return props.majors.data.filter(major =>
        major.name.toLowerCase().includes(keyword) ||
        major.code.toLowerCase().includes(keyword)
    );
});

function resetForm() {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    editingId.value = null;
}

function submitForm() {
    if (isEditing.value) {
        form.put(route('admin.majors.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => resetForm(),
        });
    } else {
        form.post(route('admin.majors.store'), {
            preserveScroll: true,
            onSuccess: () => resetForm(),
        });
    }
}

function editMajor(major) {
    isEditing.value = true;
    editingId.value = major.id;

    form.name = major.name;
    form.code = major.code;
    form.description = major.description ?? '';
    form.is_active = Boolean(major.is_active);
    form.subject_ids = major.subjects.map(subject => subject.id);

    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function deleteMajor(major) {
    if (confirm(`Bạn có chắc chắn muốn xóa chuyên ngành "${major.name}" không?`)) {
        router.delete(route('admin.majors.destroy', major.id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Quản lý chuyên ngành" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Quản lý chuyên ngành
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- FORM THÊM / SỬA -->
                <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="mb-5 text-lg font-semibold text-gray-800">
                        {{ isEditing ? 'Cập nhật chuyên ngành' : 'Thêm chuyên ngành mới' }}
                    </h3>

                    <form @submit.prevent="submitForm" class="space-y-5">

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Tên chuyên ngành <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ví dụ: Công nghệ thông tin"
                                />

                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Mã chuyên ngành <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="form.code"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ví dụ: CNTT"
                                />

                                <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.code }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Mô tả
                            </label>

                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Nhập mô tả chuyên ngành..."
                            ></textarea>

                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- CHỌN MÔN HỌC -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Các môn học thuộc chuyên ngành
                            </label>

                            <div class="grid grid-cols-1 gap-3 rounded-md border border-gray-200 p-4 sm:grid-cols-2 lg:grid-cols-3">
                                <label
                                    v-for="subject in subjects"
                                    :key="subject.id"
                                    class="flex cursor-pointer items-center gap-2 rounded-md p-2 hover:bg-gray-50"
                                >
                                    <input
                                        v-model="form.subject_ids"
                                        type="checkbox"
                                        :value="subject.id"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />

                                    <span class="text-sm text-gray-700">
                                        {{ subject.name }}
                                        <span class="text-xs text-gray-400">
                                            ({{ subject.code }})
                                        </span>
                                    </span>
                                </label>

                                <p v-if="subjects.length === 0" class="text-sm text-gray-500">
                                    Chưa có môn học nào. Hãy thêm môn học trước.
                                </p>
                            </div>

                            <p v-if="form.errors.subject_ids" class="mt-1 text-sm text-red-600">
                                {{ form.errors.subject_ids }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />

                            <label for="is_active" class="text-sm text-gray-700">
                                Đang hoạt động
                            </label>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{ form.processing
                                    ? 'Đang xử lý...'
                                    : (isEditing ? 'Cập nhật chuyên ngành' : 'Thêm chuyên ngành')
                                }}
                            </button>

                            <button
                                v-if="isEditing"
                                type="button"
                                @click="resetForm"
                                class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Hủy chỉnh sửa
                            </button>
                        </div>
                    </form>
                </div>

                <!-- DANH SÁCH CHUYÊN NGÀNH -->
                <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                    <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Danh sách chuyên ngành
                        </h3>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Tìm theo tên hoặc mã chuyên ngành..."
                            class="w-full rounded-md border-gray-300 shadow-sm sm:max-w-sm"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        STT
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Chuyên ngành
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Môn học
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">
                                        Trạng thái
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">
                                        Thao tác
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="(major, index) in filteredMajors"
                                    :key="major.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                        {{ (majors.current_page - 1) * majors.per_page + index + 1 }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ major.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ major.code }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="subject in major.subjects"
                                                :key="subject.id"
                                                class="rounded-full bg-indigo-50 px-2 py-1 text-xs text-indigo-700"
                                            >
                                                {{ subject.name }}
                                            </span>

                                            <span
                                                v-if="major.subjects.length === 0"
                                                class="text-sm text-gray-400"
                                            >
                                                Chưa có môn học
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-center">
                                        <span
                                            :class="major.is_active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-100 text-gray-600'"
                                            class="rounded-full px-3 py-1 text-xs font-medium"
                                        >
                                            {{ major.is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-center">
                                        <button
                                            type="button"
                                            @click="editMajor(major)"
                                            class="mr-3 text-sm font-medium text-indigo-600 hover:text-indigo-900"
                                        >
                                            Sửa
                                        </button>

                                        <button
                                            type="button"
                                            @click="deleteMajor(major)"
                                            class="text-sm font-medium text-red-600 hover:text-red-900"
                                        >
                                            Xóa
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="filteredMajors.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Không tìm thấy chuyên ngành nào.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PHÂN TRANG -->
                    <div
                        v-if="majors.links && majors.links.length > 3"
                        class="mt-5 flex flex-wrap gap-2"
                    >
                        <template v-for="(link, index) in majors.links" :key="index">
                            <button
                                v-if="link.url"
                                type="button"
                                @click="router.get(link.url, {}, {
                                    preserveScroll: true,
                                    preserveState: true,
                                })"
                                v-html="link.label"
                                :class="link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm"
                            ></button>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
