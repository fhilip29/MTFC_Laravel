<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckPostTags extends Command
{
    protected $signature = 'check:post-tags';
    protected $description = 'Check if post_tags table exists and has data';

    public function handle()
    {
        $this->info('Checking post_tags table...');
        
        if (!Schema::hasTable('post_tags')) {
            $this->error('post_tags table does not exist!');
            return 1;
        }
        
        $this->info('post_tags table exists.');
        
        $count = DB::table('post_tags')->count();
        $this->info("post_tags table has {$count} records.");
        
        if ($count > 0) {
            $tags = DB::table('post_tags')->get();
            $this->table(['id', 'name', 'slug'], $tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug
                ];
            }));
        }
        
        return 0;
    }
} 