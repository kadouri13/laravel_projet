<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Users
        DB::table('users')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'string',
                'email' => 'user@example.com',
                'email_verified_at' => null,
                'password' => '$2y$10$EKKg.J3tsX3X8aoPp92CT.mzKgE6SXA6.RZW4dYr7VIzE8vS8iDYC',
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => '2026-04-25 13:05:05',
                'updated_at' => '2026-04-25 13:05:05',
                'profile_picture' => 'string',
                'background' => 'string'
            ],
            [
                'id' => 2,
                'name' => 'testt',
                'email' => 'accy@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$8nxEQo1Gaoues0e24EFV/endlSb1Ho8bkAzEpWqigr78ZNM/qkDqi',
                'role' => 'author',
                'remember_token' => null,
                'created_at' => '2026-05-01 13:00:08',
                'updated_at' => '2026-05-01 13:44:30',
                'profile_picture' => 'https://github.com/shadcn.png',
                'background' => 'TEST'
            ],
            [
                'id' => 4,
                'name' => 'Reviewer',
                'email' => 'reviewer@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$qgl5IhdhAPQea3m8Nj2gY.1whCMrVsEYn1goQvDZhLiXjiLctWRtW',
                'role' => 'reviewer',
                'remember_token' => null,
                'created_at' => '2026-05-01 14:38:07',
                'updated_at' => '2026-05-01 14:38:07',
                'profile_picture' => 'https://github.com/shadcn.png',
                'background' => null
            ],
            [
                'id' => 5,
                'name' => 'Editor',
                'email' => 'editor@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$zL600J0V0znD7dzgZQu17OqosspYmkzbG7yiNlMkwgFIsKq6XCuk6',
                'role' => 'editor',
                'remember_token' => null,
                'created_at' => '2026-05-01 14:38:44',
                'updated_at' => '2026-05-01 14:38:44',
                'profile_picture' => 'https://github.com/shadcn.png',
                'background' => null
            ],
            [
                'id' => 6,
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$b9k.KY8VAsDBIZXO64HwLOf6QvBCrCrK0U4tNFnibVUkxa1oKhi1W',
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => '2026-05-01 14:39:29',
                'updated_at' => '2026-05-01 14:39:29',
                'profile_picture' => 'https://github.com/shadcn.png',
                'background' => null
            ]
        ]);

        // Articles
        DB::table('articles')->insertOrIgnore([
            [
                'id' => 1,
                'author_id' => 2,
                'title' => 'test',
                'abstract' => 'test',
                'file_path' => null,
                'status' => 'submitted',
                'created_at' => '2026-05-01 14:05:50',
                'updated_at' => '2026-05-01 19:59:17',
                'published_at' => null,
                'ai_decision' => null
            ],
            [
                'id' => 2,
                'author_id' => 2,
                'title' => 'sidahmed sidahmed',
                'abstract' => 'test test testtest test testtest test testtest test testtest test testtest test testtest test testtest test testtest test testtest test test',
                'file_path' => null,
                'status' => 'submitted',
                'created_at' => '2026-05-01 14:28:56',
                'updated_at' => '2026-05-01 20:02:11',
                'published_at' => null,
                'ai_decision' => 'Human Authored'
            ],
            [
                'id' => 3,
                'author_id' => 2,
                'title' => 'test',
                'abstract' => 'test',
                'file_path' => null,
                'status' => 'submitted',
                'created_at' => '2026-05-01 20:11:03',
                'updated_at' => '2026-05-01 20:11:03',
                'published_at' => null,
                'ai_decision' => null
            ],
            [
                'id' => 4,
                'author_id' => 2,
                'title' => 'test',
                'abstract' => 'test',
                'file_path' => null,
                'status' => 'submitted',
                'created_at' => '2026-05-01 20:12:00',
                'updated_at' => '2026-05-01 20:12:00',
                'published_at' => null,
                'ai_decision' => null
            ],
            [
                'id' => 5,
                'author_id' => 2,
                'title' => 'article 3',
                'abstract' => 'tesdt',
                'file_path' => 'https://wxbupnqadndzgthnnebo.supabase.co/storage/v1/object/public/main/manuscripts/d123b868-e8d7-4acd-9e34-073d3fe2dfad.pdf',
                'status' => 'submitted',
                'created_at' => '2026-05-01 20:25:11',
                'updated_at' => '2026-05-01 20:25:11',
                'published_at' => null,
                'ai_decision' => null
            ]
        ]);

        // Comments
        DB::table('comments')->insertOrIgnore([
            [
                'id' => 1,
                'article_id' => 1,
                'user_id' => 4,
                'content' => 'sdq',
                'created_at' => '2026-05-01 15:35:01',
                'updated_at' => '2026-05-01 15:35:01'
            ],
            [
                'id' => 2,
                'article_id' => 1,
                'user_id' => 4,
                'content' => 'hello',
                'created_at' => '2026-05-01 18:31:30',
                'updated_at' => '2026-05-01 18:31:30'
            ],
            [
                'id' => 3,
                'article_id' => 3,
                'user_id' => 5,
                'content' => 'HELLO',
                'created_at' => '2026-05-01 20:48:47',
                'updated_at' => '2026-05-01 20:48:47'
            ]
        ]);

        // Reviews
        DB::table('reviews')->insertOrIgnore([
            [
                'id' => 1,
                'article_id' => 1,
                'reviewer_id' => 4,
                'status' => 'completed',
                'decision' => 'accepted',
                'comments' => 'test',
                'created_at' => '2026-05-01 15:10:40',
                'updated_at' => '2026-05-01 16:12:55'
            ],
            [
                'id' => 2,
                'article_id' => 2,
                'reviewer_id' => 4,
                'status' => 'pending',
                'decision' => null,
                'comments' => null,
                'created_at' => '2026-05-01 18:30:56',
                'updated_at' => '2026-05-01 18:30:56'
            ]
        ]);
        
        // Reset sequences for PostgreSQL so it knows we manually inserted IDs
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));");
            DB::statement("SELECT setval('articles_id_seq', (SELECT MAX(id) FROM articles));");
            DB::statement("SELECT setval('comments_id_seq', (SELECT MAX(id) FROM comments));");
            DB::statement("SELECT setval('reviews_id_seq', (SELECT MAX(id) FROM reviews));");
        }
    }
}
