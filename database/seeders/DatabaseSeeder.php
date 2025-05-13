<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Role, User, Book, Bookshelf, Chapter, Page};
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // 1) Core lookups

       Role::factory()->state(['name'=>'admin'])->create();
       Role::factory()->state(['name'=>'employee'])->create();
       Role::factory()->state(['name'=>'customer'])->create();

       $adminRole    = Role::where('name','admin')->first();
       $empRole      = Role::where('name','employee')->first();
       $custRole     = Role::where('name','customer')->first();

       // 2) Users
       User::factory(2)->create(['role_id' => $adminRole->id]);
       User::factory(8)->create(['role_id' => $empRole->id]);
       User::factory(20)->create(['role_id'=> $custRole->id]);

       // create a single admin user with known credentials
       User::factory()->admin()->create();

        // 3) Bookshelves
        Bookshelf::factory(5)->create();
        //  get the IDs of all bookshelves
        $bookshelf_Id  = Bookshelf::all()->pluck('id')->toArray();


        // 4) Books
        Book::factory(20)->create(['bookshelf_id' => $bookshelf_Id[array_rand($bookshelf_Id)]]);
        //  get the IDs of all books
        $book_Id  = Book::all()->pluck('id')->toArray();

        // 5) Chapters
        Chapter::factory(50)->create(['book_id' => $book_Id[array_rand($book_Id)]]);
        //  get the IDs of all chapters
        $chapter_Id  = Chapter::all()->pluck('id')->toArray();

        // 6) Pages
        Page::factory(100)->create(['chapter_id' => $chapter_Id[array_rand($chapter_Id)]]);
    }
}
