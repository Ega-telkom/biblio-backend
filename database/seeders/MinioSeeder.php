<?php

namespace Database\Seeders;
use Aws\S3\S3Client;
use Illuminate\Database\Seeder;

class MinioSeeder extends Seeder
{
    public function run(): void
    {
        $client = new S3Client([
            'version'  => 'latest',
            'region'   => config('filesystems.disks.s3.region'),
            'endpoint' => config('filesystems.disks.s3.endpoint'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        // Bucket private untuk file buku
        try {
            $client->createBucket(['Bucket' => 'biblio']);
            $this->command->info('Bucket biblio created');
        } catch (\Exception $e) {
            $this->command->warn('biblio: ' . $e->getMessage());
        }


        try {
            $client->createBucket(['Bucket' => 'biblio-covers']);
            $client->putBucketPolicy([
                'Bucket' => 'biblio-covers',
                'Policy' => json_encode([
                    'Version' => '2012-10-17',
                    'Statement' => [[
                        'Effect'    => 'Allow',
                        'Principal' => '*',
                        'Action'    => 's3:GetObject',
                        'Resource'  => 'arn:aws:s3:::biblio-covers/*',
                    ]],
                ]),
            ]);
            $this->command->info('Bucket biblio-covers created');
        } catch (\Exception $e) {
            $this->command->warn('biblio-covers: ' . $e->getMessage());
        }
        
        try {
            $client->putBucketLifecycleConfiguration([
                'Bucket' => 'biblio-covers',
                'LifecycleConfiguration' => [
                    'Rules' => [
                        [
                            'ID'     => 'delete-temp',
                            'Status' => 'Enabled',
                            'Filter' => ['Prefix' => 'temp/'],
                            'Expiration' => ['Days' => 1],
                        ],
                    ],
                ],
            ]);
            $this->command->info('Lifecycle temp/ set');
        } catch (\Exception $e) {
            $this->command->warn('Lifecycle: ' . $e->getMessage());
        }
        
        try {
            $client->createBucket(['Bucket' => 'biblio-avatars']);
            $client->putBucketPolicy([
                'Bucket' => 'biblio-avatars',
                'Policy' => json_encode([
                    'Version' => '2012-10-17',
                    'Statement' => [[
                        'Effect'    => 'Allow',
                        'Principal' => '*',
                        'Action'    => 's3:GetObject',
                        'Resource'  => 'arn:aws:s3:::biblio-avatars/*',
                    ]],
                ]),
            ]);
            $this->command->info('Bucket biblio-avatars created');
        } catch (\Exception $e) {
            $this->command->warn('biblio-avatars: ' . $e->getMessage());
        }
    }
}
