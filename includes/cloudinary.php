<?php
// Cloudinary Configuration for InsectaBase
require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Tag\ImageTag;

// Cloudinary configuration
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? 'insectabase',
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? 'your_api_key',
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? 'your_api_secret',
    ],
    'url' => [
        'secure' => true
    ]
]);

/**
 * Upload an image to Cloudinary
 */
function uploadImageToCloudinary($filePath, $folder = 'insectabase/images') {
    try {
        $upload = new UploadApi();
        $result = $upload->upload($filePath, [
            'folder' => $folder,
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);
        return $result['secure_url'];
    } catch (Exception $e) {
        error_log("Cloudinary upload error: " . $e->getMessage());
        return false;
    }
}

/**
 * Upload a PDF to Cloudinary
 */
function uploadPdfToCloudinary($filePath, $folder = 'insectabase/pdfs') {
    try {
        $upload = new UploadApi();
        $result = $upload->upload($filePath, [
            'resource_type' => 'raw',
            'folder' => $folder
        ]);
        return $result['secure_url'];
    } catch (Exception $e) {
        error_log("Cloudinary PDF upload error: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate optimized image URL from Cloudinary
 */
function getCloudinaryImageUrl($publicId, $width = null, $height = null, $quality = 'auto') {
    $transformations = [
        'quality' => $quality,
        'fetch_format' => 'auto'
    ];
    
    if ($width) $transformations['width'] = $width;
    if ($height) $transformations['height'] = $height;
    
    return (new ImageTag($publicId))
        ->resize($transformations)
        ->toUrl();
}

/**
 * Get sample Cloudinary URLs for demo purposes
 */
function getSampleCloudinaryUrls() {
    return [
        'images' => [
            'tortricidae_moth_1' => 'https://res.cloudinary.com/insectabase/image/upload/v1/insectabase/images/tortricidae_moth_1.jpg',
            'tortricidae_moth_2' => 'https://res.cloudinary.com/insectabase/image/upload/v1/insectabase/images/tortricidae_moth_2.jpg',
            'tortricidae_moth_3' => 'https://res.cloudinary.com/insectabase/image/upload/v1/insectabase/images/tortricidae_moth_3.jpg',
            'tortricidae_moth_4' => 'https://res.cloudinary.com/insectabase/image/upload/v1/insectabase/images/tortricidae_moth_4.jpg',
            'tortricidae_moth_5' => 'https://res.cloudinary.com/insectabase/image/upload/v1/insectabase/images/tortricidae_moth_5.jpg',
            'tortricidae_moth_6' => 'https://res.cloudinary.com/insectabase/image/upload/v1/insectabase/images/tortricidae_moth_6.jpg',
        ],
        'pdfs' => [
            'research_paper_1' => 'https://res.cloudinary.com/insectabase/raw/upload/v1/insectabase/pdfs/tortricidae_research_2024.pdf',
            'field_guide' => 'https://res.cloudinary.com/insectabase/raw/upload/v1/insectabase/pdfs/indian_tortricidae_field_guide.pdf',
            'taxonomic_study' => 'https://res.cloudinary.com/insectabase/raw/upload/v1/insectabase/pdfs/molecular_phylogeny_study.pdf',
            'conservation_report' => 'https://res.cloudinary.com/insectabase/raw/upload/v1/insectabase/pdfs/conservation_status_report.pdf',
        ]
    ];
}
?>
