<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Http\Requests\StoreConfiguration;
use App\Http\Requests\UpdateConfiguration;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;

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
            'sector_id' => auth()->user()->sector->id ?? null,
            'logo'      => $logo,
            'contact'   => $data['contact'],
            'copyright' => $data['copyright']
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou uma nova configuração"
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

        $configuration->sector_id = auth()->user()->sector->id ?? null;
        $configuration->contact   = $data['contact'];
        $configuration->copyright = $data['copyright'];

        $configuration->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Atualizou a configuração"
        ]);

        return redirect()
            ->route('configuration')
            ->with('status', __('Configuration updated successfully'))
        ;
    }
}
