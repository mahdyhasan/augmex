<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Traits\ImageTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    use ImageTrait;

    public function show()
    {
        return view('setting.index');
    }

    /**
     * @throws Exception
     */
    public function list(): JsonResponse
    {
        $setting = Setting::all();
        return DataTables::of($setting)
            ->addColumn('logo', function (Setting $setting) {
                if (isset($setting->logo)) {
                    return '<img height="50px" width="50px" src="' . url($setting->logo) . '" alt="">';
                }
                return '';
            })
            ->setRowAttr([
                'align'=>'center',
            ])
            ->rawColumns(['logo'])
            ->make(true);
    }

    public function create()
    {
        return view('setting.create');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validate($request, [
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp',
            'playstore_app_url' => 'nullable|string|max:255',
        ]);

        Setting::query()->create([
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'logo' => isset($validated['logo']) ? $this->save_image('settingImage', $validated['logo']) : null,
            'playstore_app_url' => $validated['playstore_app_url'],
        ]);

        Session::flash('success', 'Setting Created Successfully!');
        return redirect()->route('setting.show');
    }

    public function edit($id)
    {
        $setting = Setting::query()->where('id', $id)->first();
        return view('setting.edit', compact( 'setting'));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $this->validate($request, [
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'playstore_app_url' => 'nullable|string|max:255',
        ]);


        $setting = Setting::query()->where('id', $id)->first();
        if ($setting) {
            if (empty($validated['logo'])) {
                $logo = $setting->logo;
            } else {
                $this->deleteImage($setting->logo);
                $logo = $this->save_image('settingImage', $validated['logo']);
            }

            $setting->update([
                'company_name' => $validated['company_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'logo' => $logo,
                'playstore_app_url' => $validated['playstore_app_url'],
            ]);
        }

        Session::flash('success', 'Settings Updated Successfully!');
        return redirect()->route('setting.show');
    }
}
