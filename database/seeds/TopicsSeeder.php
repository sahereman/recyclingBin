<?php

use App\Models\Topic;
use App\Models\TopicCategory;
use Illuminate\Database\Seeder;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(TopicCategory::class, 4)->create();

        TopicCategory::all()->each(function (TopicCategory $category) {
            factory(Topic::class, 20)->create([
                'category_id' => $category->id
            ]);
        });
    }
}
