<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCredential;
use App\Models\AdminCredential;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Check credentials against either user_credentials (by phone) or admin_credentials (by username).
     * If validated, log in the related User model and redirect based on role.
     *
     * If the request includes 'login_for', the controller will only consult the corresponding
     * credential table:
     *   - login_for in ['user','resident','employee'] => user_credentials only
     *   - login_for in ['admin','approver','treasurer','moderator'] => admin_credentials only
     *
     * If login_for is not provided, the controller falls back to original behavior:
     *   try user_credentials (phone) first, then admin_credentials (username).
     */
    public function check(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string', // reused for username or phone
            'password' => 'required|string',
            'login_for' => 'nullable|string',
        ]);

        $rawLogin = trim($data['phone']);
        $providedPassword = $data['password'];
        $loginFor = strtolower(trim($data['login_for'] ?? ''));

        // Decide strict mode: only user credentials, only admin credentials, or fallback (null)
        $userTargets = ['user', 'resident', 'employee'];
        $adminTargets = ['admin', 'approver', 'treasurer', 'moderator'];

        if (in_array($loginFor, $userTargets, true)) {
            $mode = 'user_only';
        } elseif (in_array($loginFor, $adminTargets, true)) {
            $mode = 'admin_only';
        } else {
            $mode = 'fallback';
        }

        // normalize phone (digits only) for user credential lookup
        $phoneOnly = preg_replace('/\D/', '', $rawLogin);

        // Helper closures for returning specific error messages
        $invalidContactNumberResponse = function () use ($request) {
            return back()->withErrors(['phone' => 'Incorrect contact number. Please try again.'])->withInput();
        };
        
        $invalidPasswordResponse = function () use ($request) {
            return back()->withErrors(['password' => 'Wrong password. Please try again.'])->withInput();
        };
        
        // Generic error for fallback cases
        $invalidCredentialsResponse = function ($field = 'phone') use ($request) {
            if ($field === 'password') {
                return back()->withErrors(['password' => 'Wrong password. Please try again.'])->withInput();
            }
            return back()->withErrors(['phone' => 'Incorrect contact number. Please try again.'])->withInput();
        };

        // -------------------------
        // USER-CREDENTIALS PATH
        // -------------------------
        $attemptUserCredentials = function () use ($phoneOnly, $providedPassword, $invalidContactNumberResponse, $invalidPasswordResponse) {
            if (empty($phoneOnly)) {
                return null; // cannot try user credentials without numeric phone
            }

            $credential = UserCredential::with('user')
                ->where('contact_number', $phoneOnly)
                ->orWhere('secondary_contact_number', $phoneOnly)
                ->first();

            if (! $credential) {
                return null;
            }

            // Check if user exists (user might have been deleted)
            $user = $credential->user ?? $credential->users ?? null;
            if (! $user) {
                // User was deleted but credential still exists - clean it up
                $credential->delete();
                \Log::info('Deleted orphaned user credential during login attempt', [
                    'phone_number' => $phoneOnly,
                    'user_cred_id' => $credential->user_cred_id
                ]);
                return back()->withErrors(['phone' => 'Account not found. The account may have been deleted. Please register again.'])->withInput();
            }

            // verify password (assuming hashed in DB)
            if (! Hash::check($providedPassword, $credential->password)) {
                return $invalidPasswordResponse();
            }

            // optional approval check
            if (isset($credential->is_approved) && ! $credential->is_approved) {
                return back()->withErrors(['phone' => 'Your registration is pending admin approval.'])->withInput();
            }

            Auth::login($user);

            $roleId = $user->fk_role_id ?? $user->role_id ?? null;

            Session::put('logged_users', [
                'id' => $user->id,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'contact_number' => $credential->contact_number,
                'secondary_number' => $credential->secondary_contact_number ?? null,
                'role_id' => $roleId,
            ]);

            return $this->redirectByRole($roleId);
        };

        // -------------------------
        // ADMIN-CREDENTIALS PATH
        // -------------------------
        $attemptAdminCredentials = function () use ($rawLogin, $providedPassword, $invalidContactNumberResponse, $invalidPasswordResponse) {
            $adminCred = AdminCredential::with('user')
                ->where('username', $rawLogin)
                ->first();

            if (! $adminCred) {
                return null;
            }

            $storedHash = $adminCred->password;
            $passwordVerified = false;
            $usedLegacy = false;

            // detect bcrypt-ish hash
            $isBcrypt = is_string($storedHash) &&
                (strpos($storedHash, '$2y$') === 0 || strpos($storedHash, '$2a$') === 0) &&
                strlen($storedHash) === 60;

            if ($isBcrypt) {
                if (Hash::check($providedPassword, $storedHash)) {
                    $passwordVerified = true;

                    if (Hash::needsRehash($storedHash)) {
                        $adminCred->password = Hash::make($providedPassword);
                        $adminCred->save();
                    }
                }
            } else {
                // Legacy checks: MD5, SHA1, plaintext (adjust as needed)
                if (!$passwordVerified && ctype_xdigit($storedHash) && strlen($storedHash) === 32) {
                    if (hash_equals($storedHash, md5($providedPassword))) {
                        $passwordVerified = true;
                        $usedLegacy = 'md5';
                    }
                }

                if (!$passwordVerified && ctype_xdigit($storedHash) && strlen($storedHash) === 40) {
                    if (hash_equals($storedHash, sha1($providedPassword))) {
                        $passwordVerified = true;
                        $usedLegacy = 'sha1';
                    }
                }

                if (!$passwordVerified && hash_equals((string)$storedHash, $providedPassword)) {
                    $passwordVerified = true;
                    $usedLegacy = 'plain';
                }

                if ($passwordVerified && $usedLegacy) {
                    // migrate to bcrypt
                    $adminCred->password = Hash::make($providedPassword);
                    $adminCred->save();
                    \Log::info("AdminCredential ID {$adminCred->id} migrated legacy password (type: {$usedLegacy}) to bcrypt on login.");
                }
            }

            if (! $passwordVerified) {
                return $invalidPasswordResponse();
            }

            if (isset($adminCred->is_approved) && ! $adminCred->is_approved) {
                return back()->withErrors(['phone' => 'Your admin account is pending approval.'])->withInput();
            }

            $user = $adminCred->user ?? $adminCred->users ?? null;
            if (! $user) {
                // User was deleted but admin credential still exists - clean it up
                $adminCred->delete();
                \Log::info('Deleted orphaned admin credential during login attempt', [
                    'username' => $rawLogin,
                    'admin_cred_id' => $adminCred->admin_cred_id
                ]);
                return back()->withErrors(['phone' => 'Account not found. The account may have been deleted. Please contact administrator.'])->withInput();
            }

            Auth::login($user);

            $roleId = $user->fk_role_id ?? $user->role_id ?? null;

            Session::put('logged_users', [
                'id' => $user->id,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'contact_number' => $user->contact_number ?? null,
                'secondary_number' => $user->secondary_number ?? null,
                'role_id' => $roleId,
            ]);

            return $this->redirectByRole($roleId);
        };

        // -------------------------
        // Execute according to mode
        // -------------------------
        if ($mode === 'user_only') {
            $result = $attemptUserCredentials();
            // attemptUserCredentials returns redirect Response when successful or a RedirectResponse on errors,
            // or null if not found. If null -> return invalid contact number error.
            return $result ?? $invalidContactNumberResponse();
        }

        if ($mode === 'admin_only') {
            $result = $attemptAdminCredentials();
            return $result ?? $invalidContactNumberResponse();
        }

        // fallback: try user credentials first, then admin credentials
        $result = $attemptUserCredentials();
        if ($result !== null) {
            return $result;
        }

        $result = $attemptAdminCredentials();
        if ($result !== null) {
            return $result;
        }

        // nothing matched - contact number not found
        return $invalidContactNumberResponse();
    }

    /**
     * Helper: decide where to redirect based on role_id.
     */
    protected function redirectByRole($roleId)
    {
        if ($roleId === 1) {
            $routeName = 'discussion_resident';
        } elseif (in_array($roleId, [2,5,6,7], true)) {
            $routeName = 'discussion_employee';
        } elseif ($roleId === 3) {
            $routeName = 'document_request_approver';
        } elseif ($roleId === 4) {
            $routeName = 'view_payment_treasurer';
        } elseif ($roleId === 9) {
            $routeName = 'dashboard_admin';
        } else {
            return redirect()->route('login')->with('error', 'Unknown role, redirected to login.');
        }

        if (Route::has($routeName)) {
            return redirect()->route($routeName);
        }

        return redirect()->route('login')->with('error', 'Unknown role, redirected to home.');
    }
}
