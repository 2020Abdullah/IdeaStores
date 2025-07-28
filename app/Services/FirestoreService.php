<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;

class FirestoreService
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'projectId' => config('services.firestore.project_id'),
            'keyFilePath' => config('services.firestore.credentials'),
        ]);
    }

    // إضافة مستند
    public function add(string $collection, array $data): string
    {
        $docRef = $this->firestore->collection($collection)->add($data);
        return $docRef->id();
    }

    // تحديث مستند
    public function update(string $collection, string $id, array $data)
    {
        $this->firestore->collection($collection)->document($id)->update($data);
    }

    // حذف مستند
    public function delete(string $collection, string $id)
    {
        $this->firestore->collection($collection)->document($id)->delete();
    }

    // جلب مستند
    public function get(string $collection, string $id): array
    {
        $snapshot = $this->firestore->collection($collection)->document($id)->snapshot();
        return $snapshot->exists() ? $snapshot->data() : [];
    }

    // جلب حسب شرط
    public function getWhere(string $collection, string $field, $value): array
    {
        $documents = $this->firestore
            ->collection($collection)
            ->where($field, '=', $value)
            ->documents();

        $results = [];
        foreach ($documents as $doc) {
            $results[$doc->id()] = $doc->data();
        }

        return $results;
    }
}
