<?php

namespace Modules\UserRole\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Cashier\Database\Seeders\GenerateCashierRoleSeeder;
use Modules\Driver\Database\Seeders\GenerateDriverRoleSeeder;
use Modules\Picker\Database\Seeders\GeneratePickerRoleSeeder;
use Modules\Supervisor\Database\Seeders\GenerateSupervisorRoleSeeder;

class UserRoleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");

        $this->call(UserRoleSeeder::class);
        $this->command->info('Seeded the Default User Roles!');

        $this->call(PermissionSeeder::class);
        $this->command->info('Seeded the Default User Permissions!');

        $this->call(GenerateSupervisorRoleSeeder::class);
        $this->command->info('Seeded the User Roles set in the Supervisor Module!');

        $this->call(GeneratePickerRoleSeeder::class);
        $this->command->info('Seeded the User Roles set in the Picker Module!');

        $this->call(GenerateDriverRoleSeeder::class);
        $this->command->info('Seeded the User Roles set in the Driver Module!');

        $this->call(GenerateCashierRoleSeeder::class);
        $this->command->info('Seeded the User Roles set in the Cashier Module!');
    }
}
