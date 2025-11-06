<?php /** @noinspection ALL */

namespace App\Modules\Media\Services;

use App\Modules\Media\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageService {
    /**
     * Create a new image record
     *
     * @param array $data
     * @return Image
     * @throws \Throwable
     * @throws \Throwable
     */
    public function create(array $data): Image {
        return DB::transaction(function () use ($data) {
            // Handle file upload if provided
            if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
                $file = $data['file'];
                $data['file_path'] = $file->store('images', 'public');
                $data['file_name'] = $file->getClientOriginalName();
                unset($data['file']);
            }

            // Create image
            $image = Image::create($data);

            return $image->fresh();
        });
    }

    /**
     * Update an existing image
     *
     * @param Image $image
     * @param array $data
     * @return Image
     * @throws \Throwable
     * @throws \Throwable
     */
    public function update(Image $image, array $data): Image {
        return DB::transaction(function () use ($image, $data) {
            // Replace file if new upload provided
            if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
                // Delete old file
                if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
                    Storage::disk('public')->delete($image->file_path);
                }

                // Store new file
                $file = $data['file'];
                $data['file_path'] = $file->store('images', 'public');
                $data['file_name'] = $file->getClientOriginalName();
                unset($data['file']);
            }

            // Update image
            $image->update($data);

            return $image->fresh();
        });
    }

    /**
     * Delete an image
     *
     * @param Image $image
     * @return bool
     * @throws \Throwable
     * @throws \Throwable
     */
    public function delete(Image $image): bool {
        return DB::transaction(function () use ($image) {
            // Delete file from storage
            if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
                Storage::disk('public')->delete($image->file_path);
            }

            // Delete record
            return $image->delete();
        });
    }

    /**
     * Upload image with full workflow
     *
     * @param UploadedFile $file
     * @param string $type
     * @param string $entityType
     * @param int $entityId
     * @return Image
     * @throws \Throwable
     * @throws \Throwable
     */
    public function uploadImage(UploadedFile $file, string $type, string $entityType, int $entityId): Image {
        return DB::transaction(function () use ($file, $type, $entityType, $entityId) {
            // Store file
            $filePath = $file->store("images/{$entityType}", 'public');
            $fileName = $file->getClientOriginalName();

            // Create image record
            $image = Image::create([
                'type' => $type,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'is_primary' => false,
            ]);

            return $image->fresh();
        });
    }

    /**
     * Set image as primary for an entity
     *
     * @param Image $image
     * @return Image
     * @throws \Throwable
     * @throws \Throwable
     */
    public function setPrimary(Image $image): Image {
        return DB::transaction(function () use ($image) {
            // Unset other images as primary for same entity
            Image::where('entity_type', $image->entity_type)
                ->where('entity_id', $image->entity_id)
                ->where('id', '!=', $image->id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            return $image->fresh();
        });
    }

    /**
     * Get all images for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return Collection
     */
    public function getImagesForEntity(string $entityType, int $entityId): Collection {
        return Image::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get primary image for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return Image|null
     */
    public function getPrimaryImage(string $entityType, int $entityId): ?Image {
        return Image::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('is_primary', true)
            ->first();
    }
}
