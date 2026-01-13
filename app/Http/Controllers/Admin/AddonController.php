<?php

namespace App\Http\Controllers\Admin;

use ZipArchive;
use Illuminate\Http\Request;
use Nwidart\Modules\Facades\Module;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB; // <-- Add this

class AddonController extends Controller
{
    public function index()
    {
        return view('admin.addons.index');
    }
// public function store(Request $request)
// {
//     $request->validate([
//         'purchase_code' => 'required', // keep it for UI only
//         'file' => 'required|file|mimes:zip',
//     ]);

//     try {
//         // 📦 Get module name from zip file
//         $module_name = pathinfo(
//             $request->file('file')->getClientOriginalName(),
//             PATHINFO_FILENAME
//         );

//         // ✅ ALWAYS VALID (SKIP ENVATO)
//         $is_valid = true;

//         if (!$is_valid) {
//             return response()->json(['message' => 'Invalid purchase code'], 406);
//         }

//         $uploadedFile = $request->file('file');
//         $zip = new ZipArchive;
//         $tempFilePath = $uploadedFile->getRealPath();

//         if ($zip->open($tempFilePath) === TRUE) {

//             // 📁 Modules directory
//             $destinationPath = base_path('Modules');

//             if (!File::exists($destinationPath)) {
//                 File::makeDirectory($destinationPath, 0755, true);
//             }

//             // 📦 Extract addon
//             $zip->extractTo($destinationPath);
//             $zip->close();

//             // 🧱 Run module migrations
//             $moduleMigrationsPath = base_path("Modules/{$module_name}/Database/migrations");
//             if (File::exists($moduleMigrationsPath)) {
//                 Artisan::call('migrate', ['--force' => true]);
//             }

//             // 🌱 Seed module if not seeded
//             if (!moduleCheck($module_name)) {
//                 Artisan::call('module:seed', ['module' => $module_name]);
//             }

//             // ✅ Enable module
//             $filePath = base_path('modules_statuses.json');
//             $data = json_decode(File::get($filePath), true);
//             $data[$module_name] = true;
//             File::put($filePath, json_encode($data, JSON_PRETTY_PRINT));

//             // 🧹 Clear caches
//             Artisan::call('cache:clear');
//             Artisan::call('config:clear');
//             Artisan::call('route:clear');
//             Artisan::call('view:clear');

//             return response()->json([
//                 'message' => 'Addon installed successfully',
//                 'redirect' => route('admin.addons.index'),
//             ]);
//         }

//         return response()->json(['message' => 'Failed to open ZIP'], 406);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }
public function store(Request $request)
{
    $request->validate([
        'purchase_code' => 'required', // keep for UI only
        'file' => 'required|file|mimes:zip',
    ]);

    try {
        // Get module name from ZIP file
        $module_name = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);

        // Always valid, skip Envato check
        $is_valid = true;

        if (!$is_valid) {
            return response()->json(['message' => 'Invalid purchase code'], 406);
        }

        $uploadedFile = $request->file('file');
        $zip = new ZipArchive;
        $tempFilePath = $uploadedFile->getRealPath();

        if ($zip->open($tempFilePath) === TRUE) {

            // Ensure Modules folder exists
            $destinationPath = base_path('Modules');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Extract addon ZIP
            $zip->extractTo($destinationPath);
            $zip->close();

            // Run module migrations if they exist
            $moduleMigrationsPath = base_path("Modules/{$module_name}/Database/migrations");
            if (File::exists($moduleMigrationsPath)) {
                Artisan::call('migrate', [
                    '--path' => "Modules/{$module_name}/Database/migrations",
                    '--force' => true
                ]);
            }

            // Seed module if not already seeded
            if (!moduleCheck($module_name)) {
                Artisan::call('module:seed', ['module' => $module_name]);
            }

            // Enable module in modules_statuses.json
            $filePath = base_path('modules_statuses.json');
            $data = json_decode(File::get($filePath), true);
            $data[$module_name] = true;
            File::put($filePath, json_encode($data, JSON_PRETTY_PRINT));

            // Clear Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'message' => "Addon '{$module_name}' installed successfully.",
                'redirect' => route('admin.addons.index'),
            ]);
        }

        return response()->json(['message' => 'Failed to open ZIP file'], 406);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}




    public function show($module)
    {
        $module = Module::findOrFail($module);
        if ($module->isEnabled()) {
            $module->disable();
        } else {
            $module->enable();
        }

        return response()->json([
            'message' => 'Addon'
        ]);
    }
}
