<?php

namespace Database\Seeders;

use App\Models\Node;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NodeTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        DB::table('nodes')->truncate();
        $data = new Node();
        $data->parent_id = null;
        $data->name = 'public';
        $data->path = 'public';
        $data->type = 'dir';
        $data->file = null;
        $data->save();

    }
}
