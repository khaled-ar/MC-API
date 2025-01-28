<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Admin;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Comment;
use App\Models\Api\V1\Favorite;
use App\Models\Api\V1\FollowUser;
use App\Models\Api\V1\Message;
use App\Models\Api\V1\Objection;
use App\Models\Api\V1\Offer;
use App\Models\Api\V1\Package;
use App\Models\Api\V1\Payment;
use App\Models\Api\V1\Permission;
use App\Models\Api\V1\Rating;
use App\Models\Api\V1\Result;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Subscription;
use App\Models\Api\V1\Tag;
use App\Models\Api\V1\User;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Database\Seeder;
use App\Models\Api\V1\Report;

class DatabaseSeeder extends Seeder {
    /**
    * Seed the application's database.
     */
    public function run(): void
    {
        // roles
        Role::create(['name' => 'owner']);
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);

        // ********************************

        // users
        User::factory(1)->create();

        // ********************************

        // admins
        $users = User::where('is_admin', '1')->get();
        foreach ($users as $user) {
            Admin::create([
                'admin_id' => $user->id,
                'role_id' => 1 //Role::all()->random()->id
            ]);
        }

        // ********************************

        // permissions
        $permissions = include base_path('data/permissins.php');

        foreach ($permissions as $key => $value) {
            // set owner permissions
            Permission::create([
                'role_id' => 1,
                'ability' => $key,
                'ar_ability' => $value,
                'status' => 'allow'
            ]);
            // set super-admin permissions
            Permission::create([
                'role_id' => 2,
                'ability' => $key,
                'ar_ability' => $value,
                'status' => fake()->randomElement(['allow', 'deny'])
            ]);
            // set admin permisssions
            Permission::create([
                'role_id' => 3,
                'ability' => $key,
                'ar_ability' => $value,
                'status' => fake()->randomElement(['allow', 'deny'])
            ]);
        }

        // ********************************

        // categories
        // Category::factory(20)->create();

        // ********************************

        // ads
        // $ads = Ad::factory(40)->create();

        // ********************************

        // tags
        // Tag::factory(100)->create();

        // ********************************

        // ad_tags
        // $ads->each(
        //         function (Ad $ad) {
        //                 $ad->tags()->sync([Tag::all()->random()->id]);
        //             }
        //         );

        // ********************************

        // results
        // Result::factory(Ad::all()->count())->create();

        // ********************************

        // ratings
        // $users = User::pluck('id');
        // $ads = Ad::pluck('id');
        // for($i = 0; $i < 100; $i++) {
        //     $rateable_type = fake()->randomElement([Ad::class, User::class]);
        //     $rateable_id = $rateable_type == Ad::class ? fake()->randomElement($ads) : fake()->randomElement($users);

        //     Rating::create([
        //         'user_id' => fake()->randomElement($users),
        //         'rateable_type' => $rateable_type,
        //         'rateable_id' => $rateable_id,
        //         'value' => fake()->numberBetween(1, 5),
        //     ]);
        // }

        // ********************************

        // objections
        // Objection::factory(50)->create();

        // ********************************

        // offers
        // Offer::factory(500)->create();

        // ********************************

        // comments
        // Comment::factory(500)->create();

        // ********************************

        // packages
        Package::factory(1)->create();

        // ********************************

        // payments
        Payment::factory(1)->create();

        // ********************************

        // subscriptions
        Subscription::factory(User::all()->count())->create();

        // ********************************

        // favorites
        // Favorite::factory(30)->create();

        // ********************************

        // follow users
        // FollowUser::factory(100)->create();

        // ********************************

        // messages
        // Message::factory(50)->create();

        // ********************************

        // notifications
        // $users = User::where('status', 'active' )->get();
        // for ( $i = 0; $i < 2000; $i++ ) {
        //     $users->random()->notify( new DatabaseUserNotification(
        //         fake()->sentence( 10 ),
        //         fake()->word(),
        //         fake()->words( 3, true )
        //         ) );
        // }

        // ********************************

        // reports
        // $users = User::pluck('id');
        // $ads = Ad::pluck('id');
        // for($i = 0; $i < 1000; $i++) {
        //     $reportable_type = fake()->randomElement([Ad::class, User::class]);
        //     $reportable_id = $reportable_type == Ad::class ? fake()->randomElement($ads) : fake()->randomElement($users);

        //     Report::create([
        //         'reportable_type' => $reportable_type,
        //         'reportable_id' => $reportable_id,
        //         'user_id' => fake()->randomElement($users),
        //         'reason' => fake()->sentence(10),
        //     ]);
        // }
    }
}
