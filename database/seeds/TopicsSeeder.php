<?php

use Illuminate\Database\Seeder;
use App\Models\TopicCategory;
use App\Models\Topic;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory(TopicCategory::class,4)->create();

        TopicCategory::all()->each(function (TopicCategory $category)
        {

            factory(Topic::class,20)->create([
                'category_id' => $category->id
            ]);

        });

    }
}
