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
            'region'   => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'endpoint' => env('AWS_ENDPOINT_INTERNAL'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
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
    }
}
