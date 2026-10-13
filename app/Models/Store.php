<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Automatically create the receiving permission
     * whenever a new store is created.
     */
    protected static function booted(): void
    {
        static::created(function (Store $store) {
            $permissionName = 'RECEIVE ITEMS TO ' .
                strtoupper(trim($store->name));

            Permission::findOrCreate($permissionName, 'web');
        });

        /**
         * Keep the permission name synchronized when
         * an existing store is renamed.
         */
        static::updated(function (Store $store) {
            if ($store->wasChanged('name')) {
                $oldPermissionName = 'RECEIVE ITEMS TO ' .
                    strtoupper(trim($store->getOriginal('name')));

                $newPermissionName = 'RECEIVE ITEMS TO ' .
                    strtoupper(trim($store->name));

                if ($oldPermissionName !== $newPermissionName) {
                    $oldPermission = Permission::query()
                        ->where('name', $oldPermissionName)
                        ->where('guard_name', 'web')
                        ->first();

                    if ($oldPermission) {
                        $newPermission = Permission::findOrCreate(
                            $newPermissionName,
                            'web'
                        );

                        // Transfer role assignments to the new permission.
                        foreach ($oldPermission->roles as $role) {
                            $role->givePermissionTo($newPermission);
                        }

                        // Transfer direct user assignments.
                        foreach ($oldPermission->users as $user) {
                            $user->givePermissionTo($newPermission);
                        }

                        // Remove the obsolete permission.
                        $oldPermission->delete();
                    }
                }
            }
        });
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(StoreStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StoreStockMovement::class);
    }

    public function sourceRequisitions(): HasMany
    {
        return $this->hasMany(
            StoreRequisition::class,
            'source_store_id'
        );
    }

    public function destinationRequisitions(): HasMany
    {
        return $this->hasMany(
            StoreRequisition::class,
            'destination_store_id'
        );
    }
}