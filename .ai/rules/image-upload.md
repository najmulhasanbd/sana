# Image Upload & Dimension Guidelines

## 1. Universal Intervention Image & WebP Conversion
- **All uploaded images** across the application (logo, favicon, banners, fish fry photos, product pictures, profile images, documents) **must be processed through Intervention Image (`intervention/image-laravel`) and saved exclusively in `.webp` format**.
- Use the central service `App\Services\ImageService`:
  ```php
  use App\Services\ImageService;

  public function __construct(protected ImageService $imageService) {}

  // Inside Controller store/update:
  if ($request->hasFile('image_field')) {
      $path = $this->imageService->uploadWebp(
          file: $request->file('image_field'),
          directory: 'uploads/folder_name',
          prefix: 'prefix_name',
          maxWidth: 800,        // optional proportional scaleDown
          maxHeight: 600,       // optional
          oldPath: $model->image_field // automatically unlinks old image if exists
      );
      $model->image_field = $path;
  }
  ```

## 2. Mandatory Dimension Guidelines on Every Upload Field
- **Whenever an image upload input is rendered**, always display clear recommended pixel dimensions and format requirements below the input.
- Use the reusable component `<x-image-dimension-help />`:
  ```blade
  <input type="file" name="product_image" class="form-control" accept="image/*">
  <x-image-dimension-help width="600" height="400" format="WebP, PNG, JPG" max="2MB" note="অনুপাত ৩:২" />
  ```
- Standard dimension presets:
  - **Main Logo:** 240x60 px or 250x70 px (WebP / PNG)
  - **Browser Favicon:** 32x32 px or 64x64 px (Square)
  - **Banners / Hero Images:** 1200x500 px or 1920x600 px
  - **Product / Fish Fry Cards:** 600x450 px (4:3) or 500x500 px (1:1)
  - **User Avatars:** 200x200 px (1:1)

## 3. WebP Conversion Badge
- When designing image upload cards/sections, show a subtle badge indicating auto-WebP conversion (e.g. `<span class="badge bg-primary bg-opacity-10 text-primary">WebP কনভার্ট</span>`) to reassure users that files are optimized for fast web loading.
