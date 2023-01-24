<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Http\Requests\StoreConfiguration;
use App\Http\Requests\UpdateConfiguration;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    public function index()
    {
        return view('configuration')->with('configuration', Configuration::where('sector_id', auth()->user()->sector->id ?? null)->first());
    }

    public function store(StoreConfiguration $request)
    {
        $data = $request->validated();
        $logo = null;

        if (isset($data['logo'])) $logo = $data['logo']->store('configurations', 'public');

        Configuration::create([
            'sector_id' => auth()->user()->sector->id,
            'logo'      => $logo,
            'contact'   => $data['contact'],
            'copyright' => $data['copyright']
        ]);

        return redirect()
            ->route('configuration')
            ->with('status', __('Configuration updated successfully'))
        ;
    }

    public function update(UpdateConfiguration $request, $configuration)
    {
        $data = $request->validated();
        $logo = null;

        if (isset($data['logo'])) {
            Storage::delete($configuration->logo);

            $configuration->logo = $data['logo']->store('configurations', 'public');
        }

        $configuration->sector_id = auth()->user()->sector->id;
        $configuration->contact   = $data['contact'];
        $configuration->copyright = $data['copyright'];

        $configuration->save();

        return redirect()
            ->route('configuration')
            ->with('status', __('Configuration updated successfully'))
        ;
    }
}
