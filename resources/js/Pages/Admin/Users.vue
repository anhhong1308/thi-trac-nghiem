
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const showForm = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'student',
});

const pendingTeachers = computed(() =>
    props.users.filter(
        user => user.role === 'teacher' && user.account_status === 'pending'
    )
);

const resetForm = () => {
    form.reset();
    form.clearErrors();
    editingUser.value = null;
    showForm.value = false;
};

const openCreateForm = () => {
    resetForm();
    showForm.value = true;
};

const openEditForm = (user) => {
    editingUser.value = user;

    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;

    form.clearErrors();
    showForm.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('admin.users.update', editingUser.value.id), {
            onSuccess: resetForm,
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: resetForm,
        });
    }
};

const approveTeacher = (user) => {
    if (confirm(`Bạn có chắc muốn duyệt tài khoản giảng viên ${user.name}?`)) {
        router.patch(route('admin.users.approve', user.id));
    }
};

const rejectTeacher = (user) => {
    if (confirm(`Bạn có chắc muốn từ chối tài khoản giảng viên ${user.name}?`)) {
        router.patch(route('admin.users.reject', user.id));
    }
};

const deleteUser = (user) => {
    if (confirm(`Bạn có chắc muốn xóa tài khoản ${user.name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const roleName = (role) => {
    return {
        admin: 'Quản trị viên',
        teacher: 'Giảng viên',
        student: 'Sinh viên',
    }[role] || role;
};

const statusName = (status) => {
    return {
        approved: 'Đã duyệt',
        pending: 'Chờ duyệt',
        rejected: 'Đã từ chối',
    }[status] || status;
};

const statusClass = (status) => {
    return {
        approved: 'bg-green-100 text-green-700',
        pending: 'bg-amber-100 text-amber-700',
        rejected: 'bg-red-100 text-red-700',
    }[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="Quản lý tài khoản" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800">
                    Quản lý tài khoản
                </h2>

                <Link
                    :href="route('admin.dashboard')"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                    ← Quay lại Dashboard
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Thống kê -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Tổng tài khoản</p>
                        <p class="mt-2 text-3xl font-bold text-gray-800">
                            {{ users.length }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-amber-50 p-5 shadow-sm">
                        <p class="text-sm text-amber-700">Giảng viên chờ duyệt</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">
                            {{ pendingTeachers.length }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-green-50 p-5 shadow-sm">
                        <p class="text-sm text-green-700">Tài khoản đã duyệt</p>
                        <p class="mt-2 text-3xl font-bold text-green-700">
                            {{ users.filter(user => user.account_status === 'approved').length }}
                        </p>
                    </div>
                </div>

                <!-- Danh sách tài khoản -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b p-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">
                                Danh sách tài khoản
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Quản lý Admin, giảng viên và sinh viên trong hệ thống.
                            </p>
                        </div>

                        <button
                            @click="openCreateForm"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            + Thêm tài khoản
                        </button>
                    </div>

                    <!-- Form thêm / sửa -->
                    <div v-if="showForm" class="border-b bg-gray-50 p-6">
                        <h3 class="mb-4 font-bold text-gray-800">
                            {{ editingUser ? 'Cập nhật tài khoản' : 'Thêm tài khoản mới' }}
                        </h3>

                        <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Họ và tên
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full rounded-lg border-gray-300"
                                    required
                                />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div v-if="!editingUser">
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Mật khẩu
                                </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    minlength="8"
                                    class="w-full rounded-lg border-gray-300"
                                    required
                                />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Vai trò
                                </label>
                                <select
                                    v-model="form.role"
                                    class="w-full rounded-lg border-gray-300"
                                >
                                    <option value="admin">Quản trị viên</option>
                                    <option value="teacher">Giảng viên</option>
                                    <option value="student">Sinh viên</option>
                                </select>
                                <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.role }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2 md:col-span-2">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-lg bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Đang lưu...' : 'Lưu tài khoản' }}
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

                    <!-- Bảng tài khoản -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-6 py-4">STT</th>
                                    <th class="px-6 py-4">Họ và tên</th>
                                    <th class="px-6 py-4">Email</th>
                                    <th class="px-6 py-4">Vai trò</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(user, index) in users" :key="user.id">
                                    <td class="px-6 py-4">{{ index + 1 }}</td>

                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ user.name }}
                                        <span
                                            v-if="user.id === $page.props.auth.user.id"
                                            class="ml-1 text-xs text-indigo-600"
                                        >
                                            (Bạn)
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-gray-600">
                                        {{ user.email }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ roleName(user.role) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="statusClass(user.account_status)"
                                        >
                                            {{ statusName(user.account_status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">

                                            <template
                                                v-if="user.role === 'teacher' && user.account_status === 'pending'"
                                            >
                                                <button
                                                    @click="approveTeacher(user)"
                                                    class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                                                >
                                                    Duyệt
                                                </button>

                                                <button
                                                    @click="rejectTeacher(user)"
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700"
                                                >
                                                    Từ chối
                                                </button>
                                            </template>

                                            <button
                                                @click="openEditForm(user)"
                                                class="rounded-md bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100"
                                            >
                                                Sửa
                                            </button>

                                            <button
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="deleteUser(user)"
                                                class="rounded-md bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="users.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        Chưa có tài khoản nào.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
