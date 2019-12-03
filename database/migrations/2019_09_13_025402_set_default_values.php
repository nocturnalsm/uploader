<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetDefaultValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::insert(["name" => "Super Admin",
                      "email" => "admin@admin.com",
                      "password" => Hash::make('admin')]);
        Role::create(["name" => "Super Admin"]);
        Role::create(["name" => "Admin"]);
        Role::create(["name" => "User"]);
        Permission::create(["name" => 'user.list']);
        Permission::create(["name" => "user.create"]);
        Permission::create(["name" => "user.edit"]);
        Permission::create(["name" => "user.delete"]);
        Permission::create(["name" => 'document.list']);
        Permission::create(["name" => "document.create"]);
        Permission::create(["name" => "document.edit"]);
        Permission::create(["name" => "document.delete"]);
        Permission::create(["name" => 'role.list']);
        Permission::create(["name" => "role.create"]);
        Permission::create(["name" => "role.edit"]);
        Permission::create(["name" => "role.delete"]);
        Permission::create(["name" => 'permission.list']);
        Permission::create(["name" => "permission.create"]);
        Permission::create(["name" => "permission.edit"]);
        Permission::create(["name" => "permission.delete"]);
        Permission::create(["name" => 'company.list']);
        Permission::create(["name" => "company.create"]);
        Permission::create(["name" => "company.edit"]);
        Permission::create(["name" => "company.delete"]);
        Permission::create(["name" => 'folder.list']);
        Permission::create(["name" => "folder.create"]);
        Permission::create(["name" => "folder.edit"]);
        Permission::create(["name" => "folder.delete"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
