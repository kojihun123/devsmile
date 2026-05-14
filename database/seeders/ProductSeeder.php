<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1, // 감정류
                'name' => '개발자의 미소',
                'description' => '전설로만 전해지는 그것. 실제로 존재하는지 미확인 상태입니다.',
                'price' => 19900,
                'stock' => 99,
                'status' => 'active',
            ],
            [
                'category_id' => 1, // 감정류
                'name' => '개발자의 눈물 전송중...',
                'description' => '배포 직전 발견된 버그와 함께 흘리는 그 눈물. 패킷 손실 없이 배송됩니다.',
                'price' => 9900,
                'stock' => 50,
                'status' => 'active',
            ],
            [
                'category_id' => 4, // 에러류
                'name' => '404 행복을 찾을 수 없습니다',
                'description' => '열심히 찾았지만 이 페이지는 존재하지 않습니다. 행복도 마찬가지입니다.',
                'price' => 4040,
                'stock' => 404,
                'status' => 'active',
            ],
            [
                'category_id' => 2, // 음료류
                'name' => '커밋 한 잔',
                'description' => 'git commit -m "일단 된다" 직후 마시는 그 한 잔. 오늘도 수고했습니다.',
                'price' => 14900,
                'stock' => 30,
                'status' => 'active',
            ],
            [
                'category_id' => 3, // 인생류
                'name' => '무한 로딩 중인 인생',
                'description' => '완료까지 예상 시간: 알 수 없음. 진행률: ░░░░░░░░░░ 0%',
                'price' => 29900,
                'stock' => 10,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
