<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        DB::table('permissions')->updateOrInsert(
            ['nombre' => 'reportes.ver'],
            [
                'descripcion' => 'Ver reportes contables',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $permission = DB::table('permissions')->where('nombre', 'reportes.ver')->first();
        $administrador = DB::table('roles')->where('nombre', 'Administrador')->first();

        if ($permission && $administrador) {
            DB::table('permission_role')->updateOrInsert(
                [
                    'permission_id' => $permission->id,
                    'role_id' => $administrador->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permission = DB::table('permissions')->where('nombre', 'reportes.ver')->first();

        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }
    }
};
