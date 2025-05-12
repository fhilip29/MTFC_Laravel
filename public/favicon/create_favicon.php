<?php
// Load the source image
$source = __DIR__ . '/../assets/MTFC_LOGO.PNG';
echo "Loading source image from: $source\n";

$sourceImage = imagecreatefrompng($source);

// Make sure we have a valid image
if (!$sourceImage) {
    die('Unable to load source image: ' . $source);
}

echo "Source image loaded successfully!\n";

// Get the dimensions
$sourceWidth = imagesx($sourceImage);
$sourceHeight = imagesy($sourceImage);
echo "Source dimensions: {$sourceWidth}x{$sourceHeight}\n";

// Define the favicon sizes
$sizes = [16, 32, 48, 64, 128, 192, 256];

// Create favicon for each size
foreach ($sizes as $size) {
    echo "Creating favicon-{$size}x{$size}.png...\n";
    
    // Create a new empty image of this size
    $favicon = imagecreatetruecolor($size, $size);
    
    // Preserve alpha channel
    imagealphablending($favicon, false);
    imagesavealpha($favicon, true);
    
    // Make it transparent
    $transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
    imagefilledrectangle($favicon, 0, 0, $size, $size, $transparent);
    
    // Scale the source image to fit the favicon size
    imagecopyresampled($favicon, $sourceImage, 0, 0, 0, 0, $size, $size, $sourceWidth, $sourceHeight);
    
    // Save the favicon
    $output_path = __DIR__ . "/favicon-{$size}x{$size}.png";
    imagepng($favicon, $output_path);
    echo "Saved to: $output_path\n";
    
    // Free memory
    imagedestroy($favicon);
}

// Create favicon.ico (for IE)
$icoPath = __DIR__ . '/favicon.ico';
echo "Creating favicon.ico at $icoPath\n";
copy(__DIR__ . '/favicon-32x32.png', $icoPath);
echo "Created favicon.ico\n";

// Create apple-touch-icon
$appleTouchIconPath = __DIR__ . '/apple-touch-icon.png';
echo "Creating apple-touch-icon.png at $appleTouchIconPath\n";
copy(__DIR__ . '/favicon-192x192.png', $appleTouchIconPath);
echo "Created apple-touch-icon.png\n";

// Clean up
imagedestroy($sourceImage);

echo "Favicon files generated successfully!\n"; 