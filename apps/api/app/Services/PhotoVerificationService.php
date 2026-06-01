<?php
namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;

class PhotoVerificationService
{
    private ?RekognitionClient $client = null;

    private function getClient(): ?RekognitionClient
    {
        if ($this->client) return $this->client;

        $key    = config('services.aws_rekognition.key');
        $secret = config('services.aws_rekognition.secret');
        $region = config('services.aws_rekognition.region', 'eu-west-1');

        if (!$key || !$secret) {
            return null;
        }

        $this->client = new RekognitionClient([
            'version'     => 'latest',
            'region'      => $region,
            'credentials' => ['key' => $key, 'secret' => $secret],
        ]);

        return $this->client;
    }

    public function checkQuality(string $imagePath): array
    {
        $client = $this->getClient();

        if (!$client) {
            return ['quality_score' => 70, 'is_acceptable' => true, 'rejection_reason' => null];
        }

        try {
            $imageBytes = file_get_contents($imagePath);

            $result = $client->detectFaces([
                'Image'      => ['Bytes' => $imageBytes],
                'Attributes' => ['ALL'],
            ]);

            // Use image quality detection via detectLabels for brightness/sharpness
            $labelsResult = $client->detectLabels([
                'Image'        => ['Bytes' => $imageBytes],
                'MaxLabels'    => 10,
                'MinConfidence'=> 50,
            ]);

            $imageProperties = $result->get('ImageProperties') ?? [];
            $quality = $imageProperties['Quality'] ?? [];
            $brightness = $quality['Brightness'] ?? 50;
            $sharpness  = $quality['Sharpness'] ?? 50;

            $score = ($brightness + $sharpness) / 2;
            $isAcceptable = $score >= 40;
            $reason = null;

            if (!$isAcceptable) {
                if ($brightness < 40) $reason = 'Image is too dark. Please take photo in better lighting.';
                elseif ($sharpness < 40) $reason = 'Image is blurry. Please retake with a steady hand.';
                else $reason = 'Image quality is too low.';
            }

            return ['quality_score' => round($score, 1), 'is_acceptable' => $isAcceptable, 'rejection_reason' => $reason];

        } catch (AwsException $e) {
            Log::error('Rekognition quality check failed', ['error' => $e->getMessage()]);
            return ['quality_score' => 70, 'is_acceptable' => true, 'rejection_reason' => null];
        }
    }

    public function detectAssetType(string $imagePath, string $declaredCategory): array
    {
        $client = $this->getClient();

        if (!$client) {
            return ['detected_type' => $declaredCategory, 'confidence' => 0, 'matches_declared' => true];
        }

        try {
            $imageBytes = file_get_contents($imagePath);
            $result = $client->detectLabels([
                'Image'         => ['Bytes' => $imageBytes],
                'MaxLabels'     => 20,
                'MinConfidence' => 60,
            ]);

            $labels = collect($result->get('Labels') ?? [])
                ->pluck('Name')
                ->map(fn($l) => strtolower($l))
                ->toArray();

            $categoryMap = [
                'car'                    => ['car', 'vehicle', 'automobile', 'sedan', 'hatchback'],
                'suv'                    => ['suv', 'car', 'vehicle', '4wd', 'jeep', 'truck'],
                'truck'                  => ['truck', 'vehicle', 'lorry', 'pickup'],
                'construction_equipment' => ['machine', 'construction', 'excavator', 'bulldozer', 'crane', 'equipment'],
                'event_equipment'        => ['tent', 'table', 'chair', 'equipment', 'furniture'],
            ];

            $catLower  = strtolower($declaredCategory);
            $expected  = [];
            foreach ($categoryMap as $key => $keywords) {
                if (str_contains($catLower, $key)) {
                    $expected = $keywords;
                    break;
                }
            }

            $matches      = count(array_intersect($labels, $expected)) > 0;
            $detectedType = $labels[0] ?? $declaredCategory;

            return [
                'detected_type'    => $detectedType,
                'confidence'       => 75,
                'matches_declared' => $matches,
                'detected_labels'  => array_slice($labels, 0, 5),
            ];

        } catch (AwsException $e) {
            Log::error('Rekognition asset type detection failed', ['error' => $e->getMessage()]);
            return ['detected_type' => $declaredCategory, 'confidence' => 0, 'matches_declared' => true];
        }
    }

    public function compareDamage(string $beforePath, string $afterPath): array
    {
        $client = $this->getClient();

        if (!$client) {
            return ['damage_detected' => false, 'confidence' => 0, 'differences' => []];
        }

        try {
            $beforeBytes = file_get_contents($beforePath);
            $afterBytes  = file_get_contents($afterPath);

            // Compare faces as a proxy — in practice, custom model would be used
            // Use label detection on both and diff
            $beforeLabels = collect($client->detectLabels(['Image' => ['Bytes' => $beforeBytes], 'MaxLabels' => 30, 'MinConfidence' => 60])->get('Labels') ?? []);
            $afterLabels  = collect($client->detectLabels(['Image' => ['Bytes' => $afterBytes],  'MaxLabels' => 30, 'MinConfidence' => 60])->get('Labels') ?? []);

            $beforeNames = $beforeLabels->pluck('Name')->toArray();
            $afterNames  = $afterLabels->pluck('Name')->toArray();

            $newLabels     = array_diff($afterNames, $beforeNames);
            $damageTerms   = ['Damage', 'Crack', 'Scratch', 'Dent', 'Broken', 'Wreck', 'Accident'];
            $damageFound   = count(array_intersect($newLabels, $damageTerms)) > 0;

            return [
                'damage_detected' => $damageFound,
                'confidence'      => $damageFound ? 80 : 70,
                'differences'     => array_values($newLabels),
                'damage_labels'   => array_values(array_intersect($newLabels, $damageTerms)),
            ];

        } catch (AwsException $e) {
            Log::error('Rekognition damage comparison failed', ['error' => $e->getMessage()]);
            return ['damage_detected' => false, 'confidence' => 0, 'differences' => []];
        }
    }

    public function detectStockImage(string $imagePath): bool
    {
        $client = $this->getClient();
        if (!$client) return false;

        try {
            $imageBytes = file_get_contents($imagePath);
            $result = $client->detectLabels([
                'Image'         => ['Bytes' => $imageBytes],
                'MaxLabels'     => 10,
                'MinConfidence' => 90,
            ]);

            $labels  = collect($result->get('Labels') ?? [])->pluck('Name')->toArray();
            // Stock images often have watermark-related labels or very generic high-confidence labels
            $stockIndicators = ['Watermark', 'Logo', 'Text', 'Advertisement'];

            return count(array_intersect($labels, $stockIndicators)) >= 2;

        } catch (AwsException $e) {
            return false;
        }
    }

    public function validateListingPhotos(Asset $asset): array
    {
        $photos    = $asset->getMedia('photos');
        $passed    = [];
        $failed    = [];
        $reasons   = [];

        foreach ($photos as $photo) {
            $path   = $photo->getPath();
            $result = $this->checkQuality($path);

            if ($result['is_acceptable']) {
                $passed[] = $photo->id;
            } else {
                $failed[]          = $photo->id;
                $reasons[$photo->id] = $result['rejection_reason'];
            }
        }

        return [
            'total'         => count($photos),
            'passed'        => $passed,
            'failed_photos' => $failed,
            'reasons'       => $reasons,
            'all_passed'    => empty($failed),
        ];
    }
}
