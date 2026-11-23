
<script setup>
import { Head, router } from '@inertiajs/vue3';

defineProps({
    accountStatus: {
        type: String,
        required: true,
    },
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Trạng thái tài khoản" />

    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-8 text-center shadow-lg">

            <div
                v-if="accountStatus === 'pending'"
                class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-amber-100"
            >
                <svg class="h-10 w-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div
                v-else
                class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-100"
            >
                <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <template v-if="accountStatus === 'pending'">
                <h1 class="mb-3 text-2xl font-bold text-gray-800">
                    Tài khoản đang chờ phê duyệt
                </h1>

                <p class="leading-relaxed text-gray-600">
                    Cảm ơn bạn đã đăng ký tài khoản giảng viên.
                    Tài khoản của bạn hiện đang chờ Admin xác nhận.
                    Bạn sẽ được sử dụng các chức năng giảng viên
                    sau khi tài khoản được phê duyệt.
                </p>

                <div class="mt-6 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">
                    Vui lòng quay lại sau khi Admin hoàn tất xét duyệt.
                </div>
            </template>

            <template v-else>
                <h1 class="mb-3 text-2xl font-bold text-gray-800">
                    Tài khoản chưa được phê duyệt
                </h1>

                <p class="leading-relaxed text-gray-600">
                    Rất tiếc, yêu cầu đăng ký tài khoản giảng viên
                    của bạn chưa được chấp thuận.
                    Vui lòng liên hệ Admin để biết thêm thông tin.
                </p>

                <div class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                    Bạn chưa thể sử dụng các chức năng giảng viên.
                </div>
            </template>

            <button
                @click="logout"
                class="mt-8 rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-700"
            >
                Đăng xuất
            </button>
        </div>
    </div>
</template>
