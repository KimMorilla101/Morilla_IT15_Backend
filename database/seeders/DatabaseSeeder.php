<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@morilla.test',
        ], [
            'name' => 'School Admin',
            'password' => 'password123',
        ]);

        $categories = [
            [
                'name' => 'Announcements',
                'slug' => 'announcements',
            ],
            [
                'name' => 'Guides',
                'slug' => 'guides',
            ],
            [
                'name' => 'Events',
                'slug' => 'events',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::query()->updateOrCreate([
                'slug' => $categoryData['slug'],
            ], [
                'name' => $categoryData['name'],
            ]);

            $posts = match ($category->slug) {
                'announcements' => [
                    [
                        'title' => 'Site Launch',
                        'description' => 'We are live. Explore the categories and see the latest updates.',
                    ],
                    [
                        'title' => 'Weekly Update',
                        'description' => 'New posts are added every week to keep you informed.',
                    ],
                ],
                'guides' => [
                    [
                        'title' => 'Getting Started',
                        'description' => 'Pick a category to see posts filtered on the right side.',
                    ],
                    [
                        'title' => 'Writing Posts',
                        'description' => 'Keep titles short and descriptions clear and focused.',
                    ],
                ],
                default => [
                    [
                        'title' => 'Campus Meetup',
                        'description' => 'Join the community meetup this weekend and meet other members.',
                    ],
                    [
                        'title' => 'Workshop Day',
                        'description' => 'Hands-on learning sessions with practical demonstrations.',
                    ],
                ],
            };

            foreach ($posts as $post) {
                Post::query()->updateOrCreate([
                    'category_id' => $category->id,
                    'title' => $post['title'],
                ], [
                    'description' => $post['description'],
                ]);
            }
        }

        $this->call([
            CourseSeeder::class,
            StudentSeeder::class,
            SchoolDaySeeder::class,
        ]);
    }
}
