
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subjects: Object,
});

const showForm = ref(false);
const editingSubject = ref(null);
const search = ref('');

const form = useForm({
    name: '',
    code: '',
    description: '',
    is_active: true,
});

const resetForm = () => {
    form.reset();
    form.clearErrors();
    editingSubject.value = null;
    showForm.value = false;
};

const openCreate = () => {
    resetForm();
    showForm.value = true;
};

const openEdit = (subject) => {
    editingSubject.value = subject;

    form.name = subject.name;
    form.code = subject.code;
    form.description = subject.description ?? '';
    form.is_active = Boolean(subject.is_active);

    form.clearErrors();
    showForm.value = true;
};

const submit = () => {
    if (editingSubject.value) {
        form.put(route('admin.subjects.update', editingSubject.value.id), {
            onSuccess: resetForm,
        });
    } else {
        form.post(route('admin.subjects.store'), {
            onSuccess: resetForm,
        });
    }
};

const deleteSubject = (subject) => {
    if (confirm(`Bạn có chắc chắn muốn xóa môn học "${subject.name}"?`)) {
        router.delete(route('admin.subjects.destroy', subject.id));
    }
};

const filteredSubjects = () => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) return props.subjects.data;

    return props.subjects.data.filter(subject =>
        subject.name.toLowerCase().includes(keyword) ||
        subject.code.toLowerCase().includes(keyword)
    );
};
</script>

<template>
    <Head title="Quản lý môn học" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Quản lý môn học
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">

                    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">
                                Danh sách môn học
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Quản lý môn học và chương học trong hệ thống.
                            </p>
                        </div>

                        <button
                            @click="openCreate"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700"
                        >
                            + Thêm môn học
                        </button>
                    </div>

                    <div v-if="$page.props.flash?.success"
                        class="mb-4 rounded-lg bg-green-100 p-3 text-green-700">
                        {{ $page.props.flash.success }}
                    </div>

                    <div v-if="$page.props.flash?.error"
                        class="mb-4 rounded-lg bg-red-100 p-3 text-red-700">
                        {{ $page.props.flash.error }}
                    </div>

                    <div v-if="showForm" class="mb-8 rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            {{ editingSubject ? 'Cập nhật môn học' : 'Thêm môn học mới' }}
                        </h3>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Tên môn học *
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Ví dụ: Lịch sử Đảng Cộng sản Việt Nam"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Mã môn học *
                                </label>
                                <input
                                    v-model="form.code"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Ví dụ: LSDCSVN01"
                                />
                                <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.code }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Mô tả
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Mô tả nội dung môn học..."
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <div>
                                <label class="flex items-center gap-2">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600"
                                    />
                                    <span class="text-sm text-gray-700">Đang hoạt động</span>
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Đang lưu...' : 'Lưu môn học' }}
                                </button>

                                <button
                                    type="button"
                                    @click="resetForm"
                                    class="rounded-lg border border-gray-300 px-5 py-2 text-gray-700 hover:bg-gray-100"
                                >
                                    Hủy
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mb-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Tìm theo tên hoặc mã môn học..."
                            class="w-full rounded-lg border-gray-300 sm:max-w-md"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="bg-gray-100 text-sm text-gray-700">
                                    <th class="border p-3">STT</th>
                                    <th class="border p-3">Tên môn học</th>
                                    <th class="border p-3">Mã môn</th>
                                    <th class="border p-3">Số chương</th>
                                    <th class="border p-3">Trạng thái</th>
                                    <th class="border p-3 text-center">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(subject, index) in filteredSubjects()"
                                    :key="subject.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="border p-3">
                                        {{ index + 1 }}
                                    </td>

                                    <td class="border p-3 font-medium text-gray-800">
                                        {{ subject.name }}
                                    </td>

                                    <td class="border p-3">
                                        {{ subject.code }}
                                    </td>

                                    <td class="border p-3">
                                        {{ subject.chapters_count }}
                                    </td>

                                    <td class="border p-3">
                                        <span
                                            :class="subject.is_active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-100 text-gray-600'"
                                            class="rounded-full px-3 py-1 text-xs font-medium"
                                        >
                                            {{ subject.is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                        </span>
                                    </td>

                                    <td class="border p-3">
                                        <div class="flex justify-center gap-2">
                                            <button
                                                @click="openEdit(subject)"
                                                class="rounded bg-yellow-100 px-3 py-1 text-sm text-yellow-700 hover:bg-yellow-200"
                                            >
                                                Sửa
                                            </button>

                                            <button
                                                @click="deleteSubject(subject)"
                                                class="rounded bg-red-100 px-3 py-1 text-sm text-red-700 hover:bg-red-200"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="filteredSubjects().length === 0">
                                    <td colspan="6" class="border p-6 text-center text-gray-500">
                                        Chưa có môn học phù hợp.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="subjects.links?.length > 3" class="mt-5 flex flex-wrap gap-2">
                        <template v-for="(link, index) in subjects.links" :key="index">
                            <button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                v-html="link.label"
                                :class="link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="rounded px-3 py-2 text-sm"
                            ></button>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
