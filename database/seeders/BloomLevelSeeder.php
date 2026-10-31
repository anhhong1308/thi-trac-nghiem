<?php

namespace Database\Seeders;

use App\Models\BloomLevel;
use Illuminate\Database\Seeder;

class BloomLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Nhớ',
                'code' => 'REMEMBER',
                'level' => 1,
                'description' => 'Khả năng ghi nhớ và nhận biết kiến thức đã học.',
            ],
            [
                'name' => 'Hiểu',
                'code' => 'UNDERSTAND',
                'level' => 2,
                'description' => 'Khả năng giải thích và diễn giải kiến thức.',
            ],
            [
                'name' => 'Vận dụng',
                'code' => 'APPLY',
                'level' => 3,
                'description' => 'Khả năng áp dụng kiến thức để giải quyết vấn đề.',
            ],
            [
                'name' => 'Phân tích',
                'code' => 'ANALYZE',
                'level' => 4,
                'description' => 'Khả năng phân tích và xác định mối quan hệ giữa các thành phần.',
            ],
            [
                'name' => 'Đánh giá',
                'code' => 'EVALUATE',
                'level' => 5,
                'description' => 'Khả năng nhận xét và đánh giá dựa trên tiêu chí.',
            ],
            [
                'name' => 'Sáng tạo',
                'code' => 'CREATE',
                'level' => 6,
                'description' => 'Khả năng tạo ra ý tưởng hoặc giải pháp mới.',
            ],
        ];

        foreach ($levels as $level) {
            BloomLevel::updateOrCreate(
                ['code' => $level['code']],
                $level
            );
        }
    }
}