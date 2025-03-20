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
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    // User-defined value
    private int $totalUsers = 10000;

    // Batch size for database operations
    private const BATCH_SIZE = 5000;

    // Dynamic configuration
    private array $config = [];

    // Store unique values
    private array $usernames = [];
    private array $emails = [];
    private array $hubNames = [];
    private $now;

    public function __construct()
    {
        $this->now = now();
    }

    public function run()
    {
        $this->command->info('Starting optimized large-scale seeding...');

        // Get user input for total users
        $this->totalUsers = (int)$this->command->ask('How many users would you like to create?', 10000);

        // Configure realistic ratios based on user count
        $this->configureRealisticRatios();

        // Display configuration
        $this->displayConfig();

        // Generate all unique values
        $this->prepareUniqueValues();

        Model::unguard();
        Event::fake();
        $startTime = microtime(true);

        // Create users and profiles in batches
        $userIds = $this->createUsersAndProfiles();

        // Create hubs with posts and comments
        $this->createHubsWithContent($userIds);

        // Create user followers
        $this->createUserFollowers($userIds);

        // Create Votes for posts
        $this->createPostVotes($userIds);

        // Create Votes for comments
        $this->createCommentVotes($userIds);

        // Create votes for users
        $this->createUserVotes($userIds);

        Model::reguard();

        // Reset sequences in a single transaction
        $this->resetSequences();

        $totalTime = round(microtime(true) - $startTime, 2);
        $this->command->info("Seeding completed in {$totalTime} seconds!");

        // Display summary
        $this->showSeedingSummary();
    }

    private function configureRealisticRatios()
    {
        // Calculate scaling factor based on user count
        $scaleFactor = $this->totalUsers / 10000;

        // Calculate realistic total hub count
        $totalHubs = max(10, min(5000, round($this->totalUsers / 50)));

        // Author pool size - larger user base means smaller percentage need to be authors
        $authorPoolRatio = min(0.5, max(0.05, 0.1 * (1 / log10(max(10, $this->totalUsers / 1000)))));

        // Scale post and comment counts
        $postScaleFactor = min(1.5, max(0.5, sqrt($scaleFactor)));
        $commentScaleFactor = min(1.2, max(0.4, sqrt($scaleFactor)));

        // Vote and follower scaling
        $interactionScaleFactor = min(1, max(0.2, log10($scaleFactor + 1)));

        $this->config = [
            'hubs' => ['total' => $totalHubs],
            'posts' => [
                'per_hub' => [
                    'min' => max(5, round(20 * $postScaleFactor * 0.5)),
                    'max' => max(20, min(300, round(200 * $postScaleFactor)))
                ],
                'author_pool' => $authorPoolRatio
            ],
            'comments' => [
                'per_post' => [
                    'min' => 0,
                    'max' => max(5, min(100, round(50 * $commentScaleFactor)))
                ],
                'nested_ratio' => 0.3,
            ],
            'votes' => [
                'users' => [
                    'min' => max(1, round(5 * $interactionScaleFactor)),
                    'max' => max(5, min($this->totalUsers / 200, round(50 * $interactionScaleFactor)))
                ],
                'posts' => [
                    'min' => max(2, round(10 * $interactionScaleFactor)),
                    'max' => max(10, min($this->totalUsers / 100, round(200 * $interactionScaleFactor)))
                ],
                'comments' => [
                    'min' => 0,
                    'max' => max(3, min($this->totalUsers / 400, round(30 * $interactionScaleFactor)))
                ],
            ],
            'followers' => [
                'users' => [
                    'min' => max(2, round(5 * $interactionScaleFactor)),
                    'max' => max(10, min($this->totalUsers / 50, round(300 * $interactionScaleFactor)))
                ],
                'hubs' => [
                    'min' => max(5, round(50 * $interactionScaleFactor * 0.5)),
                    'max' => max(20, min($this->totalUsers / 20, round(500 * $interactionScaleFactor)))
                ],
            ]
        ];
        $this->config['votes']['upvote_ratio'] = 0.8; // 80% of votes are upvotes
        $this->config['votes']['viral_content_ratio'] = 0.05; // 5% of content goes viral
        $this->config['votes']['power_law_factor'] = 2.5; // Power law distribution factor
    }

    private function displayConfig()
    {
        $this->command->info("Seeding configuration based on {$this->totalUsers} users:");
        $this->command->info("- Hubs: {$this->config['hubs']['total']}");
        $this->command->info("- Posts per hub: {$this->config['posts']['per_hub']['min']} to {$this->config['posts']['per_hub']['max']}");
        $this->command->info("- Comments per post: {$this->config['comments']['per_post']['min']} to {$this->config['comments']['per_post']['max']}");
        $authorPercentage = round($this->config['posts']['author_pool'] * 100, 1);
        $this->command->info("- Author pool: {$authorPercentage}% of users");
        $this->command->info("- Upvote ratio: " . ($this->config['votes']['upvote_ratio'] * 100) . "%");
        $this->command->info("- Viral content ratio: " . ($this->config['votes']['viral_content_ratio'] * 100) . "%");

        // Ask for confirmation to proceed
        if (!$this->command->confirm('Proceed with these settings?', true)) {
            $this->command->info('Aborting seeding process.');
            exit;
        }
    }

    private function prepareUniqueValues()
    {
        $this->command->info('Preparing unique values...');
        // Pre-generate values in chunks to reduce memory pressure
        $chunkSize = self::BATCH_SIZE;

        // Generate usernames and emails in chunks
        for ($i = 0; $i < $this->totalUsers; $i += $chunkSize) {
            $chunk = min($chunkSize, $this->totalUsers - $i);
            for ($j = 0; $j < $chunk; $j++) {
                $index = $i + $j;
                $this->usernames[$index] = "user_{$index}_" . Str::random(5);
                $this->emails[$index] = "user_{$index}@example.com";
            }
        }

        // Generate hub names
        for ($i = 0; $i < $this->config['hubs']['total']; $i++) {
            $this->hubNames[$i] = "Hub_{$i}_" . Str::random(5);
        }

        $this->command->info('Unique values prepared!');
    }

    private function createUsersAndProfiles()
    {
        $this->command->info('Creating users and profiles...');
        $progress = $this->command->getOutput()->createProgressBar($this->totalUsers);
        $defaultPassword = Hash::make('password');
        $userIds = [];

        // Process users in larger batches
        foreach (array_chunk(range(0, $this->totalUsers - 1), self::BATCH_SIZE) as $chunk) {
            $userRecords = [];
            $profileRecords = [];
            $batchUserIds = [];

            foreach ($chunk as $i) {
                $userId = $i + 1; // Ensure user IDs start from 1 and are sequential
                $batchUserIds[] = $userId;

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

            // Use query builder insertOrIgnore to handle potential duplicates more gracefully
            DB::table('users')->insertOrIgnore($userRecords);
            DB::table('user_profiles')->insertOrIgnore($profileRecords);

            $userIds = array_merge($userIds, $batchUserIds);
            $progress->advance(count($chunk));

            // Free up memory
            unset($userRecords, $profileRecords);
        }

        $progress->finish();
        $this->command->info("\nUsers and profiles created!");
        return $userIds;
    }

    private function createHubsWithContent(array $userIds)
    {
        $this->command->info('Creating hubs with content...');
        $progress = $this->command->getOutput()->createProgressBar($this->config['hubs']['total']);

        // Pre-select a subset of user IDs for authors to avoid constant array_rand operations
        $authorPool = (int)($this->totalUsers * $this->config['posts']['author_pool']);
        $authorPool = max($authorPool, 100); // Ensure we have at least 100 authors
        $authorIds = array_slice($userIds, 0, $authorPool);

        // Define some sample descriptions (this can be expanded or modified as needed)
        $descriptions = [
            'This hub is dedicated to the latest trends in technology.',
            'A place where creative minds come together to discuss new ideas.',
            'Sharing knowledge and experiences about various hobbies and interests.',
            'Discuss everything about science, from astronomy to biology.',
            'A hub for discussions around the world of gaming.',
            'Join us to explore the world of literature and writing.',
            'Connect with others to share the latest in art and design.',
            'A space to dive deep into the world of music, from genres to composition.',
            'Talk about world events, history, and the social issues that shape our lives.',
            'The ultimate gathering space for food lovers and culinary enthusiasts.',
            'Everything about sports – from match analysis to fan discussions.',
            'A hub to explore the mysteries of the universe and deep space exploration.',
            'A gathering spot for DIY enthusiasts, crafters, and builders.',
            'Join us to share your passion for photography and visual storytelling.',
            'Everything you need to know about coding, programming, and web development.',
            'This hub is dedicated to mental health discussions and support.',
            'Explore the world of entrepreneurship, startups, and innovation.',
            'Discuss environmental issues, sustainability, and green solutions.',
            'A community for pet lovers, from dog owners to exotic animal enthusiasts.',
            'A safe place for discussing life advice, self-improvement, and motivation.',
            'For anyone interested in the world of movies, TV shows, and entertainment.',
            'A space to share knowledge about personal finance, investing, and budgeting.',
            'For fashion lovers, designers, and style enthusiasts.',
            'A hub for health enthusiasts, fitness experts, and wellness advocates.',
            'Join the conversation about travel, adventure, and exploring the world.',
            'Discuss and learn about various cultures, languages, and traditions.',
            'A gathering place for science fiction, fantasy, and comic book fans.',
            'For tech enthusiasts who love discussing the future of AI, robotics, and gadgets.',
            'A space for book lovers to share their favorite novels, genres, and authors.',
            'A place for entrepreneurs to network, share ideas, and collaborate on projects.',
            'Discuss new trends in photography, videography, and digital media.',
            'Everything about the world of fashion, from the latest trends to fashion history.',
            'A place to exchange tips, tricks, and experiences in gardening and landscaping.',
            'A hub for those interested in studying and discussing psychology and human behavior.',
            'A gathering space for science lovers, from physics to chemistry and biology.',
            'A place to share tips, hacks, and stories about life as a student.',
            'A forum for discussing politics, elections, and current affairs.',
            'Explore the world of online gaming, from competitive esports to casual play.',
            'A space for discussing everything about software, applications, and programming.',
            'For people who enjoy DIY home projects, repairs, and home improvement.',
            'A place to talk about the world of startups, investors, and business growth.',
            'For fans of anime, manga, and everything related to Japanese pop culture.',
            'A space for car enthusiasts to discuss their passion for vehicles, from classics to modern machines.',
            'A hub for learning new languages and connecting with language learners around the world.',
            'For pet owners to share advice, tips, and funny pet stories.',
        ];

        // Use smaller chunks for hubs to balance memory usage
        $hubChunkSize = 100;
        $hubOffset = 0;

        foreach (array_chunk(range(0, $this->config['hubs']['total'] - 1), $hubChunkSize) as $hubChunk) {
            $hubRecords = [];
            $batchHubIds = [];

            // First, create all hubs in this batch
            foreach ($hubChunk as $hubIndex) {
                $hubId = $hubIndex + 1; // Hub IDs start from 1
                $batchHubIds[] = $hubId;

                $hubOwnerId = $authorIds[array_rand($authorIds)];

                // Select a random description
                $randomDescription = $descriptions[array_rand($descriptions)];

                $hubRecords[] = [
                    'id' => $hubId,
                    'user_id' => $hubOwnerId,
                    'name' => $this->hubNames[$hubIndex],
                    'slug' => $this->hubNames[$hubIndex],
                    'description' => $randomDescription, // Add the description here
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];
            }

            // Insert all hubs at once
            DB::table('hubs')->insertOrIgnore($hubRecords);
            unset($hubRecords); // Free memory

            // Now create posts and comments for each hub we just created
            foreach ($batchHubIds as $hubId) {
                $this->createPostsForHub($hubId, $authorIds, $hubOffset);
                $hubOffset++;
            }

            $progress->advance(count($hubChunk));
        }

        $progress->finish();
        $this->command->info("\nHubs and content created!");
    }






    private function createFollowersForHub($hubId, array $userIds)
    {
        // Select a reasonable number of followers based on available users
        $followerCount = min(rand(
            $this->config['followers']['hubs']['min'],
            $this->config['followers']['hubs']['max']
        ), count($userIds));

        // Get a random subset of users to follow this hub
        $shuffledKeys = array_rand($userIds, $followerCount);
        if (!is_array($shuffledKeys)) {
            $shuffledKeys = [$shuffledKeys];
        }

        $followerRecords = [];
        foreach ($shuffledKeys as $key) {
            $followerRecords[] = [
                'hub_id' => $hubId,
                'user_id' => $userIds[$key],
                'created_at' => $this->now,
            ];
        }

        // Insert followers in a single batch
        if (!empty($followerRecords)) {
            // Use insertOrIgnore to handle duplicates gracefully
            DB::table('hub_followers')->insertOrIgnore($followerRecords);
            DB::table('hubs')
                ->where('id', $hubId)
                ->increment('followers_count', $followerCount);
        }
    }

    private function createUserFollowers(array $userIds)
    {
        $this->command->info('Creating user followers...');
        $progress = $this->command->getOutput()->createProgressBar(count($userIds));

        $followerRecords = [];
        $batchSize = 10000;
        $recordCount = 0;

        // Create followers for each user
        foreach ($userIds as $userId) {
            // Determine number of followers for this user
            $followerCount = min(rand(
                $this->config['followers']['users']['min'],
                $this->config['followers']['users']['max']
            ), count($userIds) - 1); // Can't follow self

            // Skip if no followers
            if ($followerCount <= 0) {
                $progress->advance();
                continue;
            }

            // Create a pool of potential followers (excluding self)
            $potentialFollowers = array_diff($userIds, [$userId]);

            // Get random followers
            $shuffledKeys = array_rand($potentialFollowers, $followerCount);
            if (!is_array($shuffledKeys)) {
                $shuffledKeys = [$shuffledKeys];
            }

            foreach ($shuffledKeys as $key) {
                $followerId = $potentialFollowers[$key];
                $followerRecords[] = [
                    'followed_id' => $userId,
                    'follower_id' => $followerId,
                    'created_at' => $this->now,
                ];

                $recordCount++;

                // Insert in batches to manage memory
                if ($recordCount >= $batchSize) {
                    DB::table('user_followers')->insertOrIgnore($followerRecords);
                    $followerRecords = [];
                    $recordCount = 0;
                    DB::table('user_profiles')
                        ->where('user_id', $userId)
                        ->increment('followers_count', $followerCount);
                }
            }

            $progress->advance();
        }

        // Insert any remaining records
        if (!empty($followerRecords)) {
            DB::table('user_followers')->insertOrIgnore($followerRecords);
        }

        $progress->finish();
        $this->command->info("\nUser followers created!");
    }


    private function createPostsForHub($hubId, array $authorIds, $hubOffset)
    {
        // Determine number of posts for this hub
        $totalPosts = rand(
            $this->config['posts']['per_hub']['min'],
            $this->config['posts']['per_hub']['max']
        );
        $postOffset = $hubOffset * 1000;
        $postRecords = [];
        $postIds = [];
        $postPopularityMap = []; // Store popularity ratings for posts

        // Create all posts for this hub
        for ($postIndex = 0; $postIndex < $totalPosts; $postIndex++) {
            $postId = $postOffset + $postIndex + 1; // Ensure unique post IDs
            $postIds[] = $postId;

            $authorId = $authorIds[array_rand($authorIds)];
            $title = "Post {$postId} in hub {$hubId}";

            // Determine post popularity category and store it
            // 0 = unpopular, 1 = average, 2 = popular, 3 = viral
            $popularityScore = $this->determinePostPopularity();
            $postPopularityMap[$postId] = $popularityScore;

            $postRecords[] = [
                'id' => $postId,
                'hub_id' => $hubId,
                'user_id' => $authorId,
                'title' => $title,
                'slug' => Str::slug($title),
                'body' => "Content for post {$postId}",
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }

        // Insert all posts at once
        if (!empty($postRecords)) {
            DB::table('posts')->insertOrIgnore($postRecords);
        }
        unset($postRecords); // Free memory

        // Now create comments for each post based on their popularity
        $this->createCommentsForPosts($postIds, $authorIds, $postPopularityMap);

        // Store post popularity mapping for later use in vote creation
        $this->postPopularityMap = array_merge($this->postPopularityMap ?? [], $postPopularityMap);

        // Create followers for this hub
        $this->createFollowersForHub($hubId, $authorIds);
    }

// Helper function to determine post popularity consistently
    private function determinePostPopularity()
    {
        $rand = mt_rand(1, 100);

        if ($rand <= 5) { // 5% are viral
            return 3; // Viral
        } elseif ($rand <= 25) { // 20% are popular
            return 2; // Popular
        } elseif ($rand <= 75) { // 50% are average
            return 1; // Average
        } else { // 25% are unpopular
            return 0; // Unpopular
        }
    }

    private function createCommentsForPosts(array $postIds, array $authorIds, array $postPopularityMap)
    {
        if (empty($postIds)) return;

        $commentRecords = [];
        $commentBatchSize = 5000;
        $commentCount = 0;
        $commentCountByPost = [];

        foreach ($postIds as $postId) {
            // Get post popularity from the map
            $popularity = $postPopularityMap[$postId] ?? 1; // Default to average if not found

            // Determine number of comments based on post popularity
            $totalComments = $this->getCommentCountByPopularity($popularity);
            $commentOffset = $postId * 1000;
            $commentCountByPost[$postId] = 0;

            for ($commentIndex = 0; $commentIndex < $totalComments; $commentIndex++) {
                $commentId = $commentOffset + $commentIndex + 1;
                $commenterId = $authorIds[array_rand($authorIds)];

                $commentRecords[] = [
                    'id' => $commentId,
                    'post_id' => $postId,
                    'user_id' => $commenterId,
                    'body' => "Comment {$commentId} on post {$postId}",
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];

                $commentCount++;
                $commentCountByPost[$postId]++;

                // Insert in batches to manage memory
                if ($commentCount >= $commentBatchSize) {
                    DB::table('comments')->insertOrIgnore($commentRecords);
                    $commentRecords = [];
                    $commentCount = 0;
                }
            }
        }

        // Insert any remaining comments
        if (!empty($commentRecords)) {
            DB::table('comments')->insertOrIgnore($commentRecords);
        }

        // Update comment count for each post
        foreach ($commentCountByPost as $postId => $count) {
            DB::table('posts')
                ->where('id', $postId)
                ->update(['comment_count' => $count]);
        }
    }

// Helper function to determine comment count based on post popularity
    private function getCommentCountByPopularity($popularity)
    {
        $min = $this->config['comments']['per_post']['min'];
        $max = $this->config['comments']['per_post']['max'];
        $range = $max - $min;

        switch ($popularity) {
            case 3: // Viral
                return mt_rand((int)($min + $range * 0.7), $max);
            case 2: // Popular
                return mt_rand((int)($min + $range * 0.3), (int)($min + $range * 0.7));
            case 1: // Average
                return mt_rand((int)($min + $range * 0.1), (int)($min + $range * 0.3));
            case 0: // Unpopular
                return mt_rand($min, (int)($min + $range * 0.1));
            default:
                return mt_rand($min, $max);
        }
    }

    private function createPostVotes(array $userIds)
    {
        $this->command->info('Creating post votes...');

        // Get total post count
        $totalPosts = DB::table('posts')->count();
        if ($totalPosts == 0) {
            $this->command->info("No posts found to vote!");
            return;
        }

        $progress = $this->command->getOutput()->createProgressBar($totalPosts);
        $voteRecords = [];
        $batchSize = 10000;
        $recordCount = 0;

        // Configure vote distribution parameters
        $upvoteRatio = $this->config['votes']['upvote_ratio'];
        $powerLawFactor = $this->config['votes']['power_law_factor'];
        $baseVoteMin = $this->config['votes']['posts']['min'];
        $baseVoteMax = $this->config['votes']['posts']['max'];

        // Process in chunks to save memory
        $postChunkSize = 100;
        DB::table('posts')->select('id')->orderBy('id')
            ->chunk($postChunkSize, function ($posts) use (
                &$voteRecords,
                &$recordCount,
                $batchSize,
                $userIds,
                &$progress,
                $upvoteRatio,
                $powerLawFactor,
                $baseVoteMin,
                $baseVoteMax
            ) {
                foreach ($posts as $post) {
                    // Use the post popularity map if available, or determine on the fly
                    $popularity = $this->postPopularityMap[$post->id] ?? $this->determinePostPopularity();

                    // Determine vote count based on post popularity
                    $voteCount = $this->getVoteCountByPopularity($popularity, $baseVoteMin, $baseVoteMax, $powerLawFactor, count($userIds));
                    $voteCount = (int)$voteCount;

                    if ($voteCount <= 0) {
                        $progress->advance();
                        continue;
                    }

                    // Get random voters
                    $shuffledKeys = array_rand($userIds, $voteCount);
                    if (!is_array($shuffledKeys)) {
                        $shuffledKeys = [$shuffledKeys];
                    }

                    $totalVoteValue = 0;
                    foreach ($shuffledKeys as $key) {
                        $userId = $userIds[$key];

                        // Determine if this vote is an upvote based on content popularity
                        $isUpvote = (mt_rand(1, 100) / 100) < $this->getUpvoteRatioByPopularity($popularity, $upvoteRatio);
                        $voteValue = $isUpvote ? 1 : -1;

                        $voteRecords[] = [
                            'votable_id' => $post->id,
                            'votable_type' => 'App\\Models\\Post',
                            'user_id' => $userId,
                            'value' => $voteValue,
                            'created_at' => $this->now,
                        ];
                        $totalVoteValue += $voteValue;
                        $recordCount++;

                        // Insert in batches to manage memory
                        if ($recordCount >= $batchSize) {
                            DB::table('votes')->insertOrIgnore($voteRecords);
                            $voteRecords = [];
                            $recordCount = 0;
                        }
                    }

                    // Update the post's vote count
                    DB::table('posts')
                        ->where('id', $post->id)
                        ->increment('vote_count', $totalVoteValue);

                    $progress->advance();
                }
            });

        // Insert any remaining records
        if (!empty($voteRecords)) {
            DB::table('votes')->insertOrIgnore($voteRecords);
        }

        $progress->finish();
        $this->command->info("\nPost votes created!");
    }

// Helper function to determine vote count based on post popularity
    private function getVoteCountByPopularity($popularity, $min, $max, $powerLawFactor, $totalUsers)
    {
        switch ($popularity) {
            case 3: // Viral
                return min(
                    $max * $powerLawFactor * (mt_rand(8, 12) / 10), // 80-120% of max * factor
                    $totalUsers * 0.8 // Cap at 80% of total users
                );
            case 2: // Popular
                return min(
                    $max * (mt_rand(5, 9) / 10), // 50-90% of max
                    $totalUsers * 0.3 // Cap at 30% of total users
                );
            case 1: // Average
                return min(
                    mt_rand($min, $max / 3),
                    $totalUsers * 0.1 // Cap at 10% of total users
                );
            case 0: // Unpopular
                return mt_rand(0, $min);
            default:
                return mt_rand($min, $max);
        }
    }

// Helper function to determine upvote ratio based on post popularity
    private function getUpvoteRatioByPopularity($popularity, $baseRatio)
    {
        switch ($popularity) {
            case 3: // Viral
                return 0.95; // 95% upvotes
            case 2: // Popular
                return 0.85; // 85% upvotes
            case 1: // Average
                return $baseRatio; // Base ratio (typically 80%)
            case 0: // Unpopular
                return 0.5; // 50% upvotes (more controversial)
            default:
                return $baseRatio;
        }
    }

    private function createCommentVotes(array $userIds)
    {
        $this->command->info('Creating comment votes...');

        // Get total comment count
        $totalComments = DB::table('comments')->count();
        if ($totalComments == 0) {
            $this->command->info("No comments found to vote!");
            return;
        }

        // Create a smaller progress bar since we may have millions of comments
        $progressStep = max(1, (int)($totalComments / 1000));
        $progress = $this->command->getOutput()->createProgressBar((int)($totalComments / $progressStep) + 1);

        $voteRecords = [];
        $batchSize = 10000;
        $recordCount = 0;
        $commentCounter = 0;

        // Configure vote distribution parameters
        $upvoteRatio = $this->config['votes']['upvote_ratio'];
        $viralContentRatio = $this->config['votes']['viral_content_ratio'] / 2; // Less viral comments than posts
        $powerLawFactor = $this->config['votes']['power_law_factor'];
        $baseVoteMin = $this->config['votes']['comments']['min'];
        $baseVoteMax = $this->config['votes']['comments']['max'];

        // Process in chunks to save memory
        $commentChunkSize = 500;
        DB::table('comments')->select('id', 'post_id')->orderBy('id')
            ->chunk($commentChunkSize, function ($comments) use (
                &$voteRecords,
                &$recordCount,
                &$commentCounter,
                $progressStep,
                $batchSize,
                $userIds,
                &$progress,
                $upvoteRatio,
                $viralContentRatio,
                $powerLawFactor,
                $baseVoteMin,
                $baseVoteMax
            ) {
                foreach ($comments as $comment) {
                    // Determine comment popularity category
                    $isViral = mt_rand(1, 100) <= ($viralContentRatio * 100);
                    $isPopular = !$isViral && mt_rand(1, 100) <= 15; // 15% are popular but not viral
                    $isAverage = !$isViral && !$isPopular && mt_rand(1, 100) <= 40; // 40% are average
                    // Remaining are unpopular or have no votes

                    // Adjust vote count based on popularity
                    if ($isViral) {
                        $voteCount = min(
                            $baseVoteMax * $powerLawFactor * (mt_rand(7, 10) / 10),
                            count($userIds) * 0.4
                        );
                    } elseif ($isPopular) {
                        $voteCount = min(
                            $baseVoteMax * (mt_rand(4, 8) / 10),
                            count($userIds) * 0.2
                        );
                    } elseif ($isAverage) {
                        $voteCount = min(
                            mt_rand($baseVoteMin, $baseVoteMax / 3),
                            count($userIds) * 0.05
                        );
                    } else {
                        // Many comments get few or no votes
                        $voteCount = mt_rand(0, $baseVoteMin);
                        if (mt_rand(1, 100) <= 40) { // 40% chance of no votes for unpopular comments
                            $voteCount = 0;
                        }
                    }

                    $voteCount = (int)$voteCount;

                    // Skip if no votes
                    if ($voteCount <= 0) {
                        $commentCounter++;
                        if ($commentCounter % $progressStep == 0) {
                            $progress->advance();
                        }
                        continue;
                    }

                    // Get random voters
                    $shuffledKeys = array_rand($userIds, $voteCount);
                    if (!is_array($shuffledKeys)) {
                        $shuffledKeys = [$shuffledKeys];
                    }

                    $totalVoteValue = 0;
                    foreach ($shuffledKeys as $key) {
                        $userId = $userIds[$key];

                        // Determine vote value based on content popularity
                        $isUpvote = (mt_rand(1, 100) / 100) < ($isViral ? 0.9 : ($isPopular ? 0.8 : $upvoteRatio));
                        $voteValue = $isUpvote ? 1 : -1;

                        $voteRecords[] = [
                            'votable_id' => $comment->id,
                            'votable_type' => 'App\\Models\\Comment',
                            'user_id' => $userId,
                            'value' => $voteValue,
                            'created_at' => $this->now,
                        ];
                        $totalVoteValue += $voteValue;
                        $recordCount++;

                        // Insert in batches to manage memory
                        if ($recordCount >= $batchSize) {
                            DB::table('votes')->insertOrIgnore($voteRecords);
                            $voteRecords = [];
                            $recordCount = 0;
                        }
                    }

                    // Update the comment's vote count (if you have this column)
                    DB::table('comments')
                        ->where('id', $comment->id)
                        ->increment('vote_count', $totalVoteValue);

                    $commentCounter++;
                    if ($commentCounter % $progressStep == 0) {
                        $progress->advance();
                    }
                }
            });

        // Insert any remaining records
        if (!empty($voteRecords)) {
            DB::table('votes')->insertOrIgnore($voteRecords);
        }

        $progress->finish();
        $this->command->info("\nComment votes created!");
    }

    private function createUserVotes(array $userIds)
    {
        $this->command->info('Creating user votes...');
        $progress = $this->command->getOutput()->createProgressBar(count($userIds));

        $voteRecords = [];
        $batchSize = 10000;
        $recordCount = 0;

        // Configure vote distribution parameters
        $upvoteRatio = 0.85; // Users tend to get more upvotes than downvotes (reputation systems)

        // Create votes for each user
        foreach ($userIds as $userId) {
            // Some users are more reputable/popular than others
            $userPopularity = mt_rand(1, 100);

            // Determine vote count based on user popularity
            if ($userPopularity >= 95) { // Top 5% users
                $voteCount = min(
                    $this->config['votes']['users']['max'] * 2,
                    count($userIds) * 0.3
                );
            } elseif ($userPopularity >= 80) { // Next 15%
                $voteCount = min(
                    $this->config['votes']['users']['max'],
                    count($userIds) * 0.1
                );
            } elseif ($userPopularity >= 50) { // Middle 30%
                $voteCount = min(
                    ($this->config['votes']['users']['min'] + $this->config['votes']['users']['max']) / 2,
                    count($userIds) * 0.05
                );
            } else { // Bottom 50%
                $voteCount = min(
                    $this->config['votes']['users']['min'],
                    count($userIds) * 0.01
                );
            }

            $voteCount = (int)$voteCount;

            // Skip if no votes
            if ($voteCount <= 0 || $voteCount >= count($userIds)) {
                $progress->advance();
                continue;
            }

            // Create a pool of potential users to vote (excluding self)
            $potentialVoted = array_diff($userIds, [$userId]);

            // Get random users to vote
            $shuffledKeys = array_rand($potentialVoted, $voteCount);
            if (!is_array($shuffledKeys)) {
                $shuffledKeys = [$shuffledKeys];
            }

            $totalVoteValue = 0;
            foreach ($shuffledKeys as $key) {
                $votedUserId = $potentialVoted[$key];

                // More popular users get more upvotes
                $upvoteProbability = $userPopularity >= 80 ? 0.9 : ($userPopularity >= 50 ? 0.8 : $upvoteRatio);
                $voteValue = (mt_rand(1, 100) / 100) < $upvoteProbability ? 1 : -1;

                $voteRecords[] = [
                    'votable_id' => $votedUserId,
                    'votable_type' => 'App\\Models\\User',
                    'user_id' => $userId,
                    'value' => $voteValue,
                    'created_at' => $this->now,
                ];
                $totalVoteValue += $voteValue;
                $recordCount++;

                // Insert in batches to manage memory
                if ($recordCount >= $batchSize) {
                    DB::table('votes')->insertOrIgnore($voteRecords);
                    $voteRecords = [];
                    $recordCount = 0;
                }
            }

            // Update user reputation/karma if you have such a column
            DB::table('user_profiles')
                ->where('user_id', $userId)
                ->increment('reputation', $totalVoteValue);

            $progress->advance();
        }

        // Insert any remaining records
        if (!empty($voteRecords)) {
            DB::table('votes')->insertOrIgnore($voteRecords);
        }

        $progress->finish();
        $this->command->info("\nUser votes created!");
    }

    private function resetSequences()
    {
        $this->command->info('Resetting database sequences...');
        $driver = DB::connection()->getDriverName();

        try {
            // For PostgreSQL
            if ($driver === 'pgsql') {
                $tables = ['users', 'hubs', 'posts', 'comments', 'votes'];
                foreach ($tables as $table) {
                    $sequenceName = "{$table}_id_seq";
                    // Check if sequence exists first
                    $sequenceExists = DB::select("SELECT 1 FROM pg_class WHERE relname = ? AND relkind = 'S'", [$sequenceName]);
                    if (!empty($sequenceExists)) {
                        DB::statement("SELECT setval('{$sequenceName}', COALESCE((SELECT MAX(id) FROM {$table}), 1), true)");
                    }
                }
            } // For MySQL
            elseif ($driver === 'mysql') {
                $tables = ['users', 'hubs', 'posts', 'comments', 'votes'];
                foreach ($tables as $table) {
                    // Get the max id safely
                    $maxId = DB::table($table)->max('id') ?? 0;
                    // Check if table has auto_increment column
                    $hasAutoIncrement = DB::select(
                        "SELECT 1 FROM information_schema.columns
                        WHERE table_schema = DATABASE()
                        AND table_name = ?
                        AND column_name = 'id'
                        AND extra LIKE '%auto_increment%'",
                        [$table]
                    );
                    if (!empty($hasAutoIncrement)) {
                        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = " . ($maxId + 1));
                    }
                }
            }
            // SQLite doesn't need sequence resets
        } catch (\Exception $e) {
            $this->command->error("Failed to reset sequences: " . $e->getMessage());
        }
    }

    private function showSeedingSummary()
    {
        $this->command->info('Seeding Summary:');
        $this->command->info("- Users created: " . DB::table('users')->count());
        $this->command->info("- Hubs created: " . DB::table('hubs')->count());
        $this->command->info("- Posts created: " . DB::table('posts')->count());
        $this->command->info("- Comments created: " . DB::table('comments')->count());
        $this->command->info("- Total votes created: " . DB::table('votes')->count());
        $this->command->info("- User followers created: " . DB::table('user_followers')->count());
        $this->command->info("- Hub followers created: " . DB::table('hub_followers')->count());
    }
}
