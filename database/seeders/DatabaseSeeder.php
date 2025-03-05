<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Hub;
use App\Models\Post;
use App\Models\Comment;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    private const TOTAL_USERS = 100_000;
    private const BATCH_SIZE = 1000; // Larger batches for faster inserts
    private const CONFIG = [
        'hubs' => ['total' => 2000],
        'posts' => [
            'per_hub' => ['min' => 20, 'max' => 200],
            'author_pool' => 0.1
        ],
        'comments' => [
            'per_post' => ['min' => 0, 'max' => 50],
            'nested_ratio' => 0.3,
        ]
    ];

    private array $usernames;
    private array $emails;
    private array $hubNames;
    private $now;

    public function __construct()
    {
        $this->now = now();

        // Pre-generate all unique values
        $this->usernames = collect(range(1, self::TOTAL_USERS))
            ->map(fn($i) => "user_{$i}_" . Str::random(5))
            ->toArray();

        $this->emails = collect(range(1, self::TOTAL_USERS))
            ->map(fn($i) => "user_{$i}@example.com")
            ->toArray();

        $this->hubNames = collect(range(1, self::CONFIG['hubs']['total']))
            ->map(fn($i) => "Hub_{$i}_" . Str::random(5))
            ->toArray();
    }

    public function run()
    {
        $this->command->info('Starting optimized large-scale seeding...');

        // Disable foreign key checks and events for faster inserts
        Model::unguard();
        Event::fake();

        $startTime = microtime(true);

        // Create users and profiles in batches
        $this->createUsersAndProfiles();

        // Create hubs with posts and comments
        $this->createHubsWithContent();

        // Re-enable foreign key checks and events
        Model::reguard();

        $totalTime = round(microtime(true) - $startTime, 2);
        $this->command->info("Seeding completed in {$totalTime} seconds!");
    }

    private function createUsersAndProfiles()
    {
        $this->command->info('Creating users and profiles...');
        $progress = $this->command->getOutput()->createProgressBar(self::TOTAL_USERS);

        $defaultPassword = Hash::make('password');

        foreach (array_chunk(range(0, self::TOTAL_USERS - 1), self::BATCH_SIZE) as $chunk) {
            $userRecords = [];
            $profileRecords = [];

            foreach ($chunk as $i) {
                $userId = $i + 1;

                $userRecords[] = [
                    'id' => $userId,
                    'user_name' => $this->usernames[$i],
                    'email' => $this->emails[$i],
                    'password' => $defaultPassword,
                    'email_verified_at' => $this->now,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];

                $profileRecords[] = [
                    'user_id' => $userId,
                    'display_name' => $this->usernames[$i],
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];
            }

            // Batch insert users and profiles
            DB::table('users')->insert($userRecords);
            DB::table('user_profiles')->insert($profileRecords);

            $progress->advance(count($chunk));
        }

        $progress->finish();
        $this->command->info("\nUsers and profiles created!");
    }

    private function createHubsWithContent()
    {
        $this->command->info('Creating hubs with content...');
        $progress = $this->command->getOutput()->createProgressBar(self::CONFIG['hubs']['total']);

        // Get user IDs for recycling
        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach (array_chunk(range(0, self::CONFIG['hubs']['total'] - 1), 500) as $hubChunk) {
            $hubRecords = [];
            $postRecords = [];
            $commentRecords = [];
            $followerRecords = [];

            foreach ($hubChunk as $hubIndex) {
                $hubId = $hubIndex + 1;
                $hubOwnerId = $userIds[array_rand($userIds)];

                // Create hub
                $hubRecords[] = [
                    'id' => $hubId,
                    'user_id' => $hubOwnerId,
                    'name' => $this->hubNames[$hubIndex],
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];

                // Create posts for this hub
                $totalPosts = rand(
                    self::CONFIG['posts']['per_hub']['min'],
                    self::CONFIG['posts']['per_hub']['max']
                );

                for ($postIndex = 0; $postIndex < $totalPosts; $postIndex++) {
                    $postId = $hubId * 1000 + $postIndex; // Ensure unique post IDs
                    $title = "Post {$postId} in hub {$hubId}";

                    $postRecords[] = [
                        'id' => $postId,
                        'hub_id' => $hubId,
                        'user_id' => $userIds[array_rand($userIds)],
                        'title' => $title,
                        'slug' => Str::slug($title),
                        'body' => "Content for post {$postId}",
                        'created_at' => $this->now,
                        'updated_at' => $this->now,
                    ];

                    // Create comments for this post
                    $totalComments = rand(
                        self::CONFIG['comments']['per_post']['min'],
                        self::CONFIG['comments']['per_post']['max']
                    );

                    for ($commentIndex = 0; $commentIndex < $totalComments; $commentIndex++) {
                        $commentId = $postId * 1000 + $commentIndex;

                        $commentRecords[] = [
                            'id' => $commentId,
                            'post_id' => $postId,
                            'user_id' => $userIds[array_rand($userIds)],
                            'body' => "Comment {$commentId} on post {$postId}",
                            'created_at' => $this->now,
                            'updated_at' => $this->now,
                        ];
                    }
                }

                // Create follower relationships
                $followerCount = rand(100, 5000);
                $followers = array_rand($userIds, $followerCount);
                foreach ($followers as $followerId) {
                    $followerRecords[] = [
                        'hub_id' => $hubId,
                        'user_id' => $userIds[$followerId],
                        'created_at' => $this->now,
                    ];
                }
            }

            // Batch insert all records
            if ($hubRecords) DB::table('hubs')->insert($hubRecords);
            $postChunks = array_chunk($postRecords, 1000);
            foreach ($postChunks as $chunk) {
                DB::table('posts')->insert($chunk);
            }
            //if ($postRecords) DB::table('posts')->insert($postRecords);
            $commentChunks = array_chunk($commentRecords, 2000);
            foreach ($commentChunks as $chunk) {
                DB::table('comments')->insert($chunk);
            }
            $followerChunks = array_chunk($followerRecords, 2000);
            foreach ($followerChunks as $chunk) {
                DB::table('hub_followers')->insert($chunk);
            }

            $progress->advance(count($hubChunk));
        }

        $progress->finish();
        $this->command->info("\nHubs and content created!");
    }
}
