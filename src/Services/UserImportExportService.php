<?php

namespace Tourad\UserManager\Services;

use Illuminate\Support\Collection;
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserType;
use Tourad\UserManager\Models\UserActivity;

class UserImportExportService
{
    /**
     * Export users to array format
     */
    public function exportUsers(array $filters = []): Collection
    {
        $query = User::with(['userType', 'roles']);

        // Apply filters similar to UserManagerService
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['user_type'])) {
            $query->where('user_type_id', $filters['user_type']);
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'verified':
                    $query->verified();
                    break;
                case 'unverified':
                    $query->unverified();
                    break;
            }
        }

        return $query->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at?->format('Y-m-d H:i:s'),
                'phone_verified_at' => $user->phone_verified_at?->format('Y-m-d H:i:s'),
                'last_login_at' => $user->last_login_at?->format('Y-m-d H:i:s'),
                'last_login_ip' => $user->last_login_ip,
                'timezone' => $user->timezone,
                'language' => $user->language,
                'user_type' => $user->userType?->name,
                'user_type_slug' => $user->userType?->slug,
                'roles' => $user->roles->pluck('name')->implode(', '),
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    /**
     * Export users to CSV format
     */
    public function exportToCsv(array $filters = []): string
    {
        $users = $this->exportUsers($filters);
        
        if ($users->isEmpty()) {
            return '';
        }

        $csv = '';
        $headers = array_keys($users->first());
        
        // Add CSV header
        $csv .= implode(',', array_map([$this, 'escapeCsvValue'], $headers)) . "\n";
        
        // Add data rows
        foreach ($users as $user) {
            $csv .= implode(',', array_map([$this, 'escapeCsvValue'], array_values($user))) . "\n";
        }

        return $csv;
    }

    /**
     * Import users from array
     */
    public function importUsers(array $usersData, bool $validateOnly = false): array
    {
        $imported = [];
        $errors = [];
        $duplicates = [];
        $skipped = [];

        foreach ($usersData as $index => $userData) {
            try {
                // Validate required fields
                $validation = $this->validateUserData($userData);
                if (!$validation['valid']) {
                    $errors[] = [
                        'row' => $index + 1,
                        'data' => $userData,
                        'errors' => $validation['errors'],
                    ];
                    continue;
                }

                // Check for duplicates
                $existingUser = User::where('email', $userData['email'])->first();
                if ($existingUser) {
                    $duplicates[] = [
                        'row' => $index + 1,
                        'email' => $userData['email'],
                        'existing_id' => $existingUser->id,
                    ];
                    continue;
                }

                // Skip if validation only
                if ($validateOnly) {
                    $skipped[] = [
                        'row' => $index + 1,
                        'data' => $userData,
                        'reason' => 'Validation only mode',
                    ];
                    continue;
                }

                // Process user type
                if (!empty($userData['user_type'])) {
                    $userType = UserType::where('slug', $userData['user_type'])
                        ->orWhere('name', $userData['user_type'])
                        ->first();
                    $userData['user_type_id'] = $userType?->id;
                    unset($userData['user_type']);
                }

                // Create user
                $userData['password'] = $userData['password'] ?? 'password123';
                $user = User::create($userData);

                // Assign roles if provided
                if (!empty($userData['roles'])) {
                    $roles = explode(',', $userData['roles']);
                    foreach ($roles as $roleName) {
                        $roleName = trim($roleName);
                        if ($user->hasRole($roleName) === false) {
                            $user->assignRole($roleName);
                        }
                    }
                }

                $imported[] = $user;

            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'data' => $userData,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Log import activity
        if (!$validateOnly) {
            UserActivity::log('bulk_user_import', "Imported " . count($imported) . " users", null, [
                'success_count' => count($imported),
                'error_count' => count($errors),
                'duplicate_count' => count($duplicates),
                'total_rows' => count($usersData),
            ]);
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
            'duplicates' => $duplicates,
            'skipped' => $skipped,
            'success_count' => count($imported),
            'error_count' => count($errors),
            'duplicate_count' => count($duplicates),
            'skipped_count' => count($skipped),
            'total_rows' => count($usersData),
        ];
    }

    /**
     * Import users from CSV
     */
    public function importFromCsv(string $csvContent, bool $validateOnly = false): array
    {
        $lines = explode("\n", trim($csvContent));
        
        if (empty($lines)) {
            return ['errors' => [['error' => 'CSV file is empty']]];
        }

        // Parse header
        $headers = str_getcsv($lines[0]);
        $users = [];

        // Parse data rows
        for ($i = 1; $i < count($lines); $i++) {
            if (empty(trim($lines[$i]))) {
                continue;
            }

            $values = str_getcsv($lines[$i]);
            
            // Skip rows with incorrect column count
            if (count($values) !== count($headers)) {
                continue;
            }

            $userData = array_combine($headers, $values);
            
            // Clean up data
            $userData = array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $userData);

            $users[] = $userData;
        }

        return $this->importUsers($users, $validateOnly);
    }

    /**
     * Validate user data
     */
    protected function validateUserData(array $userData): array
    {
        $errors = [];

        // Check required fields
        $requiredFields = ['name', 'email'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                $errors[] = "The {$field} field is required";
            }
        }

        // Validate email format
        if (!empty($userData['email']) && !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'The email format is invalid';
        }

        // Validate boolean fields
        $booleanFields = ['is_active', 'email_verified_at', 'phone_verified_at'];
        foreach ($booleanFields as $field) {
            if (isset($userData[$field]) && !is_bool($userData[$field]) && !in_array($userData[$field], ['0', '1', 'true', 'false', 'yes', 'no'])) {
                $errors[] = "The {$field} field must be a boolean value";
            }
        }

        // Validate user type exists
        if (!empty($userData['user_type'])) {
            $userType = UserType::where('slug', $userData['user_type'])
                ->orWhere('name', $userData['user_type'])
                ->first();
            
            if (!$userType) {
                $errors[] = "User type '{$userData['user_type']}' not found";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Escape CSV values
     */
    protected function escapeCsvValue($value): string
    {
        if (is_null($value)) {
            return '';
        }

        $value = (string) $value;

        // Escape quotes and wrap in quotes if necessary
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }

    /**
     * Get import template
     */
    public function getImportTemplate(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'username' => 'johndoe',
            'phone' => '+1234567890',
            'password' => 'password123',
            'is_active' => true,
            'email_verified_at' => '2024-01-01 00:00:00',
            'phone_verified_at' => null,
            'timezone' => 'UTC',
            'language' => 'en',
            'user_type' => 'user',
            'roles' => 'user, editor',
        ];
    }

    /**
     * Get import template as CSV
     */
    public function getImportTemplateCsv(): string
    {
        $template = $this->getImportTemplate();
        $headers = array_keys($template);
        $values = array_values($template);

        $csv = implode(',', $headers) . "\n";
        $csv .= implode(',', array_map([$this, 'escapeCsvValue'], $values)) . "\n";

        return $csv;
    }
}