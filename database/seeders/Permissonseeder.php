<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Permissonseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the permissions
        $createTaskPermission = Permission::create(['name' => 'create task']);
        $updateTaskPermission = Permission::create(['name' => 'update task']);
        $retrieveTaskPermission = Permission::create(['name' => 'retrieveByID task']);
        $retrieveAllTaskPermission = Permission::create(['name' => 'retrieveAll task']);


        $userRole = Role::create(['name' => 'client']);
        $userRole->givePermissionTo([
            $createTaskPermission,
            $updateTaskPermission,
            $retrieveTaskPermission,
            $retrieveAllTaskPermission
        ]);
    }
}