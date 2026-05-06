<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrate Authors and Publishers from books table
        DB::table('books')->orderBy('id')->chunk(100, function ($books) {
            foreach ($books as $book) {
                $authorId = null;
                $publisherId = null;

                if (!empty($book->author)) {
                    DB::table('authors')->updateOrInsert(
                        ['name' => $book->author],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                    $authorId = DB::table('authors')->where('name', $book->author)->value('id');
                }

                if (!empty($book->publisher)) {
                    DB::table('publishers')->updateOrInsert(
                        ['name' => $book->publisher],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                    $publisherId = DB::table('publishers')->where('name', $book->publisher)->value('id');
                }

                DB::table('books')->where('id', $book->id)->update([
                    'author_id' => $authorId,
                    'publisher_id' => $publisherId,
                ]);
            }
        });

        // 2. Migrate Digital Collections to Books table
        $digitalCollections = DB::table('digital_collections')->get();
        foreach ($digitalCollections as $dc) {
            $authorId = null;
            $publisherId = null;

            if (!empty($dc->author)) {
                DB::table('authors')->updateOrInsert(
                    ['name' => $dc->author],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                $authorId = DB::table('authors')->where('name', $dc->author)->value('id');
            }

            if (!empty($dc->publisher)) {
                DB::table('publishers')->updateOrInsert(
                    ['name' => $dc->publisher],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                $publisherId = DB::table('publishers')->where('name', $dc->publisher)->value('id');
            }

            // Create Book entry
            DB::table('books')->insert([
                'title' => $dc->title,
                'isbn' => $dc->isbn,
                'author' => $dc->author ?? '', 
                'publisher' => $dc->publisher,
                'author_id' => $authorId,
                'publisher_id' => $publisherId,
                'publication_year' => $dc->year,
                'description' => $dc->description,
                'cover_image' => $dc->thumbnail,
                'is_digital' => true,
                'digital_file_path' => $dc->file_path,
                'digital_file_type' => $dc->type,
                'digital_file_size' => $dc->file_size,
                'digital_download_count' => $dc->download_count,
                'digital_view_count' => $dc->view_count,
                'keywords' => $dc->keywords,
                'nim' => $dc->nim,
                'supervisor' => $dc->supervisor,
                'recommended_for_major_id' => $dc->major_id,
                'is_available' => true,
                'added_by' => $dc->uploaded_by,
                'created_at' => $dc->created_at,
                'updated_at' => $dc->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('books')->where('is_digital', true)->delete();
        DB::table('books')->update(['author_id' => null, 'publisher_id' => null]);
    }
};

