<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $posts = Post::factory(200)->recycle($users)->create();

        $comments = Comment::factory(100)->recycle($users)->recycle($posts)->create();

        $luisa = User::factory()
        ->has(Post::factory(45))
        ->has(Comment::factory(120)->recycle($posts))
        ->create([
            'name' => 'Luísa',
            'email' => 'work.luisa.rvieira@gmail.com',
            'password' => 'Lvieira012'
       ]);
    }
}
